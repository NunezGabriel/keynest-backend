<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyImage;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;

class PropertyImageController extends Controller
{
    public function store(Request $request, $propertyId)
    {
        $request->validate([
            'images.*' => 'required|image|max:5120', // máximo 5MB por imagen
        ]);

        $imagePaths = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('properties', 'public'); // guarda en storage/app/public/properties
            $url = Storage::url($path);

            PropertyImage::create([
                'property_id' => $propertyId,
                'image_url' => $url,
            ]);

            $imagePaths[] = $url;
        }

        return response()->json([
            'message' => 'Imágenes subidas correctamente',
            'images' => $imagePaths,
        ], 201);
    }
    // Laravel: PropertyController.php
    public function show($id)
    {
        $property = Property::with('images')->where('property_id', $id)->firstOrFail();
        return response()->json($property);
    }
}
