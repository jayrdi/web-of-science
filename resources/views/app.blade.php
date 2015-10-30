<!DOCTYPE HTML>

<html lang="en">

	<head>
		{{-- this stops the default compatibility view for intranet sites in IE --}}
		<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
		<title>Academic Intelligence</title>

		{{-- LINKS --}}

		{{-- local css file --}}
		{!! HTML::style('css/style.css') !!}
		{{-- newcastle university corporate style --}}
		{!! HTML::style('//resviz.ncl.ac.uk/static/style/cvi.css') !!}
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

		{{-- META --}}

		<meta charset="UTF-8"/>
		{{-- ensure proper rendering and touch zooming in mobile devices --}}
		<meta name="viewport" content="width=device-width, initial-scale=1">
		{{-- metadata --}}
		<meta name="author" content="John Robert Dawson"/>
		<meta name="version" content="v1.2"/>
		<meta name="date" content="27/08/2015"/>

		{{-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries --}}
		{{-- WARNING: Respond.js doesn't work if you view the page via file:// --}}
		{{-- [if lt IE 9]
			     <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			     <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		     [endif] --}}
	</head>

	<body>
		{{-- BREADCRUMBS --}}
		<div class="sg-orientation">    
	        <a href="#content" class="sg-button sg-skiptocontent">Skip to Content</a>
	        <span class="sg-breadcrumbs">
	            <a href="http://www.ncl.ac.uk/">Newcastle University</a> &gt;&gt;
	            <a href="https://resviz.ncl.ac.uk/">Research Visualisation</a> &gt;&gt;
	            <strong href="#">Academic Intelligence</strong>
	        </span>
	    </div>

		{{-- TITLE BAR --}}
		<div class="sg-titlebar">
			<h1><a title="Newcastle University Homepage" accesskey="1" href="http://www.ncl.ac.uk/"/><span title="Newcastle University">Newcastle University</span></a></h1>
			<h2><a href="https://resviz.ncl.ac.uk/wos/">Academic Intelligence</a></h2>
		</div> 

		{{-- UNUSED NAVIGATION BAR --}}
		<div class="sg-navigation">&nbsp;</div>

		<div class="sg-content">
			{{-- NAVIGATION BAR --}}
			<nav class="navbar navbar-default" role="navigation">
				<div class="container">
					<div class="navbar-header">
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li><a href="{{ action('PagesController@index') }}"><span class="glyphicon glyphicon-home"></span></a></li>
							<li><a href="{{ action('PagesController@about') }}">About</a></li>
						</ul>
					</div> {{-- navbar-collapse --}}
				</div> {{-- container --}}
			</nav> {{-- navbar --}}

			{{-- main content --}}
			<section class="container">

				{{-- unique section to other pages --}}
				@yield('content')

			</section> {{-- main content; container --}}
		</div> {{-- sg-content --}}

		{{-- FOOTER --}}
		<div class="sg-clear">&nbsp;</div>
		<div class="sg-footer">
			<p>Research &amp; Enterprise Services<br/>Newcastle University, Newcastle Upon Tyne,<br/>NE1 7RU, United Kingdom<br/><a href="mailto:res.policy@ncl.ac.uk">Email Webmaster</a><br/><br/>&copy; {{ date('Y') }} Newcastle University</p>
		</div>

	</body>

</html>
