@extends('layouts.app')

@section('content')
<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold">Mes documents</h3>
        <a href="{{ route('documents.create') }}" class="py-2 px-4 rounded bg-indigo-600 text-white">Uploader</a>
    </div>

    @if(session('status'))<div class="mb-3 text-sm text-green-300">{{ session('status') }}</div>@endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($documents as $d)
            <div class="p-4 rounded-lg glass-effect">
                <div class="mb-2">
                    <div class="font-medium text-white">{{ $d->filename }}</div>
                    <div class="text-xs text-indigo-200">{{ $d->created_at->diffForHumans() }}</div>
                </div>
                <div class="flex gap-2 items-center">
                    <a href="{{ Storage::url($d->path) }}" target="_blank" class="text-sm text-indigo-300 hover:text-indigo-200">Télécharger</a>
                    <a href="{{ route('admin.compare', $d->id) }}" class="text-sm text-indigo-300 hover:text-indigo-200">Comparer</a>
                    <span class="ml-auto text-sm px-2 py-1 rounded text-white {{ $d->approved ? 'bg-green-600' : 'bg-gray-600' }}">{{ $d->approved ? 'Approuvé' : 'Non approuvé' }}</span>
                </div>
            </div>
        @empty
            <div class="col-span-full text-sm text-indigo-200">Aucun document</div>
        @endforelse
    </div>
</div>
@endsection
