<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .container {
                margin: 20px;
                padding 20px
            }

            .border {
                border: 1px #636b6f solid;
                border-radius: 2px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    Converter
                </div>

                <form action="{{ route('convert-number')}}" method="post">
                    @csrf
                    <label for="answer">Input number:</label>
                    <input type="text" name="answer" id="answer" />
                    <br />
                    <input type="submit" />
                </form>

                @isset($conversion)
                    <div class="container border">
                        <div class="container">
                            Value: <strong> PHP {{ number_format($conversion, 2) }} </strong> <br/>
                            To USD: <strong> ${{ number_format($currency, 2) }} </strong> <br/>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </body>
</html>
