<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SupplyOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SupplyOrderDigitalController extends Controller
{
    public function show(string $token)
    {
        $order = $this->findOrder($token);

        return view('supply-orders.digital', [
            'order' => $order,
        ]);
    }

    public function receive(Request $request, string $token)
    {
        $order = $this->findOrder($token);
        abort_unless($order->status === 'remitted', 403);

        $validated = $request->validate([
            'receiving_pin' => ['required', 'digits:4'],
        ]);

        if (! hash_equals((string) $order->receiving_pin, (string) $validated['receiving_pin'])) {
            throw ValidationException::withMessages([
                'receiving_pin' => 'La contrasena de 4 digitos no coincide con esta remision.',
            ]);
        }

        $order->update([
            'status' => 'delivered',
            'received_on' => now()->toDateString(),
            'received_by_name' => $order->warehouse_to ?: 'Almacen receptor',
        ]);

        AuditLog::create([
            'user_id' => null,
            'auditable_type' => SupplyOrder::class,
            'auditable_id' => $order->id,
            'action' => 'received',
            'description' => 'Mercancia recibida desde el formato digital de remision.',
        ]);

        return redirect()
            ->route('supply-orders.digital.show', $order->remission_token)
            ->with('status', "{$order->folio} marcada como recibida.");
    }

    private function findOrder(string $token): SupplyOrder
    {
        $order = SupplyOrder::with(['requester', 'company', 'items.catalogItem', 'deliveredBy'])
            ->where('remission_token', $token)
            ->firstOrFail();

        abort_unless(in_array($order->status, ['remitted', 'delivered'], true), 404);

        return $order;
    }
}
