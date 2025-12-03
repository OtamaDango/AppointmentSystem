@extends('layout.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<h2>Officers</h2>
<a href="{{ route('officers.create') }}" style="margin-bottom:10px; display:inline-block;">Add Officer</a>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Post</th>
            <th>Status</th>
            <th>Workdays</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($officers as $officer)
        <tr>
            <td>{{ $officer->officer_id }}</td>
            <td>{{ $officer->name }}</td>
            <td>{{ $officer->post->name ?? 'N/A' }}</td>
            <td>{{ $officer->status }}</td>
            <td>{{ implode(', ', $officer->workDays->pluck('day_of_week')->toArray()) }}</td>
            <td>
                <a href="{{ route('officers.edit', $officer->officer_id) }}">Edit</a> |

                @if($officer->status == 'Active')
                    <form action="{{ route('officers.deactivate', $officer->officer_id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">Deactivate</button>
                    </form>
                @else
                    <form action="{{ route('officers.activate', $officer->officer_id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">Activate</button>
                    </form>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
