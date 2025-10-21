@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Comparison for: {{ $doc->filename }}</h3>
    <table class="table">
        <thead><tr><th>Document</th><th>Similarity</th><th>Snippet</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($results as $r)
            <tr>
                <td>{{ $r['other']->filename }}</td>
                <td>{{ round($r['sim']*100,2) }}%</td>
                <td><code>{{ $r['snippet'] }}</code></td>
                <td>
                    <a class="inline-block px-3 py-2 rounded bg-yellow-500 text-white" href="{{ route('admin.reAnalyze', $r['other']->id) }}">Ré-analyser</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
