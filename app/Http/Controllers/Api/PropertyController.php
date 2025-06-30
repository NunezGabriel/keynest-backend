<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    // Listar propiedades (admin ve todas, landlord solo las suyas)
    public function index()
    {
        $properties = Property::with('user', 'images')->get();
        return response()->json($properties);
    }


    // Ver una propiedad específica
    public function show($id)
    {
        $property = Property::with('user', 'images')->find($id);

        if (!$property) {
            return response()->json(['error' => 'Propiedad no encontrada'], 404);
        }

        return response()->json($property);
    }


    // Crear una propiedad
    public function store(Request $request)
    {
        if (auth()->user()->user_type !== 'landlord') {
            return response()->json(['error' => 'Solo los landlords pueden crear propiedades'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'property_type' => 'required|in:casa,departamento',
            'price' => 'required|numeric',
            'maintenance_cost' => 'nullable|numeric',
            'is_rent' => 'required|boolean',
            'square_meters' => 'required|integer',
            'bedrooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'pets_allowed' => 'boolean',
            'location' => 'required|string|max:255',
            'status' => 'in:disponible,cerrada',
        ]);

        $validated['user_id'] = auth()->id();

        $property = Property::create($validated);

        return response()->json($property, 201);
    }

    // Mostrar solo las propiedades creadas por el usuario autenticado (landlord)
    public function myProperties()
    {
        $user = auth()->user();

        if ($user->user_type !== 'landlord') {
            return response()->json(['error' => 'Solo los landlords pueden acceder a sus propiedades'], 403);
        }

        $properties = Property::with('images') // puedes incluir 'user' si lo necesitas
            ->where('user_id', $user->id)
            ->get();

        return response()->json($properties);
    }


    // Actualizar una propiedad
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        $user = auth()->user();

        // Solo el landlord dueño o admin puede editar
        if (
            $user->user_type !== 'admin' &&
            !($user->user_type === 'landlord' && $property->user_id === $user->id)
        ) {
            return response()->json(['error' => 'No autorizado para editar esta propiedad'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:150',
            'description' => 'sometimes|required|string',
            'property_type' => 'sometimes|required|in:casa,departamento',
            'price' => 'sometimes|required|numeric',
            'maintenance_cost' => 'nullable|numeric',
            'is_rent' => 'sometimes|required|boolean',
            'square_meters' => 'sometimes|required|integer',
            'bedrooms' => 'sometimes|required|integer',
            'bathrooms' => 'sometimes|required|integer',
            'pets_allowed' => 'boolean',
            'location' => 'sometimes|required|string|max:255',
            'status' => 'in:disponible,cerrada',
        ]);

        $property->update($validated);

        return response()->json($property);
    }

    // Eliminar propiedad
    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $user = auth()->user();

        // Admin puede borrar todo, landlord solo sus propias propiedades
        if (
            $user->user_type !== 'admin' &&
            !($user->user_type === 'landlord' && $property->user_id === $user->id)
        ) {
            return response()->json(['error' => 'No autorizado para eliminar esta propiedad'], 403);
        }

        $property->delete();

        return response()->json(['message' => 'Propiedad eliminada correctamente']);
    }

    // Cambiar estado a "cerrada"
    public function closeProperty($id)
    {
        $property = Property::findOrFail($id);

        if (auth()->id() !== $property->user_id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $property->update(['status' => 'cerrada']);
        return response()->json($property);
    }

    // Restablecer a "disponible"
    public function reopenProperty($id)
    {
        $property = Property::findOrFail($id);

        if (auth()->id() !== $property->user_id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $property->update(['status' => 'disponible']);
        return response()->json($property);
    }
}
