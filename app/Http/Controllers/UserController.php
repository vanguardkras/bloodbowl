<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }

    public function updateData(ProfileUpdateRequest $request)
    {
        $user = auth()->user();
        $user->name = $request->name;
        $user->save();

        return back()->with('success', __('profile.update_successfull_message'));
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', __('profile.change_pass_message'));
    }
}
