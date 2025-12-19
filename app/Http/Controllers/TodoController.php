<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'gambar' => 'nullable|file|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // Handle file upload if present
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('todos', 'public');
        }

        Todo::create([
            'title' => $data['title'],
            'gambar' => $data['gambar'] ?? null,
        ]);

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $todo = Todo::findOrFail($id);

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'is_completed' => 'sometimes|boolean',
            'gambar' => 'nullable|file|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // If there's a new image, delete old one and store the new file
        if ($request->hasFile('gambar')) {
            if ($todo->gambar && Storage::disk('public')->exists($todo->gambar)) {
                Storage::disk('public')->delete($todo->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('todos', 'public');
        }

        // Only update the fields that are present in $data
        $todo->update($data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $todo = Todo::findOrFail($id);

        // delete associated image if any
        if ($todo->gambar && Storage::disk('public')->exists($todo->gambar)) {
            Storage::disk('public')->delete($todo->gambar);
        }

        $todo->delete();
        return back();
    }
}
