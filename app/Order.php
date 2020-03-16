<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
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

    public static function getAllLastBuys()
    {
        $amount = 'convert((amount - processed), decimal(11,2)) as amount';

        $total = 'convert((amount / unit_price), decimal(11,8)) as total';

        $raw = "$amount, unit_price, $total";

        return Order::selectRaw($raw)
            ->where('type', 'buy')
            ->where('status', 'opened')
            ->orderBy('position', 'ASC')
            ->limit(10)
            ->get();
    }

    public static function getAllLastSales()
    {
        $amount = 'convert(((amount - processed) / pow(10,8)), decimal(11,8)) as amount';

        $total = 'convert(((amount / pow(10,8)) * unit_price), decimal(11,2)) as total';

        $raw = "$amount, unit_price, $total";

        return Order::selectRaw($raw)
            ->where('type', 'sale')
            ->where('status', 'opened')
            ->orderBy('position', 'ASC')
            ->limit(10)
            ->get();
    }

    public static function getAllLastExecuteds()
    {
        $amount = 'if(type="sale", convert((amount / pow(10,8)), decimal(11,8)), amount) as amount';

        $raw = "executed_at, type, $amount, unit_price";

        return Order::selectRaw($raw)
            ->where('status', 'executed')
            ->orderBy('executed_at', 'DESC')
            ->limit(10)
            ->get();
    }

    public function tax()
    {
        if ($this->type == 'buy') {

            $value = real_to_satoshi($this->amount, $this->unit_price);

            $this->fee = formatSatoshi(fee($value, System::marketBuyFee()));

        }

        else {

            $value = satoshi_to_real($this->amount, $this->unit_price);

            $this->fee = formatReal(fee($value, System::marketSaleFee()));

        }
    }

    public function order()
    {
        if ($this->type == 'buy') {

            $this->position = Order::where('type', 'buy')
                ->where('status', 'opened')
                ->where('unit_price', '>=', $this->amount)
                ->count() + 1;

        }

        else {

            $this->position = Order::where('type', 'sale')
                ->where('status', 'opened')
                ->where('unit_price', '<=', $this->amount)
                ->count() + 1;

        }
    }

    public function reorder()
    {
        if ($this->status == 'opened') {

            Order::where('type', $this->type)
                ->where('position', '>=', $this->position)
                ->where('id', '<>', $this->id)
                ->where('status', 'opened')
                ->increment('position', 1);

        }

        else {

            Order::where('type', $this->type)
                ->where('position', '>=', $this->position)
                ->where('id', '<>', $this->id)
                ->where('status', 'opened')
                ->decrement('position', 1);

        }
    }

    public function updateUserBalances()
    {
        $user = User::select('balance_BRL', 'balance_use_BRL', 'balance_BTC', 'balance_use_BTC')
            ->where('id', $this->user_id)
            ->first();

        if ($this->type == BUY) {

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

        $user = User::select('balance_BRL', 'balance_use_BRL', 'balance_BTC', 'balance_use_BTC')
            ->where('id', $this->user_id)
            ->first();

        if ($this->type == BUY) {

            $user->balance_BTC += real_to_satoshi($this->amount, $this->unit_price) - $this->fee;

            $user->balance_use_BRL -= $this->amount;

        }

        else {

            $user->balance_BRL += satoshi_to_real($this->amount, $this->unit_price) - $this->fee;

            $user->balance_use_BTC -= $this->amount;

        }

        $user->save();

        $this->save();

        $this->sendExecuteds();

    }

    private function generateExtract()
    {
        if ($this->type == BUY) {

            $value = real_to_satoshi($this->amount, $this->unit_price);

            $type = BUY;

            $fee_type = BUY_FEE;

            $description = BUY_DESCRIPTION;

            $description_fee = BUY_FEE_DESCRIPTION;

        }

        else {

            $value = satoshi_to_real($this->amount, $this->unit_price);

            $type = SALE;

            $fee_type = SALE_FEE;

            $description = SALE_DESCRIPTION;

            $description_fee = SALE_FEE_DESCRIPTION;

        }

        Extract::create([
            'user_id' => $this->user_id,
            'reference_id' => $this->id,
            'type' => $type,
            'value' => $value,
            'description' => $description
        ]);

        Extract::create([
            'user_id' => $this->user_id,
            'reference_id' => $this->id,
            'type' => $fee_type,
            'value' => $this->fee,
            'description' => $description_fee
        ]);
    }

    public function processBuy()
    {
        $sales = Order::where('type', 'sale')
            ->where('status', 'opened')
            ->where('unit_price', '<=', $this->unit_price)
            ->orderBy('position', 'asc')
            ->get();

        foreach ($sales as $sale) {

            if ($this->status == EXECUTED) break;

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

        $this->sendBuys();

        $this->sendSales();

    }

    public function processSale()
    {
        $buys = Order::where('type', 'buy')
            ->where('status', 'opened')
            ->where('unit_price', '>=', $this->unit_price)
            ->orderBy('position', 'ASC')
            ->get();

        foreach ($buys as $buy) {

            if ($this->status == EXECUTED) break;

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

        $this->sendSales();

        $this->sendBuys();

    }

    private function sendBuys()
    {
        $buys = $this->getAllLastBuys();

        $ClientConfig = new ClientConfig();

        $ClientConfig->setHeaders(['Authorization' => env('WEB_SOCKET_AUTH')]);

        $client = new WebSocketClient('ws://192.168.0.35:3000', $ClientConfig);

        $client->send(json_encode([
            'type' => 'buys',
            'data' => $buys
        ]));
    }

    private function sendSales()
    {
        $sales = $this->getAllLastSales();

        $ClientConfig = new ClientConfig();

        $ClientConfig->setHeaders(['Authorization' => env('WEB_SOCKET_AUTH')]);

        $client = new WebSocketClient('ws://192.168.0.35:3000', $ClientConfig);

        $client->send(json_encode([
            'type' => 'sales',
            'data' => $sales
        ]));
    }

    private function sendExecuteds()
    {
        $executeds = $this->getAllLastExecuteds();

        $ClientConfig = new ClientConfig();

        $ClientConfig->setHeaders(['Authorization' => env('WEB_SOCKET_AUTH')]);

        $client = new WebSocketClient('ws://192.168.0.35:3000', $ClientConfig);

        $client->send(json_encode([
            'type' => 'executeds',
            'data' => $executeds
        ]));
    }
}
