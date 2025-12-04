@extends('layout.app')

@section('content')

<h2>Visitors</h2>
<a href="{{ route('visitors.create') }}">Add Visitor</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Mobile</th>
        <th>Email</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    @foreach($visitors as $visitor)
    <tr>
        <td>{{ $visitor->visitor_id }}</td>
        <td>{{ $visitor->name }}</td>
        <td>{{ $visitor->mobileno }}</td>
        <td>{{ $visitor->email }}</td>
        <td>{{ $visitor->status }}</td>
        <td>
            <a href="{{ route('visitors.edit', $visitor->visitor_id) }}">Edit</a> |
            @if($visitor->status == 'Active')
            <form action="{{ route('visitors.deactivate', $visitor->visitor_id) }}" method="POST">
                @csrf
                <button type="submit">Deactivate</button>
            </form>            
            @else
            <form action="{{ route('visitors.activate', $visitor->visitor_id) }}" method="POST">
                @csrf
                <button type="submit">Activate</button>
            </form>
            @endif
        </td>
    </tr>
    @endforeach
</table>
@endsection
