<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\ProfileUpdateRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Profile settings page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }

    /**
     * Update user data.
     *
     * @param ProfileUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateData(ProfileUpdateRequest $request)
    {
        $user = auth()->user();
        $user->name = $request->name;
        $user->save();

        return back()->with('success', __('profile.update_successfull_message'));
    }

    /**
     * Change user password.
     *
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', __('profile.change_pass_message'));
    }

    /**
     * This method makes current user a commissioner.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function becomeCommissioner()
    {
        $user = auth()->user();
        $user->commissioner = true;
        $user->save();

        return back()
            ->with('success', __('profile.commissioner_message'));
    }
}
