@extends('layout')

@section('title', 'Skelbimai')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Visi skelbimai</h2>
    
</div>

{{-- Success message --}}
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($skelbimai->isEmpty())
    <div class="alert alert-info">Šiuo metu skelbimų nėra.</div>
@else

{{-- Filtravimo forma --}}
<form method="GET" class="mb-4 p-3 bg-white rounded shadow-sm">

    <div class="row g-3">

        <!-- Paieška -->
        <div class="col-md-4">
            <input type="text" name="q" class="form-control"
                   placeholder="Ieškoti pagal pavadinimą..."
                   value="{{ request('q') }}">
        </div>

        <!-- Rūšiavimas -->
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="naujausi" {{ request('sort')=='naujausi'?'selected':'' }}>Naujausi viršuje</option>
                <option value="seniausi" {{ request('sort')=='seniausi'?'selected':'' }}>Seniausi viršuje</option>
                <option value="kaina_asc" {{ request('sort')=='kaina_asc'?'selected':'' }}>Pigiausi viršuje</option>
                <option value="kaina_desc" {{ request('sort')=='kaina_desc'?'selected':'' }}>Brangiausi viršuje</option>
            </select>
        </div>

        <!-- Mygtukas -->
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtruoti</button>
        </div>
        <div class="col-md-3 text-end">
            @auth
                @if(auth()->user()->gali_kurti == 1)
                    <a href="/skelbimai/kurti" class="btn btn-primary">Naujas skelbimas</a>
                @endif
            @endauth
        </div>

    </div>
</form>

{{-- Skelbimų kortelės --}}
<div class="row">
    @foreach ($skelbimai as $skelbimas)
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">

                @if ($skelbimas->nuotraukos->count())
                    <img src="{{ asset('storage/' . $skelbimas->nuotraukos->first()->failo_kelias) }}"
                        class="card-img-top"
                        style="height: 180px; object-fit: cover;">
                @else
                    <div class="bg-secondary text-white text-center p-4" style="height: 180px; display: flex; align-items: center; justify-content: center;">
                        Nėra nuotraukos
                    </div>
                @endif

                <div class="card-body">
                    <h5 class="card-title">{{ $skelbimas->pavadinimas }}</h5>
                    <p class="card-text fw-bold">{{ $skelbimas->kaina }} €</p>
                    <a href="/skelbimai/{{ $skelbimas->id }}" class="btn btn-primary">Peržiūrėti</a>
                </div>
            </div>
        </div>
    @endforeach
</div>


{{-- Puslapiavimas --}}
<div class="d-flex justify-content-between align-items-center mt-3">

    {{-- Pirmas puslapis --}}
    @if($skelbimai->currentPage() > 1)
        <a href="{{ $skelbimai->url(1) }}" class="btn btn-sm btn-outline-primary">&laquo; Pirmas</a>
    @else
        <span class="btn btn-sm btn-outline-secondary disabled">&laquo; Pirmas</span>
    @endif

    {{-- Standartinis pagination --}}
    <div>
        {{ $skelbimai->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>

    {{-- Paskutinis puslapis --}}
    @if($skelbimai->currentPage() < $skelbimai->lastPage())
        <a href="{{ $skelbimai->url($skelbimai->lastPage()) }}" class="btn btn-sm btn-outline-primary">Paskutinis &raquo;</a>
    @else
        <span class="btn btn-sm btn-outline-secondary disabled">Paskutinis &raquo;</span>
    @endif

</div>
@endif

@endsection
