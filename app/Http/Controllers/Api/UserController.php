<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarberPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\FotoRequest;

class UserController extends Controller
{
    public function list_approved_barbers()
    {
        $barbers = User::where('user_type', 'B')
            ->where('approved', 1)
            ->orderBy('name')
            ->with('barberPhotos')
            ->get(['id', 'name', 'email', 'profile_photo']);

        return response()->json(['success' => true, 'data' => $barbers]);
    }

    public function add_photo(FotoRequest $request)
    {
        try {
            $data = $request->validated();

            $user = Auth::user();

            if (!$user || $user->user_type !== 'B') {
                return response()->json(['error' => 'Apenas barbeiros podem adicionar fotos'], 403);
            }

            $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $data['foto']));

            if (!$imageData) {
                throw new \Exception('Dados de imagem inválidos');
            }

            $f = finfo_open();
            $mimeType = finfo_buffer($f, $imageData, FILEINFO_MIME_TYPE);
            finfo_close($f);

            $extension = explode('/', $mimeType)[1] ?? 'jpg';
            $fileName = 'barbearia_' . $user->id . '_' . Str::random(10) . '.' . $extension;
            $path = 'barber_photos/' . $fileName;

            Storage::disk('public')->put($path, $imageData);

            $photo = BarberPhoto::create([
                'user_id' => $user->id,
                'photo_path' => $fileName,
                'description' => $data['descricao'] ?? null
            ]);

            return response()->json(['success' => true, 'photo_url' => Storage::disk('public')->url($path), 'photo_id' => $photo->id, 'message' => 'Photo added successfully!']);
        } catch (\Exception $e) {
            Log::error('Erro ao adicionar foto: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Erro ao processar imagem: ' . $e->getMessage()], 500);
        }
    }

    public function list_photos()
    {
        try {
            $user = auth()->user();

            if (!$user || $user->user_type !== 'B') {
                return response()->json(['error' => 'Acesso não autorizado'], 403);
            }

            $photos = $user->barberPhotos()->get();

            return response()->json(['success' => true, 'photos' => $photos->map(function ($foto) {
                return [
                    'id' => $foto->id,
                    'photo_path' => $foto->photo_path,
                    'created_at' => $foto->created_at,
                    'url' => asset('storage/barber_photos/' . $foto->photo_path)
                ];
            })]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Erro ao buscar fotos: ' . $e->getMessage()], 500);
        }
    }

    public function remove_photo($id)
    {
        try {
            $user = Auth::user();
            $foto = BarberPhoto::find($id);

            if (!$user || $user->user_type !== 'B') {
                return response()->json(['error' => 'Apenas barbeiros podem remover fotos'], 403);
            }

            if (!$foto || $foto->user_id !== $user->id) {
                return response()->json(['error' => 'Foto não encontrada ou não pertence ao usuário'], 404);
            }

            $path = 'barber_photos/' . $foto->photo_path;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $foto->delete();

            return response()->json(['success' => true, 'message' => 'Foto removida com sucesso!']);
        } catch (\Exception $e) {
            Log::error('Erro ao remover foto: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Erro ao remover foto: ' . $e->getMessage()], 500);
        }
    }

    public function update_photo(FotoRequest $request)
    {
        try {
            $data = $request->validated();

            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Usuário não autenticado'], 401);
            }

            $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $data['foto']));

            if (!$imageData) {
                throw new \Exception('Dados de imagem inválidos');
            }

            $f = finfo_open();
            $mimeType = finfo_buffer($f, $imageData, FILEINFO_MIME_TYPE);
            finfo_close($f);

            $extension = explode('/', $mimeType)[1] ?? 'jpg';
            $fileName = 'user_' . $user->id . '_' . Str::random(10) . '.' . $extension;
            $path = 'profile_photos/' . $fileName;

            Storage::disk('public')->put($path, $imageData);

            if ($user->profile_photo) {
                $oldPath = 'profile_photos/' . $user->profile_photo;
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $user->profile_photo = $fileName;
            $user->save();

            return response()->json(['success' => true, 'photo_url' => Storage::disk('public')->url($path), 'message' => 'Profile photo updated successfully!']);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar foto: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Erro ao processar imagem: ' . $e->getMessage()], 500);
        }
    }

    public function get_photo()
    {
        $user = User::find(Auth::id());

        if (!$user || !$user->profile_photo) {
            return response()->json(['success' => false, 'message' => 'User does not have a profile photo'], 404);
        }

        $path = 'profile_photos/' . $user->profile_photo;

        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['success' => false, 'message' => 'Foto não encontrada no servidor'], 404);
        }

        return response()->json(['success' => true, 'foto_url' => Storage::disk('public')->url($path)]);
    }
}
