<html>
<head>

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
    
    // TIMING INITIALISE
    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    $starttime = $mtime; 

    // prevent browser from using cached data
    header('Cache-Control: no-cache');

    // variable to store average time/record retrieval
    $avg = 0.015; 

    // initialise session in order to store data to session variable
    // session_start();

    // set processing time for browser before timeout
    ini_set('max_execution_time', 3600);
    // override default PHP memory limit
    ini_set('memory_limit', '-1');

    // ensures anything dumped out will be caught, output buffer
    // ob_start();

    // set WSDL for authentication and create new SOAP client
    $auth_url  = "http://search.webofknowledge.com/esti/wokmws/ws/WOKMWSAuthenticate?wsdl";
    // array options are temporary and used to track request & response data
    $auth_client = @new SoapClient($auth_url, array(
                     "trace" => 1,
                     "exceptions" => 0));
    // run 'authenticate' method and store as variable
    $auth_response = $auth_client->authenticate();

    // set WSDL for search and create new SOAP client
    $search_url = "http://search.webofknowledge.com/esti/wokmws/ws/WokSearch?wsdl";
    // array options are temporary and used to track request & response data
    $search_client = @new SoapClient($search_url, array(
                     "trace" => 1,
                     "exceptions" => 0));
    // call 'setCookie' method on '$search_client' storing SID (Session ID) as the response (value) given from the 'authenticate' method
    // check if an SID has been set, if not it means Throttle server has stopped the query, therefore display error message
    if (isset($auth_response->return)) {
      $search_client->__setCookie('SID',$auth_response->return);
    } else {
        echo ("<div class='panel panel-danger col-lg-3' id='alertBox' role='alert'>
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
                           <button type='button' class='back btn btn-danger'>
                               <span class='glyphicon glyphicon-fast-backward'></span>
                               <strong>Click here to return to search page</strong>
                           </button>
                       </h2>
                   </div>
               </div>");
        exit;
    };

    // =================================================================== //
    // ============== PASS IN PARAMETERS FOR SOAP REQUEST ================ //
    // =================================================================== //

    // data passed in from user via form in index.html

    // search type for journals (publication name)
    $queryType1 = "SO";

    // keyword(s)
    // check if journal1 field has been populated, if not entered then set to blank
    if ($_POST["journal1"] != "") {
        $queryJournal1 = $_POST["journal1"];
        $queryJournal1 = $queryType1. "=" .$queryJournal1;
    } else {
        $queryJournal1 = "";
    };

    // check if journal2 field has been populated, if not entered then set to blank
    if (isset($_POST["journal2"])) {
        $queryJournal2 = $_POST["journal2"];
        $queryJournal2 = " OR " .$queryType1. "=" .$queryJournal2;
        // for search params
        $searchJournal2 = $_POST["journal2"];
    } else {
        $queryJournal2 = "";
        $searchJournal2 = "";
    };

    // check if journal3 field has been populated
    if (isset($_POST["journal3"])) {
        $queryJournal3 = $_POST["journal3"];
        $queryJournal3 = " OR " .$queryType1. "=" .$queryJournal3;
        // for search params
        $searchJournal3 = $_POST["journal3"];
    } else {
        $queryJournal3 = "";
        $searchJournal3 = "";
    };

    // search type for titles
    $queryType2 = "TI";

    // keyword(s)
    // check if title1 field has been populated
    if (($_POST["title1"] != "") && ($_POST["journal1"] != "")) {
        $queryTitle1 = $_POST["title1"];
        $queryTitle1 = " AND " .$queryType2. "=" .$queryTitle1;
    } elseif (($_POST["title1"] != "") && ($_POST["journal1"] == "")) {
        $queryTitle1 = $_POST["title1"];
        $queryTitle1 = $queryType2. "=" .$queryTitle1;
    } else {
        $queryTitle1 = "";
    };

    // check if title2 field has been populated
    if (isset($_POST["title2"])) {
        $queryTitle2 = $_POST["title2"];
        $queryTitle2 = " OR " .$queryType2. "=" .$queryTitle2;
        // for search params
        $searchTitle2 = $_POST["title2"];
    } else {
        $queryTitle2 = "";
        $searchTitle2 = "";
    };

    // check if title3 field has been populated
    if (isset($_POST["title3"])) {
        $queryTitle3 = $_POST["title3"];
        $queryTitle3 = " OR " .$queryType2. "=" .$queryTitle3;
        // for search params
        $searchTitle3 = $_POST["title3"];
    } else {
        $queryTitle3 = "";
        $searchTitle3 = "";
    };
    
    // sort type
    $sortType = "TC";

    // check if timespan fields have been populated
    if ((isset($_POST["timeStart"])) && (isset($_POST["timeEnd"]))) {
        $timeStart = $_POST["timeStart"];
        $timeEnd = $_POST["timeEnd"];
    } elseif ((isset($_POST["timeStart"])) && (!isset($_POST["timeEnd"]))) {
        $timeStart = $_POST["timeStart"];
        $timeEnd = date("Y");
    } elseif ((!isset($_POST["timeStart"])) && (isset($_POST["timeEnd"]))) {
        $timeStart = "1970";
        $timeEnd = $_POST["timeEnd"];
    } else {
        $timeStart = "1970";
        $timeEnd = date("Y");
    };

    // create an array to store all the search parameters to pass to data.html to display with the graph
    // journals and titles 2 & 3 are not always set so can't use $_POST
    $searchParams = array('journal1' => $_POST['journal1'],
                          'journal2' => $searchJournal2,
                          'journal3' => $searchJournal3,
                          'title1' => $_POST['title1'],
                          'title2' => $searchTitle2,
                          'title3' => $searchTitle3,
                          'from' => $timeStart,
                          'to' => $timeEnd,
                         );
    
    // pass in relevant parameters for search, this is the format necessary for Web of Science Web Service
    // perform search for all time, process different time scales later
    $search_array = array(
        'queryParameters' => array(
            'databaseId' => 'WOS',
            'userQuery' => $queryJournal1 . $queryJournal2 . $queryJournal3 . $queryTitle1 . $queryTitle2 . $queryTitle3,
            'editions' => array('collection' => 'WOS', 'edition' => 'SCI'),
            'timeSpan' => array('begin' => "1970-01-01", 'end' => (date("Y-m-d"))),
            'queryLanguage' => 'en'
        ),
        'retrieveParameters' => array(
            'count' => '100',
            'sortField' => array(
                array('name' => $sortType, 'sort' => 'D')
            ),
            'firstRecord' => '1'
        )
    );

    // =================================================================== //
    // ==================== REST REQUEST FOR GTR ========================= //
    // =================================================================== //

    // REST HTTP GET Request searching for people associated with keywords (term)
    $GtRurl = "http://gtr.rcuk.ac.uk/search/project.json?term=" . $_POST['title1'] "+" . $_POST["title2"] "+" . $_POST["title3"] . "&fetchSize=100";
    // save results to a variable
    $GtRresponse = file_get_contents($GtRurl);
    // convert JSON to PHP variable
    $GtRjson = json_decode($GtRresponse, true);


    // =================================================================== //
    // ======== PERFORM SEARCH USING PARAMETERS & SOAP CLIENT ============ //
    // =================================================================== //


    // try to store as a variable the 'search' method on the '$search_array' called on the SOAP client with associated SID
    // if it fails, redirect back to index.php with error message
    try {
        $search_response = $search_client->search($search_array);
    } catch (Exception $e) {  
        echo $e->getMessage();
    };

    // SOAP request and response data, for error handling, str_ireplace for easier viewing
    // echo "AUTHENTICATION REQUEST: </br>" . htmlspecialchars($auth_client->__getLastRequest()) . "<br/><br/>";
    // echo "AUTHENTICATION RESPONSE: </br>" . htmlspecialchars($auth_client->__getLastResponse()) . "<br/><br/>";
    // echo "SEARCH REQUEST: </br>" . htmlspecialchars($search_client->__getLastRequest()) . "<br/><br/>";
    /* echo "SEARCH RESPONSE:";
    print "<pre>\n";
    print "\n" . htmlentities(str_ireplace('><', ">\n</br></br><", $search_client->__getLastResponse())) . "\n";
    print "</pre>"; */

    // number of records found by search, used to finish loop (check if no records first)
    // if soap fault, i.e. no recordsFound then set $len to null to avoid undefined variable on line 205
    if (isset($search_response->return->recordsFound)) { 
        $len = $search_response->return->recordsFound;
    } else {
        $len = 0;
    }

    // check if there has been a soap fault with the query OR if there are 0 records for the search
    if (is_soap_fault($search_client->__getLastResponse()) || $len == 0) {
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


    // =================================================================== //
    // ============ CREATE VARIABLES TO STORE REQUIRED DATA ============== //
    // ================== FROM XML & DISPLAY IN TABLE ==================== //
    // =================================================================== //


    // create an array to store data for each record per iteration
    $recordArray = array();
    // create an array to represent citation values to ignore, i.e. not interested in any publications
    // with less than 1 (4) citation(s)
    $ignore = array(0, 1, 2, 3);
    // create a counter variable to use for progress bar
    $counter = 1;

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
          </div>";

    // iterate through all records, perform search for each 100 records (max per call) and tabulate data
    for ($i = 1; $i <= $len; $i+=100) {

        // set search parameters for current iteration (first record = 1, 101, 201, 301 etc.)
        $search_array = array(
            'queryParameters' => array(
                'databaseId' => 'WOS',
                'userQuery' => $queryJournal1 . $queryJournal2 . $queryJournal3 . $queryTitle1 . $queryTitle2 . $queryTitle3,
                'editions' => array('collection' => 'WOS', 'edition' => 'SCI'),
                'timeSpan' => array('begin' => "1970-01-01", 'end' => (date("Y-m-d"))),
                'queryLanguage' => 'en'
            ),
            'retrieveParameters' => array(
                'count' => '100',
                'sortField' => array(
                    array('name' => $sortType, 'sort' => 'D')
                ),
                'firstRecord' => $i
            )
        );

        // gather search response for current iteration
        try {
            $search_response = $search_client->search($search_array);
        } catch (Exception $e) {  
            echo $e->getMessage(); 
        };

        // turn Soap Client object from current response into SimpleXMLElement
        $xml = new SimpleXMLElement($search_response->return->records);

        // save variable names for global use, author, citations and publication year
        $author1 = "";
        // $author2 = "";
        // $author3 = "";
        $citations = "";
        $pubyear = "";

        // iterate through current data set and tabulate onto webpage plus store in variable
        foreach($xml->REC as $record) {

            ob_flush(); // flush anything from the header output buffer
            flush(); // send contents so far to the browser

            echo "<script type='text/javascript'>
                      setRecord(" .$counter. ");
                  </script>";
            
            // first author
            $author1 = (string)$record->static_data->summary->names->name[0]->full_name;
            
            // publication year
            $pubyear = (string)$record->static_data->summary->pub_info->attributes()->pubyear;

            // number of citations, if zero then finish populating array then 'break' out of loop entirely (not interested in zero cited records)
            if (!in_array($record->dynamic_data->citation_related->tc_list->silo_tc->attributes(), $ignore)) {
                $citations = (string)$record->dynamic_data->citation_related->tc_list->silo_tc->attributes();
            } else {
                break 2;
            };

            // for this iteration map all the values recorded into a temporary array variable, aRecord (equivalent to one row of data in table)
            $arecord = array("author1"=>strtoupper($author1),
                             // "author2"=>$author2,
                             // "author3"=>$author3,
                             "pubyear"=>$pubyear,
                             "citations"=>$citations
                            );

            // pass the data from this iteration into the array variable '$recordArray', after all iterations, each element in $recordArray will be a single record or row of data for a single journal
            array_push($recordArray, $arecord) ;
        }
        // increment for next record
        $counter+=100; 
    };

    // need to replace single quotes in text to avoid escaping when inserting to mysql, and other charas to help remove duplicates
    for ($i = 0; $i < count($recordArray); $i++) {
        $recordArray[$i]['author1'] = str_replace("'", "", $recordArray[$i]['author1']);
        $recordArray[$i]['author1'] = str_replace(".", " ", $recordArray[$i]['author1']);
        $recordArray[$i]['author1'] = str_replace(". ", " ", $recordArray[$i]['author1']);
    };

    /*********************/
    /*** ASSIGN VALUES ***/
    /***** TO RECORDS ****/
    /*********************/

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


    /********* VALUES END ************/

    /******* FREQUENCY OF AUTHOR *******/

    // iterate data and add 'frequency' to each element with value 1
    for ($i = 0; $i < (count($recordArray)); $i++) {
        $recordArray[$i]['frequency'] = 1;
    };

    /**********************************/


    // as length of $j loop will decrease each time because of 'unset' its elements, create a variable to dynamically store its length
    $length = count($recordArray);
    $count = 0;

    // iterate each author in $recordArray, ignore last value otherwise would end up comparing it to itself in inner loop
    for ($i = 0; $i < (count($recordArray) - 1); $i++) {
        // iterate each author in $recordArray a step ahead of the outer loop, compare each author with every other author in array
        for ($j = ($i + 1); $j < $length; $j++) {
            // if there is a match between author names then (@ignores undefined offset error occuring due to 'unset'):
            if ($recordArray[$i]['author1'] === $recordArray[$j]['author1']) {
                // add second citations value to first
                $recordArray[$i]['citations'] += $recordArray[$j]['citations'];
                // add second value to first
                $recordArray[$i]['values'] += $recordArray[$j]['values'];
                // remove second instance
                unset($recordArray[$j]);
                // add to a variable the number of times 'unset' has been used for this iteration of $i
                $count++;
                // add 1 to frequency for author
                $recordArray[$i]['frequency'] += 1;
            }; // end if
        }; // end inner loop ($j)
        // decrease length of inner loop by $count, i.e. the number of elements that were removed in the last iteration, to make the length of the inner loop correct
        $length -= $count;
        // reset $count for next iteration of $i
        $count = 0;
        // reset indices
        $recordArray = array_values($recordArray);
    }; // end outer loop ($i)

    /******************************************/
    /******* PROCESS DATA ACCORDING TO ********/
    /********* USER TIME SPAN INPUT ***********/
    /******************************************/


    // create new array from $recordArray that only contains data from years
    // specified by user in Time Span in form input
    $timeArray = array();
    // create new arrays for previous 2, 5 and 10 years for dropdown menu
    $tenArray = array();
    $fiveArray = array();
    $twoArray = array();

    for ($i = 0; $i < count($recordArray); $i++) {
        // if the publication year of the current record is less than or equal to the end of the time span
        // AND greater than or equal to the start of the time span then include the full record in $timeArray
        if (($recordArray[$i]['pubyear'] <= $timeEnd) && ($recordArray[$i]['pubyear'] >= $timeStart)) {
            array_push($timeArray, $recordArray[$i]);
        }
        if ($recordArray[$i]['pubyear'] >= (date("Y")-10)) {
            array_push($tenArray, $recordArray[$i]);
        }
        if ($recordArray[$i]['pubyear'] >= (date("Y")-5)) {
            array_push($fiveArray, $recordArray[$i]);
        }
        if ($recordArray[$i]['pubyear'] >= (date("Y")-2)) {
            array_push($twoArray, $recordArray[$i]);
        }
    };

    // create  a new array to process values
    $valueArray = array_merge(array(), $recordArray);
    $freqArray = array_merge(array(), $recordArray);

    // sort array according to citations
    // make sure that data is sorted correctly (value, high -> low)
    usort($recordArray, function ($a, $b) {
        return $b['citations'] - $a['citations'];
    });

    // sort time span array according to citations
    // make sure that data is sorted correctly (value, high -> low)
    usort($timeArray, function ($a, $b) {
        return $b['citations'] - $a['citations'];
    });

    // sort 10yr array according to citations
    // make sure that data is sorted correctly (value, high -> low)
    usort($tenArray, function ($a, $b) {
        return $b['citations'] - $a['citations'];
    });

    // sort 5yr array according to citations
    // make sure that data is sorted correctly (value, high -> low)
    usort($fiveArray, function ($a, $b) {
        return $b['citations'] - $a['citations'];
    });

    // sort 2yr array according to citations
    // make sure that data is sorted correctly (value, high -> low)
    usort($twoArray, function ($a, $b) {
        return $b['citations'] - $a['citations'];
    });

    // sort array according to value
    // make sure that data is sorted correctly (value, high -> low)
    usort($valueArray, function ($a, $b) {
        return $b['values'] - $a['values'];
    });

    // sort array according to frequency of author
    usort($freqArray, function ($a, $b) {
        return $b['frequency'] - $a['frequency'];
    });

    // only include first ten elements in array
    $recordArray = array_slice($recordArray, 0, 10);
    $timeArray = array_slice($timeArray, 0, 10);
    $tenArray = array_slice($tenArray, 0, 10);
    $fiveArray = array_slice($fiveArray, 0, 10);
    $twoArray = array_slice($twoArray, 0, 10);
    $valueArray = array_slice($valueArray, 0, 10);
    $freqArray = array_slice($freqArray, 0, 15);

    // sort frequency data so that it only has 2 values for bubble chart (author & frequency)
    for ($i = 0; $i <=(count($freqArray)); $i++) {
        unset($freqArray[$i]['citations']);
        unset($freqArray[$i]['values']);
        unset($freqArray[$i]['pubyear']);
    };

    for ($i = 0; $i <=(count($valueArray)); $i++) {
        unset($valueArray[$i]['citations']);
        unset($valueArray[$i]['frequency']);
        unset($valueArray[$i]['pubyear']);
    };

    // for data to work in d3 as bubble chart, needs to have parent and children
    $frequencyJSON = array();
    $frequencyJSON["name"] = "frequencyData";
    $frequencyJSON["children"] = $freqArray;

    $valuesJSON = array();
    $valuesJSON["name"] = "rankedData";
    $valuesJSON["children"] = $valueArray;

    // clear the output buffer
    while (ob_get_status()) {
        ob_end_clean();
    };

    // call function to remove panel
    echo "<script type='text/javascript'>
                      removePanel();
                  </script>";

    include "data.html";


    // =================================================== //
    // ================ TIMING END ======================= //
    // =================================================== //


    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    $endtime = $mtime;
    $totaltime = ($endtime - $starttime);
    // echo "This page was created in ".$totaltime." seconds";

?>

<!-- create jscript variable here to use in graphs.js -->
<script type="text/javascript">
    var topCited = $.parseJSON('<?php echo json_encode($recordArray)?>');
    var topCitedYears = $.parseJSON('<?php echo json_encode($timeArray)?>');
    var topCitedTen = $.parseJSON('<?php echo json_encode($tenArray)?>');
    var topCitedFive = $.parseJSON('<?php echo json_encode($fiveArray)?>');
    var topCitedTwo = $.parseJSON('<?php echo json_encode($twoArray)?>');
    var topValued = '<?php echo json_encode($valuesJSON)?>';
    var searchData = $.parseJSON('<?php echo json_encode($searchParams)?>');
</script>

</body>
</html>