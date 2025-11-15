@extends('layout')

@section('title', 'Vartotojų valdymas')

@section('content')

<div class="card shadow-sm mb-4">
    <div class="card-body">

        <h2 class="mb-4">Vartotojų valdymas</h2>

        {{-- Sėkmės žinutė --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="/" class="btn btn-secondary mb-3">Atgal</a>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Slapyvardis</th>
                        <th>Email</th>
                        <th>Rolė</th>
                        <th>Gali kurti?</th>
                        <th>Veiksmai</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->slapyvardis }}</td>
                            <td>{{ $user->el_pastas }}</td>

                            <td>
                                <span class="badge 
                                    @if($user->role === 'administratorius') bg-primary
                                    @elseif($user->role === 'kontrolierius') bg-warning text-dark
                                    @else bg-secondary @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            <td>
                                @if ($user->gali_kurti)
                                    <span class="badge bg-success">TAIP</span>
                                @else
                                    <span class="badge bg-danger">NE</span>
                                @endif
                            </td>

                            <td>
                                {{-- Tik paprastas naudotojas gali būti valdomas --}}
                                @if ($user->role === 'naudotojas')
                                    
                                    @if (!$user->gali_kurti)
                                        <form action="{{ route('admin.allow', $user->id) }}"
                                              method="post" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success">
                                                Suteikti leidimą
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.deny', $user->id) }}"
                                              method="post" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-danger">
                                                Atimti leidimą
                                            </button>
                                        </form>
                                    @endif

                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>
</div>

@endsection
