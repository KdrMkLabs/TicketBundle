<?php

namespace Kdrmklabs\Bundle\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdrmklabs\Bundle\TicketBundle\Model\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Ticket
 *
 * @ORM\Table(name="kdrmklabs_ticket")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Ticket {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var UserInterface
     * @ORM\ManyToOne(targetEntity="\Kdrmklabs\Bundle\TicketBundle\Model\UserInterface")
     */
    private $user;
    
    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @ORM\OneToMany(targetEntity="TicketMessage", mappedBy="ticket")
     */
    private $messages;

    /**
     * @ORM\ManyToOne(targetEntity="TicketCategory")
     * @ORM\JoinColumn(name="id_category", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="TicketStatus")
     * @ORM\JoinColumn(name="id_status", referencedColumnName="id")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="ticket_number", type="string", length=255)
     */
    private $ticketNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="closed", type="boolean", options={"default" = 0})
     */
    private $closed = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="date_added", type="integer")
     * @Gedmo\Timestampable(on="create")
     */
    private $dateAdded;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="date_upd", type="integer")
     * @Gedmo\Timestampable(on="update")
     */
    private $dateUpd;

    /**
     * @ORM\PrePersist()
     */
    public function preSave() {
        $this->ticketNumber = uniqid();
    }
    
    public function __construct() {
        $this->messages = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     * @return Ticket
     */
    public function setUser($user) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get userId
     *
     * @return UserInterface 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Ticket
     */
    public function setSubject($subject) {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * Add messages.
     *
     * @param TicketMessage $messages
     *
     * @return Ticket
     */
    public function addMessage(TicketMessage $messages) {
        $this->messages[] = $messages;
        return $this;
    }
    
    /**
     * Remove messages.
     *
     * @param TicketMessage $messages
     */
    public function removeMessage(TicketMessage $messages)
    {
        $this->messages->removeElement($messages);
    }
    
    /**
     * Get messages.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set category
     *
     * @param TicketCategory $category
     * @return Ticket
     */
    public function setCategory($category) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return TicketCategory 
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set status
     *
     * @param TicketStatus $status
     * @return Ticket
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return TicketStatus 
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set ticketNumber
     *
     * @param string $ticketNumber
     * @return Ticket
     */
    public function setTicketNumber($ticketNumber) {
        $this->ticketNumber = $ticketNumber;

        return $this;
    }

    /**
     * Get ticketNumber
     *
     * @return string 
     */
    public function getTicketNumber() {
        return $this->ticketNumber;
    }

    /**
     * Set closed
     *
     * @param boolean $closed
     * @return Ticket
     */
    public function setClosed($closed) {
        $this->closed = $closed;

        return $this;
    }

    /**
     * Get closed
     *
     * @return boolean 
     */
    public function getClosed() {
        return $this->closed;
    }

    /**
     * Set dateAdded
     *
     * @param integer $dateAdded
     * @return Ticket
     */
    public function setDateAdded($dateAdded) {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return integer 
     */
    public function getDateAdded() {
        return $this->dateAdded;
    }
    
    /**
     * Set dateUpd
     *
     * @param integer $dateUpd
     * @return Ticket
     */
    public function setDateUpd($dateUpd) {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return integer 
     */
    public function getDateUpd() {
        return $this->dateUpd;
    }

}
