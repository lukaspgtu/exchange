<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use WSSC\WebSocketClient;
use \WSSC\Components\ClientConfig;

class Order extends Model
{
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'type', 'amount', 'fee', 'unit_price', 'processed', 'position'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'processed' => 0.00,
        'status' => 'opened'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'fee' => 'float',
        'unit_price' => 'float',
        'processed' => 'float'
    ];

    public function getAmount() {

        if ($this->type == 'sale')
            return satoshi_to_bitcoin($this->amount);

        return $this->amount;

    }

    public function tax()
    {
        if ($this->type == 'buy') {

            $value = real_to_satoshi($this->amount, $this->unit_price);

            $this->fee = formatSatoshi(fee($value, System::feeSale()));

        }

        else {

            $value = satoshi_to_real($this->amount, $this->unit_price);

            $this->fee = formatReal(fee($value, System::feeSale()));

        }
    }

    public function order()
    {
        if ($this->type == 'buy') {

            $this->position = DB::table('orders')
                ->where('status', 'opened')
                ->where('type', 'buy')
                ->where('unit_price', '>=', $this->amount)
                ->count() + 1;

        }

        else {

            $this->position = DB::table('orders')
                ->where('status', 'opened')
                ->where('type', 'sale')
                ->where('unit_price', '<=', $this->amount)
                ->count() + 1;

        }
    }

    public function reorder()
    {
        if ($this->status == 'opened') {

            $this->where('type', $this->type)
                ->where('position', '>=', $this->position)
                ->where('id', '<>', $this->id)
                ->where('status', 'opened')
                ->increment('position', 1);

        }

        else {

            $this->where('type', $this->type)
                ->where('position', '>=', $this->position)
                ->where('id', '<>', $this->id)
                ->where('status', 'opened')
                ->decrement('position', 1);

        }
    }

    public function updateUserBalances()
    {
        $user = User::find($this->user_id);

        if ($this->type == 'buy') {

            $user->balance_BRL -= $this->amount;

            $user->balance_use_BRL += $this->amount;

        }

        else {

            $user->balance_BTC -= $this->amount;

            $user->balance_use_BTC += $this->amount;

        }

        $user->save();
    }

    private function execute()
    {
        $this->position = 0;

        $this->status = 'executed';

        $this->executed_at = date('Y-m-d H:i:s');

        $user = User::find($this->user_id);

        if ($this->type == 'buy') {

            $user->balance_BTC += real_to_satoshi($this->amount, $this->unit_price) - $this->fee;

            $user->balance_use_BRL -= $this->amount;

        }

        else {

            $user->balance_BRL += satoshi_to_real($this->amount, $this->unit_price) - $this->fee;

            $user->balance_use_BTC -= $this->amount;

        }

        $user->save();

        $this->save();
    }

    private function generateExtract()
    {
        if ($this->type == 'buy') {

            $value = real_to_satoshi($this->amount, $this->unit_price);

            $type = 'buy';

            $fee_type = 'buy_fee';

        }

        else {

            $value = satoshi_to_real($this->amount, $this->unit_price);

            $type = 'sale';

            $fee_type = 'sale_fee';

        }

        Extract::create([
            'reference_id' => $this->id,
            'type' => $type,
            'value' => $value
        ]);

        Extract::create([
            'reference_id' => $this->id,
            'type' => $fee_type,
            'value' => $this->fee
        ]);
    }

    public function processBuy()
    {
        $sales = $this->where('type', 'sale')
            ->where('status', 'opened')
            ->where('unit_price', '<=', $this->unit_price)
            ->orderBy('position', 'asc')
            ->get();

        foreach ($sales as $sale) {

            if ($this->status == 'executed') break;

            $sale_amount = $sale->amount - $sale->processed;

            $buy_amount = real_to_satoshi($this->amount - $this->processed, $this->unit_price);

            if ($sale_amount > $buy_amount) {

                $sale->processed += $buy_amount;

                $sale->save();

                $this->processed += satoshi_to_real($buy_amount, $this->unit_price);

                $this->execute();

                $this->reorder();

                $this->generateExtract();

                if ($sale->amount == $sale->processed) {

                    $sale->execute();

                    $sale->reorder();

                    $sale->generateExtract();

                }

            }

            else {

                $this->processed += satoshi_to_real($sale_amount, $this->unit_price);

                $this->save();

                $sale->processed += $sale_amount;

                $sale->execute();

                $sale->reorder();

                $sale->generateExtract();

                if ($this->amount == $this->processed) {

                    $this->execute();

                    $this->reorder();

                    $this->generateExtract();

                }

            }

            if ($sale->unit_price < $this->unit_price) {

                $value = satoshi_to_real($sale->amount, $this->unit_price) - satoshi_to_real($sale->amount, $sale->unit_price);

                if ($value > 0) {

                    Gain::create([
                        'buy_id' => $this->id,
                        'sale_id' => $sale->id,
                        'value' => $value
                    ]);

                }

            }

        }

    }

    public function processSale()
    {
        $buys = $this->where('type', 'buy')
            ->where('status', 'opened')
            ->where('unit_price', '>=', $this->unit_price)
            ->orderBy('position', 'ASC')
            ->get();

        foreach ($buys as $buy) {

            if ($this->status == 'executed') break;

            $buy_amount = real_to_satoshi($buy->amount - $buy->processed, $buy->unit_price);

            $sale_amount = $this->amount - $this->processed;

            if ($buy_amount > $sale_amount) {

                $buy->processed += satoshi_to_real($sale_amount, $buy->unit_price);

                $buy->save();

                $this->processed += $sale_amount;

                $this->execute();

                $this->reorder();

                $this->generateExtract();

                if ($buy->amount == $buy->processed) {

                    $buy->execute();

                    $buy->reorder();

                    $buy->generateExtract();

                }

            }

            else {

                $this->processed += $buy_amount;

                $this->save();

                $buy->processed += satoshi_to_real($buy_amount, $buy->unit_price);

                $buy->execute();

                $buy->reorder();

                $buy->generateExtract();

                if ($this->amount == $this->processed) {

                    $this->execute();

                    $this->reorder();

                    $this->generateExtract();

                }

            }

            if ($this->unit_price < $buy->unit_price) {

                $value = satoshi_to_real($this->amount, $buy->unit_price) - satoshi_to_real($this->amount, $this->unit_price);

                if ($value > 0) {

                    Gain::create([
                        'buy_id' => $this->id,
                        'sale_id' => $buy->id,
                        'value' => $value
                    ]);

                }

            }

        }

    }

    private function sendSocket()
    {
        $client = new WebSocketClient('ws://localhost:3000', new ClientConfig());
        $client->send(json_encode($this));
    }

}
