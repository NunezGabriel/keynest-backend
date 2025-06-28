<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    private function ensureAdmin(Request $request)
    {
        if ($request->user()->user_type !== 'admin') {
            abort(403, 'Acceso denegado. Solo para administradores.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin($request);
        return User::all();
    }

    public function show(Request $request, $id)
    {
        $this->ensureAdmin($request);
        return User::findOrFail($id);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'user_type' => 'required|in:landlord,seeker,admin'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdmin($request);

        $user = User::findOrFail($id);

        $user->update($request->only(['name', 'email', 'user_type']));

        return response()->json($user);
    }

    public function destroy(Request $request, $id)
    {
        $this->ensureAdmin($request);

        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['error' => 'No puedes eliminar tu propio usuario.'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    }
}
