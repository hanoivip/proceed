@extends('hanoivip::layouts.app')

@section('title', 'Chuyển điểm qua xu')

@section('content')

@if (isset($error))
<p>{{$error}}</p>
@endif

@if (isset($message))
<p>{{$message}}</p>
@endif


@endsection