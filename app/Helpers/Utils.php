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

// if (!function_exists('current_date')) {

//     function current_date($format)
//     {
//         date_default_timezone_set('America/Sao_Paulo');

//         return date($format);
//     }
// }

// if (!function_exists('dollar_to_bitcoin')) {

//     function dollar_to_bitcoin($value, $price)
//     {
//         return round(($value / $price), 8);
//     }
// }

// if (!function_exists('dollar_to_satoshi')) {

// 	function dollar_to_satoshi($value, $price)
// 	{
// 		return round(($value / $price) * pow(10, 8));
// 	}
// }

// if (!function_exists('satoshi_to_dollar')) {

// 	function satoshi_to_dollar($satoshi, $price)
// 	{
// 		return round(($satoshi * pow(10, -8) * $price), 2);
// 	}
// }

// if (!function_exists('value_to_percentage')) {

//     function value_to_percentage($value, $base)
//     {
//         $value = ($value * 100) / $base;

//         return round($value);
//     }
// }

// if (!function_exists('percentage_to_value')) {

//     function percentage_to_value($percentage, $base)
//     {
//         return round($percentage * ($base / 100), 2);
//     }
// }

// if (!function_exists('format_money')) {

//     function format_money($value)
//     {
//         return round($value, 2);
//     }
// }

if(!function_exists('validateCNPJ'))
{
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
