<?php

namespace Kdrmklabs\Bundle\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdrmklabs\Bundle\TicketBundle\Model\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TicketMessage
 *
 * @ORM\Table(name="kdr_ticket_messages")
 * @ORM\Entity
 */
class TicketMessage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ticket", inversedBy="messages")
     * @ORM\JoinColumn(name="id_ticket", referencedColumnName="id", onDelete="CASCADE")
     */
    private $ticket;

    /**
     * ORM defined in Kdrmklabs\Bundle\TicketBundle\EventListener\LoadMetadata
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var integer
     *
     * @ORM\Column(name="date_added", type="integer")
     * @Gedmo\Timestampable(on="create")
     */
    private $dateAdded;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ticket
     *
     * @param Ticket $ticket
     * @return TicketMessage
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return Ticket 
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     * @return TicketMessage
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return UserInterface 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return TicketMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set dateAdded
     *
     * @param integer $dateAdded
     * @return TicketMessage
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return integer 
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }
}
