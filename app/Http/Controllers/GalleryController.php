<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $publications = Publication::with(['user', 'likes'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get()
            ->map(function ($pub) {
                return [
                    'id'           => $pub->id,
                    'user_id'      => $pub->user_id,
                    'title'        => $pub->title,
                    'location'     => $pub->location,
                    'photographer' => $pub->user->username ?? 'Desconhecido',
                    'image_url'    => asset('storage/' . $pub->image_path),
                    'likes_count'  => $pub->likes->count(),
                    'is_liked'     => auth()->check() ? $pub->likes->where('user_id', auth()->id())->isNotEmpty() : false,
                ];
            });

        return response()->json($publications);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'fotografo') {
            return response()->json(['message' => 'Apenas fotógrafos podem cadastrar fotos.'], 403);
        }

        $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'image'    => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $imagePath = $request->file('image')->store('publications', 'public');

        $Publication = Publication::create([
            'user_id'  => Auth::id(),
            'title'    => $request->title,
            'location' => $request->location,
            'image_path'    => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto publicada com sucesso!',
            'photo'   => $Publication
        ], 201);
    }

    public function toggleLike(Publication $publication)
    {
        $userId = auth()->id();

        $like = Like::where('publication_id', $publication->id)
                    ->where('user_id', $userId)
                    ->first();

        if ($like) {
            $like->delete();
            $isLiked = false;
        } else {
            Like::create([
                'user_id' => $userId,
                'publication_id' => $publication->id,
            ]);
            $isLiked = true;
        }

        return response()->json([
            'is_liked' => $isLiked,
            'likes_count' => $publication->likes()->count(),
        ]);
    }

    public function destroy($id)
    {
        $photo = Publication::findOrFail($id);

        if (auth()->user()->role === 'admin' || auth()->id() === $photo->user_id) {
            
            if ($photo->image_path && Storage::disk('public')->exists($photo->image_path)) {
                Storage::disk('public')->delete($photo->image_path);
            }

            $photo->delete();

            return response()->json(['message' => 'Foto excluída com sucesso!']);
        }

        return response()->json(['message' => 'Acesso não autorizado.'], 403);
    }

}