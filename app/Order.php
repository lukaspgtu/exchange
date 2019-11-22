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
        $this->where('category', $this->category)
            ->where('position', '>=', $this->position)
            ->where('id', '<>', $this->id)
            ->where('status', 'opened')
            ->increment('position', 1);
    }

    public function process_buy()
    {
        $orders = DB::table('orders')
            ->where('category', 'sale')
            ->where('status', 'opened')
            ->where('unit_price', '<=', $this->unit_price)
            ->orderBy('position', 'ASC')
            ->get();

        foreach ($orders as $order) {

            $amount = $order->amount - $order->processed;

            if ($amount > $this->amount) {

                $order->processed += $this->amount;

                $this->processed

            }

            elseif ($amount < $this->amount) {

                $order->processed -= $this->amount;

            }

        }

    }

}
