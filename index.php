<!DOCTYPE>

<html>
	<head>

		<title>Research Beehive Data Collection</title>

		<!-- link to CSS stylesheet, fonts & plugins -->
		<link rel="stylesheet" type="text/css" href="style.css"/>
		<link href='http://fonts.googleapis.com/css?family=Raleway:700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Lora:400,700' rel='stylesheet' type='text/css'>
		<!--[if lt IE 9] >
			<script src="dist/html5shiv.js"></script>
		<![endif]-->

		<!-- links to various javascript files used -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
		<script src="script.js"/></script>

		<meta charset="UTF-8"/>
		<meta name="keywords" content="web of science, thomson reuters"/>
		<meta name="description" content="A means of querying the Thomson Reuters Web of Science database using their API with SOAP HTTPS exchanges"/>
		<meta name="author" content="John Dawson, Pete Wheldon"/>

	</head>
	
	<header>
		<div class="wrapper">
			<h1 class="logo">Research & Enterprise Services</h1>
			<h2>Web of Science Database Query</h2>
		</div> <!-- wrapper --> 
	</header>

	<article class="about">
		<div class="wrapper">
			<p>The aim of this site is to retrieve data from the Thomson Reuters Web of Science database according to a user-defined set of search parameters</p>
			<p class="instruct">Please complete the necessary fields for your search and click 'Go'</p>
		</div> <!-- wrapper -->
	</article> <!-- about -->

	<div id="mainBlock">

		<form name="submit">
			<p class="input">Category</p></br>
			<input type="text" name="category">
			</br></br>

			<p class="input">Search Type</p></br>
			<select>
			    <option value="TS">Topic</option>
			    <option value="TI">Title</option>
			    <option value="AU">Author</option>
			    <option value="ED">Editor</option>
			    <option value="SO">Publication Name</option>
			    <option value="DO">DOI</option>
			    <option value="PY">Year Published</option>
			    <option value="AD">Address</option>
			</select></br></br>

			<p class="input">Sort By</p></br>
			<select>
			    <option value="relevance">Relevance</option>
			    <option value="citeddown">Cited Most -> Least</option>
			    <option value="citedup">Cited Least -> Most</option>
			</select>

			<input type="button" onClick="" value="Go">
		</form>

	</div>

	<footer class="credit">
		<div class="wrapper">
			<h3 class="instruct">Credit</h3></br>
			<p id="links"><a href="mailto:john@artgecko.co.uk" id="mail">John Dawson</a> @ <a href="http://www.ncl.ac.uk/res/about/office/research/" id="mail">Research & Enterprise Services, Newcastle University</a></p>
			<img src="images/ncl logo.jpg"></img>
		</div> <!-- wrapper -->
	</footer>

</html>