<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opening;
use App\Models\Category;
use App\Traits\Uploader;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OpportunityController extends Controller
{
    use Uploader;
    
    public function __construct()
    {
        $this->middleware('permission:opportunities');
    }

    public function index(Request $request)
    {
        $opportunities = Opening::query();

        if ($request->search !== null && $request->search !== '') {
            $opportunities = match ($request->type) {
                'name', 'email' => $opportunities->whereHas('user', fn ($q) => $q->where($request->type, $request->search)),
                'status' => $opportunities->where($request->type, $request->search),
                'category' => $opportunities->whereHas('categories', fn ($q) => $q->where('title', 'LIKE', '%' . $request->search . '%')),
                'service' => $opportunities->whereHas('service', fn ($q) => $q->where('title', 'LIKE', '%' . $request->search . '%')),
                'opportunity_type' => $opportunities->where('type', $request->search),
                default => $opportunities->where($request->type, 'LIKE', '%' . $request->search . '%'),
            };
        }
        
        // Add filter by opportunity category if requested
        if ($request->opportunity_category) {
            $category = $request->opportunity_category;
            $opportunities = $opportunities->ofCategory($category);
        }
        
        $opportunities = $opportunities->with(['categories', 'service', 'user:id,name,avatar,created_at'])->latest()->paginate(10);
        
        // Count statistics for different opportunity types
        $totalOpportunities = Opening::count();
        $pendingOpportunities = Opening::where('status', 2)->count();
        $activeOpportunities = Opening::where('status', 1)->count();
        $inActiveOpportunities = Opening::where('status', 0)->count();
        
        // Count by opportunity category
        $jobCount = Opening::ofCategory('job')->count();
        $scholarshipCount = Opening::ofCategory('scholarship')->count();
        $grantCount = Opening::ofCategory('grant')->count();
        $trainingCount = Opening::ofCategory('training')->count();
        $internshipCount = Opening::ofCategory('internship')->count();
        
        $type = $request->type ?? 'email';
        
        return Inertia::render('Admin/Opportunity/Index', [
            'opportunities' => $opportunities,
            'totalOpportunities' => $totalOpportunities,
            'activeOpportunities' => $activeOpportunities,
            'pendingOpportunities' => $pendingOpportunities,
            'inActiveOpportunities' => $inActiveOpportunities,
            'jobCount' => $jobCount,
            'scholarshipCount' => $scholarshipCount,
            'grantCount' => $grantCount,
            'trainingCount' => $trainingCount,
            'internshipCount' => $internshipCount,
            'opportunityTypes' => Opening::OPPORTUNITY_TYPES,
            'opportunityCategories' => array_keys(Opening::OPPORTUNITY_CATEGORIES),
            'request' => $request,
            'type' => $type
        ]);
    }

    public function show($id)
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => __('Back'),
                'url' => '/admin/opportunity'
            ],
        ];

        $opportunity = Opening::query()
            ->with(['categories', 'service', 'tags', 'user:id,name,avatar,created_at,meta', 'country', 'state'])
            ->findOrFail($id);

        return Inertia::render('Admin/Opportunity/Show', [
            'opportunity' => $opportunity,
            'opportunityCategory' => $opportunity->getOpportunityCategory(),
            'opportunityTypeDisplay' => $opportunity->getOpportunityTypeDisplay(),
            'buttons' => $buttons,
            'segments' => $segments
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'featured_expire_at' => ['nullable', 'date'],
            'live_expire_at' => ['nullable', 'date'],
            'status' => ['required', 'integer'],
            'type' => ['nullable', 'string'],
        ]);

        $opportunity = Opening::with(['user'])->findOrFail($id);
        
        if ($request->status == 1 && $opportunity->status != 1) {
            $live_opportunity_for_days = now()->addDays($opportunity->user->plan['live_job_for_days'] ?? 30);
        }
        
        $opportunity->update([
            'status' => $request->status,
            'type' => $request->type ?? $opportunity->type,
            'featured_expire_at' => $request->featured_expire_at,
            'live_expire_at' => $live_opportunity_for_days ?? $request->live_expire_at ?? null,
        ]);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $opportunity = Opening::findOrFail($id);
        $this->removeFile($opportunity->attachment);
        $opportunity->delete();
        return back();
    }
}
