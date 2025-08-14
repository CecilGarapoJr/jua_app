<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Opening;
use App\Models\Visitor;
use App\Models\Category;
use App\Models\Location;
use App\Mail\JobApplyAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Userjob;
use App\Services\SeoMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OpportunityController extends Controller
{
    public function index(Request $request, $slug = null)
    {
        $opportunitiesQuery = Opening::query()
            ->where('status', 1)
            ->where('live_expire_at', '>', today())
            ->when(request()->filled('keyword'), function ($query) {
                $query->where('title', 'like', '%' . request('keyword') . '%');
            })
            ->when(request()->filled('experience'), function ($query) {
                $query->where('experience', request('experience'));
            })
            ->when(request()->filled('opportunity_type'), function ($query) {
                $query->where('type', request('opportunity_type'));
            })
            ->when(request()->filled('opportunity_category'), function ($query) {
                $query->ofCategory(request('opportunity_category'));
            })
            ->when(request()->filled('currency'), function ($query) {
                $query->where('currency', request('currency'));
            })
            ->when(
                request()->filled('min_salary') && request()->filled('max_salary'),
                function ($query) use ($request) {
                    $query->whereRaw("CAST(SUBSTRING_INDEX(salary_range, '-', 1) AS UNSIGNED) >= ? AND CAST(SUBSTRING_INDEX(salary_range, '-', -1) AS UNSIGNED) <= ?", [$request->min_salary, $request->max_salary]);
                }
            )
            ->when(request()->filled('salary_type'), function ($query) {
                $query->where('salary_type', request('salary_type'));
            })
            ->when(request()->filled('is_remote'), function ($query) {
                $query->where('meta->is_remote', true);
            })->with([
                'country:id,name',
                'state:id,name',
                'user:id,avatar,created_at',
                'categories:id,title,slug',
                'tags:id,title,slug',
            ]);

        if ($slug) {
            $opportunitiesQuery->where(function ($query) use ($slug) {
                $query->whereHas('categories', function ($query) use ($slug) {
                    $query->where('slug', 'like', '%' . $slug . '%');
                })->orWhereHas('service', function ($query) use ($slug) {
                    $query->where('slug', 'like', '%' . $slug . '%');
                });
            });
        }

        if ($request->filled('category')) {
            $opportunitiesQuery->whereHas('categories', function ($query) {
                $query->where('slug', 'like', '%' . request('category') . '%');
            });
        }

        if ($request->filled('service')) {
            $opportunitiesQuery->whereHas('service', function ($query) {
                $query->where('slug', 'like', '%' . request('service') . '%');
            });
        }

        if ($request->filled('tags')) {
            $opportunitiesQuery->whereHas('categories', function ($query) {
                $query->whereIn('id', request('tags'));
            });
        }

        if ($request->filled('country')) {
            $opportunitiesQuery->whereHas('country', function ($query) {
                return $query->where('country_id', request('country'));
            });
        }
        
        if ($request->filled('state')) {
            $opportunitiesQuery->whereHas('state', function ($query) {
                return $query->where('state_id', request('state'));
            });
        }
        
        if (in_array(request('sort'), ['asc', 'desc'])) {
            $requestOrder = request('sort');
        }
        
        $opportunities = $opportunitiesQuery->orderBy('featured_expire_at', $requestOrder ?? 'desc')
            ->latest()
            ->paginate(8)->withQueryString();

        $locations = Location::query()->whereNull('location_id')->get();

        // Get opportunity types and counts
        $opportunityTypes = Opening::selectRaw('type, count(*) as count')
            ->where('status', 1)
            ->groupBy('type')
            ->get();
            
        // Get experience levels and counts
        $opportunityExperiences = Opening::selectRaw('experience, count(*) as count')
            ->where('status', 1)
            ->groupBy('experience')
            ->get();

        // Get services (opportunity services)
        $services = Category::query()
            ->select('id', 'title', 'slug')
            ->where('type', Category::TYPE_OPPORTUNITY_SERVICE)
            ->get();

        // Get categories (opportunity categories)
        $categories = Category::query()
            ->select('id', 'title', 'slug')
            ->where('type', Category::TYPE_OPPORTUNITY_CATEGORY)
            ->withCount('opportunities')
            ->get();

        // Get tags (opportunity tags)
        $tags = Category::query()
            ->select('id', 'title')
            ->where('type', Category::TYPE_OPPORTUNITY_TAG)
            ->get();

        // Get currencies
        $currencies = collect(json_decode(file_get_contents(base_path('database/json/currencies.json')), true))
            ->values()->toArray();

        // Get max salary for filter
        $maxSalary = Opening::selectRaw("CAST(SUBSTRING_INDEX(salary_range, '-', -1) AS UNSIGNED) as max_salary")
            ->orderBy('max_salary', 'desc');
        if ($maxSalary->exists()) {
            $maxSalary = $maxSalary->first()->max_salary;
        }
        
        // Get theme data
        $theme_data = get_option('theme_path', true);
        $path = env('APP_DEBUG') ? request('v', $theme_data?->opportunity_list?->path ?? $theme_data?->job_list?->path) : $theme_data?->opportunity_list?->path ?? $theme_data?->job_list?->path ?? "One";
        $type = env('APP_DEBUG') ? request('type', $theme_data?->opportunity_list?->type ?? $theme_data?->job_list?->type) : $theme_data?->opportunity_list?->type ?? $theme_data?->job_list?->type ?? "One";
        
        // Use the same template as jobs but with opportunity data
        $fullPath = 'Web/Opportunity/' . $path . '/Index' . $type;

        // Get SEO data
        $seo = get_option('seo_opportunity_list', true) ?? get_option('seo_job_list', true);
        $meta['title'] = $seo->site_name ?? '';
        $meta['site_name'] = $seo->site_name ?? '';
        $meta['description'] = $seo->matadescription ?? '';
        $meta['tags'] = $seo->matatag ?? '';
        $meta['preview'] = asset($seo->preview ?? '');

        return Inertia::render($fullPath, [
            'opportunities' => $opportunities,
            'locations' => $locations,
            'opportunityTypes' => $opportunityTypes,
            'opportunityExperiences' => $opportunityExperiences,
            'categories' => $categories,
            'tags' => $tags,
            'services' => $services,
            'maxSalary' => $maxSalary ?? 1000,
            'currencies' => $currencies,
            'request' => $request,
            'seo' => SeoMeta::set($meta),
            'opportunityCategories' => array_keys(Opening::OPPORTUNITY_CATEGORIES),
            'opportunityTypeLabels' => Opening::OPPORTUNITY_TYPES,
        ]);
    }

    public function show($slug)
    {
        $opportunity = Opening::query()->where('slug', $slug)
            ->where('status', 1)
            ->where('live_expire_at', '>', today())
            ->with(['tags:id,title,slug', 'country', 'state', 'user:id,meta,avatar,created_at,username', 'service:id,title,slug'])
            ->firstOrFail();
            
        $opportunity->fields = json_decode($opportunity->fields);

        // Get related opportunities from the same provider
        $relatedOpportunities = Opening::query()
            ->whereNot('id', $opportunity->id)
            ->where('status', 1)
            ->where('live_expire_at', '>', today())
            ->where('user_id', $opportunity->user_id)
            ->with(['tags:id,title', 'country', 'state', 'user:id,meta,avatar,created_at'])
            ->limit(6)->get();

        // Check if user has already applied
        $alreadyApplied = (bool) (auth()->check() && auth()->user()->appliedJobs()->find($opportunity->id));

        // Get theme data
        $theme_data = get_option('theme_path', true);
        $path = env('APP_DEBUG') ? request('v', $theme_data?->opportunity_detail?->path ?? $theme_data?->job_detail?->path) : $theme_data?->opportunity_detail?->path ?? $theme_data?->job_detail?->path ?? "One";
        $type = env('APP_DEBUG') ? request('type', $theme_data?->opportunity_detail?->type ?? $theme_data?->job_detail?->type) : $theme_data?->opportunity_detail?->type ?? $theme_data?->job_detail?->type ?? "One";
        
        // Use the same template as jobs but with opportunity data
        $fullPath = 'Web/Opportunity/' . $path . '/Show' . $type;

        // Get SEO data
        $seo = get_option('seo_opportunity_detail', true) ?? get_option('seo_job_detail', true);
        $meta['title'] = $opportunity->title ?? '';
        $meta['site_name'] = $seo->site_name ?? '';
        $meta['description'] = $opportunity->short_description ?? '';
        $meta['tags'] = $seo->matatag ?? '';
        $meta['preview'] = asset($seo->preview ?? '');

        // Track visitor
        if (!session()->has('visitor_' . $opportunity->id)) {
            session()->put('visitor_' . $opportunity->id, true);
            Visitor::create([
                'visitable_id' => $opportunity->id,
                'visitable_type' => Opening::class,
                'ip' => request()->ip(),
                'agent' => request()->userAgent(),
            ]);
        }

        return Inertia::render($fullPath, [
            'opportunity' => $opportunity,
            'relatedOpportunities' => $relatedOpportunities,
            'alreadyApplied' => $alreadyApplied,
            'seo' => SeoMeta::set($meta),
            'opportunityCategory' => $opportunity->getOpportunityCategory(),
            'opportunityTypeDisplay' => $opportunity->getOpportunityTypeDisplay(),
        ]);
    }

    public function toggleBookmark(Opening $opportunity)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $user->bookmarks()->toggle($opportunity->id);

        return back();
    }

    public function apply(Opening $opportunity)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->appliedJobs()->find($opportunity->id)) {
            return back()->with('error', 'You have already applied for this opportunity.');
        }

        $fields = json_decode($opportunity->fields, true) ?? [];

        return Inertia::render('Web/Opportunity/Apply', [
            'opportunity' => $opportunity,
            'fields' => $fields,
        ]);
    }

    public function applyStore(Request $request, Opening $opportunity)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->appliedJobs()->find($opportunity->id)) {
            return back()->with('error', 'You have already applied for this opportunity.');
        }

        $fields = json_decode($opportunity->fields, true) ?? [];
        $rules = [];
        $messages = [];

        foreach ($fields as $key => $field) {
            $fieldName = 'fields.' . $key;
            $rules[$fieldName] = 'required';

            if ($field['type'] === 'email') {
                $rules[$fieldName] .= '|email';
            }

            if ($field['type'] === 'file') {
                $rules[$fieldName] = [
                    'required',
                    File::types(['pdf', 'doc', 'docx'])
                        ->max(5 * 1024),
                ];
            }

            $messages[$fieldName . '.required'] = $field['label'] . ' is required.';
            if ($field['type'] === 'email') {
                $messages[$fieldName . '.email'] = $field['label'] . ' must be a valid email.';
            }
        }

        $request->validate($rules, $messages);

        $data = [];
        foreach ($fields as $key => $field) {
            if ($field['type'] === 'file') {
                $file = $request->file('fields')[$key];
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/applications', $fileName);
                $data[$key] = [
                    'label' => $field['label'],
                    'value' => Storage::url('applications/' . $fileName),
                    'type' => $field['type'],
                ];
            } else {
                $data[$key] = [
                    'label' => $field['label'],
                    'value' => $request->fields[$key],
                    'type' => $field['type'],
                ];
            }
        }

        auth()->user()->appliedJobs()->attach($opportunity->id, [
            'data' => json_encode($data),
        ]);

        // Create notification for employer
        Notification::create([
            'user_id' => $opportunity->user_id,
            'title' => 'New application',
            'message' => auth()->user()->name . ' has applied for your opportunity: ' . $opportunity->title,
            'link' => '/employer/opportunity/' . $opportunity->slug,
        ]);

        // Send email to employer
        $this->sendJobAlertToTheEmployer($request, $opportunity->user->email);

        return redirect()->route('opportunity.show', $opportunity->slug)->with('success', 'Application submitted successfully.');
    }

    private function sendJobAlertToTheEmployer(Request $request, $email)
    {
        try {
            Mail::to($email)->send(new JobApplyAlert($request->all()));
        } catch (\Exception $e) {
            // Log the error but don't stop execution
            \Log::error('Failed to send job application email: ' . $e->getMessage());
        }
    }
}
