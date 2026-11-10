<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - 403</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md text-center max-w-md">
        <h1 class="text-4xl font-bold text-red-600 mb-4">403</h1>
        <p class="text-gray-700 mb-6">{{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses ke halaman ini.' }}</p>
        <a href="{{ url('/') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Kembali ke Beranda</a>
    </div>
</body>
</html>
