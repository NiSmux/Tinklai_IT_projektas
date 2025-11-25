@extends('layout')

@section('title', 'Sukurti skelbimą')

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

        <h2 class="mb-4">Sukurti naują skelbimą</h2>

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

        <form action="/skelbimai" method="post" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Pavadinimas</label>
                <input type="text" name="pavadinimas" class="form-control"
                       value="{{ old('pavadinimas') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Aprašymas</label>
                <textarea name="aprasymas" class="form-control" rows="4" required>{{ old('aprasymas') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Kaina (€)</label>
                <input type="number" name="kaina" class="form-control" min="0"
                       value="{{ old('kaina') }}" required>
            </div>

            <label>Galiojimas:</label>
            <select name="galiojimas" class="form-select" required>
                <option value="7">7 dienos</option>
                <option value="14">14 dienų</option>
                <option value="30">30 dienų</option>
            </select>

            <div class="mb-3">
                <label class="form-label">Nuotraukos (iki 5 vnt.)</label>
                <input type="file" name="nuotraukos[]" class="form-control" accept="image/*" multiple>
            </div>

            <button type="submit" class="btn btn-success">Sukurti</button>
            <a href="/skelbimai" class="btn btn-secondary">Atgal</a>
        </form>

    </div>
</div>

@endsection
