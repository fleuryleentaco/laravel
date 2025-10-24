@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white/10 p-8 rounded-xl shadow border border-white/20">
    <h2 class="text-2xl font-bold mb-6 text-indigo-200">Modifier le document : {{ $doc->filename }}</h2>
    <form method="POST" action="{{ route('documents.update', $doc->id) }}" class="space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm text-indigo-200 mb-1">Contenu</label>
            <textarea name="content" rows="10" class="w-full rounded-lg bg-gray-900/60 border border-indigo-700 text-indigo-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('content', $doc->content) }}</textarea>
        </div>
        <div class="flex justify-end">
            <button class="py-2 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold shadow transition-all">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
