@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-white mb-2">Visualisation du fichier Excel</h2>
        <div class="glass-effect p-4 rounded-lg">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-indigo-300">Fichier:</span>
                    <span class="text-white font-medium ml-2">{{ $document->filename }}</span>
                </div>
                <div>
                    <span class="text-indigo-300">Taille:</span>
                    <span class="text-white font-medium ml-2">{{ number_format($document->size / 1024, 2) }} KB</span>
                </div>
                <div>
                    <span class="text-indigo-300">Date d'upload:</span>
                    <span class="text-white font-medium ml-2">{{ $document->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <span class="text-indigo-300">Type:</span>
                    <span class="text-white font-medium ml-2">{{ $document->mime }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($document->content)
        <div class="glass-effect p-6 rounded-lg">
            <h3 class="text-xl font-semibold text-white mb-4">Contenu extrait</h3>
            <div class="bg-gray-900/50 rounded-lg p-4 overflow-x-auto">
                <div class="text-sm text-indigo-100 whitespace-pre-wrap font-mono">{{ $document->content }}</div>
            </div>
            
            <div class="mt-4 text-xs text-indigo-300">
                <p>💡 Le contenu ci-dessus a été extrait du fichier Excel pour l'analyse de plagiat.</p>
                <p>Les données sont présentées sous forme de texte brut avec séparation par feuilles.</p>
            </div>
        </div>
        
        <div class="mt-6 flex gap-3">
            <a href="{{ route('documents.download', $document->id) }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                📥 Télécharger le fichier original
            </a>
            <a href="{{ route('documents.compare', $document->id) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                🔍 Comparer avec d'autres documents
            </a>
            <a href="{{ route('documents.index') }}" 
               class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600">
                ← Retour à mes documents
            </a>
        </div>
    @else
        <div class="glass-effect p-6 rounded-lg text-center">
            <div class="text-yellow-400 text-lg mb-2">⚠️ Contenu non disponible</div>
            <p class="text-indigo-300 text-sm mb-4">
                Le contenu de ce fichier Excel n'a pas pu être extrait. 
                Cela peut être dû à un format non supporté ou à un fichier corrompu.
            </p>
            <a href="{{ route('documents.download', $document->id) }}" 
               class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                📥 Télécharger le fichier pour le consulter localement
            </a>
        </div>
    @endif
</div>
@endsection
