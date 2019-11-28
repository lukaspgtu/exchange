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
        $sales = $this->where('category', 'sale')
            ->where('status', 'opened')
            ->where('unit_price', '<=', $this->unit_price)
            ->orderBy('position', 'asc')
            ->get();

        foreach ($sales as $sale) {

            if ($this->status == 'executed') break;

            $sale_amount = $sale->amount - $sale->processed;

            $buy_amount = toBTC($this->amount - $this->processed, $this->unit_price);

            if ($sale_amount > $buy_amount) {

                $sale->processed += $buy_amount;

                $sale->save();

                $this->processed += toBRL($buy_amount, $this->unit_price);

                $this->position = 0;

                $this->status = 'executed';

                $this->executed_at = date('Y-m-d H:i:s');

                $this->save();

                $this->reorder();

            }

            else {

                $this->processed += toBRL($sale_amount, $this->unit_price);

                $this->save();

                $sale->processed += $sale_amount;

                $sale->position = 0;

                $sale->status = 'executed';

                $sale->executed_at = date('Y-m-d H:i:s');

                $sale->save();

                $sale->reorder();

            }

            if ($this->amount == $this->processed) {

                $this->position = 0;

                $this->status = 'executed';

                $this->executed_at = date('Y-m-d H:i:s');

                $this->save();

            }

            if ($sale->unit_price < $this->unit_price) {

                $value = $this->amount - toBRL($sale->amount, $sale->unit_price);

                Gain::create([
                    'buy_id' => $this->id,
                    'sale_id' => $sale->id,
                    'value' => $value
                ]);

            }

        }

    }

    public function process_sale()
    {
        $buys = $this->where('category', 'buy')
            ->where('status', 'opened')
            ->where('unit_price', '>=', $this->unit_price)
            ->orderBy('position', 'ASC')
            ->get();

        foreach ($buys as $buy) {

            if ($this->status == 'executed') break;

            $buy_amount = toBTC($buy->amount - $buy->processed, $buy->unit_price);

            $sale_amount = $this->amount - $this->processed;

            if ($buy_amount > $sale_amount) {

                $buy->processed += toBRL($sale_amount, $buy->unit_price);

                $buy->save();

                $this->processed += $sale_amount;

                $this->position = 0;

                $this->status = 'executed';

                $this->executed_at = date('Y-m-d H:i:s');

                $this->save();

                $this->reorder();

            }

            else {

                $this->processed += $buy_amount;

                $this->save();

                $buy->processed += toBRL($buy_amount, $buy->unit_price);

                $buy->position = 0;

                $buy->status = 'executed';

                $buy->executed_at = date('Y-m-d H:i:s');

                $buy->save();

                $buy->reorder();

            }

            if ($this->amount == $this->processed) {

                $this->position = 0;

                $this->status = 'executed';

                $this->executed_at = date('Y-m-d H:i:s');

                $this->save();

            }

            if ($this->unit_price < $buy->unit_price) {

                $value = $buy->amount - toBRL($this->amount, $this->unit_price);

                Gain::create([
                    'buy_id' => $this->id,
                    'sale_id' => $buy->id,
                    'value' => $value
                ]);

            }

        }

    }

}
