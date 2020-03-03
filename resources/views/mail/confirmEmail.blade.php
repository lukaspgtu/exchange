<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Confirm E-mail</title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>

    <!-- Styles -->
    <style>
        html,
        body {
            margin: 10px auto 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background: #f1f1f1;
        }

        * {
            -ms-text-size-adjust: 100%;

            -webkit-text-size-adjust: 100%;
        }

        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        a {
            text-decoration: none;
        }

        *[x-apple-data-detectors],
        .unstyle-auto-detected-links *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }

        .im {
            color: inherit !important;
        }

        img.g-img+div {
            display: none !important;
        }

        @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {

            u~div .email-container {
                min-width: 320px !important;
            }

        }

        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {

            u~div .email-container {
                min-width: 375px !important;
            }

        }

        @media only screen and (min-device-width: 414px) {

            u~div .email-container {
                min-width: 414px !important;
            }

        }

        .primary {
            background: -webkit-linear-gradient(rgb(61, 102, 172), rgb(94, 204, 214));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 500 !important;
        }

        .light {
            color: #fff !important;
            font-weight: 500 !important;
        }

        .bg_white {
            background: #ffffff !important;
        }

        .bg_light {
            background: #F8F8F8;
        }

        .bg_primary {
            background-color: #2C303B;
            background: linear-gradient(to right, rgb(61, 102, 172), rgb(94, 204, 214));
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Poppins';
            color: #000000 !important;
            margin-top: 0;
            font-weight: 400;
        }

        p {
            color: #2C303B !important;
        }

        body {
            font-family: 'Poppins';
            font-weight: 400;
            font-size: 15px;
            line-height: 1.8;
            color: rgba(0, 0, 0, .4);
        }

        a {
            color: #2C303B;
        }

        a.btn {
            border-radius: 50px;
            cursor: pointer;
            border: 0;
            background: #2C303B;
            background: linear-gradient(to right, rgb(61, 102, 172), rgb(94, 204, 214));
            color: #FFF !important;
            font-weight: 500;
            font-size: 16px;
            padding: 10px 20px;
        }
    </style>

</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f1f1f1;">

    <br>

    <center style="width: 100%; background-color: #f1f1f1;">

        <div style="max-width: 600px; margin: 0 auto; border: 1px solid #D9D9D9;" class="email-container">

            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">

                <tr>

                    <td valign="top" class="bg_white" style="padding: 1em 2.5em 0 2.5em;">

                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">

                            <tr>

                                <td class="logo" style="text-align: center;">

                                    <img src="http://api.proexbit.com/storage/logo2.png" width="250" style="margin-bottom: 30px">
                                </td>

                            </tr>

                        </table>

                    </td>

                </tr>

                <tr>

                    <td valign="middle" class="hero bg_primary" style="padding: 1em 0 0 0; text-align: center">

                        <h1 class="light">Ative sua conta</h1>

                    </td>

                </tr>

                <tr>

                    <td valign="middle" class="hero bg_white" style="padding: 3em 0 0 0;">

                        <div class="text" style="text-align: center;">

                            <h1 class="primary">Olá {{ explode(' ', $user->name)[0] }}!</h1>

                            <p style="font-size: 16px;">Bem-vindo à ProExBit!</p>

                            <p style="font-size: 16px;">Para começar a utilizar sua conta, clique no botão abaixo<br>para confirmar o seu e-mail e ativar sua conta.</p>
                        </div>

                    </td>

                </tr>

                <tr>

                    <td valign="middle" class="hero bg_white" style="padding: 1em 0 0 0;">

                        <div class="text" style="text-align: center;">

                            <a href="{{ route('activateAccount', ['id' => $user->id]) }}" class="btn">Ativar minha conta</a>

                        </div>

                    </td>

                </tr>

                <tr>

                    <td valign="middle" class="hero bg_white" style="padding: 1em 0 0 0;">

                        <div class="text" style="text-align: center;">

                            <p style="font-size: 16px;">

                                Caso você não tenha se registrado para esta conta,<br>ignore esta mensagem e a conta será removida.
                            </p>

                        </div>

                    </td>

                </tr>

                <tr>

                    <td valign="middle" class="hero bg_white" style="padding: 1em 0 0 0;">

                        <div class="text" style="text-align: center;">

                            <p style="font-size: 16px;">

                                Em caso de dúvidas, envie um e-mail para <br>

                                <a href="mail:suporte@proexbit.com"><b>suporte@proexbit.com</b></a>
                            </p>

                        </div>

                    </td>

                </tr>

                <tr>

                    <td valign="middle" class="hero bg_white" style="padding: 1em 0 0 0;">

                        <div class="text" style="text-align: center;">

                            <p style="font-size: 16px;">

                                Obrigado.<br>Equipe ProExBit.

                            </p>

                        </div>

                    </td>

                </tr>

            </table>

        </div>

    </center>

</body>

</html>
