@extends('layout')

@section('title', 'Mano skelbimai')

@section('content')
    <h2 class="mb-3">Mano skelbimai</h2>

    {{-- Success message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
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

            <!-- Mygtukai -->
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


    @if($skelbimai->count())
        <div class="row">
            @foreach($skelbimai as $s)
                <div class="col-md-3">
                    <div class="card shadow-sm mb-4">

                        @if($s->nuotraukos->first())
                            <img src="{{ asset('storage/' . $s->nuotraukos->first()->failo_kelias) }}"
                                    class="card-img-top" style="height:170px; object-fit:cover;">
                        @else
                            <div style="height:170px; background:#eee;"></div>
                        @endif

                        <div class="card-body">
                            <h5>{{ $s->pavadinimas }}</h5>
                            <p class="card-text text-success fw-bold">{{ $s->kaina }} €</p>
                            <p class="text-muted small mb-1">
                                Būsena: {{ $s->busena }}
                            </p>

                            <a href="/skelbimai/{{ $s->id }}" class="btn btn-primary btn-sm">Atidaryti</a>
                            <a href="/skelbimai/{{ $s->id }}/redaguoti" class="btn btn-warning btn-sm">Redaguoti</a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">

            @if($skelbimai->currentPage() > 1)
                <a href="{{ $skelbimai->url(1) }}" class="btn btn-sm btn-outline-primary">&laquo; Pirmas</a>
            @else
                <span class="btn btn-sm btn-outline-secondary disabled">&laquo; Pirmas</span>
            @endif

            <div>
                {{ $skelbimai->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>

            @if($skelbimai->currentPage() < $skelbimai->lastPage())
                <a href="{{ $skelbimai->url($skelbimai->lastPage()) }}" class="btn btn-sm btn-outline-primary">Paskutinis &raquo;</a>
            @else
                <span class="btn btn-sm btn-outline-secondary disabled">Paskutinis &raquo;</span>
            @endif

        </div>
    @else
        <p class="text-muted">Neturite sukurtų skelbimų.</p>
    @endif

@endsection
