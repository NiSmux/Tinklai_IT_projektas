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

        {{-- Filtravimo forma --}}
        <form method="GET" class="row g-2 mb-3">

            {{-- Paieška --}}
            <div class="col-md-4">
                <label class="form-label">Ieškoti pagal slapyvardį:</label>
                <input type="text" name="search" class="form-control"
                    placeholder="Slapyvardis..."
                    value="{{ request('search') }}">
            </div>

            {{-- Rolė --}}
            <div class="col-md-3">
                <label class="form-label">Filtruoti pagal rolę:</label>
                <select name="role" class="form-select">
                    <option value="visi">Visos rolės</option>
                    <option value="administratorius" {{ request('role')=='administratorius' ? 'selected' : '' }}>Administratorius</option>
                    <option value="kontrolierius" {{ request('role')=='kontrolierius' ? 'selected' : '' }}>Kontrolierius</option>
                    <option value="naudotojas" {{ request('role')=='naudotojas' ? 'selected' : '' }}>Naudotojas</option>
                </select>
            </div>

            {{-- Gali kurti --}}
            <div class="col-md-3">
                <label class="form-label">Filtruoti pagal kūrimo leidimą:</label>
                <select name="gali_kurti" class="form-select">
                    <option value="visi">visi</option>
                    <option value="1" {{ request('gali_kurti')=='1' ? 'selected' : '' }}>Taip</option>
                    <option value="0" {{ request('gali_kurti')=='0' ? 'selected' : '' }}>Ne</option>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filtruoti</button>
            </div>

        </form>

        {{-- Vartotojų lentelė --}}
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Slapyvardis</th>
                        <th>Email</th>
                        <th>Rolė</th>
                        <th>Keisti role</th>
                        <th>Gali kurti?</th>
                        <th>Keisti kūrimo leidimus</th>
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
                                {{-- Negalima keisti savo paties rolės --}}
                                @if ($user->id === auth()->id())
                                    <span class="text-muted">Savo rolės keisti negalima</span>

                                @else

                                    {{-- Rolės keitimas --}}
                                    <form action="{{ route('admin.changeRole', $user->id) }}" method="post" class="d-inline">
                                        @csrf

                                        <select name="role" class="form-select form-select-sm d-inline-block w-auto">
                                            <option value="naudotojas" {{ $user->role == 'naudotojas' ? 'selected' : '' }}>Naudotojas</option>
                                            <option value="kontrolierius" {{ $user->role == 'kontrolierius' ? 'selected' : '' }}>Kontrolierius</option>
                                            <option value="administratorius" {{ $user->role == 'administratorius' ? 'selected' : '' }}>Administratorius</option>
                                        </select>

                                        <button class="btn btn-sm btn-primary">Keisti</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                @if ($user->gali_kurti)
                                    <span class="badge bg-success">TAIP</span>
                                @else
                                    <span class="badge bg-danger">NE</span>
                                @endif
                            </td>

                            <td>
                                {{-- Gali kurti leidimas tik tada, kai tai nėra admin ar kontrolierius --}}
                                @if ($user->role === 'naudotojas')
                                    @if (!$user->gali_kurti)
                                        <form action="{{ route('admin.allow', $user->id) }}" method="post" class="d-inline ms-1">
                                            @csrf
                                            <button class="btn btn-sm btn-success">Leisti kurti</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.deny', $user->id) }}" method="post" class="d-inline ms-1">
                                            @csrf
                                            <button class="btn btn-sm btn-danger">Atimti leidimą</button>
                                        </form>
                                    @endif
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        {{-- Puslapiavimas --}}
        <div class="d-flex justify-content-between align-items-center mt-3">

            {{-- Pirmas puslapis --}}
            @if($users->currentPage() > 1)
                <a href="{{ $users->url(1) }}" class="btn btn-sm btn-outline-primary">&laquo; Pirmas</a>
            @else
                <span class="btn btn-sm btn-outline-secondary disabled">&laquo; Pirmas</span>
            @endif

            {{-- Standartinis pagination --}}
            <div>
                {{ $users->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>

            {{-- Paskutinis puslapis --}}
            @if($users->currentPage() < $users->lastPage())
                <a href="{{ $users->url($users->lastPage()) }}" class="btn btn-sm btn-outline-primary">Paskutinis &raquo;</a>
            @else
                <span class="btn btn-sm btn-outline-secondary disabled">Paskutinis &raquo;</span>
            @endif

        </div>

    </div>
</div>

@endsection
