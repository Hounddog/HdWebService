<?php

namespace ZfcUser\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements ModuleOptionsInterface;
{
    /**
     * Turn off strict options mode
     */
    protected $serviceUrl = '';

    protected $timeOut = 10;

    protected $apiVersion = '';


    /**
     * @return string
     */
    public function getServiceUrl() {
        return $this->serviceUrl;
    }

    /**
     * @param string $serviceUrl
     */
    public function setServiceUrl($serviceUrl) {
        $this->serviceUrl = $serviceUrl;

        return $this;
    }


    /**
     * @return string
     */
    public function getTimeOut() {
        return $this->timeOut;
    }

    /**
     * @param string $timeOut
     */
    public function setTimeOut($timeOut) {
        $this->timeOut = $timeOut;

        return $this;
    }


    /**
     * @return string
     */
    public function getApiVersion() {
        return $this->apiVersion;
    }

    /**
     * @param string $apiVersion
     */
    public function setApiVersion($apiVersion) {
        $this->apiVersion = $apiVersion;

        return $this;
    }
}
