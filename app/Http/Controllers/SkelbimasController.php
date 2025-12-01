<?php
namespace App\Http\Controllers;
use App\Models\Skelbimas;
use App\Models\SkelbimoNuotrauka;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SkelbimasController extends Controller
{
    // VISI skelbimai
    public function index(Request $request)
    {
        $query = Skelbimas::query();

        // --- FILTRAI ---

        // Filtras pagal pavadinimą (paieška)
        if ($request->filled('q')) {
            $query->where('pavadinimas', 'LIKE', '%' . $request->q . '%');
        }

        // --- RŪŠIAVIMAS ---
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'kaina_asc':
                    $query->orderBy('kaina', 'asc');
                    break;
                case 'kaina_desc':
                    $query->orderBy('kaina', 'desc');
                    break;
                case 'naujausi':
                    $query->orderBy('id', 'desc');
                    break;
                case 'seniausi':
                    $query->orderBy('id', 'asc');
                    break;
            }
        } else {
            $query->orderBy('id', 'desc'); // default naujausi
        }

        // --- PUSLAPIAVIMAS ---
        $skelbimai = $query->paginate(12)->withQueryString();

        return view('skelbimai.index', compact('skelbimai'));
    }

    // VIENAS skelbimas
    public function show($id)
    {
        $skelbimas = Skelbimas::findOrFail($id);
        if ($skelbimas->isExpired() && $skelbimas->aktyvus == 1) {
            $skelbimas->update([
                'aktyvus' => 0
            ]);
        }

        if (!Auth::check() || Auth::id() !== $skelbimas->vartotojas_id) {
            $skelbimas->increment('perziuros');
        }

        return view('skelbimai.show', compact('skelbimas'));
    }

    // Kūrimo forma
    public function create()
    {
        if (Auth::user()->gali_kurti != 1) {
            abort(403, 'Neturite teisės kurti skelbimų');
        }

        return view('skelbimai.create');
    }

    // Saugojimas
    public function store(Request $request)
    {
        if (Auth::user()->gali_kurti != 1) {
            abort(403);
        }

        $validated = $request->validate([
            'pavadinimas'   => ['required','string','min:3','max:255'],
            'aprasymas'     => ['required','string','min:1'],
            'kaina'         => ['required','integer','min:0'],
            'nuotraukos.*'  => ['nullable','image','mimes:jpg,jpeg,png','max:20480'], // 20 MB per failą
        ],[
            'nuotraukos.*.image' => 'Kiekvienas įkeltas failas turi būti nuotrauka (jpg, jpeg, png).',
            'nuotraukos.*.mimes' => 'Kiekvienas įkeltas failas turi būti nuotrauka (jpg, jpeg, png).',
            'nuotraukos.*.max'   => 'Kiekviena nuotrauka negali būti didesnė nei 20 MB.',
            'pavadinimas.required' => 'Įveskite skelbimo pavadinimą.',
            'pavadinimas.min' => 'Skelbimo pavadinimas turi būti bent 3 simbolių.',
            'pavadinimas.max' => 'Skelbimo pavadinimas negali būti ilgesnis nei 255 simbolių.',
            'aprasymas.required' => 'Įveskite skelbimo aprašymą.',
            'aprasymas.min' => 'Skelbimo aprašymas turi būti bent 1 simbolio.',
            'kaina.required' => 'Įveskite skelbimo kainą.',
            'kaina.integer' => 'Kaina turi būti sveikas skaičius.',
            'kaina.min' => 'Kaina negali būti neigiama.',
        ]);

        $days = intval($request->galiojimas);

        $skelbimas = Skelbimas::create([
            'vartotojas_id' => auth()->id(),
            'pavadinimas'   => $validated['pavadinimas'],
            'aprasymas'     => $validated['aprasymas'],
            'kaina'         => $validated['kaina'],
            'sukurimo_data' => now(),
            'galioja_iki'   => now()->addDays($days),
            'aktyvus'       => 1,
            'busena'        => 'neparduotas',
        ]);

        // Sukuriame Intervention Image managerį
        $manager = new ImageManager(new Driver());

        // Jei buvo įkeltos nuotraukos
        if ($request->hasFile('nuotraukos')) {

            $files = $request->file('nuotraukos');

            // max 5 nuotraukos
            $files = array_slice($files, 0, 5);

            foreach ($files as $file) {
                // perskaitom tikrą failo kelią
                $img = $manager->read($file->getPathname());

                // sumažinam dydi
                $img->scaleDown(1600);

                // watermark (jei turi public/watermark.png)
                if (file_exists(public_path('watermark.png'))) {
                    $img->place(public_path('watermark.png'), 'bottom-right', 10, 10);
                }

                $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                // saugom į storage/app/public/skelbimu_nuotraukos
                $relativePath = 'skelbimu_nuotraukos/' . $filename;

                Storage::disk('public')->put(
                    $relativePath,
                    (string) $img->encode()
                );

                // įrašas DB – saugom kelią be "storage/", kad Blade naudotų asset('storage/...')
                SkelbimoNuotrauka::create([
                    'skelbimas_id' => $skelbimas->id,
                    'failo_kelias' => $relativePath,
                    'pozicija' => $skelbimas->nuotraukos()->count() + 1,
                ]);
            }
        }

        return redirect()->route('skelbimai.mano')
            ->with('success', 'Skelbimas sukurtas!');
    }

    // Redagavimo forma
    public function edit($id)
    {
        $skelbimas = Skelbimas::findOrFail($id);

        if ($skelbimas->vartotojas_id !== Auth::id()) {
            abort(403);
        }

        return view('skelbimai.edit', compact('skelbimas'));
    }

    // Redagavimo saugojimas
    public function update(Request $request, $id)
    {
        $skelbimas = Skelbimas::findOrFail($id);

        if ($skelbimas->vartotojas_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'pavadinimas' => ['required', 'min:3', 'max:255'],
            'aprasymas'   => ['required', 'min:1'],
            'kaina'       => ['required', 'integer', 'min:0'],
            'busena'      => ['required', 'in:parduotas,neparduotas'],
            'pratesti'    => ['nullable', 'integer', 'min:0'],
        ],[
            'pavadinimas.required' => 'Įveskite skelbimo pavadinimą.',
            'pavadinimas.min' => 'Skelbimo pavadinimas turi būti bent 3 simbolių.',
            'pavadinimas.max' => 'Skelbimo pavadinimas negali būti ilgesnis nei 255 simbolių.',
            'aprasymas.required' => 'Įveskite skelbimo aprašymą.',
            'aprasymas.min' => 'Skelbimo aprašymas turi būti bent 1 simbolio.',
            'kaina.required' => 'Įveskite skelbimo kainą.',
            'kaina.integer' => 'Kaina turi būti sveikas skaičius.',
            'kaina.min' => 'Kaina negali būti neigiama.',
            'busena.required' => 'Pasirinkite skelbimo būseną.',
            'busena.in' => 'Neteisinga skelbimo būsenos reikšmė.',
            'pratesti.integer' => 'Pratęsimo dienų skaičius turi būti sveikas skaičius.',
            'pratesti.min' => 'Pratęsimo dienų skaičius negali būti neigiamas.',
        ]);
        // jei yra naujų nuotraukų
        if ($request->hasFile('nuotraukos')) {

            // kiek dar galima įkelti
            $remaining = 5 - $skelbimas->nuotraukos->count();
            $files = array_slice($request->file('nuotraukos'), 0, $remaining);

            $manager = new ImageManager(new Driver());

            foreach ($files as $file) {

                $img = $manager->read($file->getPathname());
                $img->scaleDown(1600);

                if (file_exists(public_path('watermark.png'))) {
                    $img->place(public_path('watermark.png'), 'bottom-right', 10, 10);
                }

                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $relativePath = 'skelbimu_nuotraukos/' . $filename;

                Storage::disk('public')->put($relativePath, (string) $img->encode());

                SkelbimoNuotrauka::create([
                    'skelbimas_id' => $skelbimas->id,
                    'failo_kelias' => $relativePath,
                    'pozicija' => $skelbimas->nuotraukos()->count() + 1,
                ]);
            }
        }
        $data['redagavimo_data'] = now()->toDateString();
        
        if ($request->filled('pratesti') && intval($request->pratesti) > 0) {
           $galiojaIki = \Carbon\Carbon::parse($skelbimas->galioja_iki);

            $galiojaIki->addDays(intval($request->pratesti));

            $skelbimas->galioja_iki = $galiojaIki->format('Y-m-d');
        }

        $skelbimas->update($data);

        return redirect('/skelbimai/' . $id)->with('success', 'Skelbimas atnaujintas!');
    }
    public function deletePhoto($id)
    {
        $foto = SkelbimoNuotrauka::findOrFail($id);

        // Tik skelbimo savininkas gali trinti savo nuotraukas
        if ($foto->skelbimas->vartotojas_id !== Auth::id()) {
            abort(403);
        }

        // Ištrinam failą iš disk'o
        Storage::disk('public')->delete($foto->failo_kelias);

        // Ištrinam DB įrašą
        $foto->delete();

        return back()->with('success', 'Nuotrauka ištrinta.');
    }
    public function addPhotos(Request $request, $id)
    {
        $skelbimas = Skelbimas::findOrFail($id);

        if ($skelbimas->vartotojas_id !== Auth::id()) {
            abort(403);
        }

        $remaining = 5 - $skelbimas->nuotraukos->count();
        if (!$request->hasFile('nuotraukos')) {
        return back()->withErrors(['nuotraukos' => 'Neįkelta jokia nuotrauka.']);
        }

        // jei bando įkelti daugiau nei leidžiama
        if (count($request->file('nuotraukos')) > $remaining) {
            return back()->withErrors([
                'nuotraukos' => "Galima įkelti tik $remaining nuotrauką(as)."
            ]);
        }
        $request->validate([
            'nuotraukos.*' => 'image|mimes:jpg,jpeg,png|max:20480'
        ]);

        $files = array_slice($request->file('nuotraukos') ?? [], 0, $remaining);

        $manager = new ImageManager(new Driver());

        foreach ($files as $file) {

            $img = $manager->read($file->getPathname());
            $img->scaleDown(1600);

            if (file_exists(public_path('watermark.png'))) {
                $img->place(public_path('watermark.png'), 'bottom-right', 10, 10);
            }

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $relative = 'skelbimu_nuotraukos/' . $filename;

            Storage::disk('public')->put($relative, $img->encode()->toString());

            SkelbimoNuotrauka::create([
                'skelbimas_id' => $skelbimas->id,
                'failo_kelias' => $relative,
                'pozicija' => $skelbimas->nuotraukos()->count() + 1,
            ]);
        }
        
        $skelbimas->redagavimo_data = now()->toDateString();
        $skelbimas->save();

        return back()->with('success', 'Nuotraukos įkeltos.');
    }
    public function destroyByKontrolierius($id)
    {
        $skelbimas = Skelbimas::findOrFail($id);

        if (auth()->user()->role !== 'kontrolierius') {
            abort(403);
        }

        // Ištrinam visus failus
        foreach ($skelbimas->nuotraukos as $foto) {
            Storage::disk('public')->delete($foto->failo_kelias);
            $foto->delete();
        }

        // Ištrinam patį skelbimą
        $skelbimas->delete();

        return redirect('/skelbimai')->with('success', 'Skelbimas pašalintas.');
    }
    public function myAds(Request $request)
    {
        $query = Skelbimas::where('vartotojas_id', auth()->id());

        // --- Filtras pagal pavadinimą ---
        if ($request->filled('q')) {
            $query->where('pavadinimas', 'LIKE', '%' . $request->q . '%');
        }

        // --- Rūšiavimas ---
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'kaina_asc':
                    $query->orderBy('kaina', 'asc');
                    break;
                case 'kaina_desc':
                    $query->orderBy('kaina', 'desc');
                    break;
                case 'seniausi':
                    $query->orderBy('id', 'asc');
                    break;
                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        // --- Puslapiavimas ---
        $skelbimai = $query->paginate(12)->withQueryString();

        return view('skelbimai.mano', compact('skelbimai'));
    }

    public function movePhotoUp($id)
    {
        $foto = SkelbimoNuotrauka::findOrFail($id);
        $skelbimas = $foto->skelbimas;

        if ($skelbimas->vartotojas_id !== auth()->id()) {
            abort(403);
        }

        // Surasti aukščiau esančią nuotrauką
        $previous = SkelbimoNuotrauka::where('skelbimas_id', $skelbimas->id)
            ->where('pozicija', '<', $foto->pozicija)
            ->orderBy('pozicija', 'desc')
            ->first();

        if (!$previous) return back(); // jau viršuje

        // sukeisti pozicijas
        [$foto->pozicija, $previous->pozicija] = [$previous->pozicija, $foto->pozicija];

        $foto->save();
        $previous->save();

        return back();
    }

    public function movePhotoDown($id)
    {
        $foto = SkelbimoNuotrauka::findOrFail($id);
        $skelbimas = $foto->skelbimas;

        if ($skelbimas->vartotojas_id !== auth()->id()) {
            abort(403);
        }

        // Surasti žemiau esančią nuotrauką
        $next = SkelbimoNuotrauka::where('skelbimas_id', $skelbimas->id)
            ->where('pozicija', '>', $foto->pozicija)
            ->orderBy('pozicija', 'asc')
            ->first();

        if (!$next) return back(); // jau apačioje

        // sukeisti pozicijas
        [$foto->pozicija, $next->pozicija] = [$next->pozicija, $foto->pozicija];

        $foto->save();
        $next->save();

        return back();
    }

    public function expiredAds()
    {
        $today = now()->toDateString();

        $skelbimai = Skelbimas::where('galioja_iki', '<', $today)
                            ->orderBy('galioja_iki')
                            ->get();

        return view('kontrolierius.expired', compact('skelbimai'));
    }
    public function expired()
    {
        $skelbimai = Skelbimas::where('aktyvus', 0)->orderBy('galioja_iki', 'asc')->get();

        return view('kontrolierius.expired', compact('skelbimai'));
    }
}
