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

    ini_set('max_execution_time', 1200);

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

        // iterate through current data set and tabulate
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
            } else echo '<td>'."".'</td>';
            // second author
            if (isset($record->static_data->summary->names->name[1]->full_name)) {
                $author2 = (string)$record->static_data->summary->names->name[1]->full_name;
                echo '<td>'.$author2.'</td>';
            } else echo '<td>'."".'</td>';
            // third author
            if (isset($record->static_data->summary->names->name[2]->full_name)) {
                $author3 = (string)$record->static_data->summary->names->name[2]->full_name;
                echo '<td>'.$author3.'</td>';
            } else echo '<td>'."".'</td>';
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

    // this array has taken all the data we need from the SimpleXMLElement and is ready to be passed into the database
    echo "</br>RECORD ARRAY: </br></br>";
    print "<pre>\n";
    print_r($recordArray);
    print "</pre";


    // file_put_contents("wosData.json", $json);


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

    $arrayString = mysql_escape_string(serialize($recordArray));

    echo "</br>STRING FOR SQL: </br></br>";
    print "<pre>\n";
    print_r($arrayString);
    print "</pre>";

    mysqli_query($connect, "INSERT INTO searchresponse (uid, journal, publication, year, author1, address, author2, author3, citations) VALUES ('$arrayString')");

?>