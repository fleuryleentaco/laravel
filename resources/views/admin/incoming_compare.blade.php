@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-white mb-2">Comparaison du document externe</h2>
        <div class="glass-effect p-4 rounded-lg">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-indigo-300">Fichier:</span>
                    <span class="text-white font-medium ml-2">{{ $doc->filename }}</span>
                </div>
                <div>
                    <span class="text-indigo-300">Uploader ID:</span>
                    <span class="text-white font-medium ml-2">{{ $doc->uploader_id }}</span>
                </div>
                <div>
                    <span class="text-indigo-300">Date:</span>
                    <span class="text-white font-medium ml-2">{{ $doc->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <span class="text-indigo-300">Taille du contenu:</span>
                    <span class="text-white font-medium ml-2">{{ strlen($doc->content) }} caractères</span>
                </div>
            </div>
        </div>
    </div>

    @if(count($results) > 0)
        <div class="mb-4">
            <h3 class="text-xl font-semibold text-white mb-3">
                Documents similaires trouvés ({{ count($results) }})
            </h3>
            <div class="space-y-3">
                @foreach($results as $r)
                    <div class="glass-effect p-4 rounded-lg border-l-4 
                        @if($r['sim'] >= 0.7) border-red-500
                        @elseif($r['sim'] >= 0.5) border-yellow-500
                        @else border-blue-500
                        @endif">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-white">{{ $r['other']->filename }}</h4>
                                <p class="text-xs text-indigo-300">
                                    Uploadé par {{ $r['other']->user->name ?? 'Utilisateur inconnu' }} 
                                    le {{ $r['other']->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold 
                                    @if($r['sim'] >= 0.7) text-red-400
                                    @elseif($r['sim'] >= 0.5) text-yellow-400
                                    @else text-blue-400
                                    @endif">
                                    {{ round($r['sim'] * 100, 1) }}%
                                </div>
                                <div class="text-xs text-indigo-300">similarité</div>
                            </div>
                        </div>
                        
                        <div class="mt-3 p-3 bg-gray-900/50 rounded text-sm text-indigo-200">
                            <div class="font-medium text-indigo-300 mb-1">Extrait commun:</div>
                            <div class="italic">{{ $r['snippet'] }}</div>
                        </div>
                        
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('admin.compare', $r['other']->id) }}" 
                               class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                                Voir détails
                            </a>
                            <a href="{{ route('admin.documents.download', $r['other']->id) }}" 
                               class="px-3 py-1 bg-gray-700 text-white rounded text-sm hover:bg-gray-600">
                                Télécharger
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="glass-effect p-6 rounded-lg text-center">
            <div class="text-green-400 text-lg mb-2">✓ Aucune similarité détectée</div>
            <p class="text-indigo-300 text-sm">Ce document externe ne présente pas de similarité significative avec les documents internes.</p>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('admin.incoming') }}" class="inline-block px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600">
            ← Retour aux documents externes
        </a>
    </div>
</div>
@endsection
