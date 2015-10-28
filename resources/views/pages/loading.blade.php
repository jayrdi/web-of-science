@extends('app')

@section('content')

{{-- local script --}}
{!! HTML::script('js/script.js') !!}

<div class='panel panel-primary' id='alertBox'>
  <div class='panel-heading'>
      <h1 class='panel-title'>
      PROGRESS<span class='glyphicon glyphicon-info-sign'></span>
      </h1>
  </div>
  <div class='panel-body'>
      <p id='progressPanel'></p>
      <strong>Loading record {{ $counter }} of {{ $length }}</strong>
      <p>The <strong>maximum</strong> estimated time for this query is {{ $minutes }} minutes and {{ $seconds }} seconds</p>
      <h2>
          <button type='submit' class='back btn btn-primary' onclick='goBack()'>
              <span class='glyphicon glyphicon-remove'></span>
              <strong>Cancel</strong>
          </button>
      </h2>
  </div>
  </br>
  <div id='processing' hidden>
      <h4 class='text-primary'>Processing retrieved data...</h4>
      <div class='progress progress-striped active'>
          <div class='progress-bar' style='width: 100%'></div>
      </div>
  </div>
</div>

@stop