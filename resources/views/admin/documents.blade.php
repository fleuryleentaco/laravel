@extends('layouts.app')

@section('content')
@section('no_hero')@endsection
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-indigo-950 to-gray-900 text-white p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-3xl font-bold mb-1">📂 Tous les fichiers</h2>
            <p class="text-sm text-indigo-300">Liste complète des fichiers uploadés par les utilisateurs</p>
        </div>
    </div>

    @if(session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-600/20 border border-green-500/30 text-green-300 text-sm">
            ✅ {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-600/20 border border-red-500/30 text-red-300 text-sm">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full table-auto text-sm">
                <thead class="text-left text-gray-100 bg-white/10">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Fichier</th>
                        <th class="px-6 py-4 font-semibold">Propriétaire</th>
                        <th class="px-6 py-4 font-semibold">Taille</th>
                        <th class="px-6 py-4 font-semibold">Upload</th>
                        <th class="px-6 py-4 font-semibold">Statut</th>
                        <th class="px-6 py-4 font-semibold">Erreurs</th>
                        <th class="px-6 py-4 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-200">
                    @forelse($documents as $d)
                        <tr class="border-t border-white/5 hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $d->filename }}</div>
                                <div class="text-xs text-gray-400 mt-1">ID: {{ $d->id }}</div>
                                @if(!$d->content)
                                    <div class="text-xs text-yellow-400 mt-1">⚠️ Pas de contenu extrait</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $d->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $d->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                {{ number_format($d->size / 1024, 2) }} KB
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                <div>{{ $d->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $d->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block text-xs font-medium px-3 py-1 rounded-full 
                                    {{ $d->approved ? 'bg-green-600/30 text-green-300 border border-green-500/30' 
                                                   : 'bg-gray-700/50 text-gray-300 border border-gray-600/50' }}">
                                    {{ $d->approved ? '✅ Approuvé' : '⏳ En attente' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($d->errors && $d->errors->count() > 0)
                                    <span class="inline-block text-xs px-3 py-1 bg-red-500/20 text-red-300 rounded-full border border-red-500/30">
                                        🚨 {{ $d->errors->count() }} erreur(s)
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Aucune</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('admin.documents.download', $d->id) }}" 
                                       class="text-xs text-indigo-400 hover:text-indigo-200 transition font-medium whitespace-nowrap">
                                        📥 Télécharger
                                    </a>
                                    
                                    @if($d->content)
                                        <a href="{{ route('admin.compare', $d->id) }}" 
                                           class="text-xs text-blue-400 hover:text-blue-200 transition font-medium whitespace-nowrap">
                                            🔍 Comparer
                                        </a>
                                        
                                        <a href="{{ route('admin.reAnalyze', $d->id) }}" 
                                           class="text-xs text-yellow-400 hover:text-yellow-200 transition font-medium whitespace-nowrap">
                                            🔎 Ré-analyser
                                        </a>
                                    @endif
                                    
                                    @if(!$d->approved)
                                        <a href="{{ route('admin.approve', $d->id) }}" 
                                           class="text-xs text-green-400 hover:text-green-200 transition font-medium whitespace-nowrap">
                                            ✅ Approuver
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <div class="text-4xl mb-3">📭</div>
                                <p>Aucun document dans le système</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $documents->links() }}
    </div>
</div>
@endsection