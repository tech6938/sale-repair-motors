<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Notification | {{ config('app.name') }}</title>

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,600" rel="stylesheet" type="text/css">
    <!-- Web Font / @font-face : BEGIN -->
    <!--[if mso]>
        <style>
            * {
                font-family: 'Roboto', sans-serif !important;
            }
        </style>
    <![endif]-->

    <!--[if !mso]>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,600" rel="stylesheet" type="text/css">
    <![endif]-->

    <!-- Web Font / @font-face : END -->

    <!-- CSS Reset : BEGIN -->

    <style>
        /* What it does: Remove spaces around the email design added by some email clients. */
        /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            font-family: 'Roboto', sans-serif !important;
            font-size: 14px;
            margin-bottom: 10px;
            line-height: 24px;
            color: #8094ae;
            font-weight: 400;
        }

        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
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

        table table table {
            table-layout: auto;
        }

        a {
            text-decoration: none;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }
    </style>

</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f5f6fa;">
    <center style="width: 100%; background-color: #f5f6fa;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f5f6fa">
            <tr>
                <td style="padding: 40px 0;">
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding-bottom:25px;">
                                    <a href="javascript:void(0);"><img style="height: 40px" src="{{ asset('assets/images/logo-dark2x.png') }}" alt="logo"></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            @if (array_key_exists('heading', $meta))
                                <tr>
                                    <td style="padding: 30px 30px 0px 30px;">
                                        <h2 style="font-size: 18px; color: #041e42; font-weight: 600; margin: 0;">{{ $meta['heading'] }}</h2>
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td style="padding: 30px;">
                                    <p style="margin-bottom: 10px;">
                                        <strong>Hi {{ $name }},</strong>
                                    </p>

                                    <p style="margin-bottom: 25px;">
                                        {!! $content !!}
                                    </p>

                                    @if (array_key_exists('quote', $meta))
                                        <blockquote style="background-color:#f5f6fa; padding: 20px; margin-bottom: 25px;">
                                            <i>{{ $meta['quote'] }}</i>
                                        </blockquote>
                                    @endif

                                    @if (array_key_exists('otp', $meta))
                                        <div style="text-align: center !important;">
                                            <h2
                                                style="background-color:#f5f6fa; padding: 20px; margin-bottom: 25px; font-size: 18px; color: #041e42; font-weight: 600; letter-spacing: 3px;">
                                                {{ $meta['otp'] }}
                                            </h2>
                                        </div>
                                    @endif

                                    @if (array_key_exists('note', $meta))
                                        <p style="margin-bottom: 25px;">{{ $meta['note'] }}</p>
                                    @endif

                                    @if (array_key_exists('url', $meta))
                                        <a href="{{ $meta['url'] }}" target="_blank"
                                            style="padding: 0 30px;background-color:#041e42;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase;">
                                            {{ array_key_exists('button_text', $meta) ? $meta['button_text'] : 'Take Action' }}
                                        </a>
                                    @endif
                                </td>
                            </tr>

                            @if (array_key_exists('url', $meta))
                                <tr>
                                    <td style="padding: 0 30px 30px 30px;">
                                        <h4 style="font-size: 15px; color: #000000; font-weight: 600; margin: 0; text-transform: uppercase; margin-bottom: 10px">or</h4>
                                        <p style="margin-bottom: 10px;">If the button above does not work, paste this link into your web browser:</p>
                                        <a href="{{ $meta['url'] }}" target="_blank"
                                            style="color: #041e42; text-decoration:none;word-break: break-all;">{{ $meta['url'] }}</a>
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td style="padding: 0px 30px 30px 30px;">
                                    @if (array_key_exists('url', $meta))
                                        <p>If you did not make this request, please contact us or ignore this message.</p>
                                    @endif

                                    <p style="margin: 0; font-size: 13px; line-height: 22px; color:#9ea8bb;">
                                        This is an automatically generated email please do not reply to this email. If you face any issues, please contact us at
                                        {{ config('mail.from.address') }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding:25px 20px 0;">
                                    <p style="text-align: center; font-size: 13px;">Copyright © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                                    <p style="text-align: center; padding-top: 15px; font-size: 12px;">
                                        This email was sent to you as a registered user of
                                        <a style="color: #041e42; text-decoration:none;" href="{{ url('/') }}" target="_blank">{{ config('app.name') }}</a>.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>
