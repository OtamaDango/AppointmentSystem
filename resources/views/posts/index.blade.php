@extends('layout.app')

@section('content')
<h2>Posts</h2>
<a href="{{ route('posts.create') }}">Add Post</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    @foreach($posts as $post)
    <tr>
        <td>{{ $post->post_id }}</td>
        <td>{{ $post->name }}</td>
        <td>{{ $post->status }}</td>
        <td>
            <a href="{{ route('posts.edit', $post->post_id) }}">Edit</a> |
            @if($post->status == 'Active')
                <form action="{{ route('posts.deactivate', $post->post_id) }}" method="POST">
                    @csrf
                    <button type="submit">Deactivate</button>
                </form>
            @else
                <form action="{{ route('posts.activate', $post->post_id) }}" method="POST">
                    @csrf
                    <button type="submit">Activate</button>
                </form> 
            @endif
        </td>
    </tr>
    @endforeach
</table>
@endsection
