<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartidoResource;
use App\Models\Partido;
use Illuminate\Http\Request;

class PartidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Partido::query();

        if ($request->boolean('proximos')) {
            $query->proximos();
        } elseif ($request->boolean('ultimos')) {
            $query->ultimos();
        } else {
            $query->orderByDesc('fecha');
        }

        if ($temporada = $request->string('temporada')->toString()) {
            $query->temporada($temporada);
        }

        $limit = min((int) $request->integer('limit', 20), 100);

        return PartidoResource::collection($query->limit($limit)->get());
    }
}
