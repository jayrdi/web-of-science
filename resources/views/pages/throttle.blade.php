<!DOCTYPE HTML>

<html lang="en">
<head>
{{-- this stops the default compatibility view for intranet sites in IE --}}
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
<title>Academic Intelligence</title>

{{-- LINKS --}}

{{-- local css file --}}
{!! HTML::style('css/style.css') !!}
{{-- bootstrap css (bootswatch readable style) --}}
{!! HTML::style('//maxcdn.bootstrapcdn.com/bootswatch/3.3.0/readable/bootstrap.min.css') !!}
{{-- favicon; newcastle logo --}}
<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" />
{{-- fonts --}}
<link href='https://fonts.googleapis.com/css?family=Raleway:700' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Lora:400,700' rel='stylesheet' type='text/css'>

{{-- SCRIPTS --}}

{{-- jquery --}}
{!! HTML::script('https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js') !!}
{{-- bootstrap js --}}
{!! HTML::script('//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js') !!}
{{-- local script --}}
{!! HTML::script('js/script.js') !!}

{{-- displays a panel if there are no records from the search --}}
{{-- bootstrap panel --}}  
<div class='panel panel-danger col-lg-4' id='alertBox' role='alert'>
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

</html>