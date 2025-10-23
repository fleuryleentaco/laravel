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
                    <div class="mt-3">
                        <form method="POST" action="{{ route('documents.analyze', $r->document_id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="inline-block px-3 py-2 rounded bg-yellow-500 text-white">Ré-analyser</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-sm text-indigo-200">Aucun rapport</div>
            @endforelse
        </div>
    </div>
@endsection
