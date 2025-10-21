@extends('layouts.app')

@section('content')
    <h3 class="text-xl font-semibold mb-4">Mes erreurs</h3>

    @forelse($errors as $e)
        <div class="p-4 rounded-lg glass-effect mb-3 flex justify-between items-start">
            <div>
                <div class="font-medium text-white">{{ $e->document->filename ?? '—' }}</div>
                <div class="text-sm text-indigo-200"><span class="px-2 py-1 rounded bg-red-600 text-white text-xs">{{ $e->error_type }}</span> {{ $e->message }}</div>
            </div>
            <div class="text-xs text-indigo-200">{{ $e->created_at->diffForHumans() }}</div>
        </div>
    @empty
        <div class="text-sm text-indigo-200">Aucune erreur</div>
    @endforelse
@endsection
