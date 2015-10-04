<?php

// perform GET request on root and call method 'index' on the PagesController class
// (app/Http/Controllers/PagesController.php)
Route::get('/', 'PagesController@index');

// perform GET request on 'about' and call method 'about' on the PagesController class
Route::get('about', 'PagesController@about');

// route for Throttle server error
Route::get('throttle', ['as' => 'throttle', 'uses' => 'PagesController@throttleError']);

// perform POST on 'data' and call method 'process' on the PagesController class
Route::post('data', 'PagesController@process');

// route for no records found error
Route::get('norecords', ['as' => 'norecords', 'uses' => 'PagesController@noRecordsError']);

?>