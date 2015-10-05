<?php namespace App\Http\Controllers;

use Artisaninweb\SoapWrapper\Facades\SoapWrapper;

class SoapController {

    public static function wos($url, $data) {
        // add new service to the wrapper
        SoapWrapper::add(function ($service) {
            $service
                ->name('wos')
                ->wsdl($url)
                ->trace(true)
                ->header('Cache-Control: no-cache')
                ->cookie('SID')
                ->cache(WSDL_CACHE_NONE)
                ->options();
        })
    }
};