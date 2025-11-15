<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Skelbimų sistema')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .break-word {
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-all;
    }
</style>
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/">Skelbimų Mašina</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- Kairė pusė -->
            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="/skelbimai">Visi skelbimai</a>
                </li>

                @auth
                    @if(auth()->user()->role === 'naudotojas')
                        <li class="nav-item">
                            <a class="nav-link" href="/mano-skelbimai">Mano skelbimai</a>
                        </li>

                        @if(auth()->user()->gali_kurti == 1)
                            <li class="nav-item">
                                <a class="nav-link" href="/skelbimai/kurti">Sukurti skelbimą</a>
                            </li>
                        @endif
                    @endif

                    @if(auth()->user()->role === 'administratorius')
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="/admin/vartotojai">Vartotojų sąrašas</a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Dešinė pusė -->
            <ul class="navbar-nav">

                @auth
                    <span class="navbar-text text-light me-3">
                        Sveikas, <strong>{{ auth()->user()->slapyvardis }}</strong>
                    </span>

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button class="btn btn-sm btn-outline-light">Atsijungti</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="/login" class="btn btn-sm btn-outline-light me-2">Prisijungti</a>
                    </li>

                    <li class="nav-item">
                        <a href="/register" class="btn btn-sm btn-outline-light">Registruotis</a>
                    </li>
                @endauth

            </ul>

        </div>
    </div>
</nav>

<div class="container">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
