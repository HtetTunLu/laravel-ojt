@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
    <div style="text-align: center;">
      <h1>laravel 9 use jquery</h1>
      <p>Laravel 9 using jquery with cdn</p>
    </div>
    <script>
      $(document).ready(function(){
          $("h1").css('color', 'red');
        $("p").css({ 'color': 'blue', 'font-size': '18px' });
      });
    </script>
</div>
@endsection
