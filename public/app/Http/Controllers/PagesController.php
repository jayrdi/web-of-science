<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use App\SoapController;
use App\Models\SearchData;
use App\Models\SoapWrapper;
use App\Models\RestWrapper;
use App\Models\ScopusWrapper;
use App\Models\DataSort;
use App\Models\BubbleChartCompatible;
use DB;
use View;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as JavaScript;

class PagesController extends Controller {

	public function __construct()
	{
		$this->middleware('guest');
	}

	// method index returns view 'home' (resources/views/home.blade.php)
	public function index()
	{
		return view('pages.home');
	}

	// method about returns view 'about' (resources/views/about.blade.php)
	public function about()
	{
		return view('pages.about');
	}

	// method about returns view 'throttle' (resources/views/throttle.blade.php)
	public function throttleError()
	{
		return view('pages.throttle');
	}

	// method about returns view 'norecords' (resources/views/norecords.blade.php)
	public function noRecordsError()
	{
		return view('pages.norecords');
	}

	// method process returns view 'data' (resources/views/data.blade.php)
	public function process()
	{
		// set processing time for browser before timeout
	    ini_set('max_execution_time', 3600);
	    // override default PHP memory limit
	    ini_set('memory_limit', '-1');

		// fetch all inputs from the submitted form //
		$input = Request::all();

		// create new objects to store params passed in from form
		$dataParams = new SearchData;
		$searchParams = new SearchData;

		// search type for journals (publication name)
    	$queryType1 = "SO"; 

		// keyword(s)
	    // check if journal1 field has been populated, if not entered then set to blank
	    if ($input["journal1"]) {
	        $queryJournal1 = $input["journal1"];
	        $queryJournal1 = $queryType1. "=" .$queryJournal1;
	        // for search params
	        $searchJournal1 = $input["journal1"];
	    } else {
	        $queryJournal1 = "";
	        $searchJournal1 = "";
	    };

	    // check if journal2 field has been populated, if not entered then set to blank
	    if (isset($input["journal2"])) {
	        $queryJournal2 = $input["journal2"];
	        $queryJournal2 = " OR " .$queryType1. "=" .$queryJournal2;
	        // for search params
	        $searchJournal2 = $input["journal2"];
	    } else {
	        $queryJournal2 = "";
	        $searchJournal2 = "";
	    };

	    // check if journal3 field has been populated
	    if (isset($input["journal3"])) {
	        $queryJournal3 = $input["journal3"];
	        $queryJournal3 = " OR " .$queryType1. "=" .$queryJournal3;
	        // for search params
	        $searchJournal3 = $input["journal3"];
	    } else {
	        $queryJournal3 = "";
	        $searchJournal3 = "";
	    };

	    // search type for titles
	    $queryType2 = "TI";

	    // keyword(s)
	    // check if title1 field has been populated
	    if (($input["title1"]) && ($input["journal1"])) {
	        $queryTitle1 = $input["title1"];
	        $queryTitle1 = " AND " .$queryType2. "=" .$queryTitle1;
	        // for search params
	        $searchTitle1 = $input["title1"];
	    } elseif (($input["title1"]) && (!($input["journal1"]))) {
	        $queryTitle1 = $input["title1"];
	        $queryTitle1 = $queryType2. "=" .$queryTitle1;
	        // for search params
	        $searchTitle1 = $input["title1"];
	    } else {
	        $queryTitle1 = "";
	    };

	    // check if title2 field has been populated
	    if (isset($input["title2"])) {
	        $queryTitle2 = $input["title2"];
	        $queryTitle2 = " OR " .$queryType2. "=" .$queryTitle2;
	        // for search params
	        $searchTitle2 = $input["title2"];
	    } else {
	        $queryTitle2 = "";
	        $searchTitle2 = "";
	    };

	    // check if title3 field has been populated
	    if (isset($input["title3"])) {
	        $queryTitle3 = $input["title3"];
	        $queryTitle3 = " OR " .$queryType2. "=" .$queryTitle3;
	        // for search params
	        $searchTitle3 = $input["title3"];
	    } else {
	        $queryTitle3 = "";
	        $searchTitle3 = "";
	    };

	    // replace any whitespace with %20 (url encoding)
	    $queryTitle1 = str_replace(" ", "%20", $queryTitle1);
	    $queryTitle2 = str_replace(" ", "%20", $queryTitle2);
	    $queryTitle3 = str_replace(" ", "%20", $queryTitle3);
	    
	    // sort type
	    $sortType = "TC";

	    // check if timespan fields have been populated
	    if (($input["timeStart"] != "Select") && ($input["timeEnd"] != "Select")) {
	        $timeStart = $input["timeStart"];
	        $timeEnd = $input["timeEnd"];
	    } elseif (($input["timeStart"] != "Select") && ($input["timeEnd"] == "Select")) {
	        $timeStart = $input["timeStart"];
	        $timeEnd = date("Y");
	    } elseif (($input["timeStart"] == "Select") && ($input["timeEnd"] != "Select")) {
	        $timeStart = "1970";
	        $timeEnd = $input["timeEnd"];
	    } else {
	        $timeStart = "1970";
	        $timeEnd = date("Y");
	    };

	    // store the relevant data in the dataParams object
	    $dataParams = [
	    			'journal1' => $queryJournal1,
	    			'journal2' => $queryJournal2,
	    			'journal3' => $queryJournal3,
	    			'title1'   => $queryTitle1,
	    			'title2'   => $queryTitle2,
	    			'title3'   => $queryTitle3,
	    			'from'     => $timeStart,
	    			'to'       => $timeEnd
	    		];

	    // create an array to store all the search parameters to display alongside graphs
	    $searchParams = [
	    					'journal1' => $searchJournal1,
	                        'journal2' => $searchJournal2,
	                        'journal3' => $searchJournal3,
	                        'title1'   => $searchTitle1,
	                        'title2'   => $searchTitle2,
	                        'title3'   => $searchTitle3,
	                        'from'     => $timeStart,
	                        'to'       => $timeEnd
	                    ];

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
	        $keyword2 = " OR " . $keyword2;
	    } else {
	        $keyword2 = "";
	    };

	    // check if title3 field has been populated
	    if (isset($_POST["title3"])) {
	        $keyword3 = $_POST["title3"];
	        $keyword3 = " OR " . $keyword3;
	    } else {
	        $keyword3 = "";
	    };

	    // replace any whitespace with %20 (url encoding)
	    // $keyword1 = str_replace(" ", "%20", $keyword1);
	    // $keyword2 = str_replace(" ", "%20", $keyword2);
	    // $keyword3 = str_replace(" ", "%20", $keyword3);
	    $keyword1 = urlencode($keyword1);
	    $keyword2 = urlencode($keyword2);
	    $keyword3 = urlencode($keyword3);

	    // create new SoapWrapper object to get SOAP data from WoS
	    //$soap = new SoapWrapper;

	    // create new RestWrapper object to get REST data from GtR
	    $rest = new RestWrapper;

	    // create new ScopusWrapper object to get REST data from Scopus
	    $scopus = new ScopusWrapper;

	    // authenticate WoS search to get SID; get initial data (SoapWrapper function)
		//$soap->soapExchange($dataParams);

		// perform REST exchange with GtR API
		$rest->restExchange($keyword1, $keyword2, $keyword3);

		// perform REST exchange with Scopus API
		$scopus->scopusWebExchange($keyword1, $keyword2, $keyword3);

		// perform iterativeWosSearch (SoapWrapper class) to get all records from WoS
		//$soap->iterateWosSearch($soap);

		// perform iterateGtrSearch (RestWrapper class) to get all records from GtR
		$rest->iterateGtrSearch($keyword1, $keyword2, $keyword3);

		// sum the funds for duplicate people in data
		$rest->sumFunds();

		// separate the data into the different arrays for time periods
		$rest->timedFunds($timeStart, $timeEnd);

		// sort the data by funds
		$rest->orderData($rest, 'projects', 'funds');
		$rest->orderData($rest, 'timeArrayFunds', 'funds');
		$rest->orderData($rest, 'tenArrayFunds', 'funds');
		$rest->orderData($rest, 'fiveArrayFunds', 'funds');
		$rest->orderData($rest, 'twoArrayFunds', 'funds');

		// make the funds more readable as they are generally in the millions
		$rest->readableFunds();

		// create a new DataSort object to store all the data for the WoS graphs
		//$wosData = new DataSort($soap->records);

		// create a new DataSort object to store all the data for the Scopus graphs
		$scopusData = new DataSort($scopus->scopusData);

		// assign a determined value to each author (WoS)
		//$wosData->assignValues();

		// assign a determined value to each author (Scopus)
		$scopusData->assignValues(); 

		// run function to create tables for db for WoS data
		//$wosData->createTables();

		// run function to create tables for db for Scopus data
		$scopusData->createTables();

		// populate tables with data from $wosData->records
		//$wosData->populateTables($wosData->records, $timeStart, $timeEnd);

		// populate tables with data from $scopusData->records
		$scopusData->populateTables($scopusData->records, $timeStart, $timeEnd);

		// sum the citations values in all the tables for duplicate authors (WoS)
		//$wosData->sumCitesAll();
		//$wosData->sumCitesUser();
		//$wosData->sumCitesTen();
		//$wosData->sumCitesFive();
		//$wosData->sumCitesTwo();
		// sum the weighted values in all the tables for duplicate authors
		//$wosData->sumValuesAll();

		// sum the citations values in all the tables for duplicate authors (Scopus)
		$scopusData->sumCitesAll();
		$scopusData->sumCitesUser();
		$scopusData->sumCitesTen();
		$scopusData->sumCitesFive();
		$scopusData->sumCitesTwo();
		// sum the weighted values in all the tables for duplicate authors
		$scopusData->sumValuesAll();

		// return processed data back from MySQL to PHP arrays & convert to associative arrays (WoS)
		//$wosData->allArray = json_decode(json_encode($wosData->pullData('searchresponse')), true);
		//$wosData->timeArray = json_decode(json_encode($wosData->pullData('userdefined')), true);
		//$wosData->tenArray = json_decode(json_encode($wosData->pullData('tenyear')), true);
		//$wosData->fiveArray = json_decode(json_encode($wosData->pullData('fiveyear')), true);
		//$wosData->twoArray = json_decode(json_encode($wosData->pullData('twoyear')), true);
		//$wosData->valueArray = json_decode(json_encode($wosData->pullData('searchresponse')), true);

		// return processed data back from MySQL to PHP arrays & convert to associative arrays (Scopus)
		$scopusData->allArray = json_decode(json_encode($scopusData->pullData('searchresponse')), true);
		$scopusData->timeArray = json_decode(json_encode($scopusData->pullData('userdefined')), true);
		$scopusData->tenArray = json_decode(json_encode($scopusData->pullData('tenyear')), true);
		$scopusData->fiveArray = json_decode(json_encode($scopusData->pullData('fiveyear')), true);
		$scopusData->twoArray = json_decode(json_encode($scopusData->pullData('twoyear')), true);
		$scopusData->valueArray = json_decode(json_encode($scopusData->pullData('searchresponse')), true);

		// sort data by highest cited first (WoS)
		//$wosData->sortData($wosData, 'allArray', 'citations');
		//$wosData->sortData($wosData, 'timeArray', 'citations');
		//$wosData->sortData($wosData, 'tenArray', 'citations');
		//$wosData->sortData($wosData, 'fiveArray', 'citations');
		//$wosData->sortData($wosData, 'twoArray', 'citations');
		//$wosData->sortData($wosData, 'valueArray', 'weight');

		// sort data by highest cited first (Scopus)
		$scopusData->sortData($scopusData, 'allArray', 'citations');
		$scopusData->sortData($scopusData, 'timeArray', 'citations');
		$scopusData->sortData($scopusData, 'tenArray', 'citations');
		$scopusData->sortData($scopusData, 'fiveArray', 'citations');
		$scopusData->sortData($scopusData, 'twoArray', 'citations');
		$scopusData->sortData($scopusData, 'valueArray', 'weight');

	    // sort value data so that it only has 2 values for bubble chart (author & value)
	    //$wosData->removeAttributes($wosData->valueArray);
	    $scopusData->removeAttributes($scopusData->valueArray);

	    // for data to work in d3 as bubble chart, needs to have parent and children
	    //$wosData->valuesJSON = [];
	    //$wosData->valuesJSON["name"] = "rankedData";
	    //$wosData->valuesJSON["children"] = $wosData->valueArray;
	    // SCOPUS
		$scopusData->valuesJSON = [];
	    $scopusData->valuesJSON["name"] = "rankedData";
	    $scopusData->valuesJSON["children"] = $scopusData->valueArray;

	    // JSON encode cited data for use in jQuery (WoS)
	    //$allCited = json_encode($wosData->allArray);
	    //$userCited = json_encode($wosData->timeArray);
	    //$tenCited = json_encode($wosData->tenArray);
	    //$fiveCited = json_encode($wosData->fiveArray);
	    //$twoCited = json_encode($wosData->twoArray);

	    // JSON encode values data for use in jQuery
	    //$valueData = json_encode($wosData->valuesJSON);

		// JSON encode cited data for use in jQuery (Scopus)
	    $allCited = json_encode($scopusData->allArray);
	    $userCited = json_encode($scopusData->timeArray);
	    $tenCited = json_encode($scopusData->tenArray);
	    $fiveCited = json_encode($scopusData->fiveArray);
	    $twoCited = json_encode($scopusData->twoArray);

	    // JSON encode values data for use in jQuery
	    $valueData = json_encode($scopusData->valuesJSON);

	    // JSON encode funds data for use in jQuery
	    $allFunds = json_encode($rest->projects);
	    $userFunds = json_encode($rest->timeArrayFunds);
	    $tenFunds = json_encode($rest->tenArrayFunds);
	    $fiveFunds = json_encode($rest->fiveArrayFunds);
	    $twoFunds = json_encode($rest->twoArrayFunds);

	    //echo "</br>BUBBLE CHART VALUES:</br>";
		//print "<pre>\n";
		//print_r($valueData);
		//print "</pre>";
	    
	    // pass data to JavaScript (uses https://github.com/laracasts/PHP-Vars-To-Js-Transformer)
		JavaScript::put([
						    'allCited' => $allCited,
						    'userCited' => $userCited,
						    'tenCited' => $tenCited,
						    'fiveCited' => $fiveCited,
						    'twoCited' => $twoCited,
						    'valueData' => $valueData,
						    'allFunded' => $allFunds,
						    'userFunded' => $userFunds,
						    'tenFunded' => $tenFunds,
						    'fiveFunded' => $fiveFunds,
						    'twoFunded' => $twoFunds,
						    'searchData' => $searchParams
						]);

		return View::make('pages.data');
	}
}