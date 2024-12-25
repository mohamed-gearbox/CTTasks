<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Simple Task Manager Scaffolding - Laravel + VUE3</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/fontAwesome5Pro.css') }}">
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased">
        <div id="vueapp" class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                @if(!$db_init)
                    <div class="flex justify-center mt-4 sm:items-center sm:justify-between">
                        <div class="text-center mt-2 text-gray-600 dark:text-gray-400 text-sm">
                            It seems that the database wasn't seeded. Make sure you review the readme file.
                        </div>
                    </div>
                @else
                    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                        <task-manager :projects="{{$projects}}"></task-manager>
                    </div>
                @endif
                <div class="flex justify-center mt-4 sm:items-center sm:justify-between">
                    <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </div>
                </div>
            </div>
            <script type="text/javascript" src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
            <script type="text/javascript" src="{{ mix('js/tasks.js') }}"></script>
        </div>
    </body>
</html>
