<?php

namespace HdWebService\Api;


interface ApiInterface
{

    protected function get($path, array $parameters = array(), $requestHeaders = array());

    public function getClient();

    public function setClient($client);
}
