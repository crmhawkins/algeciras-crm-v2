<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'canal' => ['required', 'in:web,app,tpv'],
            'nombre_cliente' => ['required', 'string', 'max:191'],
            'email_cliente' => ['required', 'email', 'max:191'],
            'telefono_cliente' => ['nullable', 'string', 'max:30'],
            'direccion_envio' => ['nullable', 'string', 'max:500'],
            'cp_envio' => ['nullable', 'string', 'max:10'],
            'ciudad_envio' => ['nullable', 'string', 'max:191'],
            'provincia_envio' => ['nullable', 'string', 'max:191'],
            'pais_envio' => ['nullable', 'string', 'size:2'],
            'metodo_pago' => ['required', 'in:redsys,bizum,efectivo,transferencia,datafono_tpv'],
            'notas_cliente' => ['nullable', 'string', 'max:1000'],
            'cupon' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'items.*.variante_id' => ['nullable', 'integer', 'exists:producto_variantes,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
        ];
    }
}
