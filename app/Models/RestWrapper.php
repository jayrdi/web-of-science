<?php namespace App\Models;

class RestWrapper {

	// how many results pages required to scroll through
	public $pages;
	// array to store all data returned
	public $projects = [];
	// create new array from $projects that only contains data from years
    // specified by user in Time Span in form input
    public $timeArrayFunds = [];
    // create new arrays for previous 2, 5 and 10 years for dropdown menu
    public $tenArrayFunds = [];
    public $fiveArrayFunds = [];
    public $twoArrayFunds = [];
	
	// get data from GtR using their REST API web service
	// determines number of pages of results for full search with iterateGtrSearch below
	public function restExchange($search1, $search2, $search3) {

		// REST HTTP GET Request searching for people associated with keywords (term)
	    $url = "http://gtr.rcuk.ac.uk/search/project.json?term=" . $search1 . $search2 . $search3 . "&fetchSize=100";

	    // save results to a variable
	    @$response = file_get_contents($url);

	    // convert JSON to PHP variable
	    $json = json_decode($response, true);

	    // store total number of projects returned by query for iteration count
	    $numProjects = $json['resourceHitCount'][0]['count'];

	    // total number of results pages
	    $pages = ceil($numProjects/100);
	    $this->pages = $pages;
	}

	public function iterateGtrSearch($search1, $search2, $search3) {

		// iterate data loading next page each time and adding new results to array
	    for($i = 1; $i <= $this->pages; $i++) {

	        // set page number to current iteration number
	        $page = $i;
	        // GET request each time with next page number
	        $thisUrl = "http://gtr.rcuk.ac.uk/search/project.json?term=" . $search1 . $search2 . $search3 . "&fetchSize=100&page=" . $page;
	        $thisResponse = file_get_contents($thisUrl);
	        $thisJson = json_decode($thisResponse, true);

	        // iterate results
	        foreach($thisJson['results'] as $project) {
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
	          array_push($this->projects, $project) ;
	        };
	    };

	    // need to replace single quotes to avoid char escape
	    for ($i = 0; $i < count($this->projects); $i++) {
	        $this->projects[$i]['author'] = str_replace("'", "", $this->projects[$i]['author']);
	        $this->projects[$i]['title'] = str_replace("'", "", $this->projects[$i]['title']);
	    };
	}

	// sum funds for same people
	public function sumFunds() {

		$count = 0;
	    $length = count($this->projects);

	    // iterate each person in $projects, ignore last value otherwise would end up comparing it
	    // to itself in inner loop
	    for ($i = 0; $i < ($length - 1); $i++) {
	        // iterate each person in $projects a step ahead of the outer loop, compare each person
	        // with every other person in array
	        for ($j = ($i + 1); $j < $length; $j++) {
	            // if there is a match between person IDs then:
	            if ($this->projects[$i]['personID'] === $this->projects[$j]['personID']) {
	                // add second citations value to first
	                $this->projects[$i]['funds'] += $this->projects[$j]['funds'];
	                // remove second instance
	                unset($this->projects[$j]);
	                // add to a variable the number of times 'unset' has been used for this iteration of $i
	                $count++;
	            }; // end if
	        }; // end inner loop ($j)
	        // decrease length of inner loop by $count, i.e. the number of elements that were removed in the last iteration, to make the length of the inner loop correct
	        $length -= $count;
	        // reset $count for next iteration of $i
	        $count = 0;
	        // reset indices
	        $this->projects = array_values($this->projects);
	    }; // end outer loop ($i)
	}

	// separate funds data into arrays according to time periods
	public function timedFunds($start, $end) {

		for ($i = 0; $i < count($this->projects); $i++) {
	        // if the publication year of the current record is less than or equal to the end of the time span
	        // AND greater than or equal to the start of the time span then include the full record in $timeArrayFunds
	        if (($this->projects[$i]['year'] <= $end) && ($this->projects[$i]['year'] >= $start)) {
	            array_push($this->timeArrayFunds, $this->projects[$i]);
	        }
	        // 10 year data
	        if ($this->projects[$i]['year'] >= (date("Y")-10)) {
	            array_push($this->tenArrayFunds, $this->projects[$i]);
	        }
	        // 5 year data
	        if ($this->projects[$i]['year'] >= (date("Y")-5)) {
	            array_push($this->fiveArrayFunds, $this->projects[$i]);
	        }
	        // 2 year data
	        if ($this->projects[$i]['year'] >= (date("Y")-2)) {
	            array_push($this->twoArrayFunds, $this->projects[$i]);
	        }
	    };
	}

	// make funds more readable as they are generally in millions
	public function readableFunds() {

		for($i = 0; $i < count($this->projects); $i++) {
	        $this->projects[$i]['funds'] = ($this->projects[$i]['funds']/1000000);
	    }
	    for($i = 0; $i < count($this->timeArrayFunds); $i++) {
	        $this->timeArrayFunds[$i]['funds'] = ($this->timeArrayFunds[$i]['funds']/1000000);
	    }
	    for($i = 0; $i < count($this->tenArrayFunds); $i++) {
	        $this->tenArrayFunds[$i]['funds'] = ($this->tenArrayFunds[$i]['funds']/1000000);
	    }
	    for($i = 0; $i < count($this->fiveArrayFunds); $i++) {
	        $this->fiveArrayFunds[$i]['funds'] = ($this->fiveArrayFunds[$i]['funds']/1000000);
	    }
	    for($i = 0; $i < count($this->twoArrayFunds); $i++) {
	        $this->twoArrayFunds[$i]['funds'] = ($this->twoArrayFunds[$i]['funds']/1000000);
	    }  
	}

	public function orderData($arrayData, $arrayType, $sortBy) {
        // sort array according to $sortBy
        // make sure that data is sorted correctly (value, high -> low)
        usort($this->$arrayType, function ($a, $b) use ($sortBy){
            return $b[$sortBy] - $a[$sortBy];
        });
    }
}

?>