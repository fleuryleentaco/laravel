@extends('layouts.app')

@section('content')
    <div class="text-center mb-6">
        <h2 class="text-3xl font-bold text-gray-100">Créer un Compte</h2>
        <p class="text-gray-400 mt-2">C'est simple et rapide !</p>
    </div>

    @if($errors->any())
        <div class="mb-3 text-sm text-red-300">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('register') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="sr-only">Nom</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name" autofocus class="form-input block w-full pl-3 pr-3 py-3 bg-white/5 border rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400 @error('name') border-red-500 @else border-white/10 @enderror" placeholder="Votre nom complet">
            @error('name')<span class="block mt-1.5 text-xs text-red-400"><strong>{{ $message }}</strong></span>@enderror
        </div>

        <div>
            <label for="email" class="sr-only">Adresse Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="form-input block w-full pl-3 pr-3 py-3 bg-white/5 border rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400 @error('email') border-red-500 @else border-white/10 @enderror" placeholder="votre.email@exemple.com">
            @error('email')<span class="block mt-1.5 text-xs text-red-400"><strong>{{ $message }}</strong></span>@enderror
        </div>

        <div>
            <label for="password" class="sr-only">Mot de passe</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" class="form-input block w-full pl-3 pr-3 py-3 bg-white/5 border rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400 @error('password') border-red-500 @else border-white/10 @enderror" placeholder="Mot de passe">
            @error('password')<span class="block mt-1.5 text-xs text-red-400"><strong>{{ $message }}</strong></span>@enderror
        </div>

        <div>
            <label for="password-confirm" class="sr-only">Confirmer le mot de passe</label>
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" class="form-input block w-full pl-3 pr-3 py-3 bg-white/5 border border-white/10 rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400" placeholder="Confirmer le mot de passe">
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-fuchsia-600 to-indigo-600 hover:from-fuchsia-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-indigo-500 transform hover:scale-105 transition duration-300">S'inscrire</button>
        </div>

        <p class="mt-4 text-center text-sm text-gray-400">Vous avez déjà un compte ? <a href="{{ route('login') }}" class="font-medium text-indigo-400 hover:text-indigo-300">Connectez-vous ici</a></p>
    </form>
@endsection
