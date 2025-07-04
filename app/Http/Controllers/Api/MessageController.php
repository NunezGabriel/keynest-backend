<?php
// app/Http/Controllers/Api/MessageController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Property;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Enviar mensaje (Seeker)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,property_id',
            'content' => 'required|string|max:1000'
        ]);

        $user = $request->user();
        $property = Property::find($validated['property_id']);

        // Verificar que el usuario es seeker
        if ($user->user_type !== 'seeker') {
            return response()->json(['error' => 'Solo los seekers pueden enviar mensajes'], 403);
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $property->user_id,
            'property_id' => $property->property_id,
            'content' => $validated['content']
        ]);

        return response()->json($message->load('sender', 'property'));
    }

    // Obtener mensajes (Landlord)
    public function index($property)
    {
        $property = Property::findOrFail($property);

        if ($property->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // SOLUCIÓN DEFINITIVA - Carga explícita de relaciones
        $messages = Message::with(['sender' => function ($query) {
            $query->select('id', 'name', 'email'); // Solo los campos necesarios
        }])
            ->where('property_id', $property->property_id)
            ->where('recipient_id', auth()->id()) // Asegura que solo mensajes para este landlord
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($messages);
    }

    // Eliminar mensaje (Landlord)
    public function destroy($id)
    {
        $message = Message::where('id', $id)
            ->where('recipient_id', auth()->id())
            ->firstOrFail();

        $message->delete();

        return response()->json(['message' => 'Mensaje eliminado']);
    }
}
