<?php

    echo '<link rel="stylesheet" type="text/css" href="style.css"/>';
    echo '<link href="http://fonts.googleapis.com/css?family=Raleway:700" rel="stylesheet" type="text/css">
          <link href="http://fonts.googleapis.com/css?family=Lora:400,700" rel="stylesheet" type="text/css">
          <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>';

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

    ini_set('max_execution_time', 300);

    // set WSDL for authentication and create new SOAP client
    $auth_url  = "http://search.webofknowledge.com/esti/wokmws/ws/WOKMWSAuthenticate?wsdl";
    // array options are temporary and used to track request & response data in printout below (line 25)
    $auth_client = @new SoapClient($auth_url, array(
                     "trace" => 1,
                     "exceptions" => 0));
    // run 'authenticate' method and store as variable
    $auth_response = $auth_client->authenticate();

    // set WSDL for search and create new SOAP client
    $search_url = "http://search.webofknowledge.com/esti/wokmws/ws/WokSearch?wsdl";
    // array options are temporary and used to track request & response data in printout below (line 58)
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

    $queryType = $_POST["type"];
    $queryCategory = $_POST["category"];
    $sortType = $_POST["sort"];

    // pass in relevant parameters for search
    $search_array = array(
        'queryParameters' => array(
            'databaseId' => 'WOS',
            'userQuery' => $queryType.'='.$queryCategory,
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
    print "</pre>"; */

    /* echo "</br>SEARCH_CLIENT: </br>";
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
                <th>Batch Number</th>
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

    // iterate through all records, perform search for each 100 records and tabulate data
    for ($i = 1; $i <= $len; $i+=100) {

        // set search parameters for current iteration (first record = 1, 101, 201, 301 etc.)
        $search_array = array(
            'queryParameters' => array(
                'databaseId' => 'WOS',
                'userQuery' => $queryType.'='.$queryCategory,
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
            echo '<td>'.$i.'</td>';
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

            // for this iteration map all the values recorded into a temporary array variable, aRecord
            $arecord = array("uid"=>$uid,
                             "journal"=>$journal,
                             "publication"=>$publication,
                             "year"=>$year,
                             "author1"=>$author1,
                             "address"=>$address,
                             "author2"=>$author2,
                             "author3"=>$author3,
                             "citations"=>$citations );

            // pass the data from this iteration into the array variable 'record', after all iterations, each element in $record will be a single record or row of data for a single journal
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
    echo "</br>RECORD ARRAY: </br></br>";
    print "<pre>\n";
    print_r($recordArray);
    print "</pre";

    // populate citedArray from recordArray, only first ten records
    for ($i = 0; $i <= 10; $i++) {
        array_push($citedArray, ($recordArray[$i]['author1']));
        array_push($citedArray, ($recordArray[$i]['author1']));
        array_push($citedArray, ($recordArray[$i]['author1']));
    }

    $singleAuthors = array_unique($citedArray);

    echo "</br></br>CITED ARRAY: </br></br>";
    print "<pre>\n";
    print_r($singleAuthors);
    print "</pre";


    // =================================================================== //
    // ===================== CONNECT TO DATABASE ========================= //
    // =================================================================== //


    // create variable to store connection details
    $connect = mysqli_connect( "localhost", "root", $password );
    // check connection; quit if fail with error
    if (!$connect)
    {
        die('Could not connect: ' . mysqli_error());
        exit();
    }

    // check connection
    if ($connect->ping()) {
        printf ("</br></br>CONNECTED TO DATABASE!</br></br>");
    } else {
        printf ("ERROR: %s\n", $connect->error);
    }

    // select database to work with using connection variable
    mysqli_select_db($connect, 'wos');

    // loop over the array
    for ($row = 0; $row < count($recordArray); $row++) {
        $sql = "INSERT INTO searchresponse (uid, journal, publication, year, author1, address, author2, author3, citations) VALUES (";
        foreach ($recordArray[$row] as $key=>$value) {
            // add to the query
            $sql .= "'".$value."',";
        }
        $sql = rtrim($sql, ',');
        $sql .= ");";
        // echo $sql;
        mysqli_query($connect, $sql);
    }

    // echo "</br></br>";

    // set up query to select authors according to total citations
    /* $getAuthors = mysqli_query($connect, "SELECT
                                          SUM(citations)
                                          AS citations_sum, author1
                                          FROM searchresponse
                                          GROUP BY author1
                                          ORDER BY citations_sum DESC
                                          LIMIT 0,10"); */

    /* if ($getAuthors === FALSE) {
        echo mysql_error();
    }

    while ($row = mysqli_fetch_array($getAuthors)) {
        echo $row['author1'] . " " . $row['citations_sum'];
        echo "<br>";
    } */

    // create an array to store the summed citations
    $citations_sum = array();
    $result = 0;

    // populate 'topcited' table
    foreach ($singleAuthors as $value) {
        // insert authors into table
        // $sql = "INSERT INTO topcited (author) VALUES ('$value');";
        // mysqli_query($connect, $sql);
        for ($i = 0; $i < count($recordArray); $i++) {
            // insert citations into array if author names match
            if (($recordArray[$i]['author1'] === $value) || ($recordArray[$i]['author2'] === $value) || ($recordArray[$i]['author3'] === $value)) {
                $result += ($recordArray[$i]['citations']);
                // array_push($citations_sum, ($recordArray[$i]['citations']));
            }
        }
        // array_push($citations_sum, $result);
        $sql2 = "INSERT INTO topcited (author, citations_sum) VALUES ('$value','$result')";
        mysqli_query($connect, $sql2);
        $result = 0;
    }

    echo "</br></br>CITATIONS_SUM: </br></br>";
    print "<pre>\n";
    print_r($citations_sum);
    print "</pre";

    // populate database table with summed citations
    /* foreach ($citations_sum as $value) {
        $sql = "INSERT INTO topcited (citations_sum) VALUES ('$value')";
        mysqli_query($connect, $sql);
    } */

    mysqli_close($connect);

?>