<?php namespace App;

use Artisaninweb\SoapWrapper\Facades\SoapWrapper;
use Illuminate\Http\RedirectResponse;

class SoapController {

    private $auth_response;
    private $cookie;
    private $search_client;
    private $search_response;
    protected $data;

    public function soapExchange() {

        // create SOAP client and add service details
        SoapWrapper::add(function ($service) {

            $service
                ->name('WoSAuthenticate')
                ->wsdl('http://search.webofknowledge.com/esti/wokmws/ws/WOKMWSAuthenticate?wsdl')
                ->trace(true)
                ->cache(WSDL_CACHE_NONE);
        });

        SoapWrapper::service('WoSAuthenticate', function($service) {
            // call authenticate() method to get SID cookie
            $auth_response = $service->call('authenticate', []);
            $cookie = $auth_response->return;
            // test for cookie return
            // print($cookie);
        });

        // create SOAP client and add service details
        $search_client = new SoapWrapper;
        $search_client::add(function ($service) {

            $service
                ->name('WoSSearch')
                ->wsdl('http://search.webofknowledge.com/esti/wokmws/ws/WokSearch?wsdl')
                ->cookie('SID', $cookie)
                ->trace(true)
                ->cache(WSDL_CACHE_NONE);
        });

        /* if (isset($auth_response->return)) {

            // if there is an SID returned then add it to the cookie attribute of the search client
            $search_client->__setCookie('SID', $cookie);
        } else {
            // route to relevant view to display throttle error
            return redirect('throttle');
        } */
    }

    public function setData($dataParams) {

        // provide data for search from form submit
        $data = [
            'queryParameters' => [
                'databaseId' => 'WOS',
                'userQuery' => $dataParams['journal1'] . $dataParams['journal2'] . $dataParams['journal3'] . $dataParams['title1'] . $dataParams['title2'] . $dataParams['title3'],
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
    }

    public function search($data) {

        $search_client->service('WoSSearch', function($service) {
            // call authenticate() method to get SID cookie
            $search_response = $service->call('search', $data);
        });
    }
}