<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\User;
use App\Models\Property;

class FavoriteController extends Controller
{
    // Solo permitir a usuarios seeker
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->user_type !== 'seeker') {
                return response()->json(['error' => 'Solo los usuarios seeker pueden gestionar favoritos'], 403);
            }
            return $next($request);
        })->only(['store', 'destroy', 'index']); // Solo para estos métodos
    }

    // Dar like a una propiedad
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,property_id',
        ]);

        // Verificar que no sea el propio landlord
        $property = Property::find($validated['property_id']);
        if ($property->user_id === auth()->id()) {
            return response()->json(['error' => 'No puedes dar like a tus propias propiedades'], 403);
        }

        $favorite = Favorite::firstOrCreate([
            'user_id' => auth()->id(),
            'property_id' => $validated['property_id'],
        ]);

        return response()->json($favorite);
    }

    // Eliminar like
    public function destroy($id)
    {
        $favorite = Favorite::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $favorite->delete();

        return response()->json(['message' => 'Favorito eliminado correctamente']);
    }

    // Obtener favoritos del usuario
    public function index()
    {
        $favorites = Favorite::where('user_id', auth()->id())
            ->with(['property.images', 'property.user'])
            ->get();

        $response = $favorites->map(function ($favorite) {
            $property = $favorite->property->toArray();
            $property['favorite_id'] = $favorite->id; // Añade el ID del favorito
            return $property;
        });

        return response()->json($response);
    }



    public function all()
    {
        $user = auth()->user();

        if ($user->user_type !== 'admin') {
            return response()->json(['error' => 'Solo los administradores pueden ver todos los favoritos'], 403);
        }

        $favorites = Favorite::with(['user:id,name', 'property:property_id,title,user_id'])
            ->get();

        return response()->json($favorites);
    }
}
