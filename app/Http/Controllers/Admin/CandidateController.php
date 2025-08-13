<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CandidateExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitor;
use App\Traits\Uploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class CandidateController extends Controller
{
    use Uploader;

    public function __construct()
    {
        $this->middleware('permission:candidates');
    }

    public function index()
    {
        if (request('export')) {
            return Excel::download(new CandidateExport, 'candidates.xlsx');
        }

        $data['segments'] = request()->segments();
        $data['buttons'] = [
           
        ];
        $data['request'] = request()->all();
        $candidates = User::query()->candidate();

        $data['total_candidates'] = $candidates->clone()->count();
        $data['active_candidates'] = $candidates->clone()->active()->count();
        $data['inactive_candidates'] = $candidates->clone()->inActive()->count();
        $data['verified_candidates'] = $candidates->clone()->where('email_verified_at')->count();

        $allowedColumnToSearch = ['name', 'email'];

        $data['candidates'] = $candidates
            ->when(
                request()->filled(['search', 'type']) && in_array(request('type'), $allowedColumnToSearch),
                function ($query) {
                    $query->where(request()->type, "LIKE", '%' . request('search') . '%');
                }
            )
            ->with(['service'])
            ->paginate();

        return Inertia::render('Admin/Candidates/Index', $data);
    }

    public function show(User $candidate)
    {
        $data['segments'] = request()->segments();
        $data['buttons'] = [
            [
                'name' => '<i class="bx bx-list"></i>&nbsp&nbsp' . __('Back to list'),
                'url' => route('admin.candidates.index'),
            ],
        ];

        $data['candidate'] = $candidate;

        $data['total_visitors'] = $candidate->hasMany(Visitor::class)->count();
        $data['total_shortlisted'] = $candidate->candidateBookmarks()->whereNotNull('opening_id')->count(); // shortlisted logic or data not found
        $data['total_bookmarks'] = $candidate->candidateBookmarks()->count();
        $data['total_applied_jobs'] = $candidate->appliedJobs()->count();

        $data['appliedJobs'] = $candidate->appliedJobs()->paginate();

        return Inertia::render('Admin/Candidates/Show', $data);
    }

    public function edit(User $candidate)
    {

        $data['segments'] = request()->segments();
        $data['buttons'] = [
            [
                'name' => '<i class="bx bx-list"></i>&nbsp&nbsp' . __('Back to list'),
                'url' => route('admin.candidates.index'),
            ],
        ];

        $candidate->avatar = '';
        $candidate->status = boolval($candidate->status);
        $candidate->email_verified = boolval($candidate->email_verified_at);
        $candidate->is_star = boolval($candidate->is_star);

        $data['candidate'] = $candidate;
        return Inertia::render('Admin/Candidates/Edit', $data);
    }

    public function update(Request $request, User $candidate)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['numeric', 'min:0'],
            'avatar' => ['nullable', 'image'],
            'email' => ['required', 'email', 'max:50', 'unique:users,email,' . $candidate->id],
            'phone' => ['nullable', 'numeric', 'digits_between:8,16'],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', 'digits_between:6,16'],
            'is_star' => ['nullable'],
        ]);


        $validatedData['avatar'] =  $this->uploadFile('avatar', $candidate->avatar);

        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        }

        $validatedData = [
            ...$validatedData,
            'status' => boolval($request->status),
            'email_verified_at' => $request->email_verified ? now()->toDateTimeString() : null,
            'is_star' => boolval($request->is_star),
        ];

        $candidate->fill($validatedData);
        $candidate->save();

        return to_route('admin.candidates.index');
    }

    public function destroy(User $candidate)
    {
        $candidate->delete();
        return to_route('admin.candidates.index');
    }
}
