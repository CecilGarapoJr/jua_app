<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Opening;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {

        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
        $user->load('plan');
        $data['user'] = $user;
        $data['posted_jobs'] = $user->jobs;

        $data['job_posted'] = $user->jobs->count();
        $data['shortlisted'] = $user->employerBookmarks()->whereNotNull('opening_id')->count();
        $data['applications'] = Opening::query()->withCount('appliedApplicants')->get()->sum('applied_applicants_count');
        $data['saved_candidate'] = $user->employerBookmarks()->count();
        $data['activeChartFilterBtn'] = request('chart_filter_by') ?? 'day';

        $data['KYC_MUST_VERIFIED'] = env('KYC_MUST_VERIFIED', true);

        $activeChartFilterBtn = request('chart_filter_by') ?? 'day';

       
        $filterBy = request('chart_filter_by') ?? 'day';
        $dateFormat = match (request('chart_filter_by')) {
            'year' => "%Y",
            'month' => "%M %Y",
            'week' => "%a",
            default => "%h:00 %p",
        };

        $jobIds = $user->jobs->pluck(['id']);

        $selected_job = request('selected_job') ?? null;
        if (request('selected_job')) {
            $job = Opening::where('user_id', Auth::id())->where('slug', $selected_job)->firstOrFail();
        }

        $data['selected_job'] = $selected_job;

        $data['visits'] = Visitor::query()
            ->when($selected_job, fn ($q) => $q->where('opening_id', $job->id))
            ->when(!$selected_job, fn ($q) => $q->whereIn('opening_id', $jobIds))
            ->selectRaw("DATE_FORMAT(visitors.created_at,'$dateFormat') as date, COUNT(visitors.id) as views")

            ->when($filterBy == 'day', function ($query) {
                $query->whereDate('created_at', today())
                    ->groupByRaw('HOUR(visitors.created_at)');
            })
            ->when($filterBy == 'week', function ($query) {
                $start = now()->startOfWeek(Carbon::SATURDAY);
                $end = now()->endOfWeek(Carbon::FRIDAY);
                $query->whereBetween('visitors.created_at', [$start, $end])
                    ->groupByRaw('DAY(visitors.created_at)');
            })
            ->when($filterBy == 'month', function ($query) {
                $query->whereYear('created_at', now()->year)
                    ->groupByRaw('MONTH(visitors.created_at)');
            })
            ->when($filterBy == 'year', function ($query) {
                $query->groupByRaw('YEAR(visitors.created_at)');
            })
            ->orderBy('visitors.created_at', 'asc')
            ->get();

        return Inertia::render('Employer/Dashboard', $data);
    }
}
