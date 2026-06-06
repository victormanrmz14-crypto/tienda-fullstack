<?php

namespace App\Http\Controllers;

use App\Jobs\EnviarConfirmacionPedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items'                  => 'required|array|min:1',
            'items.*.producto_id'    => 'required|exists:productos,id',
            'items.*.cantidad'       => 'required|integer|min:1',
            'items.*.precio'         => 'required|numeric|min:0',
        ]);

        $pedido = DB::transaction(function () use ($request) {
            $total = collect($request->items)
                ->sum(fn($i) => $i['precio'] * $i['cantidad']);

            $p = Pedido::create([
                'user_id' => auth()->id(),
                'total'   => $total,
            ]);

            foreach ($request->items as $item) {
                $p->items()->create([
                    'producto_id'     => $item['producto_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                ]);
                Producto::find($item['producto_id'])
                        ->decrement('stock', $item['cantidad']);
            }

            return $p;
        });

        EnviarConfirmacionPedido::dispatch($pedido)->delay(now()->addSeconds(5));

        return response()->json([
            'pedido_id' => $pedido->id,
            'mensaje'   => 'Pedido creado. Recibirás un email de confirmación.',
        ], 201);
    }

    public function show(Pedido $pedido)
    {
        return response()->json([
            'id'               => $pedido->id,
            'total'            => $pedido->total,
            'estado'           => $pedido->estado,
            'email_enviado_at' => $pedido->email_enviado_at,
            'items'            => $pedido->items()->with('producto:id,nombre,precio')->get(),
        ]);
    }

    public function index(Request $request)
    {
        $pedidos = Pedido::where('user_id', auth()->id())
                         ->with('items')
                         ->latest()
                         ->get();
        return response()->json($pedidos);
    }
}
