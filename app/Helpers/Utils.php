<?php

if (!function_exists('formatDate')) {

    function formatDate($date, $format)
    {
        if ($format == 'Y-m-d') {
            $date = str_replace('/', '-', $date);
            return date('Y-m-d', strtotime($date));
        } elseif ($format == 'Y-m-d H:i:s') {
            $date = str_replace('/', '-', $date);
            return date('Y-m-d H:i:s', strtotime($date));
        }
    }
}

if (!function_exists('satoshi_to_real')) {

    function satoshi_to_real($satoshi, $price)
    {
        return round(($satoshi / pow(10,8)) * $price, 2);
    }
}

if (!function_exists('real_to_satoshi')) {

    function real_to_satoshi($real, $price)
    {
        return intval(($real / $price) * pow(10,8));
    }
}

if (!function_exists('satoshi_to_bitcoin')) {

    function satoshi_to_bitcoin($satoshi)
    {
        return round($satoshi / pow(10,8), 8);
    }
}

if (!function_exists('bitcoin_to_satoshi')) {

    function bitcoin_to_satoshi($bitcoin)
    {
        return intval($bitcoin * pow(10,8));
    }
}

if (!function_exists('formatSatoshi')) {

    function formatSatoshi($value)
    {
        return intval($value);
    }
}

if (!function_exists('formatReal')) {

    function formatReal($value)
    {
        return round($value, 2);
    }
}

if (!function_exists('formatBitcoin')) {

    function formatBitcoin($value)
    {
        return round($value, 8);
    }
}

if (!function_exists('fee')) {

    function fee($value, $percent)
    {
        return ($value * $percent) / 100;
    }
}

if(!function_exists('validateCNPJ')) {
    function validateCNPJ($cnpj)
    {
		$cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

		if (strlen($cnpj) != 14) {
			return false;
		}
        elseif ($cnpj == '00000000000000' ||
            $cnpj == '11111111111111' ||
            $cnpj == '22222222222222' ||
            $cnpj == '33333333333333' ||
            $cnpj == '44444444444444' ||
            $cnpj == '55555555555555' ||
            $cnpj == '66666666666666' ||
            $cnpj == '77777777777777' ||
            $cnpj == '88888888888888' ||
            $cnpj == '99999999999999') {
            return false;

		}
		else {

            for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
				$soma += $cnpj{$i} * $j;
				$j = ($j == 2) ? 9 : $j - 1;
			}

			$resto = $soma % 11;

			if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto)) {
				return false;
			}

			for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
				$soma += $cnpj{$i} * $j;
				$j = ($j == 2) ? 9 : $j - 1;
			}

			$resto = $soma % 11;

			return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
        }
    }
}

if (!function_exists('validateCPF')) {

    function validateCPF($cpf)
    {
        if (empty($cpf)) {
            return false;
        }

        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        if (strlen($cpf) != 11) {
            return false;
        } else if (
            $cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999'
        ) {

            return false;
        } else {

            for ($t = 9; $t < 11; $t++) {

                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{
                    $c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{
                $c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }
}

if (!function_exists('validateEmail')) {

    function validateEmail($email)
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        else {
            return false;
        }
    }
}

if (!function_exists('generatePasswd')) {

    function generatePasswd()
    {
        $symbols = '@#$&*;_';

        $letters = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ';

        $numbers = '0123456789';

        $passwd = "";

        while (strlen($passwd) < 10) {

            $passwd .= $letters[rand(0, strlen($letters) - 1)];

            if (rand(0, 1)) {
                $passwd .= $numbers[rand(0, strlen($numbers) - 1)];
            }
            if (rand(0, 1)) {
                $passwd .= $symbols[rand(0, strlen($symbols) - 1)];
            }

        }

        return $passwd;
    }
}

if (!function_exists('removeSymbols')) {

    function removeSymbols($string)
    {
        return preg_replace('/[^0-9]/', '', $string);
    }
}
