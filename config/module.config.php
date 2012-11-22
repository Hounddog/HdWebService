<?php
return array(
    'service_manager' => array(
        'invokables' => array(
            'HdWebService\Client'      => 'HdWebservice\Client',
            'HdWebService\HttpClient'  => 'HdWebService\Http\Client',
        ),
    ),
);
