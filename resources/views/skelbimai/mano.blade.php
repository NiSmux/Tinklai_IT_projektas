@extends('layout')

@section('title', 'Mano skelbimai')

@section('content')
    <h2 class="mb-3">Mano skelbimai</h2>

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
                            <p class="text-success fw-bold">{{ $s->kaina }} €</p>

                            <a href="/skelbimai/{{ $s->id }}" class="btn btn-primary btn-sm">Atidaryti</a>
                            <a href="/skelbimai/{{ $s->id }}/redaguoti" class="btn btn-warning btn-sm">Redaguoti</a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    @else
        <p class="text-muted">Neturite sukurtų skelbimų.</p>
    @endif

@endsection
