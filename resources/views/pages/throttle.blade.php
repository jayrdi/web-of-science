@extends('app')

@section('content')

{{-- displays a panel if there are no records from the search --}}
{{-- bootstrap panel --}}  
<div class='panel panel-danger col-lg-3' id='alertBox' role='alert'>
   <div class='panel-heading'>
       <h1 class='panel-title'>
           ALERT<span class='glyphicon glyphicon-exclamation-sign'></span>
       </h1>
   </div>
   <div class='panel-body'>
       <p>Request has been denied by Throttle server.</p>
       <p>Web of Science enforces a limit of 5 requests in as many minutes, 
          if you exceed this then the query will fail.</p>
       <p><strong>This will include queries from other computers on campus.</strong></p>
       <h2>
           {{-- uses class 'back' to run function from script.js to return to homepage --}}
           <button type='button' class='back btn btn-danger'>
               <span class='glyphicon glyphicon-fast-backward'></span>
               <strong>Click here to return to search page</strong>
           </button>
       </h2>
   </div>
</div>

@stop