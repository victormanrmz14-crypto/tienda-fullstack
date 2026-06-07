<?php

namespace App\Http\Controllers;

use App\Events\NuevoPedidoRecibido;
use App\Events\StockBajoAlerta;
use App\Jobs\EnviarConfirmacionPedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            $total  = 0;
            $lineas = [];

            // 1ª pasada: bloquea cada producto y valida que haya stock suficiente
            foreach ($request->items as $item) {
                $producto = Producto::lockForUpdate()->find($item['producto_id']);

                if ($producto->stock < $item['cantidad']) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para «{$producto->nombre}». "
                                 . "Disponible: {$producto->stock}, solicitado: {$item['cantidad']}.",
                    ]);
                }

                // Usa el precio real de la BD, no el que envía el cliente
                $total   += $producto->precio * $item['cantidad'];
                $lineas[] = ['producto' => $producto, 'cantidad' => $item['cantidad']];
            }

            $p = Pedido::create([
                'user_id' => auth()->id(),
                'total'   => $total,
            ]);

            // 2ª pasada: ya validado, registra líneas y descuenta stock
            foreach ($lineas as ['producto' => $producto, 'cantidad' => $cantidad]) {
                $p->items()->create([
                    'producto_id'     => $producto->id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $producto->precio,
                ]);
                $producto->decrement('stock', $cantidad);

                $producto->refresh();
                if ($producto->stock <= 5) {
                    broadcast(new StockBajoAlerta($producto, $producto->stock));
                }
            }

            return $p;
        });

        EnviarConfirmacionPedido::dispatch($pedido)->delay(now()->addSeconds(5));

        broadcast(new NuevoPedidoRecibido($pedido))->toOthers();

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
