<?php

namespace HdWebService\Http;

use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Curl;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;

use HdWebService\Options\ModuleOptionsInterface;

class Client implements EventManagerAwareInterface, ClientInterface
{
    /*
     * EventManager
     */
    protected $events;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var Curl
     */
    protected $httpAdapter;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Request
     */
    protected $request;

    protected $options;

    /**
     * Get Request
     *
     * @param  string $path
     * @param  array  $parameters
     * @param  array  $headers
     * @return Zend\Http\Response
     */
    public function get($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'GET', $headers);
    }

    /**
     * Send POST Request
     *
     * @param  string $path
     * @param  array  $parameters
     * @param  array  $headers
     * @return Zend\Http\Response
     */
    public function post($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'POST', $headers);
    }

    /**
     * Send Delete Request
     *
     * @param  string $path
     * @param  array  $parameters
     * @param  array  $headers
     * @return Zend\Http\Response
     */
    public function delete($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'DELETE', $headers);
    }

    /**
     * Send Put Request
     *
     * @param  string $path
     * @param  array  $headers
     * @return Zend\Http\Response
     */
    public function put($path, array $headers = array())
    {
        return $this->request($path, array(), 'PUT', $headers);
    }

    /**
     * Send Request
     *
     * @param  string $path
     * @param  array  $parameters
     * @param  string $httpMethod
     * @param  array  $headers
     * @return Zend\Http\Response
     */
    public function request($path, array $parameters = array(), $httpMethod = 'GET', array $headers = array())
    {
        $client = $this->getHttpClient($path);
        $request = $client->getRequest();
        $query = $request->getQuery();

        foreach($parameters as $key => $value) {
            $query->set($key, $value);
        }
        //Trigger Pre Send Event to modify Request Object
        $this->getEventManager()->trigger('pre.send', $request);

        $response = $client->dispatch($request);

        //Trigger Post Send to Modify/Validate Response object
        $this->getEventManager()->trigger('post.send', $response);

        $this->response = $response;
        $this->request = $request;

        return $response;
    }

    /**
     * Get Http Client
     *
     * @param  string $path
     * @return HttpClient
     */
    public function getHttpClient($path)
    {
        if(null === $this->httpClient) {
            $this->httpClient = new HttpClient();
            $this->httpClient->setAdapter($this->getHttpAdapter());
        }
        $this->httpClient->setUri($this->options->baseUrl . $path);
        return $this->httpClient;
    }

    /**
     * Get Http Adpter
     * @return
     */
    public function getHttpAdapter()
    {
        if(null === $this->httpAdapter){
            $this->httpAdapter = new Curl();
            $this->httpAdapter->setOptions(array(
                'sslverifypeer' =>false,
            ));
        }
        return $this->httpAdapter;
    }

    public function setOptions(ModuleOptionsInterface $options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set Event Manager
     *
     * @param  EventManagerInterface $events
     * @return HybridAuth
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->events = $events;
        return $this;
    }

    /**
     * Get Event Manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
