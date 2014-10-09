<?php

    // css
    echo '<link rel="stylesheet" type="text/css" href="style.css"/>';
    // fonts and favicon
    echo '<link href="http://fonts.googleapis.com/css?family=Raleway:700" rel="stylesheet" type="text/css">
          <link href="http://fonts.googleapis.com/css?family=Lora:400,700" rel="stylesheet" type="text/css">
          <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon"/>';
    // jquery & javascript
    echo '<script src="script.js"/></script>
          <script src="http://code.jquery.com/jquery-latest.min.js "></script>';

    // local password file
    include '../config.php';

    // =================================================================== //
    // == Author: John Dawson                                           == //
    // == Date: 28/08/2014                                              == //
    // == Description: Processing for a website to query Web of Science == //
    // ==              Web Service using their API and return relevant  == //
    // ==              data                                             == //
    // =================================================================== //

    
    // =================================================================== //
    // ================ SET UP SOAP CLIENTS & AUTHENTICATE =============== //
    // =================================================================== //

    // set processing time for browser before timeout
    ini_set('max_execution_time', 300);

    // button to display top ten cited authors in bar chart
    echo "</br></br><a href='data.html' class='button'>Click here to display the top cited authors</a></br></br>";

    $db_host = (isset($_SERVER['WOS_MYSQL_HOST'])) ? $_SERVER['WOS_MYSQL_HOST'] : 'localhost';
    $db_user = (isset($_SERVER['WOS_MYSQL_USER'])) ? $_SERVER['WOS_MYSQL_USER'] : 'root';
    $db_password = (isset($_SERVER['WOS_MYSQL_PASS'])) ? $_SERVER['WOS_MYSQL_PASS'] : $local_password;
    $db_database = (isset($_SERVER['WOS_MYSQL_DB'])) ? $_SERVER['WOS_MYSQL_DB'] : 'wos';


    // ensures anything dumped out will be caught
    ob_start();

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
    // ================= PRINT OUT REQUEST & RESPONSE ==================== //
    // ====================== OF AUTHORISATION =========================== //
    // =================================================================== // 


    // print details of XML request and response data for Authentication exchange
    /* print "<pre>\n";
    print "<br />\n Request : ".htmlspecialchars($auth_client->__getLastRequest());
    print "<br />\n Response: ".htmlspecialchars($auth_client->__getLastResponse());
    print "</pre>"; */


    // =================================================================== //
    // ============== PASS IN PARAMETERS FOR SOAP REQUEST ================ //
    // =================================================================== //

    $queryType1 = $_POST["type1"];
    $queryCategory1 = $_POST["category1"];
    $sortType = $_POST["sort"];
    // check if 'hidden' extra search facility is being used
    if (!$_POST["category2"]) {
        $queryLogic = "";
        $queryType2 = "";
        $queryCategory2 = "";
    } else {
        $queryLogic = $_POST["logic"];
        $queryType2 = $_POST["type2"]."=";
        $queryCategory2 = $_POST["category2"];
    }
    
    // pass in relevant parameters for search
    $search_array = array(
        'queryParameters' => array(
            'databaseId' => 'WOS',
            'userQuery' => $queryType1.'='.$queryCategory1. ' ' .$queryLogic. ' ' .$queryType2.$queryCategory2,
            'editions' => array('collection' => 'WOS', 'edition' => 'SCI'),
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
    try {
        $search_response = $search_client->search($search_array);
    } catch (Exception $e) {  
        echo $e->getMessage(); 
    };


    // =================================================================== //
    // ================ PRINT VALUES TO CHECK DATA ======================= //
    // =================================================================== //

    /* echo "</br>SEARCH_RESPONSE: </br>";
    print "<pre>\n";
    print_r($search_response);
    print "</pre>";

    echo "</br>SEARCH_CLIENT: </br>";
    print "<pre>\n";
    print_r($search_client);
    print "</pre>"; */


    // =================================================================== //
    // ================= TURN SOAP RESPONSE STRING INTO ================== //
    // ================== SIMPLE XML ELEMENT OBJECT TO =================== //
    // ========== TRAVERSE AND EXTRACT INDIVIDUAL DATA ELEMENTS ========== //
    // =================================================================== //


    // number of records found by search
    $len = $search_response->return->recordsFound;

    echo "</br>RECORDS FOUND: </br>";
    print "<pre>\n";
    print $len;
    print "</pre>";

    /* echo "</br>SIMPLE XML ELEMENT OBJECT: </br>";
    print "<pre>\n";
    print_r($xml);
    print "</pre>"; */

    // encode XML data as JSON for use with Javascript
    // $json = json_encode($xml);
    /* echo "</br>JSON: </br>";
    print "<pre>\n";
    print_r($json);
    print "</pre>"; */


    // =================================================================== //
    // ============ CREATE VARIABLES TO STORE REQUIRED DATA ============== //
    // ================== FROM XML & DISPLAY IN TABLE ==================== //
    // =================================================================== //


    // print table with suitable headers
    echo '<table id="table" <tr>
                <th>Record Number</th>
                <th>Unique Identifier</th>
                <th>Journal Name</th>
                <th>Publication Name</th>
                <th>Publication Year</th>
                <th>Author 1</th>
                <th>Address</th>
                <th>Author 2</th>
                <th>Author 3</th>
                <th>Number of Citations</th>
            </tr>>';

    // create an array to store data for each record per iteration
    $recordArray = array();
    // create an array for top cited authors
    $citedArray = array();
    // create a variable to store and display row number
    $count = 1;

    // iterate through all records, perform search for each 100 records and tabulate data
    for ($i = 1; $i <= $len; $i+=100) {

        // set search parameters for current iteration (first record = 1, 101, 201, 301 etc.)
        $search_array = array(
            'queryParameters' => array(
                'databaseId' => 'WOS',
                'userQuery' => $queryType1.'='.$queryCategory1. ' ' .$queryLogic. ' ' .$queryType2.$queryCategory2,
                'editions' => array('collection' => 'WOS', 'edition' => 'SCI'),
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
        $uid = "";
        $journal = "";
        $publication = "";
        $year = "";
        $author1 = "";
        $address = "";
        $author2 = "";
        $author3 = "";
        $citations = "";

        // iterate through current data set and tabulate onto webpage plus store in variable
        foreach($xml->REC as $record) {
            // start table row
            echo '<tr>';
            // batch number
            echo '<td>'.$count.'</td>';
            $count++;
            // store unique id for database and echo to html table
            $uid = (string)$record->UID;
            echo '<td>'.$uid.'</td>';
            // journal name
            $journal = (string)$record->static_data->summary->titles->title[0];
            echo '<td>'.$journal.'</td>';
            // publication name
            $publication = (string)$record->static_data->summary->titles->title[5];
            echo '<td>'.$publication.'</td>';
            // publication year
            $year = (string)$record->static_data->summary->pub_info->attributes()->pubyear;
            echo '<td>'.$year.'</td>';
            // first author
            $author1 = (string)$record->static_data->summary->names->name[0]->full_name;
            echo '<td>'.$author1.'</td>';
            // address
            if (isset($record->static_data->fullrecord_metadata->addresses->address_name->address_spec->full_address)) {
                $address = (string)$record->static_data->fullrecord_metadata->addresses->address_name->address_spec->full_address;
                echo '<td>'.$address.'</td>';
            } else {
                echo '<td>'."no record".'</td>';
                $address = "no record";
            }
            // second author
            if (isset($record->static_data->summary->names->name[1]->full_name)) {
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
            }
            // number of citations
            $citations = (string)$record->dynamic_data->citation_related->tc_list->silo_tc->attributes();
            echo '<td>'.$citations.'</td>';
            // close table row
            echo '</tr>';

            // for this iteration map all the values recorded into a temporary array variable, aRecord (equivalent to one row of data in table)
            $arecord = array("uid"=>$uid,
                             "journal"=>$journal,
                             "publication"=>$publication,
                             "year"=>$year,
                             "author1"=>$author1,
                             "address"=>$address,
                             "author2"=>$author2,
                             "author3"=>$author3,
                             "citations"=>$citations );

            // pass the data from this iteration into the array variable '$recordArray', after all iterations, each element in $recordArray will be a single record or row of data for a single journal
            array_push($recordArray, $arecord) ;
        }
    }    
    echo '</table>';

    // need to replace single quotes in text to avoid escaping when inserting to mysql
    $pattern = "/\'/";
    $replace = '"';

    for ($i = 0; $i < count($recordArray); $i++) {
        $recordArray[$i]['publication'] = preg_replace("#[^\\\]'#", '"', $recordArray[$i]['publication']);
        $recordArray[$i]['journal'] = str_replace("'", "", $recordArray[$i]['journal']);
        $recordArray[$i]['author1'] = str_replace("'", " ", $recordArray[$i]['author1']);
        $recordArray[$i]['author2'] = str_replace("'", " ", $recordArray[$i]['author2']);
        $recordArray[$i]['author3'] = str_replace("'", " ", $recordArray[$i]['author3']);
        $recordArray[$i]['address'] = str_replace("'", "", $recordArray[$i]['address']);
    }

    // this array has taken all the data we need from the SimpleXMLElement and is ready to be passed into the database
    /* echo "</br>RECORD ARRAY (full data): </br></br>";
    print "<pre>\n";
    print_r($recordArray);
    print "</pre>"; */

    // populate citedArray from recordArray, only first ten records
    for ($i = 0; $i < 10 && $i < count($recordArray); $i++) {
            array_push($citedArray, ($recordArray[$i]['author1']));
            array_push($citedArray, ($recordArray[$i]['author2']));
            array_push($citedArray, ($recordArray[$i]['author3']));
    };

    echo "</br>CITED ARRAY (30 records, author names): </br></br>";
    print "<pre>\n";
    print_r($citedArray);
    print "</pre>";

    // get rid of duplicates
    // $singleAuthors = array_unique($citedArray);
    // print_r($singleAuthors);

    /* echo "</br>UNIQUE CITED ARRAY: </br></br>";
    print "<pre>\n";
    print_r($singleAuthors);
    print "</pre>"; */

    $length = count($citedArray);

    // get rid of 'no record' values (useless data)
    for ($i = 0; $i < $length; $i++) {
        if ($citedArray[$i] == 'no record') {
            unset($citedArray[$i]);
        };
    };

    // $singleAuthors = array_map('strtoupper', $singleAuthors);

    echo "</br>'no record' REMOVED: </br></br>";
    print "<pre>\n";
    print_r($citedArray);
    print "</pre>";

    // get rid of duplicates
    $singleAuthors = array_unique($citedArray);
    echo "</br>NO DUPLICATES: </br></br>";
    print "<pre>\n";
    print_r($singleAuthors);
    print "</pre>";


    // =================================================================== //
    // ===================== CONNECT TO DATABASE ========================= //
    // =================================================================== //


    // create variable to store connection details
    $connect = mysqli_connect($db_host, $db_user, $db_password, $db_database);
    // check connection; quit if fail with error
    /* if (!$connect)
    {
        die('Could not connect: ' . mysqli_error($connect));
        exit();
    } */

    // check connection
    /* if ($connect->ping()) {
        printf ("</br></br>CONNECTED TO DATABASE!</br></br>");
    } else {
        printf ("ERROR: %s\n", $connect->error);
    } */

    // create the database if it doesn't already exist
    mysqli_query($connect, "CREATE DATABASE IF NOT EXISTS wos");

    // select database to work with using connection variable
    // mysqli_select_db($connect, $db_database);

    // create the tables if they don't exist
    // check if 'uid' can be selected (if it exists)
    $selectTest1 = "SELECT uid FROM searchresponse";
    $con1 = mysqli_query($connect, $selectTest1);

    if (empty($con1)) {
        $query = "CREATE TABLE searchresponse (uid VARCHAR(20) NOT NULL,
                                               journal VARCHAR(100) NOT NULL,
                                               publication VARCHAR(100) NULL,
                                               year INT(4),
                                               author1 VARCHAR(100) NOT NULL,
                                               address VARCHAR(200) NULL,
                                               author2 VARCHAR(100) NULL,
                                               author3 VARCHAR(100) NULL,
                                               citations INT(4) NOT NULL)";
        $result = mysqli_query($connect, $query);
    }

    // check if 'author' can be selected (if it exists)
    $selectTest2 = "SELECT author FROM topcited";
    $con2 = mysqli_query($connect, $selectTest2);

    if (empty($con2)) {
        $query = "CREATE TABLE topcited (author VARCHAR(30) NOT NULL,
                                         citations_sum INT(4) NOT NULL)";
        $result = mysqli_query($connect, $query);
    }

    // empty tables ready for new data
    mysqli_query($connect, "TRUNCATE TABLE searchresponse");
    mysqli_query($connect, "TRUNCATE TABLE topcited");

    // loop over the $recordArray (full data)
    for ($row = 0; $row < count($recordArray); $row++) {
        $sql = "INSERT INTO searchresponse (uid, journal, publication, year, author1, address, author2, author3, citations) VALUES (";
        foreach ($recordArray[$row] as $key=>$value) {
            // add to the query as 'value',
            $sql .= "'".$value."',";
        }
        $sql = rtrim($sql, ','); // remove the comma from the final value entry
        $sql .= ");"; // end query, now has format ... VALUES ('value1','value2','value3');
        // echo $sql;
        mysqli_query($connect, $sql);
    }

    // set up query to select authors according to total citations
    /* $getAuthors = mysqli_query($connect, "SELECT
                                          SUM(citations)
                                          AS citations_sum, author1
                                          FROM searchresponse
                                          GROUP BY author1
                                          ORDER BY citations_sum DESC
                                          LIMIT 0,10"); */

    /* while ($row = mysqli_fetch_array($getAuthors)) {
        echo $row['author1'] . " " . $row['citations_sum'];
        echo "<br>";
    } */

    // create a 'result' variable to store the summed citation throughout the iterations
    $result = 0;

    // populate 'topcited' table, first loop $singleAuthors
    foreach ($singleAuthors as $value) {
        // loop $recordArray
        for ($i = 0; $i < count($recordArray); $i++) {
            // add citations into variable if author names match
            if (($recordArray[$i]['author1'] === $value) || ($recordArray[$i]['author2'] === $value) || ($recordArray[$i]['author3'] === $value)) {
                $result += ($recordArray[$i]['citations']);
            }
        }
        // insert current author and summed citation into table
        $sql = "INSERT INTO topcited (author, citations_sum) VALUES ('$value','$result')";
        mysqli_query($connect, $sql);
        // reset $result for next record
        $result = 0;
    }

    // select DB table, ignore duplicate authors, insert into $rows array ordered by citations sum
    $sql = "SELECT DISTINCT author, citations_sum FROM topcited ORDER BY citations_sum DESC";
    $rows = array();
    $query = mysqli_query($connect, $sql);

    while ($r = mysqli_fetch_assoc($query)) {
        $rows[] = $r;
    };

    // iterate array and make all author names uppercase (to merge same authors with names in different cases)
    for ($i = 0; $i < count($rows); $i++) {
        $rows[$i]['author'] = strtoupper($rows[$i]['author']);
    };

    // create temporary array to compare $rows with itself
    $tempArray = $rows;

    // tests
    echo "</br>WITH DUPLICATES (uppercase): </br></br>";
    print "<pre>\n";
    print_r($rows);
    print "</pre>";

    // if authors are same, sum citations to first instance and delete second instance
    // iterate original array sequentially
    for ($i = 0; $i < count($rows)-1; $i++) {
        // iterate temp array to compare with original, hence start at 1 (don't need to compare values at same index as we know they are the same)
        for ($j = $i + 1; $j < count($tempArray); $j++) {
            // check each author in $rows with $tempArray
            if ($rows[$i]['author'] === $tempArray[$j]['author']) {
                // sum citations for duplicate from both tables into $rows
                $rows[$i]['citations_sum'] += $rows[$j]['citations_sum'];
                // delete duplicate in $rows at position $j from location found in $tempArray
                unset($rows[$j]);
            };  // end if
        }; // end internal for-loop ($j)
    }; // end external for-loop ($i)

    // reset indices that were messed up by 'unset'
    $rows = array_values($rows);

    // make sure that data is sorted correctly (citations_sum, high -> low)
    usort($rows, function ($a, $b) {
        return $b['citations_sum'] - $a['citations_sum'];
    });

    // only include first ten elements in array
    $rows = array_slice($rows, 0, 10);

    echo "</br>NO DUPLICATES: </br></br>";
    print "<pre>\n";
    print_r($rows);
    print "</pre>";

    // turn top cited authors data into JSON file for displaying with JavaScript
    file_put_contents('data.json', json_encode($rows));

    // print table with suitable headers
    echo '<table id="citationsTable"
          <tr id="citationsRow">
          <th id="citationsHeader">Author</th>
          <th id="citationsHeader">Total Citations</th>
          </tr> >';

    // print data from $rows into table
    // for ($i = 0; $i < 10; $i++) {
    for ($i = 0; $i < 10; $i++) {
        echo "<tr id='citationsRow'>";
        echo "<td id='citationsData'>".$rows[$i]['author']."</td>";
        echo "<td id='citationsData'>".$rows[$i]['citations_sum']."</td>";
        echo "</tr>";
    };

    echo "</table>";

    mysqli_close($connect);

    $url = 'data.html';

    // clear the output buffer
    while (ob_get_status()) {
        ob_end_clean();
    }

    // no redirect
    header("Location: data.html");

?>