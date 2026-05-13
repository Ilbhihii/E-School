@extends('layouts.admin')

@section('title', 'Register')

@section('content')

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-sky-100 via-white to-indigo-100 relative overflow-hidden">

    <!-- décor -->
    <div class="absolute w-72 h-72 bg-indigo-300/20 rounded-full top-10 left-10 blur-3xl"></div>
    <div class="absolute w-72 h-72 bg-sky-300/20 rounded-full bottom-10 right-10 blur-3xl"></div>

    <div class="w-full max-w-md bg-white/70 backdrop-blur-xl p-8 rounded-3xl shadow-2xl border border-white/40">

        <h2 class="text-3xl font-bold text-center text-indigo-600 mb-6">Créer un compte</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <input type="text" name="name" placeholder="Nom"
                class="w-full p-3 rounded-xl border focus:ring-2 focus:ring-indigo-400">

            <input type="email" name="email" placeholder="Email"
                class="w-full p-3 rounded-xl border focus:ring-2 focus:ring-indigo-400">

            <input type="password" name="password" placeholder="Mot de passe"
                class="w-full p-3 rounded-xl border focus:ring-2 focus:ring-indigo-400">

            <input type="password" name="password_confirmation" placeholder="Confirmation"
                class="w-full p-3 rounded-xl border focus:ring-2 focus:ring-indigo-400">

            <input type="text" name="country" placeholder="Pays"
                class="w-full p-3 rounded-xl border">

            <input type="text" name="city" placeholder="Ville"
                class="w-full p-3 rounded-xl border">

            <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold">
                S’inscrire
            </button>
        </form>

        <p class="text-center mt-4 text-sm">
            Déjà inscrit ?
            <a href="{{ route('login') }}" class="text-indigo-600 font-bold">Connexion</a>
        </p>

    </div>
</div>

@endsection
