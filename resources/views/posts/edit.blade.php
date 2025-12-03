@extends('layout.app')

@section('content')
<h2>Edit Post</h2>

<form action="{{ route('posts.update', $post->post_id) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Name:</label>
    <input type="text" name="name" value="{{ $post->name }}" required>
    <button type="submit">Update</button>
</form>
@endsection
