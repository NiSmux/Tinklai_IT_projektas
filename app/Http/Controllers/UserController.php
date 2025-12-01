<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showRegisterForm() {
        return view('register');
    }
    public function register(Request $request){
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:100', 'unique:vartotojas,slapyvardis'],
            'email' => ['required', 'email', 'unique:vartotojas,el_pastas'],
            'tel' => ['required', 'regex:/^\+?[0-9]+$/', 'min:8', 'max:20'],
            'password' => ['required','min:6', 'max:200'],
            'confirm_password' => ['required','same:password']
        ], [
            'username.unique' => 'Toks slapyvardis jau egzistuoja.',
            'username.required' => 'Įveskite slapyvardį.',
            'username.min' => 'Slapyvardis turi būti bent 3 simbolių.',
            'username.max' => 'Slapyvardis negali būti ilgesnis nei 100 simbolių.',
            'email.unique' => 'Toks el. paštas jau egzistuoja.',
            'slapyvardis.required' => 'Įveskite slapyvardį.',
            'slapyvardis.min' => 'Slapyvardis turi būti bent 3 simbolių.',
            'el_pastas.required' => 'Įveskite el. paštą.',
            'el_pastas.email' => 'Neteisingas el. pašto formatas.',
            'password.required' => 'Įveskite slaptažodį.',
            'password.min' => 'Slaptažodis turi būti bent 6 simbolių.',
            'password.max' => 'Slaptažodis negali būti ilgesnis nei 200 simbolių.',
            'password.confirmed' => 'Slaptažodžiai nesutampa.',
            'confirm_password.same' => 'Slaptažodžiai nesutampa.',
            'tel.required' => 'Įveskite telefono numerį.',
            'tel.regex' => 'Telefono numeris gali būti sudarytas tik iš skaičių ir pliuso ženklo.',
            'tel.min' => 'Telefono numeris turi būti bent 8 simbolių.',
            'tel.max' => 'Telefono numeris negali būti ilgesnis nei 20 simbolių.',
        ]);

        $hashedPassword = Hash::make($incomingFields['password']);
        $user = User::create([
            'slapyvardis' => $incomingFields['username'],
            'slaptazodis' => $hashedPassword,
            'el_pastas' => $incomingFields['email'],
            'tel' => $incomingFields['tel'],
            'registracijos_data' => date('Y-m-d'),
            'role' => 'naudotojas',
            'gali_kurti' => 0
        ]);

        return redirect('/login')->with('success', 'Registracija sėkminga!');
    }


    public function showLoginForm(){
        return view('login');
    }
    public function login(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if(Auth::attempt(['el_pastas' => $credentials['email'], 'password' => $credentials['password']])){
            $request->session()->regenerate();

            return redirect('/')->with('success', 'Sėkmingai prisijungei!');
        }
        return back()->withErrors([
            'email' => 'Neteisingas el. paštas arba slaptažodis.',
        ])->onlyInput('email');
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Sėkmingai atsijungei.');
    }
}
