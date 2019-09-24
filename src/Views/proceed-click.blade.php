@extends('hanoivip::layouts.app')

@section('title', 'Xúc tiến')

@section('content')

Note: chú ý preview page này!!!

@if (isset($error))
<p>{{$error}}</p>
@endif

@if (isset($message))
<p>{{$message}}</p>
@endif

<form method="post" action="{{route('proceed.click')}}">
	@csrf
	<input type="hidden" id="code" name="code" value="{{$code}}"/>
	{{ captcha_img() }}
	<button type="submit">Xúc tiến</button>
</form>	

@endsection