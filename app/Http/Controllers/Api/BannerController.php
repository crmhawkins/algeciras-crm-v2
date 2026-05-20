<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\BannerPublicidad;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = BannerPublicidad::query()->activo()->orderBy('orden');

        if ($posicion = $request->string('posicion')->toString()) {
            $query->posicion($posicion);
        }

        return BannerResource::collection($query->get());
    }
}
