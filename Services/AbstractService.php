<?php
namespace Kdrmklabs\Bundle\TicketBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

class AbstractService {

    protected $serviceContainer;

    public function __construct($serviceContainer){
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @return Container
     */
    public function getServiceContainer() {
        return $this->serviceContainer;
    }

    /**
     * @return EntityManager
     */
    public function getDoctrineManager() {
        return $this->serviceContainer->get('doctrine')->getManager();
    }

    /**
     * @param $repositoryName
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository($repositoryName){
        return $this->getDoctrineManager()->getRepository($repositoryName);
    }
    
    public function trans($message){
        return $this->serviceContainer->get('translator')->trans($message);
    }
}