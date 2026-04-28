@extends('layout.template')

@section('title', 'Homepage')

@section('content')

@include('components.alert')

<h1>Popular Movie</h1>
<div class="row">
    @foreach ($movies as $movie)
        @include('components.movie-card', ['movie' => $movie])
    @endforeach
    <div class="d-flex justify-content-center">
        {{ $movies->links() }}
    </div>
</div>
@endsection