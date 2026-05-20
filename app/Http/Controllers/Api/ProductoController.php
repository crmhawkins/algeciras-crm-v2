<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $canal = $request->string('canal')->lower()->toString() ?: 'web';
        $allowed = ['web', 'app', 'tpv'];
        if (! in_array($canal, $allowed, true)) {
            $canal = 'web';
        }

        $query = Producto::query()
            ->with(['categoria', 'imagenes', 'variantes'])
            ->visibleEnCanal($canal);

        if ($categoriaSlug = $request->string('categoria')->toString()) {
            $query->whereHas('categoria', fn ($q) => $q->where('slug', $categoriaSlug));
        }

        if ($request->boolean('destacados')) {
            $query->destacados();
        }

        if ($request->boolean('novedades')) {
            $query->novedades();
        }

        if ($request->boolean('con_stock')) {
            $query->conStock();
        }

        $query->buscar($request->string('busqueda')->toString() ?: null);

        $perPage = min((int) $request->integer('per_page', 20), 100);

        return ProductoResource::collection($query->orderBy('nombre')->paginate($perPage));
    }

    public function show(string $slug)
    {
        $producto = Producto::query()
            ->with(['categoria', 'imagenes', 'variantes', 'preciosCanal'])
            ->where('slug', $slug)
            ->firstOrFail();

        return new ProductoResource($producto);
    }
}
