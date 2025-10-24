@extends('layouts.app')

@section('content')
@section('no_hero')@endsection
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-indigo-950 to-gray-900 text-white p-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold mb-1">📄 Mes Documents</h2>
            <p class="text-indigo-300 text-sm">Gérez, téléchargez et comparez vos fichiers facilement</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('documents.create') }}" 
               class="mt-4 sm:mt-0 inline-flex items-center gap-2 py-2.5 px-5 rounded-lg bg-indigo-600 hover:bg-indigo-500 shadow-md transition-all duration-300 hover:scale-105 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Uploader
            </a>
            <a href="{{ route('documents.errors') }}" 
               class="mt-4 sm:mt-0 inline-flex items-center gap-2 py-2.5 px-5 rounded-lg bg-pink-600 hover:bg-pink-500 shadow-md transition-all duration-300 hover:scale-105 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414 1.414M6.343 17.657l-1.414 1.414M5 12H3m18 0h-2M6.343 6.343l-1.414-1.414M17.657 17.657l-1.414-1.414" />
                </svg>
                Voir mes erreurs
            </a>
        </div>
    </div>

    <!-- Message de succès -->
    @if(session('status'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-green-600/20 border border-green-500/30 text-green-300 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- Grille des documents -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($documents as $d)
        <div class="relative group rounded-2xl backdrop-blur-md bg-white/5 border border-white/10 
                    hover:border-indigo-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/10 
                    overflow-hidden flex flex-col">

            <!-- En-tête de la carte -->
            <div class="p-5 pb-3 flex-grow">
                <!-- Nom du document -->
                <div class="mb-3">
                    <h4 class="text-lg font-semibold truncate group-hover:text-indigo-300 transition mb-1">
                        {{ $d->filename }}
                    </h4>
                    <p class="text-xs text-gray-400">
                        {{ $d->created_at->diffForHumans() }}
                    </p>
                </div>

                <!-- Statut du contenu -->
                @if(!$d->content)
                    <div class="mb-3 px-3 py-2 rounded-lg bg-yellow-500/10 border border-yellow-500/30">
                        <p class="text-xs text-yellow-300">⚠️ Aucun contenu extrait</p>
                    </div>
                @endif

                <!-- Badge de statut -->
                <div class="mb-3">
                    <span class="inline-block text-xs font-medium px-3 py-1.5 rounded-full 
                        {{ $d->approved ? 'bg-green-600/30 text-green-300 border border-green-500/30' 
                                       : 'bg-gray-700/50 text-gray-300 border border-gray-600/50' }}">
                        {{ $d->approved ? '✅ Approuvé' : '⏳ Non approuvé' }}
                    </span>
                </div>

                <!-- Erreurs détectées -->
                @if($d->errors && $d->errors->count() > 0)
                    <div class="mb-3 px-3 py-2 rounded-lg bg-red-500/10 border border-red-500/30">
                        <p class="text-xs text-red-300 font-medium">
                            🚨 {{ $d->errors->count() }} erreur(s) détectée(s)
                        </p>
                    </div>
                @endif
            </div>

            <!-- Actions (footer fixe) -->
            <div class="px-5 py-4 bg-white/5 border-t border-white/10 flex flex-wrap items-center gap-3">
                <a href="{{ route('documents.compare', $d->id) }}"
                   class="text-xs text-blue-400 hover:text-blue-200 transition font-medium">
                    🔍 Comparer
                </a>
                <form method="POST" action="{{ route('documents.analyze', $d->id) }}" class="inline-block">
                    @csrf
                    <button type="submit" class="text-xs text-yellow-400 hover:text-yellow-200 transition font-medium">
                        🔎 Détecter erreurs
                    </button>
                </form>
                @if(!$d->approved)
                    <a href="{{ route('reports.create', ['document_id' => $d->id]) }}"
                       class="text-xs text-red-400 hover:text-red-200 transition font-medium">
                        🚩 Réclamer
                    </a>
                @endif
                @if(!$d->locked)
                    <a href="{{ route('documents.edit', $d->id) }}"
                       class="text-xs text-green-400 hover:text-green-200 transition font-medium">
                        ✏️ Modifier
                    </a>
                    <a href="{{ route('documents.show', $d->id) }}"
                       class="text-xs text-blue-400 hover:text-blue-200 transition font-medium">
                        👁️ Visualiser
                    </a>
                    <form method="POST" action="{{ route('documents.destroy', $d->id) }}" class="inline-block" onsubmit="return confirm('Supprimer ce fichier ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-200 transition font-medium">🗑️ Supprimer</button>
                    </form>
                @else
                    <span class="text-xs text-gray-400">🔒 Verrouillé</span>
                @endif
            </div>

            <!-- Effet décoratif -->
            <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-10 transition bg-gradient-to-r from-indigo-500 to-blue-500 pointer-events-none"></div>
        </div>
        @empty
            <div class="col-span-full text-center text-indigo-300 py-20">
                <div class="text-5xl mb-4">🗂️</div>
                <p class="text-lg font-medium">Aucun document disponible pour le moment</p>
                <p class="text-sm text-gray-400 mt-2">Commencez par uploader votre premier fichier</p>
            </div>
        @endforelse
    </div>
</div>
@endsection