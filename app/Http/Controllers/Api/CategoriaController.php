<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $canal = $request->string('canal')->lower()->toString() ?: 'web';

        $query = Categoria::query()
            ->with(['hijas'])
            ->root()
            ->ordenado();

        match ($canal) {
            'app' => $query->visibleEnApp(),
            'tpv' => $query->visibleEnTpv(),
            default => $query->visibleEnWeb(),
        };

        return CategoriaResource::collection($query->get());
    }
}
