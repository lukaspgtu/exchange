<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlatformMarket extends Model
{
    protected $table = 'platform_market';

    protected $fillable = [
        'user_id', 'type', 'amount', 'fee', 'unit_price', 'ticker_earning'
    ];

    public function tax()
    {
        $settings = System::settings();

        if ($this->type == BUY) {

            $value = real_to_satoshi($this->amount, $this->unit_price);

            $this->fee = formatSatoshi(fee($value, $settings->platform_buy_fee));

        }

        else {

            $value = bitcoin_to_real($this->amount, $this->unit_price);

            return response()->json([
                'value' => $value,
                'sett' => $settings->platform_sale_fee,
                'fee' => fee($value, $settings->platform_sale_fee)
            ]);

            $this->fee = formatReal(fee($value, $settings->platform_sale_fee));

        }
    }

    public function setTickerEarning()
    {
        $settings = System::settings();

        if ($this->type == BUY) {

            $unit_price_without_increase = $this->unit_price - fee($this->unit_price, $settings->platform_buy_price);

            $total_received = real_to_satoshi($this->amount, $this->unit_price) - $this->fee;

            $total_without_increase = real_to_satoshi($this->amount, $unit_price_without_increase) - $this->fee;

            $total = $total_received - $total_without_increase;

            $this->ticker_earning = satoshi_to_real($total, $unit_price_without_increase);

        }

        else {

            $unit_price_without_increase = $this->unit_price + fee($this->unit_price, $settings->platform_sale_price);

            $total_received = ($this->amount - $this->fee) * $this->unit_price;

            $total_without_increase = ($this->amount - $this->fee) * $unit_price_without_increase;

            $total = $total_without_increase - $total_received;

            $this->ticker_earning = $total;

        }
    }
}
