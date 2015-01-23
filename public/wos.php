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

</head>

<?php

    // =================================================================== //
    // ==== Search data entered by user sent by the HTML form in ========= //
    // ==== index.php is sent here for processing.  SOAP Request ========= //
    // ==== sent to Web of Science using their API and data ============== //
    // ==== retrieved from the SOAP Response.  Data is then ============== //
    // ==== organised according to author by number of times their ======= //
    // ==== publications have been cited.  Sent to data.php for ========== //
    // ==== display ====================================================== //
    // =================================================================== //

    // =================================================================== //
    // ================ SET UP SOAP CLIENTS & AUTHENTICATE =============== //
    // =================================================================== //

    // TIMING INITIALISE
    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    $starttime = $mtime; 

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
    // array options are temporary and used to track request & response data in printout below (line 65)
    $auth_client = @new SoapClient($auth_url, array(
                     "trace" => 1,
                     "exceptions" => 0));
    // run 'authenticate' method and store as variable
    $auth_response = $auth_client->authenticate();

    // set WSDL for search and create new SOAP client
    $search_url = "http://search.webofknowledge.com/esti/wokmws/ws/WokSearch?wsdl";
    // array options are temporary and used to track request & response data in printout below (line 130)
    $search_client = @new SoapClient($search_url, array(
                     "trace" => 1,
                     "exceptions" => 0));
    // call 'setCookie' method on '$search_client' storing SID (Session ID) as the response (value) given from the 'authenticate' method
    $search_client->__setCookie('SID',$auth_response->return);


    // =================================================================== //
    // ============== PASS IN PARAMETERS FOR SOAP REQUEST ================ //
    // =================================================================== //

    // data passed in from user via form in index.html

    // search type for journals (publication name)
    $queryType1 = "SO";

    // keyword(s)
    // check if journal1 field has been populated, if not entered then set to blank
    if (isset($_POST["journal1"])) {
        $queryJournal1 = $_POST["journal1"];
        $queryJournal1 = " OR " .$queryType1. "=" .$queryJournal1;
    } else {
        $queryJournal1 = "";
    };

    // check if journal2 field has been populated, if not entered then set to blank
    if (isset($_POST["journal2"])) {
        $queryJournal2 = $_POST["journal2"];
        $queryJournal2 = " OR " .$queryType1. "=" .$queryJournal2;
    } else {
        $queryJournal2 = "";
    };

    // check if journal3 field has been populated
    if (isset($_POST["journal3"])) {
        $queryJournal3 = $_POST["journal3"];
        $queryJournal3 = " OR " .$queryType1. "=" .$queryJournal3;
    } else {
        $queryJournal3 = "";
    };

    // search type for titles
    $queryType2 = "TI";

    // keyword(s)
    // check if title1 field has been populated
    if (isset($_POST["title1"])) {
        $queryTitle1 = $_POST["title1"];
        $queryTitle1 = " OR " .$queryType2. "=" .$queryTitle1;
    } else {
        $queryTitle1 = "";
    };

    // check if title2 field has been populated
    if (isset($_POST["title2"])) {
        $queryTitle2 = $_POST["title2"];
        $queryTitle2 = " OR " .$queryType2. "=" .$queryTitle2;
    } else {
        $queryTitle2 = "";
    };

    // check if title3 field has been populated
    if (isset($_POST["title3"])) {
        $queryTitle3 = $_POST["title2"];
        $queryTitle3 = " OR " .$queryType2. "=" .$queryTitle3;
    } else {
        $queryTitle3 = "";
    };
    
    // sort type
    $sortType = "TC";

    // check if timespan fields have been populated
    if (!$_POST["timeStart"]) {
        $timeStart = "1864-01-01";
        $timeEnd = "2080-01-01";
    } else {
        $timeStart = $_POST["timeStart"];
        $timeEnd = $_POST["timeEnd"];
    };

    // create an array to store all the search parameters to pass to data.html to display with the graph
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
    $search_array = array(
        'queryParameters' => array(
            'databaseId' => 'WOS',
            'userQuery' => $queryType1.'='.$queryJournal1 . $queryJournal2 . $queryJournal3 . ' AND ' .$queryType2. '=' .$queryTitle1 . $queryTitle2 . $queryTitle3,
            'editions' => array('collection' => 'WOS', 'edition' => 'SCI'),
            'timeSpan' => array('begin' => $timeStart, 'end' => $timeEnd),
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
    // ======== PERFORM SEARCH USING PARAMETERS & SOAP CLIENT ============ //
    // =================================================================== //


    // try to store as a variable the 'search' method on the '$search_array' called on the SOAP client with associated SID
    // if it fails, redirect back to index.php with error message
    try {
        $search_response = $search_client->search($search_array);
    } catch (Exception $e) {  
        echo $e->getMessage();
    };

    // check if there has been a soap fault
    if (is_soap_fault($search_response)) {
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

    // number of records found by search, used to finish loop (check if no records first)
    /* if (isset($search_response->return->recordsFound)) {
        $len = $search_response->return->recordsFound;
    } */

    // if there are no results, display an alert box with javascript and return to index.php
    /* if ($len == 0) {
        echo ("<script language='javascript'>
                window.location.href='index.php'
                window.alert('No records found');
               </script>");
    } */

    /* echo "</br>RECORDS FOUND: </br>";
    print "<pre>\n";
    print $len;
    print "</pre>"; */


    // =================================================================== //
    // ============ CREATE VARIABLES TO STORE REQUIRED DATA ============== //
    // ================== FROM XML & DISPLAY IN TABLE ==================== //
    // =================================================================== //


    // create an array to store data for each record per iteration
    $recordArray = array();
    // create an array to represent citation values to ignore, i.e. not interested in any publications with less than 4 citations
    $ignore = array(0, 1, 2, 3, 4);

    // iterate through all records, perform search for each 100 records and tabulate data
    for ($i = 1; $i <= $len; $i+=100) {

        // set search parameters for current iteration (first record = 1, 101, 201, 301 etc.)
        $search_array = array(
            'queryParameters' => array(
                'databaseId' => 'WOS',
                'userQuery' => $queryType1.'='.$queryJournal1 . $queryJournal2 . $queryJournal3 . ' AND ' .$queryType2. '=' .$queryTitle1 . $queryTitle2 . $queryTitle3,
                'editions' => array('collection' => 'WOS', 'edition' => 'SCI'),
                'timeSpan' => array('begin' => $timeStart, 'end' => $timeEnd),
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

        // save variable names for global use
        $author1 = "";
        // $author2 = "";
        // $author3 = "";
        $citations = "";

        // iterate through current data set and tabulate onto webpage plus store in variable
        foreach($xml->REC as $record) {
            // first author
            $author1 = (string)$record->static_data->summary->names->name[0]->full_name;
            // second author
            /* if (isset($record->static_data->summary->names->name[1]->full_name)) {
                $author2 = (string)$record->static_data->summary->names->name[1]->full_name;
                echo '<td>'.$author2.'</td>';
            } else {
                echo '<td>'."no record".'</td>';
                $author2 = "no record";
            }
            // third author
            if (isset($record->static_data->summary->names->name[2]->full_name)) {
                $author3 = (string)$record->static_data->summary->names->name[2]->full_name;
                echo '<td>'.$author3.'</td>';
            } else {
                echo '<td>'."no record".'</td>';
                $author3 = "no record";
            } */
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
                             "citations"=>$citations
                            );

            // pass the data from this iteration into the array variable '$recordArray', after all iterations, each element in $recordArray will be a single record or row of data for a single journal
            array_push($recordArray, $arecord) ;
        }
    };    

    // need to replace single quotes in text to avoid escaping when inserting to mysql, and other charas to help remove duplicates
    for ($i = 0; $i < count($recordArray); $i++) {
        $recordArray[$i]['author1'] = str_replace("'", " ", $recordArray[$i]['author1']);
        $recordArray[$i]['author1'] = str_replace(".", "", $recordArray[$i]['author1']);
        $recordArray[$i]['author1'] = str_replace(". ", "", $recordArray[$i]['author1']);
        $recordArray[$i]['author1'] = str_replace(" ", "", $recordArray[$i]['author1']);
        // $recordArray[$i]['author2'] = str_replace("'", " ", $recordArray[$i]['author2']);
        // $recordArray[$i]['author3'] = str_replace("'", " ", $recordArray[$i]['author3']);
    };

    /* echo "</br>RETRIEVED DATA: </br>";
    print "<pre>\n";
    print_r($recordArray);
    print "</pre>"; */

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
                // remove second instance
                unset($recordArray[$j]);
                // add to a variable the number of times 'unset' has been used for this iteration of $i
                $count++;
            }; // end if
        }; // end inner loop ($j)
        // decrease length of inner loop by $count, i.e. the number of elements that were removed in the last iteration, to make the length of the inner loop correct
        $length -= $count;
        // reset $count for next iteration of $i
        $count = 0;
        // reset indices
        $recordArray = array_values($recordArray);
    }; // end outer loop ($i)

    // sort array according to citation values
    // make sure that data is sorted correctly (citations_sum, high -> low)
    usort($recordArray, function ($a, $b) {
        return $b['citations'] - $a['citations'];
    });

    // only include first ten elements in array
    $recordArray = array_slice($recordArray, 0, 10);

    // make sure all the values are strings, when encoding the summed ints seem to cause problems
    for ($i = 0; $i < (count($recordArray)); $i++) {
        $recordArray[$i]['citations'] = (string)$recordArray[$i]['citations'];
    };

    /* echo "</br>FINAL DATA: </br>";
    print "<pre>\n";
    print_r($recordArray);
    print "</pre>"; */

    // turn top cited authors data into JSON file for displaying with JavaScript
    // file_put_contents('data.json', json_encode($recordArray));

    // clear the output buffer
    /* while (ob_get_status()) {
        ob_end_clean();
    } */

    // store data in session variable
    // $_SESSION['data'] = json_encode($recordArray);

    // test session data
    // echo $_SESSION['data'];

    // output $recordArray in JSON format to be picked up by JavaScript in data.html
    // echo json_encode($recordArray);

    // include "data.php";


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

</html>