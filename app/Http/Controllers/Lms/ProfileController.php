<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Concerns\UpdatesSharedProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller
{
    use UpdatesSharedProfile;

    public function edit()
    {
        return view('lms.profile', [
            'lmsUser' => Auth::guard('lms')->user(),
        ]);
    }

    public function update(Request $request)
    {
        return $this->doUpdateProfile($request, 'lms');
    }

    public function updatePassword(Request $request)
    {
        return $this->doUpdatePassword($request, 'lms');
    }

    public function updateAvatar(Request $request)
    {
        return $this->doUpdateAvatar($request, 'lms');
    }

    public function deleteAvatar()
    {
        return $this->doDeleteAvatar('lms');
    }
}
