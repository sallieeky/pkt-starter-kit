<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - {{ config('app.name', 'Laravel') }}</title>
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
        <img src="{{ asset('images\illustration\illustration_2.png') }}" alt="404 Error" class="w-96">
        <h1 class="text-3xl font-bold text-gray-900 my-6">Oops! Error 404: Page Not Found</h1>
        <h2 class="text-xl font-semibold text-gray-900">This is awkward...</h2>
        <p class="text-gray-600">We can't find the page you're looking for. But don't worry, it's probably not your
            fault.</p>
        <a href="{{ route('home') }}"
            class="mt-6 px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 focus:outline-none focus:bg-gray-800">Return
            to safety<span aria-hidden="true"> &rarr;</span></a>
    </div>
</body>

</html>
