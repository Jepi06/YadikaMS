<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Dipakai bareng oleh Pkl\ProfileController, Spmb\ProfileController, dan
 * Lms\ProfileController. Ketiganya guard beda ('pkl'/'spmb'/'lms') tapi
 * sama-sama nunjuk ke baris yang sama di tabel `users` — jadi logic
 * validasi & update-nya satu, tinggal beda guard yang dipakai buat
 * ambil user yang sedang login. Karena satu tabel, ganti nama/password
 * di modul manapun otomatis kepakai di modul lain juga.
 */
trait UpdatesSharedProfile
{
    protected function doUpdateProfile(Request $request, string $guard)
    {
        $user = Auth::guard($guard)->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->update($data);

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    protected function doUpdatePassword(Request $request, string $guard)
    {
        $user = Auth::guard($guard)->user();

        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('status', 'Password berhasil diubah.');
    }
}
