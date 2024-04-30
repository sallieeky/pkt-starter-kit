<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Server Error - {{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" />

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex items-center justify-center" style="height: 100vh" x-data>
    <div class="flex flex-col items-center justify-center text-center">
        <img src="{{ asset('images\illustration\illustration_7.png') }}" alt="500 Error" class="w-96">        
        <h1 class="text-3xl font-bold text-gray-900 my-6">
            Error 500: Server Error
        </h1>
        <h2 class="text-xl font-semibold text-gray-900">
            Something went wrong. We're working on it!
        </h2>
        <p class="text-gray-600">
            It seems something went wrong on our end. Don't worry, we're working on it and we'll get it fixed as soon as we can.
        </p>
        <a href="{{ route('home') }}"
            class="mt-6 px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 focus:outline-none focus:bg-gray-800">Return
            to safety<span aria-hidden="true"> &rarr;</span></a>

        <div class="mt-3">
            <small>
                If you think this is an error, please let us know. <a href="#"
                    class="font-bold text-slate-900 hover:text-slate-700">Contact us</a>.
            </small>
        </div>
    </div>
</body>

</html>
