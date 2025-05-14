<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	<title>{{ $title ?? config('app.name', 'Laravel') }}</title>
</head>

<body>
	@include('components.flash-message')
	@include('layouts.nav')
	<main>
	{{ $slot }}
	</main>
	@include('layouts.footer')
</body>

</html>
