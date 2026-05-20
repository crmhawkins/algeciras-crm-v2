<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JugadorResource;
use App\Models\Jugador;
use Illuminate\Http\Request;

class JugadorController extends Controller
{
    public function index(Request $request)
    {
        $query = Jugador::query()->activo();

        if ($temporada = $request->string('temporada')->toString()) {
            $query->temporada($temporada);
        } else {
            $query->temporada('2025-2026');
        }

        if ($equipo = $request->string('equipo')->toString()) {
            $query->equipo($equipo);
        }

        if ($posicion = $request->string('posicion')->toString()) {
            $query->porPosicion($posicion);
        }

        return JugadorResource::collection(
            $query->orderBy('dorsal')->get(),
        );
    }
}
