@extends('layout')

@section('title', $skelbimas->pavadinimas)

@section('content')

<div class="row mb-4">

    {{-- LEFT COLUMN: Nuotraukos --}}
    <div class="col-md-6">

        @php
            $firstPhoto = $skelbimas->nuotraukos->first();
        @endphp

        {{-- Didelė pagrindinė foto --}}
        @if ($firstPhoto)
            <img id="mainPhoto"
                 src="{{ asset('storage/' . $firstPhoto->failo_kelias) }}"
                 class="img-fluid rounded shadow mb-3"
                 style="max-height: 400px; object-fit: cover;">
        @else
            <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded mb-3"
                 style="height: 400px;">
                Nėra nuotraukų
            </div>
        @endif

        {{-- Mini galerija --}}
        @if ($skelbimas->nuotraukos->count() > 1)
            <div class="d-flex gap-2">
                @foreach ($skelbimas->nuotraukos as $foto)
                    <img src="{{ asset('storage/' . $foto->failo_kelias) }}"
                         class="img-thumbnail"
                         style="width: 90px; height: 90px; object-fit: cover; cursor: pointer;"
                         onclick="document.getElementById('mainPhoto').src = this.src;">
                @endforeach
            </div>
        @endif

    </div>


    {{-- RIGHT COLUMN: Informacija --}}
    <div class="col-md-6">

        <h2>{{ $skelbimas->pavadinimas }}</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <p class="mt-3"><strong>Aprašymas:</strong><br>
             <span class="break-word">{{ $skelbimas->aprasymas }}</span>
        </p>

        <p><strong>Kaina:</strong>
            <span class="badge bg-success fs-5">{{ $skelbimas->kaina }} €</span>
        </p>

        <p><strong>Būsena:</strong>
            <span class="badge {{ $skelbimas->busena == 'parduotas' ? 'bg-danger' : 'bg-primary' }}">
                {{ $skelbimas->busena }}
            </span>
        </p>

        <p><strong>Peržiūros:</strong> {{ $skelbimas->perziuros }}</p>

        <p><strong>Sukurta:</strong> {{ $skelbimas->sukurimo_data }}</p>

        @if ($skelbimas->redagavimo_data)
            <p><strong>Redaguota:</strong> {{ $skelbimas->redagavimo_data }}</p>
        @endif

        <hr>

        <h4>Apie pardavėją</h4>
        <p><strong>Vardas:</strong> {{ $skelbimas->vartotojas->slapyvardis }}</p>
        <p><strong>Telefonas:</strong> {{ $skelbimas->vartotojas->tel }}</p>

        <hr>

        {{-- Kontrolieriaus trynimas --}}
        @auth
            @if(auth()->user()->role === 'kontrolierius')
                <form action="{{ route('kontrolierius.delete', $skelbimas->id) }}" method="post" class="mb-3">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger w-100"
                            onclick="return confirm('Ar tikrai norite pašalinti šį skelbimą?')">
                        <strong>Pašalinti skelbimą</strong>
                    </button>
                </form>
            @endif
        @endauth

        {{-- Savininko redagavimas --}}
        @auth
            @if ($skelbimas->vartotojas_id == auth()->id())
                <a href="/skelbimai/{{ $skelbimas->id }}/redaguoti"
                   class="btn btn-warning w-100 mb-3">
                    Redaguoti skelbimą
                </a>
            @endif
        @endauth

        <a href="/skelbimai" class="btn btn-secondary w-100">Grįžti į skelbimų sąrašą</a>

    </div>
    <h3 class="mt-5">Komentarai</h3>

    @if ($skelbimas->komentarai->count())
       @foreach ($skelbimas->komentarai as $komentaras)
            <div class="card mb-2 p-2">
                <strong>{{ $komentaras->vartotojas->slapyvardis }}</strong>
                <p class="mb-1">{{ $komentaras->zinute }}</p>

                {{-- Tik kontrolierius gali trinti --}}
                @auth
                    @if(auth()->user()->role === 'kontrolierius')
                        <form action="{{ route('komentaras.delete', $komentaras->id) }}"
                            method="post"
                            onsubmit="return confirm('Ar tikrai ištrinti komentarą?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Ištrinti</button>
                        </form>
                    @endif
                @endauth
            </div>
        @endforeach
    @else
        <p class="text-muted">Komentarų dar nėra.</p>
    @endif

    @auth
        <h4 class="mt-4">Rašyti komentarą</h4>

        <form action="{{ route('komentarai.store', $skelbimas->id) }}" method="post">
            @csrf

            @if ($errors->has('zinute'))
                <div class="alert alert-danger">{{ $errors->first('zinute') }}</div>
            @endif

            <textarea name="zinute" class="form-control mb-2" rows="3" placeholder="Jūsų žinutė..." required></textarea>

            <button class="btn btn-primary">Siųsti</button>
        </form>
    @endauth
</div>

@endsection
