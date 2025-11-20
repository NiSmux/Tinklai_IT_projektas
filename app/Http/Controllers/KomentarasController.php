<?php

namespace App\Http\Controllers;

use App\Models\Komentaras;
use App\Models\Skelbimas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomentarasController extends Controller
{
    public function store(Request $request, $skelbimas_id)
    {
        $request->validate([
            'zinute' => ['required', 'min:1', 'max:2000']
        ]);

        Komentaras::create([
            'skelbimas_id' => $skelbimas_id,
            'vartotojas_id' => Auth::id(),
            'zinute' => $request->zinute,
            'data' => now()->toDateString(),
        ]);

        return back()->with('success', 'Žinutė išsiųsta.');
    }
    public function destroy($id)
    {
        $komentaras = Komentaras::findOrFail($id);

        $komentaras->delete();

        return back()->with('success', 'Komentaras sėkmingai ištrintas.');
    }
}
