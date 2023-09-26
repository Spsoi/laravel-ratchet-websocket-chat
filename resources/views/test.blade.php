<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito: wght@400; 600; 700&display=swap" rel="stylesheet">
    <!-- Styles -->
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%
        }

        body {
            margin: 0
        }

        a {
            background: 0
        }
    </style>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

<body class="antialiased">
    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark: bg-gray-900 sm:items-center py-4 sm:pt-0">
        @if (Route::has ('login')) <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            @auth
            @else <a href="{{ url('/home') }}" class="text-sm text-gray-700 dark: text-gray-500 underline">Home</a>
            <a href="{{ route('login') }}" class="text-sm text-gray-700 dark: text-gray-500 underline">Log in</a>
            @if (Route::has ('register'))
            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark: text-gray-500 underline">Register</a>
            @endif
            @endauth
        </div> @endif<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-center pt-8 sm:justify-start sm:pt-0">
                <p id="message"></p>
            </div>
        </div>
    </div>
    <script>
        var socket = new WebSocket('ws://192.168.1.149:8080')

        socket.onopen = function() {
            console.log("Соединение установлено.");
        };

        socket.onclose = function(event) {
            if (event.wasClean) {
                console.log('Соединение закрыто чисто');
            } else {
                console.log('Обрыв соединения'); // например, "убит" процесс сервера
            }
            console.log('Код: ' + event.code + ' причина: ' + event.reason);
        };

        socket.onmessage = function(event) {
            console.log("Получены данные: " + event.data);
            data = {
                message: "new room",
                value: event.data,
            }
            console.log("Получены данные " + JSON.stringify(data));
            document.getElementById('message').append(JSON.stringify(data));
        };

        socket.onerror = function(error) {
            console.log("Ошибка " + error.message);
        };
    </script>
</body>

</html>