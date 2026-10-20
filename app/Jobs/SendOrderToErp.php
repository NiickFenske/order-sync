<?php

namespace App\Jobs;

use App\Models\OrderSync;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOrderToErp implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param  array<string, mixed>  $order
     */
    public function __construct(public array $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("processing order {$this->order['id']}");

        try {
            $sent = Http::post(config('services.erp.url'), $this->payload())->successful();
        } catch (ConnectionException $e) {
            Log::error("ERP unreachable for order {$this->order['id']}", ['error' => $e->getMessage()]);
            $sent = false;
        }

        OrderSync::updateOrCreate(
            ['shopify_order_id' => $this->order['id']],
            ['status' => $sent ? 'sent' : 'failed'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'order_number' => $this->order['order_number'] ?? null,
            'email' => $this->order['email'] ?? null,
            'total' => $this->order['total_price'] ?? null,
            'line_items' => collect($this->order['line_items'] ?? [])
                ->map(fn (array $item) => [
                    'title' => $item['title'] ?? null,
                    'quantity' => $item['quantity'] ?? null,
                ])
                ->all(),
        ];
    }
}
