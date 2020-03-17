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
        if ($this->type == BUY) {

            $value = real_to_satoshi($this->amount, $this->unit_price);

            $this->fee = formatSatoshi(fee($value, System::platformBuyFee()));

        }

        else {

            $value = satoshi_to_real($this->amount, $this->unit_price);

            $this->fee = formatReal(fee($value, System::platformSaleFee()));

        }
    }
}
