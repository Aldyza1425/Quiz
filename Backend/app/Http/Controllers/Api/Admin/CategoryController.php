<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::with('creator')->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Kategori berhasil dibuat',
            'category' => $category
        ], 201);
    }

    public function show(Category $category)
    {
        return response()->json($category->load('creator'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Kategori berhasil diperbarui',
            'category' => $category
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}
