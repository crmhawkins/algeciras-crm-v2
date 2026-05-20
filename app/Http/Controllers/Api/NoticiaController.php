<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticiaResource;
use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function index(Request $request)
    {
        $query = Noticia::query()
            ->with(['categoria', 'autor'])
            ->publicadas()
            ->orderByDesc('publicada_en');

        if ($request->boolean('destacadas')) {
            $query->destacadas();
        }

        if ($categoriaSlug = $request->string('categoria')->toString()) {
            $query->whereHas('categoria', fn ($q) => $q->where('slug', $categoriaSlug));
        }

        $perPage = min((int) $request->integer('per_page', 12), 50);

        return NoticiaResource::collection($query->paginate($perPage));
    }

    public function show(string $slug)
    {
        $noticia = Noticia::query()
            ->with(['categoria', 'autor'])
            ->publicadas()
            ->where('slug', $slug)
            ->firstOrFail();

        $noticia->increment('vistas');

        return new NoticiaResource($noticia);
    }
}
