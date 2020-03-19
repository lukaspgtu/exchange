<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlatformMarket extends Model
{
    protected $table = 'platform_market';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'type', 'amount', 'fee', 'unit_price', 'ticker_earning'
    ];

    public function obeysMinimumPrice()
    {
        $settings = System::settings();

        if ($this->type == BUY) {
            return $this->amount >= $settings->min_amount_buy;
        }

        return $this->amount >= $settings->min_amount_sale;
    }

    public function userHasBalance()
    {
        $user = User::find($this->user_id);

        if ($this->type == BUY) {
            return $user->balance_BRL >= $this->amount;
        }

        return $user->balance_BTC >= $this->amount;
    }

    public function tax()
    {
        $settings = System::settings();

        if ($this->type == BUY) {

            $value = real_to_satoshi($this->amount, $this->unit_price);

            $this->fee = formatSatoshi(fee($value, $settings->platform_buy_fee));

        } else {

            $value = satoshi_to_real($this->amount, $this->unit_price);

            $this->fee = formatReal(fee($value, $settings->platform_sale_fee));
        }
    }

    public function setTickerEarning()
    {
        $settings = System::settings();

        if ($this->type == BUY) {

            $unit_price_without_increase = $this->unit_price - fee($this->unit_price, $settings->platform_buy_price);

            $total_received = real_to_satoshi($this->amount, $this->unit_price);

            $total_without_increase = real_to_satoshi($this->amount, $unit_price_without_increase);

            $total = $total_without_increase - $total_received;

            $this->ticker_earning = satoshi_to_real($total, $unit_price_without_increase);

        } else {

            $unit_price_without_increase = $this->unit_price + fee($this->unit_price, $settings->platform_sale_price);

            $total_received = satoshi_to_real($this->amount, $this->unit_price);

            $total_without_increase = satoshi_to_real($this->amount, $unit_price_without_increase);

            $total = $total_without_increase - $total_received;

            $this->ticker_earning = $total;
        }
    }

    public function updateUserBalance()
    {
        $user = User::find($this->user_id);

        if ($this->type == BUY) {

            $user->balance_BRL -= $this->amount;

            $user->balance_BTC += real_to_satoshi($this->amount, $this->unit_price) - $this->fee;

        }

        else {

            $user->balance_BTC -= $this->amount;

            $user->balance_BRL += satoshi_to_real($this->amount, $this->unit_price) - $this->fee;

        }

        $user->save();
    }
}
