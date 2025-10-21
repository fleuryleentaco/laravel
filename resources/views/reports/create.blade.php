@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mx-auto" style="max-width:720px">
        <div class="card-body">
            <h4>Signaler un problème</h4>
            <form method="post" action="{{ route('reports.store') }}">
                @csrf
                <div class="mb-3">
                    <label>Document (facultatif)</label>
                    <select name="document_id" class="form-control">
                        <option value="">-- Aucun --</option>
                        @foreach($documents as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="text-end"><button class="btn btn-primary">Envoyer</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
