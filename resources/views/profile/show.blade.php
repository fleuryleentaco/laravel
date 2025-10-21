@extends('layouts.app')

@section('content')
  <h2 class="text-2xl font-semibold mb-4">Mon profil</h2>
  @if(session('status'))<div class="mb-3 text-sm text-green-300">{{ session('status') }}</div>@endif
  <dl class="mb-4">
    <dt class="text-sm text-indigo-200">Nom</dt>
    <dd class="mb-2">{{ $user->name }}</dd>
    <dt class="text-sm text-indigo-200">Email</dt>
    <dd class="mb-2">{{ $user->email }}</dd>
  </dl>
  <div class="flex gap-3">
    <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Modifier</a>
    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Supprimer le compte ? Cette action est irréversible.');">
      @csrf
      @method('DELETE')
      <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Supprimer le compte</button>
    </form>
  </div>
@endsection
