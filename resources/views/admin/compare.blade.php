@extends('layouts.app')

@section('content')
@section('no_hero')@endsection
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-indigo-950 to-gray-900 text-white p-6">
    <!-- En-tête -->
    <div class="mb-8">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-indigo-400 hover:text-indigo-300 transition mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour
        </a>
        
        <h2 class="text-3xl font-bold mb-2">🔍 Comparaison de similarité</h2>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-indigo-600/20 border border-indigo-500/30 rounded-lg">
                <p class="text-sm text-indigo-300">Document analysé :</p>
                <p class="font-semibold">{{ $doc->filename }}</p>
            </div>
        </div>
    </div>

    @if(isset($shortCommonError) && $shortCommonError)
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-600/20 border border-red-500/30 text-red-200 text-center text-lg font-semibold shadow">
            {{ $shortCommonError }}
        </div>
    @endif

    @if(count($results) > 0)
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="px-5 py-4 bg-white/10 border border-indigo-500/20 rounded-xl shadow">
                <div class="text-2xl font-bold text-indigo-400">{{ count($results) }}</div>
                <div class="text-sm text-gray-400">Document(s) similaire(s)</div>
            </div>
            <div class="px-5 py-4 bg-white/10 border border-yellow-500/20 rounded-xl shadow">
                <div class="text-2xl font-bold text-yellow-400">{{ round($results[0]['sim'] * 100, 2) }}%</div>
                <div class="text-sm text-gray-400">Similarité maximale</div>
            </div>
            <div class="px-5 py-4 bg-white/10 border border-blue-500/20 rounded-xl shadow">
                <div class="text-2xl font-bold text-blue-400">{{ round(collect($results)->avg('sim') * 100, 2) }}%</div>
                <div class="text-sm text-gray-400">Similarité moyenne</div>
            </div>
        </div>
        <!-- Tableau des résultats -->
        <div class="overflow-hidden rounded-xl bg-white/10 border border-white/20 shadow">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-100 bg-white/10">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Document</th>
                            <th class="px-6 py-4 font-semibold">Similarité</th>
                            <th class="px-6 py-4 font-semibold">Extrait commun</th>
                            <th class="px-6 py-4 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-200">
                        @foreach($results as $r)
                            <tr class="border-t border-white/10 hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium">{{ $r['other']->filename }}</div>
                                    <div class="text-xs text-gray-400 mt-1">ID: {{ $r['other']->id }}</div>
                                    <div class="text-xs text-gray-400">
                                        Propriétaire: {{ $r['other']->user->name ?? $r['other']->user->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $percentage = round($r['sim'] * 100, 2);
                                        $colorClass = $percentage >= 80 ? 'text-red-400' : 
                                                     ($percentage >= 50 ? 'text-yellow-400' : 'text-blue-400');
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl font-bold {{ $colorClass }}">
                                            {{ $percentage }}%
                                        </span>
                                        <div class="flex-1 h-2 bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full {{ $percentage >= 80 ? 'bg-red-500' : 
                                                                   ($percentage >= 50 ? 'bg-yellow-500' : 'bg-blue-500') }}" 
                                                 style="width: {{ $percentage }}%">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-md">
                                        <code class="text-xs text-gray-300 bg-gray-800/50 px-3 py-2 rounded block overflow-hidden">
                                            {{ $r['snippet'] }}
                                        </code>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2">
                                        <a href="{{ route('admin.reAnalyze', $r['other']->id) }}" 
                                           class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg 
                                                  bg-yellow-600/20 hover:bg-yellow-600/30 text-yellow-300 
                                                  border border-yellow-500/30 transition text-xs font-medium">
                                            🔎 Ré-analyser
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        @if(isset($shortCommonError) && $shortCommonError)
            <div class="text-center py-20 px-6 bg-white/5 border border-white/10 rounded-xl backdrop-blur-sm">
                <div class="text-6xl mb-4">⚠️</div>
                <h3 class="text-2xl font-bold mb-2 text-red-300">{{ $shortCommonError }}</h3>
                <p class="text-gray-400">Le contenu commun ne dépasse pas 20 mots.</p>
            </div>
        @else
            <div class="text-center py-20 px-6 bg-white/5 border border-white/10 rounded-xl backdrop-blur-sm">
                <div class="text-6xl mb-4">✅</div>
                <h3 class="text-2xl font-bold mb-2">Aucune similarité détectée</h3>
                <p class="text-gray-400">Ce document ne présente pas de similarité significative avec d'autres documents du système.</p>
            </div>
        @endif
    @endif
</div>
@endsection