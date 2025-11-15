@extends('layout')

@section('title', 'Redaguoti skelbimą')

@section('content')

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h2 class="mb-4">Redaguoti skelbimą</h2>

        {{-- Validacijos klaidos --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Forma pagrindinei informacijai --}}
        <form action="/skelbimai/{{ $skelbimas->id }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Pavadinimas --}}
            <div class="mb-3">
                <label class="form-label">Pavadinimas</label>
                <input type="text" name="pavadinimas" class="form-control"
                       value="{{ old('pavadinimas', $skelbimas->pavadinimas) }}" required>
            </div>

            {{-- Aprašymas --}}
            <div class="mb-3">
                <label class="form-label">Aprašymas</label>
                <textarea name="aprasymas" class="form-control" rows="4" required>{{ old('aprasymas', $skelbimas->aprasymas) }}</textarea>
            </div>

            {{-- Kaina --}}
            <div class="mb-3">
                <label class="form-label">Kaina (€)</label>
                <input type="number" name="kaina" class="form-control"
                       min="0" value="{{ old('kaina', $skelbimas->kaina) }}" required>
            </div>

            {{-- Būsena --}}
            <div class="mb-3">
                <label class="form-label">Būsena</label>
                <select name="busena" class="form-select">
                    <option value="neparduotas" {{ $skelbimas->busena == 'neparduotas' ? 'selected' : '' }}>Neparduotas</option>
                    <option value="parduotas" {{ $skelbimas->busena == 'parduotas' ? 'selected' : '' }}>Parduotas</option>
                </select>
            </div>

            <button class="btn btn-primary">Atnaujinti</button>
        </form>
    </div>
</div>

{{-- Esamos nuotraukos --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h3 class="mb-3">Esamos nuotraukos</h3>

        @if ($skelbimas->nuotraukos->count())
            <div class="row">
                @foreach ($skelbimas->nuotraukos as $index => $foto)
                    <div class="col-md-3 text-center mb-4">

                        

                        {{-- Foto --}}
                        <img src="{{ asset('storage/' . $foto->failo_kelias) }}"
                            class="img-fluid rounded mb-2"
                            style="max-height:170px; object-fit:cover;">

                        {{-- Rodyklės --}}
                        <div class="d-flex justify-content-center gap-2 mb-2">

                            {{-- Aukštyn --}}
                            <form action="{{ route('nuotrauka.up', $foto->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary"
                                        {{ $index == 0 ? 'disabled' : '' }}>
                                    ▲
                                </button>
                            </form>

                            {{-- Žemyn --}}
                            <form action="{{ route('nuotrauka.down', $foto->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary"
                                        {{ $index == $skelbimas->nuotraukos->count() - 1 ? 'disabled' : '' }}>
                                    ▼
                                </button>
                            </form>

                        </div>
                        {{-- Pozicija --}}
                        <div class="badge bg-secondary mb-2">Pozicija: {{ $index + 1 }}</div>
                        {{-- Trinimo mygtukas --}}
                        <form action="{{ route('nuotrauka.delete', $foto->id) }}"
                            method="POST"
                            onsubmit="return confirm('Ar tikrai ištrinti?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger w-100">Ištrinti</button>
                        </form>

                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">Nuotraukų nėra.</p>
        @endif
    </div>
    {{-- Naujos nuotraukos --}}
    @php
        $remaining = 5 - $skelbimas->nuotraukos->count();
    @endphp

    @if ($remaining > 0)
        <div class="card-body">

            <h3 class="mb-3">Įkelti papildomas nuotraukas (liko {{ $remaining }})</h3>

            {{-- Klaidos tik nuotraukų įkėlime --}}
            @if ($errors->has('nuotraukos'))
                <div class="alert alert-danger">
                    {{ $errors->first('nuotraukos') }}
                </div>
            @endif

            <form action="/skelbimai/{{ $skelbimas->id }}/nuotraukos" method="post" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <input type="file" name="nuotraukos[]" class="form-control" multiple accept="image/*">
                </div>

                <button class="btn btn-success">Įkelti</button>
            </form>

        </div>
    @endif

</div>

<a href="/skelbimai/{{ $skelbimas->id }}" class="btn btn-secondary">Atgal</a>
@endsection
