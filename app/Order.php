<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_user', 'category', 'type', 'amount', 'fee', 'unit_price', 'processed', 'position'
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

    public static function positionBuy($value)
    {
        return DB::table('orders')
            ->where('status', 'opened')
            ->where('category', 'buy')
            ->where('unit_price', '>=', $value)
            ->count() + 1;
    }

    public static function positionSale($value)
    {
        return DB::table('orders')
            ->where('status', 'opened')
            ->where('category', 'sale')
            ->where('unit_price', '<=', $value)
            ->count() + 1;
    }

    public function reorder()
    {
        if ($this->status == 'opened') {

            $this->where('category', $this->category)
                ->where('position', '>=', $this->position)
                ->where('id', '<>', $this->id)
                ->where('status', 'opened')
                ->increment('position', 1);

        }

        else {

            $this->where('category', $this->category)
                ->where('position', '>=', $this->position)
                ->where('id', '<>', $this->id)
                ->where('status', 'opened')
                ->decrement('position', 1);

        }
    }

    public function process_buy()
    {
        $orders = $this->where('category', 'sale')
            ->where('status', 'opened')
            ->where('unit_price', '<=', $this->unit_price)
            ->orderBy('position', 'ASC')
            ->get();

        foreach ($orders as $order) {

            if ($this->status == 'executed') break;

            $amount = $order->amount - $order->processed;

            $amount2 = toBTC($this->amount - $this->processed, $this->unit_price);

            if ($amount > $amount2) {

                $this->processed += toBRL($amount, $order->unit_price);

                $this->save();

                $order->processed += $amount;

                $order->position = 0;

                $order->status = 'executed';

                $order->executed_at = date('Y-m-d H:i:s');

                $order->save();

                $order->reorder();

            }

            else {

                $order->processed += $amount2;

                $order->save();

                $this->processed += toBRL($amount2, $this->unit_price);

                $this->position = 0;

                $this->status = 'executed';

                $this->executed_at = date('Y-m-d H:i:s');

                $this->save();

                $this->reorder();

            }

            if ($order->amount == $order->processed) {

                $order->position = 0;

                $order->status = 'executed';

                $order->executed_at = date('Y-m-d H:i:s');

                $order->save();

            }

            // if ($order->unit_price < $this->unit_price) {

            //     $value = $this->unit_price - $order->unit_price;

            //     Gain::create([
            //         'buy_id' => $this->id,
            //         'sale_id' => $order->id,
            //         'value' => $value
            //     ]);

            // }

        }

    }

    public function process_sale()
    {
        $orders = $this->where('category', 'buy')
            ->where('status', 'opened')
            ->where('unit_price', '>=', $this->unit_price)
            ->orderBy('position', 'ASC')
            ->get();

        foreach ($orders as $order) {

            if ($this->status == 'executed') break;

            $amount = toBTC($order->amount - $order->processed, $order->unit_price);

            $amount2 = $this->amount - $this->processed;

            if ($amount > $amount2) {

                $order->processed += toBRL($amount2, $this->unit_price);

                $order->save();

                $this->processed += $amount2;

                $this->position = 0;

                $this->status = 'executed';

                $this->executed_at = date('Y-m-d H:i:s');

                $this->save();

                $this->reorder();

            }

            else {

                $this->processed += $amount;

                $this->save();

                $order->processed += toBRL($amount, $order->unit_price);

                $order->position = 0;

                $order->status = 'executed';

                $order->executed_at = date('Y-m-d H:i:s');

                $order->save();

                $order->reorder();

            }

            if ($this->amount == $this->processed) {

                $this->position = 0;

                $this->status = 'executed';

                $this->executed_at = date('Y-m-d H:i:s');

                $this->save();

            }

            // if ($order->unit_price < $this->unit_price) {

            //     $value = $this->unit_price - $order->unit_price;

            //     Gain::create([
            //         'buy_id' => $this->id,
            //         'sale_id' => $order->id,
            //         'value' => $value
            //     ]);

            // }

        }

    }

}
