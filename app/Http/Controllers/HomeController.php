<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Получаем параметры запроса
        $search = $request->input('search');
        $currentFolderId = $request->query('folder');
        $currentFolder = null;

        // Проверяем текущую папку
        if ($currentFolderId) {
            $currentFolder = Folder::where('id', $currentFolderId)
                ->where('user_id', auth()->id())
                ->first();
        }

        // Строим запросы для файлов и папок
        $filesQuery = File::where('user_id', auth()->id());
        $foldersQuery = Folder::where('user_id', auth()->id());

        // Фильтрация по поиску
        if ($search) {
            $filesQuery->where('name', 'like', "%{$search}%");
            $foldersQuery->where('name', 'like', "%{$search}%");
        }
        // Фильтрация по текущей папке
        else {
            if ($currentFolder) {
                $filesQuery->where('folder_id', $currentFolder->id);
                $foldersQuery->where('parent_id', $currentFolder->id);
            } else {
                $filesQuery->whereNull('folder_id');
                $foldersQuery->whereNull('parent_id');
            }
        }

        // Получаем данные
        $files = $filesQuery->get();
        $folders = $foldersQuery->get();

        // Оптимизация: загружаем все папки с количеством файлов
        $allFolders = Folder::where('user_id', auth()->id())
            ->withCount('files')
            ->get();

        // Формируем данные для представления
        return view('home', [
            'files' => $files,
            'folders' => $folders,
            'currentFolder' => $currentFolder,
            'search' => $search,
            'allResults' => $search ? $folders->concat($files) : null,
            'allFolders' => $allFolders // Передаем все папки с количеством файлов
        ]);
    }
}

