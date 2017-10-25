<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- local script -->
    <script src="script.js"/></script>
    <!-- bootstrap js -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <!-- bootstrap css -->
    <link href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.0/readable/bootstrap.min.css" rel="stylesheet">
    <!-- local css file -->
    <link href="style.css" rel="stylesheet" type="text/css" />
    <!-- favicon, newcastle logo -->
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />

</head>
<body>

<?php

    error_reporting(E_ERROR);

    // prevent browser from using cached data
    header('Cache-Control: no-cache');

    // variable to store average time/record retrieval
    $avg = 0.06; 

    // initialise session in order to store data to session variable
    // session_start();

    // set processing time for browser before timeout
    ini_set('max_execution_time', 3600);
    // override default PHP memory limit
    ini_set('memory_limit', '-1');

    // ================================================= //
    // ============= WEB OF SCIENCE API ================ //
    // ================================================= //

    // set WSDL for authentication and create new SOAP client
    // $auth_url  = "http://search.webofknowledge.com/esti/wokmws/ws/WOKMWSAuthenticate?wsdl";
    // array options are temporary and used to track request & response data
    // $auth_client = @new SoapClient($auth_url, array(
    //                  "trace" => 1,
    //                  "exceptions" => 0));
    // run 'authenticate' method and store as variable
    // $auth_response = $auth_client->authenticate();

    // set WSDL for search and create new SOAP client
    // $search_url = "http://search.webofknowledge.com/esti/wokmws/ws/WokSearch?wsdl";
    // array options are temporary and used to track request & response data
    // $search_client = @new SoapClient($search_url, array(
    //                  "trace" => 1,
    //                  "exceptions" => 0));
    // call 'setCookie' method on '$search_client' storing SID (Session ID) as the response (value) given from the 'authenticate' method
    // check if an SID has been set, if not it means Throttle server has stopped the query, therefore display error message
    // if (isset($auth_response->return)) {
    //   $search_client->__setCookie('SID',$auth_response->return);
    // } else {
    //     echo ("<div class='panel panel-danger col-lg-3' id='alertBox' role='alert'>
    //                <div class='panel-heading'>
    //                    <h1 class='panel-title'>
    //                        ALERT<span class='glyphicon glyphicon-exclamation-sign'></span>
    //                    </h1>
    //                </div>
    //                <div class='panel-body'>
    //                    <p>Request has been denied by Throttle server.</p>
    //                    <p>Web of Science enforces a limit of 5 requests in as many minutes, 
    //                       if you exceed this then the query will fail.</p>
    //                    <p><strong>This will include queries from other computers on campus.</strong></p>
    //                    <h2>
    //                        <button type='button' class='back btn btn-danger'>
    //                            <span class='glyphicon glyphicon-fast-backward'></span>
    //                            <strong>Click here to return to search page</strong>
    //                        </button>
    //                    </h2>
    //                </div>
    //            </div>");
    //     exit;
    // };


    // ================================================== //
    // ========= PASS IN PARAMETERS FOR QUERY =========== //
    // ================================================== //

    // data passed in from user via form in index.php

    // search type for journals (source title)
    $queryType1 = "SRCTITLE";

    // keyword(s)
    // check if journal1 field has been populated, if not entered then set to blank
    if ($_POST["journal1"] != "") {
        $queryJournal1 = "Journal 1: " .$_POST["journal1"];
        $searchJournal1 = "%28". $queryType1. "%28" .$_POST["journal1"]. "%29";
        // $searchJournal1 = $queryType1. "%28" .$searchJournal1;
    } else {
        $queryJournal1 = "";
        $searchJournal1 = NULL;
    };

    // check if journal2 field has been populated, if not entered then set to blank
    if (isset($_POST["journal2"])) {
        $queryJournal2 = "Journal 2: " .$_POST["journal2"];
        // for search params
        $searchJournal2 = $_POST["journal2"];
        $searchJournal2 = "%20%20OR%20%20". $queryType1. "%28" .$searchJournal2. "%29";
    } else {
        $queryJournal2 = "";
        $searchJournal2 = NULL;
    };

    // check if journal3 field has been populated
    if (isset($_POST["journal3"])) {
        $queryJournal3 = "Journal 3: " .$_POST["journal3"];
        // for search params
        $searchJournal3 = $_POST["journal3"];
        $searchJournal3 = "%20%20OR%20%20". $queryType1. "%28" .$searchJournal3. "%29";
    } else {
        $queryJournal3 = "";
        $searchJournal3 = NULL;
    };

    // check where to put the closing bracket for journal query
    // if ((isset($searchJournal1)) && ($searchJournal2 == NULL)) {
    //     $searchJournal1 .= "%29";
    // } elseif ((isset($_POST["journal1"])) && (isset($_POST["journal2"])) && ($searchJournal3 == NULL)) {
    //     $searchJournal2 .= "%29";
    // } elseif ((isset($_POST["journal1"])) && (isset($_POST["journal2"])) &&  (isset($_POST["journal3"]))) {
    //     $searchJournal3 .= "%29";
    // };

    // search type for KEYWORDS (article title)
    $queryType2 = "TITLE";
    
    // keyword(s)
    // check if title1 field has been populated
    if (($_POST["title1"] != "") && ($_POST["journal1"] != "")) {
        $queryTitle1 = "Keyword 1: " .$_POST["title1"];
        $searchTitle1 = $_POST["title1"];
        $searchTitle1 = "%20%20AND%20%20" .$queryType2. "%28" .$searchTitle1. "%29";
    } elseif (($_POST["title1"] != "") && ($_POST["journal1"] == "")) {
        $queryTitle1 = "Keyword 1: " .$_POST["title1"];
        $searchTitle1 = $_POST["title1"];
        $searchTitle1 = $queryType2. "%28%28" .$searchTitle1. "%29";
    } else {
        $queryTitle1 = "";
        $searchTitle1 = "";
    };

    // check if title2 field has been populated
    if (isset($_POST["title2"])) {
        $queryTitle2 = "Keyword 2: " .$_POST["title2"];
        $searchTitle2 = $_POST["title2"];
        $searchTitle2 = "%20%20AND%20%20" .$queryType2. "%28" .$searchTitle2. "%29";
    } else {
        $queryTitle2 = "";
        $searchTitle2 = "";
    };

    // check if title3 field has been populated
    if (isset($_POST["title3"])) {
        $queryTitle3 = "Keyword 3: " .$_POST["title3"];
        $searchTitle3 = $_POST["title3"];
        $searchTitle3 = "%20%20AND%20%20" .$queryType2. "%28" .$searchTitle3. "%29";
    } else {
        $queryTitle3 = "";
        $searchTitle3 = "";
    };
    
    // sort type
    $sortType = "PUBYEAR";

    // check if timespan fields have been populated
    if ((isset($_POST["timeStart"])) && (isset($_POST["timeEnd"]))) {
        $timeStart = $_POST["timeStart"];
        $timeEnd = $_POST["timeEnd"];
    } elseif ((isset($_POST["timeStart"])) && (!isset($_POST["timeEnd"]))) {
        $timeStart = $_POST["timeStart"];
        $timeEnd = date("Y");
    } elseif ((!isset($_POST["timeStart"])) && (isset($_POST["timeEnd"]))) {
        $timeStart = "1990";
        $timeEnd = $_POST["timeEnd"];
    } else {
        $timeStart = "1990";
        $timeEnd = date("Y");
    };

    // replace any whitespace with %20 (url encoding)
    $searchJournal1 = str_replace(" ", "%20AND%20", $searchJournal1);
    $searchJournal2 = str_replace(" ", "%20AND%20", $searchJournal2);
    $searchJournal3 = str_replace(" ", "%20AND%20", $searchJournal3);
    $searchTitle1   = str_replace(" ", "%20AND%20", $searchTitle1);
    $searchTitle2   = str_replace(" ", "%20AND%20", $searchTitle2);
    $searchTitle3   = str_replace(" ", "%20AND%20", $searchTitle3);

    // create an array to store all the search parameters to pass to data.html to display with the graph
    // journals and titles 2 & 3 are not always set so can't use $_POST
    $searchParams = array('journal1' => $queryJournal1,
                          'journal2' => $queryJournal2,
                          'journal3' => $queryJournal3,
                          'title1' => $queryTitle1,
                          'title2' => $queryTitle2,
                          'title3' => $queryTitle3,
                          'from' => $timeStart,
                          'to' => $timeEnd,
                         );

    // pass in relevant parameters for search, this is the format necessary for Web of Science Web Service
    // perform search for all time, process different time scales later
    // $search_array = array(
    //     'queryParameters' => array(
    //         'databaseId' => 'WOS',
    //         'userQuery' => $queryJournal1 . $queryJournal2 . $queryJournal3 . $queryTitle1 . $queryTitle2 . $queryTitle3,
    //         'editions' => array('collection' => 'WOS', 'edition' => 'SCI'),
    //         'timeSpan' => array('begin' => "1970-01-01", 'end' => (date("Y-m-d"))),
    //         'queryLanguage' => 'en'
    //     ),
    //     'retrieveParameters' => array(
    //         'count' => '100',
    //         'sortField' => array(
    //             array('name' => $sortType, 'sort' => 'D')
    //         ),
    //         'firstRecord' => '1'
    //     )
    // );


    // ================================================================ //
    // ========== PASS IN PARAMETERS FOR REST REQUEST: GtR ============ //
    // ================================================================ //


    // keyword(s)
    // check if title1 field has been populated
    if ($_POST["title1"] != "") {
        $keyword1 = $_POST["title1"];
    } else {
        $keyword1 = "";
    };

    // check if title2 field has been populated
    if (isset($_POST["title2"])) {
        $keyword2 = $_POST["title2"];
        $keyword2 = "%20OR%20" . $keyword2;
    } else {
        $keyword2 = "";
    };

    // check if title3 field has been populated
    if (isset($_POST["title3"])) {
        $keyword3 = $_POST["title3"];
        $keyword3 = "%20OR%20" . $keyword3;
    } else {
        $keyword3 = "";
    };

    // ==================================================== //
    // ============= SCOPUS INITIAL SEARCH ================ //
    // ==================================================== //

    // api key
    $apiKey = "&apiKey=7804b8bef2d4dc6e5a85ef2dfb84a87c";

    // GET Request searching for people associated with keywords (term)
    $searchLink = "https://api.elsevier.com/content/search/scopus?query=" . $searchJournal1 . $searchJournal2 . $searchJournal3 . $searchTitle1 . $searchTitle2 . $searchTitle3 . "%29" . $apiKey . "&sort=citedby-count&view=COMPLETE";

    echo "</br>URL:</br>";
    print "<pre>\n";
    print_r($searchLink);
    print "</pre>";

    // save results to a variable
    $searchResponse = file_get_contents($searchLink);

    // convert JSON to PHP variable
    $searchJson = json_decode($searchResponse, true);

    // get total number of results for query to know when to stop iterating data in loop
    $len = $searchJson['search-results']['opensearch:totalResults'];


    // =============================================================== //
    // ===== PERFORM WoS SEARCH USING PARAMETERS & SOAP CLIENT ======= //
    // =============================================================== //


    // try to store as a variable the 'search' method on the '$search_array' called on the SOAP client with associated SID
    // if it fails, redirect back to index.php with error message
    // try {
    //     $search_response = $search_client->search($search_array);
    // } catch (Exception $e) {  
    //     echo $e->getMessage();
    // };

    // number of records found by search, used to finish loop (check if no records first)
    // if soap fault, i.e. no recordsFound then set $len to null to avoid undefined variable on line 205
    // if (isset($search_response->return->recordsFound)) { 
    //     $len = $search_response->return->recordsFound;
    // } else {
    //     $len = 0;
    // };

    // check if there has been a soap fault with the query OR if there are 0 records for the search
    if ($len == 0) {
        echo ("<div class='panel panel-danger col-lg-3' id='alertBox' role='alert'>
                   <div class='panel-heading'>
                       <h1 class='panel-title'>
                           ALERT<span class='glyphicon glyphicon-exclamation-sign'></span>
                       </h1>
                   </div>
                   <div class='panel-body'>
                       <p>There were no records found for your search</p>
                       <p>Please review your search options and try again</p>
                       <h2>
                           <button type='button' class='back btn btn-danger'>
                               <span class='glyphicon glyphicon-fast-backward'></span>
                               <strong>Click here to return to search page</strong>
                           </button>
                       </h2>
                   </div>
               </div>");
        exit;
    };


    // ======================================================== //
    // ===== PERFORM GtR SEARCH USING PARAMETERS & REST ======= //
    // ======================================================== //


    // REST HTTP GET Request searching for people associated with keywords (term)
    $url = "http://gtr.rcuk.ac.uk/search/project.json?term=" . $keyword1 . $keyword2 . $keyword3 . "&fetchSize=100";

    // save results to a variable
    @$response = file_get_contents($url);

    // convert JSON to PHP variable
    $json = json_decode($response, true);

    // store total number of projects returned by query for iteration count
    $numProjects = $json['searchResult']['resourceHitCount'][0]['count'];

    // total number of results pages
    $pages = ceil($numProjects/100);

    // set initial page so that each iteration adds to this to get next page
    $page = 1;

    // array to store id details for projects retured from search
    $projects = [];

    // initiate a counter to give records a number
    $counter1 = 1;


    // ==================================================== //
    // ========= ITERATE  DATA & STORE IN ARRAY =========== //
    // ==================================================== //


    // create an array to store data for each record per iteration
    $recordArray = [];
    // create an array to represent citation values to ignore, i.e. not interested in any publications
    // with less than 1 (4) citation(s)
    $ignore = array(0, 1, 2, 3);
    // create a counter variable to use for progress bar
    $counter2 = 1;

    // create a variable to store time for loading screen
    $timeDecimal = round(($len*$avg), 2);
    // turn time into readable format
    if ($timeDecimal > 59.99) {
        $minutes = round(($timeDecimal/60), 0, PHP_ROUND_HALF_DOWN);
        while ($timeDecimal > 59.99) {
            $timeDecimal -= 60;
            $seconds = round($timeDecimal, 0);
        };
    } else {
        $minutes = 0;
        $seconds = round($timeDecimal, 0);
    };

    // panel to display records loading progress, js updates current record in #progressPanel
    echo "<div class='panel panel-primary' id='alertBox'>
              <div class='panel-heading'>
                  <h1 class='panel-title'>
                  PROGRESS<span class='glyphicon glyphicon-info-sign'></span>
                  </h1>
              </div>
              <div class='panel-body'>
                  <p id='progressPanel'></p>
                  <p>The <strong>maximum</strong> estimated time for this query is " .$minutes. " minutes & " .$seconds. " seconds</p>
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
                      <div class='progress-bar' style='width: 100%''></div>
                  </div>
              </div>
          </div>";

    // iterate through all records, perform search for each 25 records (max per call) and tabulate data
    for ($i = 0; $i <= $len; $i+=25) {

        // set search parameters for current iteration (first record = 1, 101, 201, 301 etc.)
        // $search_array = array(
        //     'queryParameters' => array(
        //         'databaseId' => 'WOS',
        //         'userQuery' => $queryJournal1 . $queryJournal2 . $queryJournal3 . $queryTitle1 . $queryTitle2 . $queryTitle3,
        //         'editions' => array('collection' => 'WOS', 'edition' => 'SCI'),
        //         'timeSpan' => array('begin' => "1970-01-01", 'end' => (date("Y-m-d"))),
        //         'queryLanguage' => 'en'
        //     ),
        //     'retrieveParameters' => array(
        //         'count' => '100',
        //         'sortField' => array(
        //             array('name' => $sortType, 'sort' => 'D')
        //         ),
        //         'firstRecord' => $i
        //     )
        // );

        // gather search response for current iteration
        // try {
        //     $search_response = $search_client->search($search_array);
        // } catch (Exception $e) {  
        //     echo $e->getMessage(); 
        // };

        // turn Soap Client object from current response into SimpleXMLElement
        // $xml = new SimpleXMLElement($search_response->return->records);

        // REST HTTP GET Request searching for people associated with keywords (term)
        $eachLink = "https://api.elsevier.com/content/search/scopus?query=" . $searchJournal1 . $searchJournal2 . $searchJournal3 . $searchTitle1 . $searchTitle2 . $searchTitle3 . "%29" . $apiKey . "&sort=citedby-count&view=COMPLETE&start=" . $i;

        // save results to a variable
        $eachResponse = file_get_contents($eachLink);

        $eachJson = json_decode($eachResponse, true);

        // echo "</br>SCOPUS DATA " . $i . ":</br>";
        // print "<pre>\n";
        // print_r($eachJson);
        // print "</pre>";

        // save variable names for global use, author, citations and publication year
        // $citations  = "";
        // $pubyear    = "";

        // iterate through current data set and tabulate onto webpage plus store in variable
        foreach($eachJson['search-results']['entry'] as $record) {

            // create arrays for authors and countries
            $authors = [];
            // $countries = [];

            ob_flush(); // flush anything from the header output buffer
            flush(); // send contents so far to the browser

            echo "<script type='text/javascript'>
                      setRecord(" .$counter2. ");
                  </script>";
            
            // iterate each author subset
            foreach ($record['author'] as $thisAuthor) {
                // check if there is a value first
                if (isset($thisAuthor['surname'])) {
                    // populate array with author name
                    array_push($authors, ($thisAuthor['initials'] . " " . $thisAuthor['surname']));
                };
            };

            // iterate each country subset
            // foreach ($record['affiliation'] as $thisCountry) {
            //     // check if there is a value first
            //     if (isset($thisCountry['affiliation-country'])) {
            //         // populate array with author name
            //         array_push($countries, ($thisCountry['affiliation-country']));
            //     };
            // };
            // country data is stored in seperate section to authors and not always populated
            // difficult to pull out and match with author so postponed for now
            $country = "";
            
            // publication year
            $pubyear = substr($record['prism:coverDate'], 0, 4);

            // citations, if less than 4 then break out of iteration
            if (!in_array(($citations = $record['citedby-count']), $ignore)) {
                $citations = $record['citedby-count'];
            } else {
                break 2;
            }

            // for this iteration map all the values recorded into a temporary array variable, aRecord (equivalent to one row of data in table)
            $arecord = array("authors"=>$authors,
                             "pubyear"=>$pubyear,
                             "country"=>$country,
                             "citations"=>$citations
                            );

            // pass the data from this iteration into the array variable '$recordArray', after all iterations, each element in $recordArray will be a single record or row of data for a single journal
            array_push($recordArray, $arecord) ;
        }
        // increment for next record
        $counter2+=25; 
    };

    // need to replace single quotes to avoid char escape & other chars to help remove duplicates
    for ($i = 0; $i < count($recordArray); $i++) {
        foreach ($recordArray[$i]['authors'] as &$value) { // reference to variable so can be modified
            $value = str_replace("'", "", $value);
            $value = str_replace(".", " ", $value);
            $value = str_replace(". ", " ", $value);
        }
    };

    // for some reason Scopus returns duplicate authors for same record
    // this will remove duplicates within the same paper
    for ($i = 0; $i < count($recordArray); $i++) {
        $recordArray[$i]['authors'] = array_unique($recordArray[$i]['authors']);
        // reset indices for array
        $recordArray[$i]['authors'] = array_values($recordArray[$i]['authors']);
    };

    // finished loading records, display 'processing' load bar
    echo "<script type='text/javascript'>showLoadBar();</script>";
    ob_flush(); // flush anything from the header output buffer
    flush(); // send contents so far to the browser


    // =========================================================== //
    // ========= ITERATE ALL GtR DATA & STORE IN ARRAY =========== //
    // =========================================================== //


    // iterate data loading next page each time and adding new results to array
    for($i = 1; $i <= $pages; $i++) {

        // set page number to current iteration number
        $page = $i;
        // GET request each time with next page number
        $thisUrl = "http://gtr.rcuk.ac.uk/search/project.json?term=" . $keyword1 . $keyword2 . $keyword3 . "&fetchSize=100&page=" . $page;
        $thisResponse = file_get_contents($thisUrl);
        $thisJson = json_decode($thisResponse, true);

        // iterate results
        foreach($thisJson['searchResult']['results'] as $project) {
          // project title
          $projTitle = $project['projectComposition']['project']['title'];
          // value
          $projFunds = $project['projectComposition']['project']['fund']['valuePounds'];
          // year, only get first 4 chars for year
          $projYear = substr(($project['projectComposition']['project']['fund']['start']), 0, 4);
          // first name
          $personFirstName = @$project['projectComposition']['personRole'][0]['firstName'];
          // surname
          $personSurname = @$project['projectComposition']['personRole'][0]['surname'];
          // person ID
          $personID = @$project['projectComposition']['personRole'][0]['id'];

          // for this iteration map all the values recorded into a temporary array variable,
          // aRecord (equivalent to one row of data in table)
          $project = [
                         "title"    =>  $projTitle,
                         "author"   =>  $personFirstName . " " . $personSurname,
                         "personID" =>  $personID,
                         "year"     =>  $projYear,
                         "funds"    =>  $projFunds
                     ];
          // pass the data from this iteration into the array '$projects', after all iterations,
          // each element in $projects will be a single record for a single project
          array_push($projects, $project) ;
        };
    };

    // need to replace single quotes to avoid char escape
    for ($i = 0; $i < count($projects); $i++) {
        $projects[$i]['author'] = str_replace("'", "", $projects[$i]['author']);
        $projects[$i]['title'] = str_replace("'", "", $projects[$i]['author']);
    };


    // ================================ //
    // === ASSIGN VALUES TO RECORDS === //
    // ================================ //


    // iterate each element (publication) in $recordArray and assign value
    // according to citations vs publication date
    for ($i = 0; $i < count($recordArray); $i++) {
        // check publication year against current year
        switch (date('Y')) {
            case ($recordArray[$i]['pubyear']) == (date('Y')):
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 10);
                break;
            case ($recordArray[$i]['pubyear']) == ((date('Y'))-1):
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 10);
                break;
            case ($recordArray[$i]['pubyear']) == ((date('Y'))-2):
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 9);
                break;
            case ($recordArray[$i]['pubyear']) == ((date('Y'))-3):
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 8);
                break;
            case ($recordArray[$i]['pubyear']) == ((date('Y'))-4):
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 7);
                break;
            case ($recordArray[$i]['pubyear']) == ((date('Y'))-5):
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 6);
                break;
            case ($recordArray[$i]['pubyear']) == ((date('Y'))-6):
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 5);
                break;
            case ($recordArray[$i]['pubyear']) == ((date('Y'))-7):
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 4);
                break;
            case ($recordArray[$i]['pubyear']) == ((date('Y'))-8):
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 3);
                break;
            case ($recordArray[$i]['pubyear']) == ((date('Y'))-9):
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 2);
                break;
            case ($recordArray[$i]['pubyear']) == ((date('Y'))-10):
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 1);
                break;
            default:
                $recordArray[$i]['values'] = (($recordArray[$i]['citations']) * 0);
                break;
        }
    };

    // ================================== //
    // =========== DATABASE  ============ //
    // ================================== //

    $db_host = $_SERVER['WOS_MYSQL_HOST'];
    $db_user = $_SERVER['WOS_MYSQL_USER'];
    $db_password = $_SERVER['WOS_MYSQL_PASS'];
    $db_database = $_SERVER['WOS_MYSQL_DB'];


    // settings for unix socket on server, check if on server first
    if (isset($_SERVER['WOS_MYSQL_SOCKET'])) {
        ini_set('mysqli.default_socket', $_ENV['WOS_MYSQL_SOCKET']);
    };

    // create variable to store connection details, variables declared at start
    $connect = mysqli_connect($db_host, $db_user, $db_password);
    // check connection; quit if fail with error
    if (!$connect)
    {
        die('Could not connect: ' . mysqli_error($connect));
        exit();
    };

    // select database to work with using connection variable
    mysqli_select_db($connect, $db_database);

    // create the tables if they don't exist
    // check if 'uid' can be selected (if it exists)
    $selectTest1 = "SELECT author FROM searchresponse";
    $con1 = mysqli_query($connect, $selectTest1);

    if (empty($con1)) {
        $query = "CREATE TABLE searchresponse (author VARCHAR(100) NOT NULL,
                                               country VARCHAR(20),
                                               year INT(4) NOT NULL,
                                               citations INT(4) NOT NULL),
                                               weight INT(5) NOT NULL)";
        mysqli_query($connect, $query);
    };
    // user defined data range
    $selectTest2 = "SELECT author FROM userDefined";
    $con2 = mysqli_query($connect, $selectTest2);

    if (empty($con2)) {
        $query = "CREATE TABLE userDefined (author VARCHAR(100) NOT NULL,
                                            country VARCHAR(20),
                                            year INT(4) NOT NULL,
                                            citations INT(4) NOT NULL)";
        mysqli_query($connect, $query);
    };
    // ten year data range
    $selectTest3 = "SELECT author FROM tenYear";
    $con3 = mysqli_query($connect, $selectTest3);

    if (empty($con3)) {
        $query = "CREATE TABLE tenYear (author VARCHAR(100) NOT NULL,
                                        country VARCHAR(20),
                                        year INT(4) NOT NULL,
                                        citations INT(4) NOT NULL)";
        mysqli_query($connect, $query);
    };
    // five year data range
    $selectTest4 = "SELECT author FROM fiveYear";
    $con4 = mysqli_query($connect, $selectTest4);

    if (empty($con4)) {
        $query = "CREATE TABLE fiveYear (author VARCHAR(100) NOT NULL,
                                         country VARCHAR(20),
                                         year INT(4) NOT NULL,
                                         citations INT(4) NOT NULL)";
        mysqli_query($connect, $query);
    };
    // two year data range
    $selectTest5 = "SELECT author FROM twoYear";
    $con5 = mysqli_query($connect, $selectTest5);

    if (empty($con5)) {
        $query = "CREATE TABLE twoYear (author VARCHAR(100) NOT NULL,
                                        country VARCHAR(20),
                                        year INT(4) NOT NULL,
                                        citations INT(4) NOT NULL)";
        mysqli_query($connect, $query);
    };

    // empty tables ready for new data, otherwise subsequent searches append data to end of existing
    mysqli_query($connect, "TRUNCATE TABLE searchresponse");
    mysqli_query($connect, "TRUNCATE TABLE userDefined");
    mysqli_query($connect, "TRUNCATE TABLE tenYear");
    mysqli_query($connect, "TRUNCATE TABLE fiveYear");
    mysqli_query($connect, "TRUNCATE TABLE twoYear");

    // loop over the $recordArray (full data) and add data to MySQL table
    for ($row = 0; $row <= count($recordArray); $row++) {
        foreach ($recordArray[$row]['authors'] as $value) {
            $sql = "INSERT INTO searchresponse (author, country, year, citations, weight) VALUES (";
            // add to the query as 'value', each author, year & citation count
            $sql .= "'" .$value. "','" .$recordArray[$row]['country']. "','" .$recordArray[$row]['pubyear']. "','" .$recordArray[$row]['citations']. "','" .$recordArray[$row]['values']. "',";
            $sql = rtrim($sql, ','); // remove the comma from the final value entry
            $sql .= ");"; // end query, now has format ... VALUES ('value1','value2','value3');
            mysqli_query($connect, $sql);
        }
    };

    // remove data pre 1990
    mysqli_query($connect, "DELETE FROM searchresponse WHERE (year < 1990)");

    // separate data into tables for each time scale
    mysqli_query($connect, "INSERT INTO userDefined SELECT author, country, year, citations FROM searchresponse WHERE year BETWEEN " .$timeStart. " AND " .$timeEnd);
    mysqli_query($connect, "INSERT INTO tenYear SELECT author, country, year, citations FROM searchresponse WHERE year BETWEEN " .(date("Y")-10). " AND " .date("Y"));
    mysqli_query($connect, "INSERT INTO fiveYear SELECT author, country, year, citations FROM searchresponse WHERE year BETWEEN " .(date("Y")-5). " AND " .date("Y"));
    mysqli_query($connect, "INSERT INTO twoYear SELECT author, country, year, citations FROM searchresponse WHERE year BETWEEN " .(date("Y")-2). " AND " .date("Y"));

    // sum citations for duplicate authors
    mysqli_query($connect, "UPDATE searchresponse AS r JOIN(SELECT author, SUM(citations) AS citations, COUNT(author) AS n FROM searchresponse GROUP BY author) AS grp ON grp.author = r.author SET r.citations = grp.citations");
    mysqli_query($connect, "UPDATE searchresponse AS r JOIN(SELECT author, SUM(weight) AS weight, COUNT(author) AS n FROM searchresponse GROUP BY author) AS grp ON grp.author = r.author SET r.weight = grp.weight");
    mysqli_query($connect, "UPDATE userDefined AS r JOIN(SELECT author, SUM(citations) AS citations, COUNT(author) AS n FROM userDefined GROUP BY author) AS grp ON grp.author = r.author SET r.citations = grp.citations");
    mysqli_query($connect, "UPDATE tenYear AS r JOIN(SELECT author, SUM(citations) AS citations, COUNT(author) AS n FROM tenYear GROUP BY author) AS grp ON grp.author = r.author SET r.citations = grp.citations");
    mysqli_query($connect, "UPDATE fiveYear AS r JOIN(SELECT author, SUM(citations) AS citations, COUNT(author) AS n FROM fiveYear GROUP BY author) AS grp ON grp.author = r.author SET r.citations = grp.citations");
    mysqli_query($connect, "UPDATE twoYear AS r JOIN(SELECT author, SUM(citations) AS citations, COUNT(author) AS n FROM twoYear GROUP BY author) AS grp ON grp.author = r.author SET r.citations = grp.citations");

    // get data back from SQL
    $allArrayGet = mysqli_query($connect, "SELECT author, country, year, citations, weight FROM (SELECT * FROM searchresponse ORDER BY year DESC) AS r GROUP BY author ORDER BY citations DESC");
    $timeArrayGet = mysqli_query($connect, "SELECT author, country, year, citations FROM (SELECT * FROM userDefined ORDER BY year DESC) AS r GROUP BY author ORDER BY citations DESC");
    $tenArrayGet = mysqli_query($connect, "SELECT author, country, year, citations FROM (SELECT * FROM tenYear ORDER BY year DESC) AS r GROUP BY author ORDER BY citations DESC");
    $fiveArrayGet = mysqli_query($connect, "SELECT author, country, year, citations FROM (SELECT * FROM fiveYear ORDER BY year DESC) AS r GROUP BY author ORDER BY citations DESC");
    $twoArrayGet = mysqli_query($connect, "SELECT author, country, year, citations FROM (SELECT * FROM twoYear ORDER BY year DESC) AS r GROUP BY author ORDER BY citations DESC");

    // populate arrays
    $topCited = [];
    while ($row_user = mysqli_fetch_assoc($allArrayGet)) {
        $topCited[] = $row_user;
    };
    // populate arrays
    $topCitedYears = [];
    while ($row_user = mysqli_fetch_assoc($timeArrayGet)) {
        $topCitedYears[] = $row_user;
    };
    // populate arrays
    $topCitedTen = [];
    while ($row_user = mysqli_fetch_assoc($tenArrayGet)) {
        $topCitedTen[] = $row_user;
    };
    // populate arrays
    $topCitedFive = [];
    while ($row_user = mysqli_fetch_assoc($fiveArrayGet)) {
        $topCitedFive[] = $row_user;
    };
    // populate arrays
    $topCitedTwo = [];
    while ($row_user = mysqli_fetch_assoc($twoArrayGet)) {
        $topCitedTwo[] = $row_user;
    };

    // empty tables ready for new data, otherwise subsequent searches append data to end of existing
    mysqli_query($connect, "TRUNCATE TABLE searchresponse");
    mysqli_query($connect, "TRUNCATE TABLE userDefined");
    mysqli_query($connect, "TRUNCATE TABLE tenYear");
    mysqli_query($connect, "TRUNCATE TABLE fiveYear");
    mysqli_query($connect, "TRUNCATE TABLE twoYear");

    // close connection
    mysqli_close($connect);
    

    // // =========================================== //
    // // ======== SUM FUNDS FOR SAME PEOPLE ======== //
    // // =========================================== //

    $reversed = array_reverse($topCited);
    echo "</br>SQL RESULTS (reversed):</br>";
    print "<pre>\n";
    print_r($reversed);
    print "</pre>";

    $count = 0;
    $length = count($projects);

    // iterate each person in $projects, ignore last value otherwise would end up comparing it
    // to itself in inner loop
    for ($i = 0; $i < ($length - 1); $i++) {
        // iterate each person in $projects a step ahead of the outer loop, compare each person
        // with every other person in array
        for ($j = ($i + 1); $j < $length; $j++) {
            // if there is a match between person IDs then:
            // (@ignores undefined offset error occuring due to 'unset')
            if ($projects[$i]['personID'] === $projects[$j]['personID']) {
                // add second citations value to first
                $projects[$i]['funds'] += $projects[$j]['funds'];
                // remove second instance
                unset($projects[$j]);
                // add to a variable the number of times 'unset' has been used for this iteration of $i
                $count++;
            }; // end if
        }; // end inner loop ($j)
        // decrease length of inner loop by $count, i.e. the number of elements that were removed in the last iteration, to make the length of the inner loop correct
        $length -= $count;
        // reset $count for next iteration of $i
        $count = 0;
        // reset indices
        $projects = array_values($projects);
    }; // end outer loop ($i)


    // ========================================= //
    // ======= PROCESS DATA ACCORDING TO ======= //
    // ========= USER TIME SPAN INPUT ========== //
    // ========================================= //


    // create new array from $projects that only contains data from years
    // specified by user in Time Span in form input
    $timeArrayFunds = array();
    // create new arrays for previous 2, 5 and 10 years for dropdown menu
    $tenArrayFunds = array();
    $fiveArrayFunds = array();
    $twoArrayFunds = array();

    for ($i = 0; $i < count($projects); $i++) {
        // if the publication year of the current record is less than or equal to the end of the time span
        // AND greater than or equal to the start of the time span then include the full record in $timeArray
        if (($projects[$i]['year'] <= $timeEnd) && ($projects[$i]['year'] >= $timeStart)) {
            array_push($timeArrayFunds, $projects[$i]);
        }
        if ($projects[$i]['year'] >= (date("Y")-10)) {
            array_push($tenArrayFunds, $projects[$i]);
        }
        if ($projects[$i]['year'] >= (date("Y")-5)) {
            array_push($fiveArrayFunds, $projects[$i]);
        }
        if ($projects[$i]['year'] >= (date("Y")-2)) {
            array_push($twoArrayFunds, $projects[$i]);
        }
    };

    // create a new array to process values
    $valueArray = array_merge(array(), $topCited);

    // sort array according to value
    // make sure that data is sorted correctly (value, high -> low)
    usort($valueArray, function ($a, $b) {
        return $b['weight'] - $a['weight'];
    });

    // sort array according to funds
    // make sure that data is sorted correctly (value, high -> low)
    usort($projects, function ($a, $b) {
        return $b['funds'] - $a['funds'];
    });

    // sort time span array according to funds
    // make sure that data is sorted correctly (value, high -> low)
    usort($timeArrayFunds, function ($a, $b) {
        return $b['funds'] - $a['funds'];
    });

    // sort 10yr array according to funds
    // make sure that data is sorted correctly (value, high -> low)
    usort($tenArrayFunds, function ($a, $b) {
        return $b['funds'] - $a['funds'];
    });

    // sort 5yr array according to funds
    // make sure that data is sorted correctly (value, high -> low)
    usort($fiveArrayFunds, function ($a, $b) {
        return $b['funds'] - $a['funds'];
    });

    // sort 2yr array according to funds
    // make sure that data is sorted correctly (value, high -> low)
    usort($twoArrayFunds, function ($a, $b) {
        return $b['funds'] - $a['funds'];
    });

    // make funds more readable values (values are in millions)
    for($i = 0; $i < count($projects); $i++) {
        $projects[$i]['funds'] = ($projects[$i]['funds']/1000000);
    }
    for($i = 0; $i < count($timeArrayFunds); $i++) {
        $timeArrayFunds[$i]['funds'] = ($timeArrayFunds[$i]['funds']/1000000);
    }
    for($i = 0; $i < count($tenArrayFunds); $i++) {
        $tenArrayFunds[$i]['funds'] = ($tenArrayFunds[$i]['funds']/1000000);
    }
    for($i = 0; $i < count($fiveArrayFunds); $i++) {
        $fiveArrayFunds[$i]['funds'] = ($fiveArrayFunds[$i]['funds']/1000000);
    }
    for($i = 0; $i < count($twoArrayFunds); $i++) {
        $twoArrayFunds[$i]['funds'] = ($twoArrayFunds[$i]['funds']/1000000);
    }  

    // sort values data so that it only has 2 values for bubble chart (author & frequency)
    for ($i = 0; $i <=(count($valueArray)); $i++) {
        unset($valueArray[$i]['citations']);
        unset($valueArray[$i]['country']);
        unset($valueArray[$i]['year']);
    };

    // insert a separator between author names so easy to read on graph mouseover
    /* foreach($valueArray as $key => $value) {
        foreach($value['authors'] as $subKey => $subValue) {
            // append appropriate char
            @$valueArray[$key]['authors'][$subKey] .= "; ";
        }
    }; */

    // echo "</br>SQL RESULTS:</br>";
    // print "<pre>\n";
    // print_r($topCited);
    // print "</pre>";

    // for data to work in d3 as bubble chart, needs to have parent and children
    $valuesJSON = array();
    $valuesJSON["name"] = "rankedData";
    $valuesJSON["children"] = $valueArray;

    // clear the output buffer
    while (ob_get_status()) {
        ob_end_clean();
    };

    // call function to remove loading panel
    echo "<script type='text/javascript'>
              removePanel();
          </script>";

    include "data.html";

?>

<!-- create jscript variable here to use in graphs.js -->
<script type="text/javascript">
    var topCited = $.parseJSON('<?php echo json_encode($topCited)?>');
    var topCitedYears = $.parseJSON('<?php echo json_encode($topCitedYears)?>');
    var topCitedTen = $.parseJSON('<?php echo json_encode($topCitedTen)?>');
    var topCitedFive = $.parseJSON('<?php echo json_encode($topCitedFive)?>');
    var topCitedTwo = $.parseJSON('<?php echo json_encode($topCitedTwo)?>');
    var topValued = '<?php echo json_encode($valuesJSON)?>';
    var searchData = $.parseJSON('<?php echo json_encode($searchParams)?>');
    var topFunded = $.parseJSON('<?php echo json_encode($projects)?>');
    var topFundedYears = $.parseJSON('<?php echo json_encode($timeArrayFunds)?>');
    var topFundedTen = $.parseJSON('<?php echo json_encode($tenArrayFunds)?>');
    var topFundedFive = $.parseJSON('<?php echo json_encode($fiveArrayFunds)?>');
    var topFundedTwo = $.parseJSON('<?php echo json_encode($twoArrayFunds)?>');
</script>

</body>
</html>