@extends('layout')

@section('title', 'Prisijungimas')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-4">

        <div class="card shadow">
            <div class="card-body">

                <h3 class="text-center mb-3">Prisijungimas</h3>

                {{-- Success message --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Error messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/login" method="post">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">El. paštas</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slaptažodis</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button class="btn btn-primary w-100">Prisijungti</button>
                </form>

                <div class="text-center mt-3">
                    <p>Neturi paskyros?</p>
                    <a href="/register" class="btn btn-outline-secondary w-100">Registruotis</a>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
