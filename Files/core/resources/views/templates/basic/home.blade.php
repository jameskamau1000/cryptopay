@extends($activeTemplate.'layouts.frontend')

@section('content')
    @if($sections->secs != null)
        @foreach(json_decode($sections->secs) as $sec)
            @if(view()->exists($activeTemplate.'sections.'.$sec))
                @include($activeTemplate.'sections.'.$sec)
            @endif
        @endforeach
    @endif
@endsection
