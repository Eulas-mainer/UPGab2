<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Tag;
use Gate;
use Illuminate\Http\Request;
use Storage;
use Str;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:5120|mimes:jpg,png,pdf',
            'folder_id' => 'nullable|exists:folders,id,user_id,' . auth()->id(),
            'tags' => 'nullable|string|max:255'
        ]);

        // Загрузка файла
        $file = $request->file('file');
        $path = $file->store('files/' . auth()->id(), 'public');

        // Создание записи в БД
        $uploadedFile = File::create([
            'user_id' => auth()->id(),
            'folder_id' => $validated['folder_id'],
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ]);

        // Обработка тегов
        if (!empty($validated['tags'])) {
            $tags = collect(explode(',', $validated['tags']))
                ->map(fn($tag) => Str::lower(trim($tag)))
                ->filter()
                ->unique();

            foreach ($tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $uploadedFile->tags()->syncWithoutDetaching($tag);
            }
        }

        return redirect()->back()
            ->with('success', 'Файл успешно загружен');
    }

    public function download(File $file)
    {
        abort_if($file->user_id !== auth()->id(), 403);
        return Storage::disk('public')->download($file->path, $file->name);
    }
    public function move(File $file, Request $request)
    {
        try {
            // Проверка прав доступа через Policy
            $this->authorize('manage', $file);

            // Валидация с проверкой принадлежности папки пользователю
            $validated = $request->validate([
                'folder_id' => [
                    'nullable',
                    'exists:folders,id,user_id,' . auth()->id()
                ]
            ]);

            // Обновление папки
            $file->update(['folder_id' => $validated['folder_id'] ?? null]);

            return back()->with('success', 'Файл успешно перемещен');

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->withErrors(['error' => 'Доступ запрещен']);
        }
    }
}
