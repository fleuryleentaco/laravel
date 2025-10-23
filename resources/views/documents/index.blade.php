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
        <a href="{{ route('documents.create') }}" 
           class="mt-4 sm:mt-0 inline-flex items-center gap-2 py-2.5 px-5 rounded-lg bg-indigo-600 hover:bg-indigo-500 
                  shadow-md transition-all duration-300 hover:scale-105 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Uploader
        </a>
    </div>

    <!-- Message de succès -->
    @if(session('status'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-green-600/20 border border-green-500/30 text-green-300 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- Grille des documents -->
    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($documents as $d)
            <div class="relative group rounded-2xl p-5 backdrop-blur-md bg-white/5 border border-white/10 
                        hover:border-indigo-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/10">

                <!-- Nom du document -->
                <div class="mb-3">
                    <h4 class="text-lg font-semibold truncate group-hover:text-indigo-300 transition">
                        {{ $d->filename }}
                    </h4>
                    <p class="text-xs text-gray-400">
                        {{ $d->created_at->diffForHumans() }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4 mt-4">
                    <a href="{{ Storage::url($d->path) }}" target="_blank"
                       class="text-sm text-indigo-400 hover:text-indigo-200 transition">
                        Télécharger
                    </a>

                    <a href="{{ route('admin.compare', $d->id) }}"
                       class="text-sm text-indigo-400 hover:text-indigo-200 transition">
                        Comparer
                    </a>

                    <!-- Detect errors button (run analysis) -->
                    <form method="POST" action="{{ route('documents.analyze', $d->id) }}" class="inline-block">
                        @csrf
                        <button type="submit" class="text-sm text-yellow-300 hover:text-yellow-200 transition ml-3">
                            Détecter erreurs
                        </button>
                    </form>

                    <span class="ml-auto text-sm font-medium px-3 py-1 rounded-full 
                        {{ $d->approved ? 'bg-green-600/30 text-green-300 border border-green-500/30' 
                                       : 'bg-gray-700/50 text-gray-300 border border-gray-600/50' }}">
                        {{ $d->approved ? '✅ Approuvé' : '⏳ Non approuvé' }}
                    </span>
                </div>

                <!-- Effet décoratif -->
                <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-10 transition bg-gradient-to-r from-indigo-500 to-blue-500"></div>
            </div>
        @empty
            <div class="col-span-full text-center text-indigo-300 text-sm py-10">
                Aucun document disponible pour le moment 🗂️
            </div>
        @endforelse
    </div>
</div>
@endsection
