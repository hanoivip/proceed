@extends('hanoivip::layouts.app')

@section('title', 'Thông tin xúc tiến')

@section('content')

Link: {{$link}}

Số lần đã được xúc tiến: {{$count}}

<form method="post" action="{{route('proceed.exchange')}}">
	{{ csrf_token() }}
	<button type="submit">Đổi xu</button>
</form>

@endsection