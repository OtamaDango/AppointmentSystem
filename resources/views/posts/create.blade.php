@extends('layout.app')

@section('content')
<h2>Add Post</h2>
<form method="POST" action="{{ route('posts.store') }}">
    @csrf
    Name: <input type="text" name="name"><br>
    <button type="submit">Save</button>
</form>
@endsection
