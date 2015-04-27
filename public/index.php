<!DOCTYPE HTML>

<html lang="en">

	<!-- USER LOGIN SECURITY -->
	<?php

	require('redis-session.php');
    RedisSession::start();

    if (!isset($_SESSION['HTTP_SHIB_EP_EMAILADDRESS'])) {
	    header('Location: https://resviz.ncl.ac.uk/signin?redirect=https://resviz.ncl.ac.uk/wos/index.php');
	    die();
    }

    ?>

	<head>
		<title>Academic Intelligence</title>

		<!-- LINKS -->

		<!-- local css file -->
		<link href="style.css" rel="stylesheet" type="text/css" />
		<!-- Corporate visual identity -->
  		<link href="//resviz.ncl.ac.uk/static/style/cvi.css" media="screen" rel="stylesheet" type="text/css" />
  		<!-- bootstrap css -->
  		<link href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.0/readable/bootstrap.min.css" rel="stylesheet">
		<!-- favicon, newcastle logo -->
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
		<!-- fonts -->
		<link href='https://fonts.googleapis.com/css?family=Raleway:700' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Lora:400,700' rel='stylesheet' type='text/css'>

		<!-- META -->

		<meta charset="UTF-8"/>
		<!-- ensure proper rendering and touch zooming in mobile devices -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=10" />
		<meta name="keywords" content="academic intelligence, resviz, research visualisation, research and enterprise services"/>
		<meta name="description" content="A means of querying the Thomson Reuters Web of Science database using their API with SOAP HTTPS exchanges"/>
		<meta name="author" content="John Dawson"/>

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body>
		<!-- BREADCRUMBS -->
		<div class="sg-orientation">    
	        <a href="#content" class="sg-button sg-skiptocontent">Skip to Content</a>
	        <span class="sg-breadcrumbs">
	            <a href="http://www.ncl.ac.uk/">Newcastle University</a> &gt;&gt;
	            <a href="https://resviz.ncl.ac.uk/">Research Visualisation</a> &gt;&gt;
	            <strong href="#">Academic Intelligence</strong>
	        </span>
	    </div>

		<!-- TITLE BAR -->
		<div class="sg-titlebar">
			<h1><a title="Newcastle University Homepage" accesskey="1" href="http://www.ncl.ac.uk/"/><span title="Newcastle University">Newcastle University</span></a></h1>
			<h2><a href="https://resviz.ncl.ac.uk/wos/">Academic Intelligence</a></h2>
		</div> 


		<div class="sg-navigation">&nbsp;</div>

		<div class="sg-content">
			<!-- NAVIGATION BAR -->
			<nav class="navbar navbar-default" role="navigation">
				<div class="container">
					<div class="navbar-header">
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li><a href="index.php"><span class="glyphicon glyphicon-home"></span></a></li>
							<li><a href="#">About</a></li>
						</ul>
					</div> <!-- navbar-collapse -->
				</div> <!-- container -->
			</nav> <!-- navbar -->

			<!-- main content -->
			<section class="container">
				<div class="row">
					<!-- search params -->
					<form action="wos.php" method="post" role="form" class="form-horizontal" id="form">
						<fieldset>
							<div class="form-group">
								<div class="col-lg-6 well bs-component">

									<div class="journal_fields_wrap">
										<!-- keyword(s) for journal name(s) -->
										<h4 class="form_title">Journal</h4>
										<div class="journal_buttons">
											<a class="btn btn-success" target="_blank" href="http://ip-science.thomsonreuters.com/cgi-bin/jrnlst/jloptions.cgi?PC=D"
											    data-toggle="tooltip-down" title="Search Thomson Reuters for journals">Journal List</a>
											<button class="add_journal_field_button btn btn-info" type="button"><span class="glyphicon glyphicon-plus"></span>    Add more fields</button>
										</div> <!-- journal_buttons -->
										<div class="form_field">
											<input class="form-control" type="text" name="journal1" data-toggle="tooltip-right"
												   title="Please enter only one journal per box">
										</div> <!-- form_field -->
									</div> <!-- journal_fields_wrap -->

									<div class="title_fields_wrap">
										<!-- keyword(s) for paper title(s) -->
										<h4 class="form_title">Keyword</h4>
										<div class="title_buttons">
											<button class="add_title_field_button btn btn-info" type="button"><span class="glyphicon glyphicon-plus"></span>    Add more fields</button>
										</div> <!-- title_buttons -->
										<div class="form_field">
											<input class="form-control" type="text" name="title1" data-toggle="tooltip-right"
												   title="Please enter only one title per box">
										</div> <!-- form_field -->
									</div> <!-- title_fields_wrap -->

									<!-- timespan -->
									<h4 class="form_title">Time Span</h4></br>
									<label for="select" class="col-lg-2 control-label">From:</label>
									<div class="col-lg-3">
									    <select class="form-control" name="timeStart" id="select">
										    <option value="" selected disabled>Select</option>
										    <script type="text/javascript">
										    	// get current year and then use loop to populate options
										    	var year = new Date().getFullYear();
										        for(i = year; i >= 1970; i--) {
										            document.write('<option value="' + i + '">' + i + '</option>');
										        };
										    </script>
										</select>
									</div> <!-- col-lg-3 -->
									<label for="select" class="col-lg-2 control-label">To:</label>
									<div class="col-lg-3">
									    <select class="form-control" name="timeEnd" id="select">
									    	<option value="" selected disabled>Select</option>
										    <script type="text/javascript">
										    	// get current year and then use loop to populate options
										        for(i = year; i >= 1970; i--) {
										            document.write('<option value="' + i + '">' + i + '</option>');
										        };
										    </script>
										</select>
									</div> <!-- col-lg-3 -->
									<br/><br/>

									<!-- execute search -->
									<button type="submit" class="btn btn-primary btn-lg" id="submit"><strong>Submit</strong><span class='glyphicon glyphicon-transfer'></span></button>

								</div> <!-- col-lg-6 -->

								<div class="col-lg-6 well bs-component">

									<div class="modal-dialog">
										<h2>Notes</h2>
										<p>This application is optimised for Chrome.</p>
										<p>In order to get the best results from your search,<br/>enter one or more journals.</p>
										<p>Keywords and time spans are optional but can be<br/>used to refine your search.</p>
									</div> <!-- modal-dialog -->

								</div> <!-- col-lg-6 -->

							</div> <!-- form-group -->
						</fieldset>
					</form>
				</div> <!-- row -->

			</section> <!-- main content; container -->
		</div> <!-- sg-content -->

		<!-- FOOTER -->
		<div class="sg-clear">&nbsp;</div>
		<div class="sg-footer">
			<p>Research &amp; Enterprise Services<br/>Newcastle University, Newcastle Upon Tyne,<br/>NE1 7RU, United Kingdom<br/><a href="mailto:res.policy@ncl.ac.uk">Email Webmaster</a><br/><br/>&copy; 2014 Newcastle University</p>
		</div>

		<!-- SCRIPTS -->

		<!-- jquery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<!-- local script -->
		<script src="script.js"/></script>
		<!-- bootstrap js -->
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
		<!-- angularJS -->
		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.0/angular.min.js"></script>
		<!-- check browser version, if outdates, prompt for update -->
		<script src="//browser-update.org/update.js"></script>

	</body>

</html>