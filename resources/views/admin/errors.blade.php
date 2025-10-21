@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Erreurs détectées</h3>
    <a href="{{ route('admin.reports') }}" class="inline-block px-3 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500">Voir rapports</a>
    </div>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <div class="list-group">
        @forelse($errors as $e)
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $e->document->filename ?? '—' }} <small class="text-muted">by {{ $e->user->email }}</small></h5>
                    <small>{{ $e->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1"><span class="badge bg-danger">{{ $e->error_type }}</span> {{ $e->message }}</p>
                <div class="mt-2">
                    <form method="post" action="{{ route('admin.sendMessage', $e->id) }}" style="display:inline">@csrf<button class="btn btn-sm btn-secondary">Send</button></form>
                    <a href="{{ route('admin.reAnalyze', $e->document_id) }}" class="inline-block px-3 py-2 rounded bg-yellow-500 text-white">Ré-analyser</a>
                    <a href="{{ route('admin.approve', $e->document_id) }}" class="btn btn-sm btn-success">Approve</a>
                </div>
            </div>
        @empty
            <div class="alert alert-info">Aucune erreur détectée</div>
        @endforelse
    </div>
</div>
@endsection
