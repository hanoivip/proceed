@extends('hanoivip::layouts.app')

@section('title', 'Thông tin xúc tiến')

@section('content')

<script>
function copyProceedLink() {
	  var copyText = document.getElementById("proceed-link");
	  copyText.select();
	  copyText.setSelectionRange(0, 99999); /*For mobile devices*/
	  document.execCommand("copy");
	}
</script>

Hãy gửi link này cho bạn bè: <input type="text" value="{{$link}}" id="proceed-link"><button onclick="copyProceedLink()">Copy</button>
<br/>
Số lần đã được xúc tiến: {{$count}}
<br/>
Thời gian sau mỗi lần xúc tiến (với mỗi bạn của bạn): {{config('proceed.interval-per-ip')}} (phút)
<br/>
Tỉ lệ chuyển đổi (xu/1click): {{config('proceed.webcoin-rate')}}
<br/>
<form method="post" action="{{route('proceed.exchange')}}">
	@csrf
	<button type="submit">Đổi xu</button>
</form>

@endsection