@extends('hanoivip::layouts.app')

@section('title', 'Xúc tiến xong')

@section('content')

@if (isset($error))
<p>{{$error}}</p>
@endif

@if (isset($message))
<p>{{$message}}</p>
@endif

@endsection