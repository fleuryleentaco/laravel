<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // show change password form
    public function password()
    {
        $user = Auth::user();
        return view('profile.password', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);
        $user->update($data);
        // revoke other sessions / remember tokens after email change
        if($request->filled('email') && $request->input('email') !== $user->getOriginal('email')){
            // delete all sessions for this user (database session driver assumed)
            DB::table('sessions')->where('user_id', $user->id)->delete();
            // revoke remember token
            $user->setRememberToken(null);
            $user->save();
        }
        return redirect()->route('profile.show')->with('status', 'Profil mis à jour.');
    }

    // update password
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if(! Hash::check($data['current_password'], $user->password)){
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }
        $user->password = Hash::make($data['password']);
        // revoke sessions & remember tokens
        DB::table('sessions')->where('user_id', $user->id)->delete();
        $user->setRememberToken(null);
        $user->save();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('status', 'Mot de passe mis à jour. Veuillez vous reconnecter.');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'password' => 'required',
        ]);
        if(! Hash::check($data['password'], $user->password)){
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
        }
        // revoke sessions and tokens
        DB::table('sessions')->where('user_id', $user->id)->delete();
        $user->setRememberToken(null);
        Auth::logout();
        // delete account
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('status', 'Compte supprimé.');
    }
}
