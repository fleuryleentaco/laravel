@extends('layouts.app')

@section('content')
@section('no_hero')@endsection
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-indigo-950 to-gray-900 text-white p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold">📁 Tous les fichiers</h2>
            <p class="text-sm text-indigo-300">Liste complète des fichiers uploadés par les utilisateurs</p>
        </div>
    </div>

    @if(session('status'))
        <div class="mb-4 px-4 py-3 rounded bg-green-600/20 border border-green-500/30 text-green-300 text-sm">{{ session('status') }}</div>
    @endif

    <div class="overflow-hidden rounded-lg bg-white/3 border border-white/10">
        <table class="w-full table-auto text-sm">
            <thead class="text-left text-gray-300 bg-white/2">
                <tr>
                    <th class="px-4 py-3">Fichier</th>
                    <th class="px-4 py-3">Propriétaire</th>
                    <th class="px-4 py-3">Taille</th>
                    <th class="px-4 py-3">Upload</th>
                    <th class="px-4 py-3">Erreurs</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-200">
                @foreach($documents as $d)
                    <tr class="border-t border-white/5">
                        <td class="px-4 py-3">{{ $d->filename }} <div class="text-xs text-gray-400">ID: {{ $d->id }}</div></td>
                        <td class="px-4 py-3">{{ $d->user->name ?? $d->user->email }}</td>
                        <td class="px-4 py-3">{{ number_format($d->size/1024,2) }} KB</td>
                        <td class="px-4 py-3">{{ $d->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            @if($d->errors && $d->errors->count())
                                <span class="text-xs px-2 py-1 bg-yellow-500/20 rounded">{{ $d->errors->count() }} erreurs</span>
                            @else
                                <span class="text-xs text-gray-400">Aucune</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ Storage::url($d->path) }}" class="mr-3 text-indigo-300">Télécharger</a>
                            <a href="{{ route('admin.compare', $d->id) }}" class="mr-3 text-indigo-300">Comparer</a>
                            <a href="{{ route('admin.reAnalyze', $d->id) }}" class="mr-3 text-yellow-300">Ré-analyser</a>
                            <a href="{{ route('admin.approve', $d->id) }}" class="text-green-300">Approuver</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $documents->links() }}</div>
</div>
@endsection
