<?php

namespace App\Http\Controllers\Employer;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Opening;
use App\Models\Userjob;
use App\Models\Category;
use App\Models\Location;
use App\Traits\Uploader;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\ApplicantExport;
use Illuminate\Support\Facades\DB;
use App\Models\DescriptionTemplate;
use App\Http\Controllers\Controller;
use App\Models\CandidateReview;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class OpportunityController extends Controller
{
    use Uploader;

    public function index(Request $request)
    {
        $user = User::findOrFail(auth()->id());

        $opportunities = $user->jobs()->with(['categories', 'user:id,name,created_at', 'country'])
            ->withCount('appliedApplicants')
            ->when($request->filled('status'), function ($query) {
                $status = match (request('status')) {
                    'active' => 1,
                    'pending' => 2,
                    default => 0,
                };
                $query->where('status', $status);
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->ofCategory($request->category);
            })
            ->orderBy('created_at', in_array($request->order, ['desc', 'asc']) ? $request->order : 'desc')
            ->paginate(10);

        return Inertia::render('Employer/Opportunity/Index', [
            'opportunities' => $opportunities,
            'request' => $request,
            'opportunityCategories' => array_keys(Opening::OPPORTUNITY_CATEGORIES),
        ]);
    }

    public function show($slug)
    {
        $user = User::findOrFail(auth()->id());
        $opportunity = $user->jobs()->where('slug', $slug)->firstOrFail();

        if (request('export')) {
            return Excel::download(new ApplicantExport($opportunity), $slug . '-applicants.csv');
        }

        $applicants = $opportunity->appliedApplicants()
            ->with(['countries', 'states', 'categories', 'tags'])
            ->withCount(['candidateBookmarks as isBookmarked' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->orderBy('created_at', request()->order ?? 'desc')
            ->paginate(10);

        return Inertia::render('Employer/Opportunity/Show', [
            'applicants' => $applicants,
            'opportunity' => $opportunity,
            'opportunityCategory' => $opportunity->getOpportunityCategory(),
            'opportunityTypeDisplay' => $opportunity->getOpportunityTypeDisplay(),
        ]);
    }

    public function create()
    {
        $user = User::findOrFail(auth()->id());
        $services = Category::query()->where('type', Category::TYPE_OPPORTUNITY_SERVICE)->get();
        $categories = Category::query()->where('type', Category::TYPE_OPPORTUNITY_CATEGORY)->get();
        $countries = Location::query()->whereNull('location_id')->get();
        $currencies = collect(json_decode(file_get_contents(base_path('database/json/currencies.json')), true))
            ->values()->toArray();
        $shortcodes = array_map(function ($el) {
            return preg_replace('/\[|\]/', '', $el);
        }, DescriptionTemplate::getShortCodes());
        
        return Inertia::render('Employer/Opportunity/Create', [
            'services' => $services,
            'categories' => $categories,
            'countries' => $countries,
            'plan' => $user->plan,
            'opportunity_count' => $user->jobs()->count(),
            'currencies' => $currencies,
            'shortcodes' => Arr::flatten($shortcodes),
            'user' => $user,
            'opportunityTypes' => Opening::OPPORTUNITY_TYPES,
            'opportunityCategories' => Opening::OPPORTUNITY_CATEGORIES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = User::findOrFail(auth()->id());
        $expirationDate = Carbon::parse($user->will_expire);
        
        if (!$user->plan) {
            return back()->with('error', 'You have not purchased a plan.');
        }
        
        if ($expirationDate->isPast()) {
            return back()->with('error', 'You have reached your opportunity post limit. Please upgrade your plan!');
        }
        
        if ($user->plan['job_limit'] !== -1 && $user->jobs()->count() >= $user->plan['job_limit']) {
            return back()->with('error', 'You have reached your opportunity post limit. Please upgrade your plan.');
        }
        
        $currencies = collect(json_decode(file_get_contents(base_path('database/json/currencies.json')), true))
            ->pluck('code')
            ->toArray();

        $isRemote = to_bool($request->meta['is_remote'] ?? false);
        
        // Base validation rules for all opportunity types
        $validationRules = [
            'title' => 'required|string',
            'description' => 'required|string',
            'short_description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'service_id' => 'required|exists:categories,id',
            'type' => 'required|string',
            'address' => $isRemote ? '' : 'required|string',
            'apply_type' => 'required',
            'expired_at' => 'nullable|date|after:today',
            'meta.apply_type.value' => [Rule::requiredIf(in_array($request->apply_type, [1, 2]))],
        ];
        
        // Add location validation if not remote
        if (!$isRemote) {
            $validationRules['country_id'] = 'required|exists:locations,id';
            $validationRules['state_id'] = 'required|exists:locations,id';
        }
        
        // Add job-specific validation rules if this is a job opportunity
        $opportunityCategory = '';
        foreach (Opening::OPPORTUNITY_CATEGORIES as $category => $types) {
            if (in_array($request->type, $types)) {
                $opportunityCategory = $category;
                break;
            }
        }
        
        if ($opportunityCategory === 'job') {
            $validationRules = array_merge($validationRules, [
                'salary_type' => 'required|string',
                'currency' => ['required', Rule::in($currencies), 'string'],
                'min_salary' => [Rule::requiredIf(isset($request->max_salary)), 'nullable', 'integer'],
                'max_salary' => [Rule::requiredIf(isset($request->min_salary)), 'nullable', 'integer'],
                'experience' => 'required|string',
                'expertise' => 'required|string',
            ]);
        }
        
        // Add attachment validation
        $validationRules['attachment'] = 'nullable|file|mimes:pdf,doc,docx';
        
        $request->validate($validationRules, [
            'category_id' => 'Category is required',
            'service_id' => 'Service is required',
            'country_id' => 'Country is required',
            'state_id' => 'State is required',
            'meta.apply_type.value.required' => 'Apply type is required',
        ]);

        if ($request->filled('fields')) {
            $request->validate([
                'fields.*.label' => 'required|string',
                'fields.*.type' => 'required|in:email,text,number,file',
            ], [
                'fields.*.label.required' => 'Label is required',
                'fields.*.type.required' => 'Type is required',
            ]);
        }

        $opportunityCount = intval(Opening::where('title', $request->title)->count() ?? 0);
        $slug = Str::of($request->title)->slug()
            ->append($opportunityCount > 0 ? ($opportunityCount + 1) : '');

        DB::beginTransaction();
        try {
            if ($request->hasFile('attachment')) {
                $attachment = $this->saveFile($request, 'attachment');
            }
            
            if ($request->filled('min_salary') && $request->filled('max_salary')) {
                $salaryRange = $request->min_salary . '-' . $request->max_salary;
            }
            
            $opening = Opening::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'short_description' => $request->short_description,
                'category_id' => $request->service_id,
                'type' => $request->type,
                'salary_type' => $request->salary_type ?? null,
                'currency' => $request->currency ?? null,
                'salary_range' => $salaryRange ?? null,
                'experience' => $request->experience ?? null,
                'expertise' => $request->expertise ?? null,
                'attachment' => $attachment ?? null,
                'address' => $request->address,
                'status' => 2,
                'apply_type' => $request->apply_type ?? 0,
                'expired_at' => $request->expired_at ?? null,
                'fields' => is_array($request->fields) ? json_encode($request->fields) : null,
                'meta' => json_encode($request->meta) ?? null,
                'live_expire_at' => now()->addDays($user->plan['live_job_for_days']),
                'featured_expire_at' => now()->addDays(30),
            ]);

            $opening->categories()->sync(array_merge($request->skills ?? [], [$request->category_id]));
            
            if (!$isRemote) {
                $opening->country()->sync([$request->country_id => ['state_id' => $request->state_id]]);
            }

            Notification::create([
                'user_id' => 1,
                'title' => 'New opportunity posted',
                'message' => 'A new opportunity has been posted by ' . $user->name,
                'link' => '/admin/opportunity/' . $opening->id,
            ]);

            DB::commit();
            return redirect()->route('employer.opportunity.index')->with('success', 'Opportunity created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        $user = User::findOrFail(auth()->id());
        $opportunity = $user->jobs()->where('slug', $slug)->firstOrFail();
        
        $services = Category::query()->where('type', Category::TYPE_OPPORTUNITY_SERVICE)->get();
        $categories = Category::query()->where('type', Category::TYPE_OPPORTUNITY_CATEGORY)->get();
        $countries = Location::query()->whereNull('location_id')->get();
        
        $currencies = collect(json_decode(file_get_contents(base_path('database/json/currencies.json')), true))
            ->values()->toArray();
            
        $shortcodes = array_map(function ($el) {
            return  preg_replace('/\[|\]/', '', $el);
        }, DescriptionTemplate::getShortCodes());
        
        $states = [];
        if ($opportunity->country->isNotEmpty()) {
            $states = Location::query()
                ->where('location_id', $opportunity->country->first()->id)
                ->get();
        }
        
        return Inertia::render('Employer/Opportunity/Edit', [
            'opportunity' => $opportunity->load(['categories', 'country']),
            'services' => $services,
            'categories' => $categories,
            'countries' => $countries,
            'states' => $states,
            'currencies' => $currencies,
            'shortcodes' => Arr::flatten($shortcodes),
            'opportunityTypes' => Opening::OPPORTUNITY_TYPES,
            'opportunityCategories' => Opening::OPPORTUNITY_CATEGORIES,
            'opportunityCategory' => $opportunity->getOpportunityCategory(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail(auth()->id());
        $opportunity = $user->jobs()->findOrFail($id);
        
        $currencies = collect(json_decode(file_get_contents(base_path('database/json/currencies.json')), true))
            ->pluck('code')
            ->toArray();

        $isRemote = to_bool($request->meta['is_remote'] ?? false);
        
        // Base validation rules for all opportunity types
        $validationRules = [
            'title' => 'required|string',
            'description' => 'required|string',
            'short_description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'service_id' => 'required|exists:categories,id',
            'type' => 'required|string',
            'address' => $isRemote ? '' : 'required|string',
            'apply_type' => 'required',
            'expired_at' => 'nullable|date|after:today',
            'meta.apply_type.value' => [Rule::requiredIf(in_array($request->apply_type, [1, 2]))],
        ];
        
        // Add location validation if not remote
        if (!$isRemote) {
            $validationRules['country_id'] = 'required|exists:locations,id';
            $validationRules['state_id'] = 'required|exists:locations,id';
        }
        
        // Add job-specific validation rules if this is a job opportunity
        $opportunityCategory = '';
        foreach (Opening::OPPORTUNITY_CATEGORIES as $category => $types) {
            if (in_array($request->type, $types)) {
                $opportunityCategory = $category;
                break;
            }
        }
        
        if ($opportunityCategory === 'job') {
            $validationRules = array_merge($validationRules, [
                'salary_type' => 'required|string',
                'currency' => ['required', Rule::in($currencies), 'string'],
                'min_salary' => [Rule::requiredIf(isset($request->max_salary)), 'nullable', 'integer'],
                'max_salary' => [Rule::requiredIf(isset($request->min_salary)), 'nullable', 'integer'],
                'experience' => 'required|string',
                'expertise' => 'required|string',
            ]);
        }
        
        // Add attachment validation
        $validationRules['attachment'] = 'nullable|file|mimes:pdf,doc,docx';
        
        $request->validate($validationRules, [
            'category_id' => 'Category is required',
            'service_id' => 'Service is required',
            'country_id' => 'Country is required',
            'state_id' => 'State is required',
            'meta.apply_type.value.required' => 'Apply type is required',
        ]);

        if ($request->filled('fields')) {
            $request->validate([
                'fields.*.label' => 'required|string',
                'fields.*.type' => 'required|in:email,text,number,file',
            ], [
                'fields.*.label.required' => 'Label is required',
                'fields.*.type.required' => 'Type is required',
            ]);
        }

        DB::beginTransaction();
        try {
            if ($request->hasFile('attachment')) {
                $this->removeFile($opportunity->attachment);
                $attachment = $this->saveFile($request, 'attachment');
            }
            
            if ($request->filled('min_salary') && $request->filled('max_salary')) {
                $salaryRange = $request->min_salary . '-' . $request->max_salary;
            }
            
            $opportunity->update([
                'title' => $request->title,
                'description' => $request->description,
                'short_description' => $request->short_description,
                'category_id' => $request->service_id,
                'type' => $request->type,
                'salary_type' => $request->salary_type ?? $opportunity->salary_type,
                'currency' => $request->currency ?? $opportunity->currency,
                'salary_range' => $salaryRange ?? $opportunity->salary_range,
                'experience' => $request->experience ?? $opportunity->experience,
                'expertise' => $request->expertise ?? $opportunity->expertise,
                'attachment' => $attachment ?? $opportunity->attachment,
                'address' => $request->address,
                'apply_type' => $request->apply_type ?? 0,
                'expired_at' => $request->expired_at ?? null,
                'fields' => is_array($request->fields) ? json_encode($request->fields) : $opportunity->fields,
                'meta' => json_encode($request->meta) ?? $opportunity->meta,
            ]);

            $opportunity->categories()->sync(array_merge($request->skills ?? [], [$request->category_id]));
            
            if (!$isRemote) {
                $opportunity->country()->sync([$request->country_id => ['state_id' => $request->state_id]]);
            } else {
                $opportunity->country()->detach();
            }

            DB::commit();
            return redirect()->route('employer.opportunity.index')->with('success', 'Opportunity updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateSeenAt(Request $request)
    {
        $user = User::findOrFail(auth()->id());
        $opportunity = $user->jobs()->findOrFail($request->opportunity_id);
        $application = $opportunity->appliedApplicants()->findOrFail($request->application_id);
        $application->pivot->update(['seen_at' => now()]);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail(auth()->id());
        $opportunity = $user->jobs()->findOrFail($id);
        $this->removeFile($opportunity->attachment);
        $opportunity->delete();
        return back()->with('success', 'Opportunity deleted successfully');
    }

    public function applicants()
    {
        $user = User::findOrFail(auth()->id());
        $applicants = Userjob::query()
            ->with(['user.countries', 'user.states', 'user.categories', 'user.tags', 'job'])
            ->whereHas('job', fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->paginate(10);

        return Inertia::render('Employer/Opportunity/Applicants', [
            'applicants' => $applicants,
        ]);
    }

    public function reviews()
    {
        $user = User::findOrFail(auth()->id());
        $reviews = CandidateReview::query()
            ->with(['user', 'job'])
            ->whereHas('job', fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->paginate(10);

        return Inertia::render('Employer/Opportunity/Reviews', [
            'reviews' => $reviews,
        ]);
    }
}
