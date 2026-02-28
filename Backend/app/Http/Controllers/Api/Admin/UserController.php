<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
        return response()->json($query->with('studentClassrooms')->latest()->get()->map(function ($user) {
            $user->classroom = $user->studentClassrooms->first();
            $user->classroom_id = $user->classroom?->id;
            return $user;
        }));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'nip' => 'nullable|string|max:50',
            'role' => 'required|in:admin,guru,siswa',
            'classroom_id' => 'nullable|exists:classes,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'nip' => $validated['nip'] ?? null,
            'role' => $validated['role'],
        ]);

        if ($request->classroom_id) {
            $user->studentClassrooms()->sync([$request->classroom_id]);
        }

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'user' => $user->load('studentClassrooms')
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|min:8',
            'nip' => 'sometimes|nullable|string|max:50',
            'classroom_id' => 'sometimes|nullable|exists:classes,id',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        if ($request->has('classroom_id')) {
            if ($request->classroom_id) {
                $user->studentClassrooms()->sync([$request->classroom_id]);
            } else {
                $user->studentClassrooms()->detach();
            }
        }

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'user' => $user->load('studentClassrooms')
        ]);
    }

    public function destroy(User $user)
    {

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Bulk Delete Request', ['ids' => $request->ids]);

        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:users,id'
            ]);

            $count = User::whereIn('id', $request->ids)->delete();
            \Illuminate\Support\Facades\Log::info('Bulk Delete Success', ['count' => $count]);

            return response()->json([
                'message' => $count . ' data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Bulk Delete Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus data massal: ' . $e->getMessage()
            ], 500);
        }
    }
}
