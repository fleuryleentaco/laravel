@extends('layouts.app')

@section('content')
  <div class="text-center mb-6">
    <h2 class="text-3xl font-bold text-gray-100">Connexion</h2>
  </div>

  @if($errors->any())
    <div class="mb-3 text-sm text-red-300">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf
    <div>
      <label for="email" class="sr-only">Adresse email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-input block w-full pl-3 pr-3 py-3 bg-white/5 border rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400 @error('email') border-red-500 @else border-white/10 @enderror" placeholder="Adresse email">
      @error('email')<span class="block mt-1.5 text-xs text-red-400">{{ $message }}</span>@enderror
    </div>

    <div>
      <label for="password" class="sr-only">Mot de passe</label>
      <input id="password" type="password" name="password" required class="form-input block w-full pl-3 pr-3 py-3 bg-white/5 border rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400 @error('password') border-red-500 @else border-white/10 @enderror" placeholder="Mot de passe">
      @error('password')<span class="block mt-1.5 text-xs text-red-400">{{ $message }}</span>@enderror
    </div>

    <div class="flex items-center gap-3">
      <label class="inline-flex items-center">
        <input type="checkbox" name="remember" class="form-checkbox h-4 w-4 text-indigo-500" {{ old('remember') ? 'checked' : '' }}>
        <span class="ml-2 text-sm text-indigo-200">Se souvenir de moi</span>
      </label>
      <div class="ml-auto">
        @if (Route::has('password.request'))
          <a class="text-sm text-indigo-300 hover:text-indigo-200" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
        @endif
      </div>
    </div>

    <div class="pt-2">
      <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-fuchsia-600 to-indigo-600 hover:from-fuchsia-700 hover:to-indigo-700">Se connecter</button>
    </div>

    <p class="mt-4 text-center text-sm text-gray-400">Pas encore de compte ? <a href="{{ route('register') }}" class="font-medium text-indigo-400 hover:text-indigo-300">S'inscrire</a></p>
  </form>
@endsection

