<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::orderBy('id')->get();
        return view('admin.vartotojai', compact('users'));
    }

    public function allow($id)
    {
        $u = User::findOrFail($id);

        if ($u->role !== 'naudotojas') {
        return back()->with('error', 'Šiam vartotojui negalima keisti teisių.');
        }

        $u->gali_kurti = 1;
        $u->save();

        return back()->with('success', 'Teisė suteikta.');
    }

    public function deny($id)
    {
        $u = User::findOrFail($id);

        if ($u->role !== 'naudotojas') {
        return back()->with('error', 'Šiam vartotojui negalima keisti teisių.');
        }
        
        $u->gali_kurti = 0;
        $u->save();

        return back()->with('success', 'Teisė atimta.');
    }
}
