<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClasificacionResource;
use App\Models\Clasificacion;
use Illuminate\Http\Request;

class ClasificacionController extends Controller
{
    public function index(Request $request)
    {
        $temporada = $request->string('temporada')->toString() ?: '2025-2026';
        $competicion = $request->string('competicion')->toString() ?: 'Primera Federación';

        $rows = Clasificacion::query()
            ->with('equipo')
            ->temporada($temporada)
            ->competicion($competicion)
            ->ordenada()
            ->get();

        return ClasificacionResource::collection($rows);
    }
}
