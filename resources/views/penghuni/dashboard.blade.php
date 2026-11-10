@extends('layouts.app')
@section('title', 'Dashboard Penghuni')
@section('header', 'Dashboard Penghuni')
@section('content')
        <div class="bg-white p-6 rounded shadow">
        <p>Selamat datang di dashboard penghuni.</p>
        <div class="mt-4">
            <a href="{{ route('penghuni.dashboard') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Kembali ke Dashboard</a>
        </div>
    </div>
@endsection
