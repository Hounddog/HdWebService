<?php

namespace HdWebService;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\Filter\Word\UnderscoreToCamelCase;

class Client implements ServiceManagerAwareInterface, EventManagerAwareInterface, Clientinterface
{
    /*
     * EventManager
     */
    protected $events;

    /**
     * Http\Client
     *
     * @var Client
     */
    private $httpClient;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function api($resource)
    {
        $filter = new UnderscoreToCamelCase();
        $resource = $filter->filter($resource);

        $em = $this->getEventManager();
        $em->trigger('api', $this);

        $service = $this->getServiceManager()->get($this->getNamespace() . '\\' . $resource);
        $service->setClient($this);
        return $service;
    }

    /**
     * Authenticate a user for all next requests
     *
     * @param string      $tokenOrLogin  GitHub private token/username/client ID
     * @param null|string $password      GitHub password/secret
     * @param string $authMethod
     */
    public function authenticate($authMethod, $tokenOrLogin, $password = null)
    {
        $filter = new UnderscoreToCamelCase();
        $authMethod = $filter->filter($authMethod);

        $sm = $this->getServiceManager();
        $authListener = $sm->get('EdpGithub\Listener\Auth\\' . $authMethod);
        $authListener->setOptions(
            array(
                'tokenOrLogin' => $tokenOrLogin,
                'password'     => $password
            )
        );
        $this->getHttpClient()->getEventManager()->attachAggregate($authListener);
    }



    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if(null === $this->httpClient) {
           /*$this->httpClient = $this->getServiceManager()->get('EdpGithub\HttpClient');
           $errorListener = $this->getServiceManager()->get('EdpGithub\Listener\Error');
           $eventManager = $this->httpClient->getEventManager();
           $eventManager->attachAggregate($errorListener);
           */

        }
        return $this->httpClient;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set HttpClient
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * Set Event Manager
     *
     * @param EventManagerInterface $events
     * @return Client
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
     * @return EventManager
     */
    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}
