<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Навигация -->
    <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-cloud-upload-alt me-2"></i>{{ config('app.name', 'FileStorage') }}
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                    <li class="nav-item">
                        <a href="{{ url('/home') }}" class="nav-link">
                            <i class="fas fa-home me-1"></i>Главная
                        </a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">
                            <i class="fas fa-sign-in-alt me-1"></i>Войти
                        </a>
                    </li>
                    @if (Route::has('register'))
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="nav-link">
                            <i class="fas fa-user-plus me-1"></i>Регистрация
                        </a>
                    </li>
                    @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main class="container my-auto py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <h1 class="display-4 mb-4">
                            <i class="fas fa-cloud-upload-alt text-primary"></i><br>
                            Добро пожаловать в FileStorage
                        </h1>
                        <p class="lead text-muted mb-4">
                            Безопасное облачное хранилище для ваших файлов с удобным управлением
                        </p>

                        <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                            @auth
                            <a href="/home" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-folder-open me-2"></i>Мои файлы
                            </a>
                            @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-sign-in-alt me-2"></i>Начать работу
                            </a>
                            @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-user-plus me-2"></i>Регистрация
                            </a>
                            @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Футер -->
    <footer class="mt-auto bg-dark text-white py-4">
        <div class="container text-center">
            <div class="mb-3">
                <a href="#" class="text-white me-3"><i class="fab fa-github fa-lg"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                <a href="#" class="text-white"><i class="fab fa-facebook fa-lg"></i></a>
            </div>
            <div class="text-muted small">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Все права защищены.
            </div>
        </div>
    </footer>

    <!-- Скрипты -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>