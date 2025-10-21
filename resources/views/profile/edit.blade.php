@extends('layouts.app')

@section('content')
  <h2 class="text-2xl font-semibold mb-4">Modifier mon profil</h2>
  @if($errors->any())<div class="mb-3 text-sm text-red-300">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label class="block text-sm text-indigo-200">Nom</label>
      <input type="text" name="name" value="{{ old('name',$user->name) }}" class="w-full p-2 rounded bg-white/5" required>
    </div>
    <div class="mb-3">
      <label class="block text-sm text-indigo-200">Email</label>
      <input type="email" name="email" value="{{ old('email',$user->email) }}" class="w-full p-2 rounded bg-white/5" required>
    </div>
    <div class="flex gap-3">
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Enregistrer</button>
      <a href="{{ route('profile.show') }}" class="px-4 py-2 bg-white/5 text-white rounded">Annuler</a>
    </div>
  </form>
@endsection
