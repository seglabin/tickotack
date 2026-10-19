<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function login()
    {
        if (Auth::check()) {
            session(['config' => 'accueil']);
            return redirect()->route('home');
        }

        // Ensure configuration is not confusing the layout
        session(['config' => 'login']);
        $titre = "Connexion à la plateforme SIGAF";

        return view('auth.login', compact('titre'));
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request)
    { //dd($request->all());
        $credentials = $request->validate([
            // 'email' => ['required', 'email'],
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $ta = explode('CL', $request->username);
        // if (count($ta) > 1 && is_numeric($ta[0]) && intval($ta[0]) > 2000) {
        //     // Connexion client

        // } else {

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $u = Auth::user();
            if ($u != null && $u->idrole != null) {
                // Set menus based on permissions
                $rekmenu = "SELECT DISTINCT idpermission FROM rolepermissions WHERE idrole = '" . $u->idrole . "' ";
                $menusUser = collect(DB::select($rekmenu))->toArray();
                $menusUser = array_column($menusUser, 'idpermission');
                // dd($rekmenu);
                session(['menusUser' => $menusUser]);
            }
            if(count($ta) > 1 && is_numeric($ta[0]) && (intval($ta[0]) > 2000)){
                session(['config' => 'site']);
                return redirect()->route('home');
            } else {
                session(['config' => 'back']);
                return redirect()->intended('/');
            }
            // }
        }

        return back()->withErrors([
            'username' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('username');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Change password for the logged-in user.
     */
    public function changerMotDePasse(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:6|confirmed',
            ]);

            $user = Auth::user();

            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Le mot de passe actuel est incorrect.');
            }

            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $user->save();

            return back()->with('success', 'Votre mot de passe a été changé avec succès.');
        } catch (\Throwable $th) {
            dd($th);
            //throw $th;
        }
    }
}
