@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Уведомления -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4">
        <i class="fas fa-check-circle me-2"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <div>
            @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
            @endforeach
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Заголовок и управление -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            @if($search)
            <i class="fas fa-search me-2"></i>Результаты поиска: "{{ $search }}"
            @else
            <i class="fas fa-folder-open me-2"></i>{{ $currentFolder ? $currentFolder->name : 'Мои файлы' }}
            @endif
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFolderModal">
            <i class="fas fa-plus me-2"></i>Новая папка
        </button>
    </div>

    <!-- Модальное окно создания папки -->
    <div class="modal fade" id="createFolderModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('folders.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title"><i class="fas fa-folder-plus me-2"></i>Новая папка</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Имя папки</label>
                            <input type="text" class="form-control" name="name" required placeholder="Введите название">
                            <input type="hidden" name="parent_id" value="{{ request('folder') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Создать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Поиск -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('home') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="Поиск файлов и папок..." value="{{ $search }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        @if($search)
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @if(request()->has('folder'))
                <input type="hidden" name="folder" value="{{ request('folder') }}">
                @endif
            </form>
        </div>
    </div>

    <!-- Хлебные крошки -->
    @if(!$search)
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light p-3 rounded-3">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
            @if($currentFolder)
            <li class="breadcrumb-item active">{{ $currentFolder->name }}</li>
            @endif
        </ol>
    </nav>
    @endif

    <!-- Форма загрузки -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="tags" class="form-control"
                            placeholder="Теги (через запятую)" value="{{ old('tags') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-upload me-2"></i>Загрузить
                        </button>
                    </div>
                    <input type="hidden" name="folder_id" value="{{ request('folder') }}">
                </div>
            </form>
        </div>
    </div>

    <!-- Список файлов и папок -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
        @foreach($allResults ?? $folders as $item)
        @if($item instanceof \App\Models\Folder)
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-folder fa-4x text-warning"></i>
                    </div>
                    <h5 class="card-title text-truncate">{{ $item->name }}</h5>
                    <a href="{{ route('home', ['folder' => $item->id]) }}"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-folder-open me-2"></i>Открыть
                    </a>
                </div>
            </div>
        </div>
        @endif
        @endforeach

        @foreach($allResults ?? $files as $file)
        <div class="col">
            <div class="card h-100 shadow-sm">
                @if($file->mime_type && Str::startsWith($file->mime_type, 'image/'))
                <img src="{{ Storage::url($file->path) }}"
                    class="card-img-top object-fit-cover"
                    style="height: 200px; cursor: pointer;"
                    data-bs-toggle="modal"
                    data-bs-target="#imageModal-{{ $file->id }}">
                @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                    style="height: 200px;">
                    <i class="fas fa-file fa-4x text-primary"></i>
                </div>
                @endif

                <div class="card-body">
                    <h6 class="card-title text-truncate mb-3">{{ $file->name }}</h6>

                    <div class="d-flex justify-content-between small text-muted mb-3">
                        <div>
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $file->created_at->format('d.m.Y') }}
                        </div>
                        <div>
                            <i class="fas fa-hdd me-1"></i>
                            {{ number_format($file->size / 1048576, 2) }} MB
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @isset($file->tags)
                        @foreach($file->tags as $tag)
                        <span class="badge bg-primary">
                            <i class="fas fa-tag me-1"></i>{{ $tag->name }}
                        </span>
                        @endforeach
                        @endisset
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('files.download', $file) }}"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-2"></i>Скачать
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Сообщение о пустой папке -->
    @if(($allResults ?? $files)->isEmpty() && $folders->isEmpty())
    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle me-2"></i>
        @if($search)
        По вашему запросу ничего не найдено
        @else
        Папка пуста. Начните с загрузки файла или создания папки
        @endif
    </div>
    @endif
</div>
@endsection