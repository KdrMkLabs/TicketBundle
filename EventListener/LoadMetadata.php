<?php

namespace Kdrmklabs\Bundle\TicketBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class LoadMetadata {
    
    
    protected $userRepository;
    protected $primary_key;
    
    public function __construct($userRepository, $primary_key)
    {
        $this->userRepository = $userRepository;
        $this->primary_key = $primary_key;
    }
    
    public function getSubscribedEvents()
    {
        return [
            'loadClassMetadata',
        ];
    }
    
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $class_name = $classMetadata->getName();
        
        if($class_name == "Kdrmklabs\Bundle\TicketBundle\Entity\Ticket" OR 
                $class_name == "Kdrmklabs\Bundle\TicketBundle\Entity\TicketMessage" ) {
            
            $mapping = array(
                'targetEntity' => $this->userRepository,
                'fieldName' => 'user',
                'joinColumns' => array(
                    array(
                        'name' => 'user_id',
                        'referencedColumnName' => $this->primary_key
                    )
                )
            );
         
            $classMetadata->mapManyToOne($mapping);
        }
        
        
    }
    
}
