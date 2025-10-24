@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h3 class="text-2xl font-semibold mb-4">Rapports</h3>
        <div class="space-y-3">
            @forelse($reports as $r)
                <div class="p-4 rounded-lg glass-effect">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-white">Rapport #{{ $r->id }} — {{ $r->user->email }}</div>
                            <div class="text-sm text-indigo-200">Document: {{ $r->document->filename ?? '—' }}</div>
                        </div>
                        <div class="text-xs text-indigo-200">{{ $r->created_at->diffForHumans() }}</div>
                    </div>
                    <p class="mt-2 text-indigo-200">{{ $r->description }}</p>
                    <div class="mt-3 flex gap-3 flex-wrap items-center">
                        <button type="button" onclick="document.getElementById('content-{{ $r->id }}').classList.toggle('hidden')" class="inline-block px-3 py-2 rounded bg-blue-600 text-white" @if(!$r->document) disabled @endif>Voir contenu</button>
                        <div id="content-{{ $r->id }}" class="hidden mt-2 w-full">
                            @if($r->document)
                                <div class="bg-gray-900 text-indigo-100 p-4 rounded-lg text-xs whitespace-pre-line max-h-64 overflow-auto border border-indigo-700">
                                    {{ $r->document->content }}
                                </div>
                            @else
                                <div class="bg-red-900 text-red-200 p-4 rounded-lg text-xs">Document introuvable ou supprimé.</div>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('documents.analyze', $r->document_id) }}" style="display:inline;">
                            @csrf
                            <!-- <button type="submit" class="inline-block px-3 py-2 rounded bg-yellow-500 text-white">Ré-analyser</button> -->
                        </form>
                        <form method="POST" action="{{ route('admin.approve', $r->document_id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="inline-block px-3 py-2 rounded bg-green-600 text-white">Approuver</button>
                        </form>
                        <form method="POST" action="{{ route('admin.reports.sendResult', $r->id) }}" class="flex gap-2 items-center">
                            @csrf
                            <input type="hidden" name="document_id" value="{{ $r->document_id }}">
                            <input type="text" name="details" placeholder="Détail des erreurs à envoyer" class="rounded px-2 py-1 text-sm bg-gray-900 border border-gray-700 text-white" required>
                            <button type="submit" class="inline-block px-3 py-2 rounded bg-red-600 text-white">Envoyer erreurs</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-sm text-indigo-200">Aucun rapport</div>
            @endforelse
        </div>
    </div>
@endsection
