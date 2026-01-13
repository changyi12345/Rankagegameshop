@extends('layouts.user')

@section('title', 'Welcome - RanKage Game Shop')

@section('content')
<div class="container mx-auto px-4 py-8 text-center">
    <!-- Hero Section -->
    <div class="max-w-3xl mx-auto mb-12">
        <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center mx-auto mb-6 shadow-2xl">
            <span class="text-6xl">🎮</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-light-text mb-4">
            Welcome to <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">RanKage</span>
        </h1>
        <p class="text-xl text-gray-400 mb-8">Your trusted game top-up platform in Myanmar</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/login" class="btn-primary px-8 py-4 text-lg">Get Started</a>
            <a href="/" class="btn-outline px-8 py-4 text-lg">Browse Games</a>
        </div>
    </div>

    <!-- Features -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto mb-12">
        <div class="card text-center">
            <div class="w-16 h-16 rounded-2xl bg-primary/20 flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">⚡</span>
            </div>
            <h3 class="text-xl font-bold text-light-text mb-2">Fast Top-up</h3>
            <p class="text-gray-400">Instant delivery via G2Bulk API</p>
        </div>
        <div class="card text-center">
            <div class="w-16 h-16 rounded-2xl bg-secondary/20 flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">💰</span>
            </div>
            <h3 class="text-xl font-bold text-light-text mb-2">Myanmar Payments</h3>
            <p class="text-gray-400">WavePay, KBZ Pay & Manual Transfer</p>
        </div>
        <div class="card text-center">
            <div class="w-16 h-16 rounded-2xl bg-primary/20 flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">🔒</span>
            </div>
            <h3 class="text-xl font-bold text-light-text mb-2">Secure & Safe</h3>
            <p class="text-gray-400">Your data is protected</p>
        </div>
    </div>

    <!-- Popular Games -->
    <div class="max-w-5xl mx-auto">
        <h2 class="text-2xl font-bold text-light-text mb-6">Popular Games</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="/games/mobile-legends" class="card hover:scale-105 transition-transform text-center">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center mx-auto mb-3">
                    <span class="text-4xl">⚔️</span>
                </div>
                <h3 class="font-bold text-light-text">Mobile Legends</h3>
            </a>
            <a href="/games/pubg" class="card hover:scale-105 transition-transform text-center">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-yellow-500 to-orange-600 flex items-center justify-center mx-auto mb-3">
                    <span class="text-4xl">🎯</span>
                </div>
                <h3 class="font-bold text-light-text">PUBG Mobile</h3>
            </a>
            <a href="/games/free-fire" class="card hover:scale-105 transition-transform text-center">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center mx-auto mb-3">
                    <span class="text-4xl">🔥</span>
                </div>
                <h3 class="font-bold text-light-text">Free Fire</h3>
            </a>
            <a href="/games" class="card hover:scale-105 transition-transform text-center border-2 border-dashed border-gray-600">
                <div class="w-16 h-16 rounded-xl bg-dark-base flex items-center justify-center mx-auto mb-3">
                    <span class="text-4xl">➕</span>
                </div>
                <h3 class="font-bold text-light-text">View All</h3>
            </a>
        </div>
    </div>
</div>
@endsection
