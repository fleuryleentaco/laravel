@extends('layouts.app')

@section('content')
  <h2 class="text-2xl font-semibold mb-4">Changer le mot de passe</h2>
  @if($errors->any())<div class="mb-3 text-sm text-red-300">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('profile.password.update') }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label class="block text-sm text-indigo-200">Mot de passe actuel</label>
      <input type="password" name="current_password" class="w-full p-2 rounded bg-white/5" required>
      @error('current_password')<div class="text-xs text-red-300">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="block text-sm text-indigo-200">Nouveau mot de passe</label>
      <input type="password" name="password" class="w-full p-2 rounded bg-white/5" required>
      @error('password')<div class="text-xs text-red-300">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="block text-sm text-indigo-200">Confirmer le nouveau mot de passe</label>
      <input type="password" name="password_confirmation" class="w-full p-2 rounded bg-white/5" required>
    </div>
    <div class="flex gap-3">
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Mettre à jour</button>
      <a href="{{ route('profile.show') }}" class="px-4 py-2 bg-white/5 text-white rounded">Annuler</a>
    </div>
  </form>
@endsection
