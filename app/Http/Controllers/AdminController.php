<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        $query = User::query();

        // Paieška pagal slapyvardį
        if ($request->filled('search')) {
            $query->where('slapyvardis', 'LIKE', '%' . $request->search . '%');
        }

        // Filtravimas pagal rolę
        if ($request->filled('role') && $request->role !== 'visi') {
            $query->where('role', $request->role);
        }

        // Filtravimas pagal leidimą kurti
        if ($request->filled('gali_kurti') && $request->gali_kurti !== 'visi') {
            $query->where('gali_kurti', $request->gali_kurti);
        }

        $users = $query->orderBy('id')->paginate(10)->withQueryString();

        return view('admin.vartotojai', [
        'users' => $users,
        'search' => $request->search
        ]);
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
    
    public function changeRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:naudotojas,kontrolierius,administratorius'
        ]);

        $user = User::findOrFail($id);

        if (auth()->user()->role !== 'administratorius') {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['role' => 'Negalite keisti savo paties rolės.']);
        }

        $role = $request->role;
        $user->role = $role;

        if ($role !== 'naudotojas') {
            $user->gali_kurti = 0;
        }
        $user->save();

        return back()->with('success', 'Rolė sėkmingai pakeista.');
    }
    
}
