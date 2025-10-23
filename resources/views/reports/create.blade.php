@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-transparent">
    <div class="w-full max-w-md p-8 rounded-2xl shadow-xl bg-white/10 border border-white/20 backdrop-blur-md">
        <h4 class="text-2xl font-bold mb-6 text-center text-indigo-200">Signaler un problème</h4>
        <form method="post" action="{{ route('reports.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm text-indigo-200 mb-1">Document (facultatif)</label>
                <select name="document_id" class="w-full rounded-lg bg-gray-900/60 border border-indigo-700 text-indigo-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Aucun --</option>
                    @foreach($documents as $id => $name)
                        <option value="{{ $id }}" @if(isset($selected) && $selected == $id) selected @endif>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-indigo-200 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full rounded-lg bg-gray-900/60 border border-indigo-700 text-indigo-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div class="flex justify-end">
                <button class="py-2 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold shadow transition-all">Envoyer</button>
            </div>
        </form>
    </div>
</div>
@endsection
