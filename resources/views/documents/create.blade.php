@extends('layouts.app')

@section('content')
    <h3 class="text-xl font-semibold mb-4">Uploader des fichiers</h3>

    <form action="{{ route('documents.store') }}" method="post" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-indigo-200 mb-1">Fichiers</label>
            <input type="file" name="files[]" multiple class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-gradient-to-r file:from-fuchsia-600 file:to-indigo-600" />
        </div>

        <div class="flex justify-end items-center gap-3">
            <a href="{{ route('documents.errors') }}" class="text-sm text-indigo-300 hover:text-indigo-200">Voir mes erreurs</a>
            <button class="py-2 px-4 rounded bg-indigo-600 text-white">Uploader</button>
        </div>
    </form>
@endsection
