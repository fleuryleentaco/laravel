@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    <!-- Header compact -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
        <div>
            <h2 class="text-3xl font-bold text-white">👥 Gestion des utilisateurs</h2>
            <p class="text-indigo-300 text-sm mt-1">Administrez les rôles, statuts et accès à la plateforme</p>
        </div>
        <div class="text-sm text-indigo-200 bg-white/5 px-4 py-2 rounded-lg border border-indigo-500/30">
            Total utilisateurs : 
            <span class="font-semibold text-indigo-100">{{ $users->total() }}</span>
        </div>
    </div>

    <!-- Alertes -->
    @if(session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-600/20 border border-green-500/30 text-green-300 text-sm shadow">
            {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-600/20 border border-red-500/30 text-red-300 text-sm shadow">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tableau principal -->
    <div class="overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-indigo-900/30 to-indigo-950/30 backdrop-blur-md shadow-lg">
        <div class="grid grid-cols-12 gap-3 px-6 py-3 text-sm font-semibold text-indigo-200 border-b border-white/10 bg-white/5">
            <div class="col-span-4">Nom / Email</div>
            <div class="col-span-3">Rôle</div>
            <div class="col-span-3">Inscription</div>
            <div class="col-span-2 text-right">Actions</div>
        </div>

        @forelse($users as $u)
            <div class="grid grid-cols-12 items-center gap-3 px-6 py-4 border-b border-white/5 hover:bg-indigo-800/20 transition duration-150 ease-in-out">
                <!-- Nom / Email -->
                <div class="col-span-4">
                    <div class="font-medium text-white">{{ $u->name }}</div>
                    <div class="text-xs text-indigo-300">{{ $u->email }}</div>
                </div>

                <!-- Rôle -->
                <div class="col-span-3">
                    @if($u->id_role_user == 1)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                            👑 Admin
                        </span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-600/20 text-gray-300 border border-gray-500/30">
                            👤 Utilisateur
                        </span>
                    @endif
                </div>

                <!-- Date -->
                <div class="col-span-3 text-sm text-indigo-200">
                    {{ $u->created_at->diffForHumans() }}
                </div>

                <!-- Actions -->
                <div class="col-span-2 text-right">
                    <div class="inline-flex gap-2 flex-wrap justify-end">
                        <!-- Bouton rôle -->
                        <form method="POST" action="{{ route('admin.users.toggleRole', $u->id) }}">
                            @csrf
                            <button 
                                class="px-3 py-1.5 text-sm rounded-md font-medium transition-all duration-150
                                       {{ $u->id_role_user == 1 
                                           ? 'bg-yellow-500/10 text-yellow-300 border border-yellow-500/30 hover:bg-yellow-500/20' 
                                           : 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30 hover:bg-indigo-600/30' }}">
                                {{ $u->id_role_user == 1 ? 'Rétrograder' : 'Promouvoir' }}
                            </button>
                        </form>

                        <!-- Bouton suppression -->
                        <form method="POST" action="{{ route('admin.users.delete', $u->id) }}" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                            @csrf
                            @method('DELETE')
                            <button 
                                class="px-3 py-1.5 text-sm rounded-md font-medium bg-red-600/20 text-red-300 border border-red-500/30 hover:bg-red-600/30 transition-all duration-150">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-indigo-300 text-sm">
                Aucun utilisateur trouvé 👀
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center">
        {{ $users->links() }}
    </div>
</div>
@endsection
