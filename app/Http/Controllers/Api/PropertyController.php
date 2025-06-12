<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;


class PropertyController extends Controller
{
    // Store una propiedad y asociar user_id desde token

    public function store(Request $request)
    {
        // Validar si el usuario es landlord
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
            'status' => 'in:disponible,vendido,alquilado,inactivo',
        ]);

        $validated['user_id'] = auth()->id();

        $property = Property::create($validated);

        return response()->json($property, 201);
    }
}
