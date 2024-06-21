<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">

    <style>
        * {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: #373B40;
            line-height: 1.75;
        }

        body {
            background-color: #ECF7FF;
            box-sizing: border-box;
        }

        .content-container {
            border: 1px solid #A1D1F7;
            border-radius: 12px;
            background-color: #fff;
        }

        .content-devider {
            border: 1px solid #E1E6ED;
        }

        .content-footer {
            background: url('https://itleaps.com/images/pkt-pattern.png') 10px repeat;
            background-size: 200px;
        }

        /* Medium devices (landscape tablets, 991px and up) */
        @media only screen and (max-width: 991px) {
            * {
                font-size: .75rem;
            }
            .content-container {
                margin: 0px 10px;
            }
            .content-wrapper {
                padding: 12px 16px;
            }
            .content-devider {
                margin: 0px 16px;
            }
            .content-footer {
                padding: 12px 16px;
                font-size: .55rem !important;
            }

            .footer-container {
                padding: 12px 16px;
                font-size: .55rem !important;
            }
        }

        /* Large devices (laptops/desktops, 992px and up) */
        @media only screen and (min-width: 992px) {
            * {
                font-size: 1rem;
            }
            .content-container {
                width: 75%;
                margin: 0px auto;
            }
            .content-wrapper {
                margin: 0px auto;
                padding: 24px 32px;
            }
            .content-devider {
                margin: 0px 32px;
            }
            .content-footer {
                padding: 16px 32px;
                font-size: .85rem !important;
            }

            .footer-container {
                width: 75%;
                margin: 0px auto;
                padding: 24px 32px;
                font-size: .85rem !important;
            }
        }

    </style>
</head>
<body>
    <div style="width: 100%; background-color: #ECF7FF">
        @if (isset($header))
            {{ $header }}
        @else
            <x-mails.layouts.header />
        @endif

        <div class="content-container">
            <div class="content-wrapper">
                {{ $slot }}
            </div>

            <hr class="content-devider">

            <div class="content-footer">
                <x-mails.layouts.content-footer />
            </div>
        </div>

        @if (isset($footer))
            {{ $footer }}
        @else
            <div class="footer-container">
                <x-mails.layouts.footer />
            </div>
        @endif
    </div>
</body>
</html>
