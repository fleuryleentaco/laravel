@extends('layouts.app')

@section('content')
  <div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
      <h2 class="text-2xl font-semibold">Gestion des utilisateurs</h2>
      <div class="text-sm text-indigo-200">Total: {{ $users->total() }}</div>
    </div>

    @if(session('status'))<div class="mb-3 text-sm text-green-300">{{ session('status') }}</div>@endif
    @if(session('error'))<div class="mb-3 text-sm text-red-300">{{ session('error') }}</div>@endif

    <div class="bg-transparent p-3 rounded-lg">
      <div class="grid grid-cols-12 gap-3 items-center text-sm text-indigo-200 font-medium px-4 py-2">
        <div class="col-span-4">Nom / Email</div>
  <div class="col-span-3">Rôle</div>
  <div class="col-span-3">Inscrit</div>
  <div class="col-span-3 text-right">Actions</div>
      </div>

      <div class="space-y-3 mt-2">
        @foreach($users as $u)
          <div class="col-span-12">
            <div class="p-3 rounded-md bg-slate-800/30">
              <div class="grid grid-cols-12 items-center gap-4">
                <div class="col-span-4">
                  <div class="font-medium text-white">{{ $u->name }}</div>
                  <div class="text-xs text-indigo-200">{{ $u->email }}</div>
                </div>
                <div class="col-span-3 text-indigo-200">
                  <div class="text-sm">{{ ($u->id_role_user==1) ? 'Admin' : 'Utilisateur' }}</div>
                </div>
                <div class="col-span-3 text-xs text-indigo-200">
                  {{ $u->created_at->diffForHumans() }}
                </div>
                <div class="col-span-3 text-right">
                  <div class="inline-flex items-center gap-2 flex-wrap">
                    <form method="POST" action="{{ route('admin.users.toggleRole', $u->id) }}">
                      @csrf
                      <button class="px-3 py-1 rounded text-white text-sm {{ ($u->id_role_user==1) ? 'bg-yellow-500' : 'bg-gray-600' }}">{{ ($u->id_role_user==1) ? 'Démote' : 'Promouvoir' }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.delete', $u->id) }}" onsubmit="return confirm('Supprimer l\'utilisateur ?');">
                      @csrf
                      @method('DELETE')
                      <button class="px-3 py-1 rounded bg-red-600 text-white text-sm">Supprimer</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4">{{ $users->links() }}</div>
    </div>
  </div>
@endsection
