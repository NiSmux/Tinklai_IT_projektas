@extends('layout')

@section('title', 'Pasibaigę skelbimai')

@section('content')
<h2>Pasibaigę skelbimai</h2>

@if($skelbimai->isEmpty())
    <p class="text-muted">Nėra pasibaigusių skelbimų.</p>
@else
    @foreach($skelbimai as $s)
        <div class="card mb-3">
            <div class="card-body">
                <h5>{{ $s->pavadinimas }}</h5>
                <p>Galiojimas baigėsi: <strong>{{ $s->galioja_iki->format('Y-m-d') }}</strong></p>
                <a href="/skelbimai/{{ $s->id }}" class="btn btn-primary btn-sm">Peržiūrėti</a>
                <form action="{{ route('kontrolierius.expired.delete', $s->id) }}" method="post" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">Ištrinti</button>
                </form>
            </div>
        </div>
    @endforeach
@endif

@endsection
