@extends('layouts.app')

@section('content')
@section('no_hero')@endsection
<div class="min-h-screen p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold">📥 Documents externes reçus</h2>
        <p class="text-sm text-indigo-300">Documents postés par des systèmes externes via l'API</p>
    </div>

    <div class="mb-6 flex items-center gap-3">
        <form method="POST" action="{{ route('admin.incoming.fetch') }}">
            @csrf
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Récupérer depuis l'API externe</button>
        </form>
        <a href="{{ route('admin.incoming') }}" class="text-sm text-indigo-200">Actualiser la liste</a>
    </div>

    @if(session('status'))
        <div class="mb-4 px-4 py-3 rounded bg-green-600/20 text-green-300 text-sm">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded bg-red-600/10 text-red-300 text-sm">{{ session('error') }}</div>
    @endif

    <div class="overflow-hidden rounded-lg bg-white/3 border border-white/10">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-200 bg-white/3">
                <tr>
                    <th class="px-4 py-3">Fichier</th>
                    <th class="px-4 py-3">Uploader ID</th>
                    <th class="px-4 py-3">Upload</th>
                    <th class="px-4 py-3">Erreurs</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-200">
                @foreach($docs as $d)
                    <tr class="border-t border-white/5">
                        <td class="px-6 py-4">{{ $d->filename }} <div class="text-xs text-gray-400">ID: {{ $d->id }}</div></td>
                        <td class="px-6 py-4">{{ $d->uploader_id }}</td>
                        <td class="px-6 py-4">{{ $d->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-4">
                            @if($d->errors && $d->errors->count())
                                <span class="text-xs px-2 py-1 bg-yellow-500/20 rounded">{{ $d->errors->count() }} erreurs</span>
                            @else
                                <span class="text-xs text-gray-400">Aucune</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.documents.download', $d->id) }}" class="mr-4 text-indigo-300">Télécharger</a>
                            <a href="{{ route('admin.incoming.compare', $d->id) }}" class="mr-4 text-indigo-300">Comparer</a>
                            <form method="POST" action="{{ route('admin.incoming.send', $d->id) }}" class="inline-block">
                                @csrf
                                <button class="text-sm text-green-300">Envoyer erreurs</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $docs->links() }}</div>
</div>
@endsection
