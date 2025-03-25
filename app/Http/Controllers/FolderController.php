<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id,user_id,' . auth()->id()
        ]);

        $folder = Folder::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id']
        ]);

        return redirect()->back()
            ->with('success', "Папка '{$folder->name}' успешно создана");
    }
}
