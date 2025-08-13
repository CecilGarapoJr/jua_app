<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\Userjob;
use App\Services\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\Opening;

class EmployerPanelController extends Controller
{

    public function accountSetting()
    {
        $user = Auth::user();
        return Inertia::render('Employer/Settings', compact('user'));
    }

    public function accountSettingUpdate(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [request()->user()->provider_id ? 'nullable' : 'required', 'email', 'max:255', 'unique:users,email,' . request()->user()->id],
            'phone' => [request()->user()->provider_id ? 'nullable' : 'required', 'string', 'unique:users,phone,' . request()->user()->id],
            'current_password' => [(request()->user()->provider_id ? 'nullable' : 'required'), 'current_password'],
        ]);

        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
        $meta = $user->meta;
        $meta['company']['name'] = $request->get('name');
        $user->meta = $meta;

        $user->update([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),

        ]);
        $user->save();
        $request->session()->regenerate();

        return back();
    }

    public function changePassword()
    {
        return Inertia::render('Employer/SettingPassChange');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed'],
        ]);

        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
        $user->password = Hash::make($request->get('new_password'));
        $user->save();

        $request->session()->regenerate();

        return back();
    }

    public function savedCandidates()
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
        $candidates = $user->employerBookmarks()
            ->with(['countries', 'states', 'categories', 'tags'])
            ->withCount(['candidateBookmarks as isBookmarked' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->orderBy('created_at', request()->sort ?? 'desc')
            ->paginate();
        return Inertia::render('Employer/SavedCandidate', compact('candidates'));
    }

    // destroy the user | soft deleted
    public function destroy()
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
        $user->deleted_at = now();
        $user->save();

        Auth::logout();

        Notify::success(__('Account has been deleted successfully'));

        return Inertia::location(url('/'));
    }

    public function hireCandidate(Request $request)
    {
        $job = Opening::where('user_id', Auth::id())->findOrFail($request->job_id);
        
        if ($request->has('apply_id')) {
            $application = Userjob::findOrFail($request->apply_id);
        } else {
            $application = Userjob::where('user_id', $request->candidate_id)
                ->where('opening_id', $request->job_id)->first();
        }


        if ($application && $application->is_hired != 1) {
            $application->is_hired = 1;
            $application->save();

            Notification::create([
                'user_id' => $application->user_id,
                'url' => route('user.applied-job'),
                'title' => __('You are hired!'),
                'comment' => __('An Employer hired you'),
            ]);
        }

        return back();
    }

}
