@extends('layout')

@section('title', 'Skelbimai')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Visi skelbimai</h2>
    
    @auth
        @if(auth()->user()->gali_kurti == 1)
            <a href="/skelbimai/kurti" class="btn btn-primary">Naujas skelbimas</a>
        @endif
    @endauth
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

<div class="row g-4">

    @foreach ($skelbimai as $skelbimas)
        <div class="col-md-4">

            <div class="card shadow-sm h-100">

                {{-- MINI NUOTRAUKA --}}
                @php
                    $foto = $skelbimas->nuotraukos->first();
                @endphp

                @if ($foto)
                    <img src="{{ asset('storage/' . $foto->failo_kelias) }}"
                         class="card-img-top"
                         style="height: 200px; object-fit: cover;">
                @else
                    <div class="bg-secondary text-center text-white p-5" style="height: 200px;">
                         Nuotraukos nėra
                    </div>
                @endif

                <div class="card-body d-flex flex-column">

                    <h5 class="card-title">{{ $skelbimas->pavadinimas }}</h5>

                    <p class="card-text text-muted mb-1">
                        <strong>Kaina:</strong> {{ $skelbimas->kaina }} €
                    </p>

                    <p class="card-text small text-muted mb-3">
                        <strong>Būsena:</strong> {{ $skelbimas->busena }}
                    </p>

                    <a href="/skelbimai/{{ $skelbimas->id }}" 
                       class="btn btn-outline-primary mt-auto">
                        Peržiūrėti
                    </a>

                </div>
            </div>

        </div>
    @endforeach

</div>

@endif

@endsection
