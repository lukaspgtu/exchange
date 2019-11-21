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
        'id_user', 'category', 'type', 'amount', 'fee', 'unit_price', 'position'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'waiting'
    ];

    public static function positionBuy($value)
    {
        return DB::table('orders')
            ->where('status', 'waiting')
            ->where('category', 'buy')
            ->where('unit_price', '>=', $value)
            ->count() + 1;
    }

    public static function positionSale($value)
    {
        return DB::table('orders')
            ->where('status', 'waiting')
            ->where('category', 'sale')
            ->where('unit_price', '<=', $value)
            ->count() + 1;
    }

    public function reorder()
    {
        $this->where('category', $this->category)
            ->where('position', '>=', $this->position)
            ->where('id', '<>', $this->id)
            ->where('status', 'waiting')
            ->increment('position', 1);
    }

    public function process_queue()
    {
        $orders = DB::table('orders')
            ->where('category', '<>', $this->category)
            ->where('status', 'waiting')
            ->where('unit_price', '<=', $this->unit_price)
            ->orderBy('position', 'ASC')
            ->get();

        // dd($orders);

        // foreach ($orders as $order) {

            // if ($order->)

        // }

    }

}
