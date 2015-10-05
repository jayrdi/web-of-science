<?php namespace App\Models;

use SoapClient;
use Illuminate\Http\RedirectResponse;
use Redirect;

class SoapWrapper {

    // SOAP exchange for WoS authentication
    public $auth_client;
    public $auth_response;
    // SOAP exchange for WoS search
    public $search_client;
    public $search_response;
    // number of records found
    public $len;
    // XML data to send as SOAP Request to WoS
    public $data;
    // array to store records
    public $records = [];
    
    // function to perform SOAP exchange with WoS API
    public function soapExchange($data) {

        try {
            // set WSDL for authentication and create new SOAP client
            $auth_url = "http://search.webofknowledge.com/esti/wokmws/ws/WOKMWSAuthenticate?wsdl";

            // set WSDL for search and create new SOAP client
            $search_url = "http://search.webofknowledge.com/esti/wokmws/ws/WokSearch?wsdl";

            // array options are temporary and used to track request & response data
            $auth_client = @new SoapClient($auth_url);
            $this->auth_client = $auth_client;

            // array options are temporary and used to track request & response data
            $search_client = @new SoapClient($search_url);

            // run 'authenticate' method and store as variable
            $auth_response = $auth_client->authenticate();
            $this->auth_response = $auth_response;

            // add SID (SessionID) returned from authenticate() to cookie of search client
            $search_client->__setCookie('SID', $auth_response->return);
            $this->search_client = $search_client;

            // put data into suitable format for API search
            $data = [
                'queryParameters' => [
                    'databaseId' => 'WOS',
                    'userQuery' => $data['journal1'] . $data['journal2'] . $data['journal3'] . $data['title1'] . $data['title2'] . $data['title3'],
                    'editions' => [
                        'collection' => 'WOS',
                        'edition' => 'SCI'
                    ],
                    'timeSpan' => [
                        'begin' => '1970-01-01',
                        'end' => (date('Y-m-d'))
                    ],
                    'queryLanguage' => 'en'
                ],
                'retrieveParameters' => [
                    'count' => '100',
                    'sortField' => [
                        [
                            'name' => 'TC',
                            'sort' => 'D'
                        ]
                    ],
                    'firstRecord' => '1'
                ]
            ];
            $this->data = $data;

            // perform search on data
            try {
                $search_response = $search_client->search($data);
                $this->search_response = $search_response;
            } catch (Exception $e) {  
                echo $e->getMessage();
            };

            // number of records found by search, used to finish loop (check if no records first)
            // if soap fault, i.e. no recordsFound then set $len to null to avoid undefined variable
            if (isset($search_response->return->recordsFound)) { 
                $len = $search_response->return->recordsFound;
                $this->len = $len;
            } else {
                $len = 0;
                $this->len = $len;
            }

            // check if there has been a soap fault with the query OR if there are 0 records for the search
            if (is_soap_fault($search_client->__getLastResponse()) || $len == 0) {
                echo "NO RECORDS";
                // return view('pages.norecords');
            };

        } catch (\SoapFault $e) {
            echo "THROTTLE SERVER OVERLOAD";
            // return view('pages.throttle');
        }
    }

    // function to iterate and store all relevant data returned from SOAP exchange
    public function iterateWosSearch($submit) {

        // variable to store average time/record retrieval (based on calculations)
        $avg = 0.08;
        // create a variable to store time for loading screen
        $timeDecimal = (round((($submit->len)*$avg), 2));
        // create an array to represent citation values to ignore, i.e. not interested
        // in any publications with less than 4 citations
        $ignore = array(0, 1, 2, 3);
        // create a counter variable to use for progress bar
        $counter = 1;

        // turn time into readable format (mins & secs, rather than just secs)
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

        ob_flush(); // flush anything from the header output buffer
        flush(); // send contents so far to the browser

        // iterate through all records, perform search for each 100 records (max per call)
        // and tabulate data
        for ($i = 1; $i <= ($submit->len); $i+=100) {

            // set search parameters for current iteration (first record = 1, 101, 201, 301 etc.)
            $submit->data['retrieveParameters']['firstRecord'] = $i;

            // gather search response for current iteration
            try {
                $submit->search_response = $submit->search_client->search($submit->data);
            } catch (Exception $e) {  
                echo $e->getMessage(); 
            };

            // turn Soap Client object from current response into XML element
            $xml = simplexml_load_string($submit->search_response->return->records);

            // save variable names for citations, country and publication year
            $citations = "";
            $pubyear = "";
            $country = "";

            // iterate through current data set and store to $records array
            foreach($xml->REC as $record) {

                // create authors array for this REC data
                $authors = [];

                // echo "<script type='text/javascript'>
                //           setRecord(" .$counter. ");
                //       </script>";

                ob_flush(); // flush anything from the header output buffer
                flush(); // send contents so far to the browser

                // authors
                foreach($record->static_data->summary->names->name as $thisAuthor) {
                    array_push($authors, $thisAuthor->full_name);
                }

                // country (if exists)
                if (isset($record->static_data->item->reprint_contact->address_spec->country)) {
                    $country = (string)$record->static_data->item->reprint_contact->address_spec->country;
                } else {
                    $country = "";
                };
                
                // set current publication year
                $pubyear = (string)$record->static_data->summary->pub_info->attributes()->pubyear;

                // number of citations, if 0-3 ($ignore array) then 'break' out of loop entirely
                if (!in_array($record->dynamic_data->citation_related->tc_list->silo_tc->attributes(), $ignore)) {
                    $citations = (string)$record->dynamic_data->citation_related->tc_list->silo_tc->attributes();
                } else {
                    // break from both loops
                    break 2;
                };

                // for this iteration map all the values recorded into a temporary array variable,
                // $aRecord (equivalent to one row of data in table)
                $arecord = [    
                                "authors"   => $authors,
                                "ID"        => "",
                                "pubyear"   => $pubyear,
                                "country"   => $country,
                                "citations" => $citations
                           ];

                // pass the data from this iteration into the array variable '$records',
                // after all iterations, each element in $records will be a single
                // record or row of data for a single journal
                array_push($submit->records, $arecord) ;
            }
        // increment for next record
        $counter+=100;
        }

        // need to replace some charas to help remove duplicates
        for ($i = 0; $i < count($submit->records); $i++) {
            foreach ($submit->records[$i]['authors'] as &$value) { // '&' = reference to variable so can be modified
                $value = str_replace("'", "", $value);
                $value = str_replace(".", " ", $value);
                $value = str_replace(". ", " ", $value);
            }
        };
    }
};