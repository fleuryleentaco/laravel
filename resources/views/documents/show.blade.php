@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white/10 p-8 rounded-xl shadow border border-white/20">
    <h2 class="text-2xl font-bold mb-6 text-indigo-200">Visualiser le document : {{ $doc->filename }}</h2>
    <div class="bg-gray-900 text-indigo-100 p-4 rounded-lg text-sm whitespace-pre-line max-h-96 overflow-auto border border-indigo-700">
        {{ $doc->content }}
    </div>
    <div class="mt-6 flex justify-end">
        <a href="{{ route('documents.index') }}" class="py-2 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold shadow transition-all">Retour</a>
    </div>
</div>
@endsection
