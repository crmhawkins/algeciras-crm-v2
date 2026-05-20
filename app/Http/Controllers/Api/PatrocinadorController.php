<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatrocinadorResource;
use App\Models\Patrocinador;
use Illuminate\Http\Request;

class PatrocinadorController extends Controller
{
    public function index(Request $request)
    {
        $query = Patrocinador::query()->activo()->ordenado();

        if ($tipo = $request->string('tipo')->toString()) {
            $query->porTipo($tipo);
        }

        return PatrocinadorResource::collection($query->get());
    }
}
