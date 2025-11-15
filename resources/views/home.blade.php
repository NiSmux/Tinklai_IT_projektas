@extends('layout')

@section('title', 'Pagrindinis puslapis')

@section('content')

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm p-4">
            <h2 class="mb-3">Naujausi skelbimai</h2>

            <div class="row">
                @forelse($skelbimai as $skelbimas)
                    <div class="col-md-3">
                        <div class="card mb-4 shadow-sm h-100">

                            {{-- Nuotrauka --}}
                            @if($skelbimas->nuotraukos->first())
                                <img src="{{ asset('storage/' . $skelbimas->nuotraukos->first()->failo_kelias) }}"
                                    class="card-img-top" style="height: 180px; object-fit: cover;">
                            @else
                                <div style="height:180px; background:#ddd;" class="d-flex justify-content-center align-items-center">
                                    <span class="text-muted">Nėra nuotraukos</span>
                                </div>
                            @endif

                            <div class="card-body">
                                <h5 class="card-title">{{ $skelbimas->pavadinimas }}</h5>

                                <p class="card-text text-success fw-bold">{{ $skelbimas->kaina }} €</p>

                                <p class="text-muted small mb-1">
                                    Peržiūros: {{ $skelbimas->perziuros }}
                                </p>

                                <a href="/skelbimai/{{ $skelbimas->id }}" class="btn btn-outline-primary w-100">
                                    Peržiūrėti
                                </a>
                            </div>

                        </div>
                    </div>
                @empty
                    <p class="text-muted">Kol kas nėra skelbimų.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>



@endsection
