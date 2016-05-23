<?php

namespace Kdrmklabs\Bundle\TicketBundle\Services;

use Kdrmklabs\Bundle\TicketBundle\Model\UserInterface;
use Kdrmklabs\Bundle\TicketBundle\Entity\Ticket;
use Kdrmklabs\Bundle\TicketBundle\Entity\TicketMessage;
use Kdrmklabs\Bundle\TicketBundle\Entity\TicketStatus;
use Kdrmklabs\Bundle\TicketBundle\Entity\TicketCategory;

class TicketService extends AbstractService {
    
    public function getTickets($user = null, $category = null, $status = null, $from_datetime = null, $to_datetime = null, $closed = null) {
        $query = $this->getDoctrineManager()->createQueryBuilder()
                ->select('t')
                ->from('KdrmklabsTicketBundle:Ticket', 't')
                ->orderBy('t.dateUpd', 'DESC');
        
        if($from_datetime) {
            $query
                ->andWhere('t.dateAdded >= :dateAdded')
                ->setParameter('dateAdded', $from_datetime);
        }
        if($to_datetime) {
            $query
                ->andWhere('t.dateAdded <= :dateAdded')
                ->setParameter('dateAdded', $to_datetime);
        }
        
        $user_class = $this->getServiceContainer()->getParameter('kdrmklabs_ticket.model.user.class');
        $user_class_instance = new $user_class();
        
        if($user instanceof UserInterface OR $user instanceof $user_class_instance OR is_integer($user)){
            $query
                ->andWhere('t.user = :user')
                ->setParameter('user', $user);
        }
        if($status instanceof TicketStatus OR is_integer($status)) {
            $query
                ->andWhere('t.status = :status')
                ->setParameter('status', $status);
        }
        if($category instanceof TicketCategory OR is_integer($category)) {
            $query
                ->andWhere('t.category = :category')
                ->setParameter('category', $category);
        }
        if($closed) {
            $query
                ->andWhere('t.closed = :closed')
                ->setParameter('closed', $closed);
        }
        
        return $query;
    }
    
    public function updateTicket($ticket = null, $category = null, $status = null, $subject = null, $user = null, $initial_message = null, $dateAdded = null, $closed = null, $ticketNumber = null, $dateUpd = null) {
        
        $em = $this->getDoctrineManager();
        
        $persist = false;
        
        if($ticket instanceof Ticket OR is_integer($ticket)) {
            $ticket = ($ticket instanceof Ticket) ? $ticket : $em->getRepository('KdrmklabsTicketBundle:Ticket')->find($ticket);
        } else {
            $ticket = new Ticket();
            $persist = true;
        }
        
        if($category){
            $category_reference = ($category instanceof TicketCategory) ? $category : $em->getReference('KdrmklabsTicketBundle:TicketCategory',$category);
            $ticket->setCategory($category_reference);
        }
        if($status){
            $status_reference = ($status instanceof TicketStatus) ? $status : $em->getReference('KdrmklabsTicketBundle:TicketStatus',$status);
            $ticket->setStatus($status_reference);
        }
        if($subject){
            $ticket->setSubject($subject);
        }
        if($user){
            $user_reference = ($user instanceof UserInterface) ? $user : (is_integer($user)) ? $this->getServiceContainer()->get('kdrmklabs_ticket.user_repository')->find($user) : $user; 
            $ticket->setUser($user_reference);
        }
        if($closed){
            $ticket->setClosed($closed);
        }
        if($dateUpd){
            $ticket->setDateUpd($dateUpd);
        }
        if($ticketNumber){
            $ticket->setTicketNumber($ticketNumber);
        }
        if($dateAdded){
            $ticket->setDateAdded($dateAdded);
        }
        if($initial_message AND $user AND $persist){
            $message = new TicketMessage();
            $message->setTicket($ticket);
            $message->setMessage($initial_message);
            $message->setUser($user_reference);
            
            $em->persist($message);
        }
        
        if($persist){
            $em->persist($ticket);
        }
        
        $em->flush();
        
        return $ticket;
    }
    
    public function createTicket($initial_message, $subject, $user, $category, $status, $dateAdded = null){
        return $this->updateTicket(null, $category, $status, $subject, $user, $initial_message, $dateAdded);
    }
    
    public function replyTicket($ticket, $user = null, $message = null, $dateAdded = null) {
        $em = $this->getDoctrineManager();
        $ticketReply = new TicketMessage();
        
        if($dateAdded){
            $ticketReply->setDateAdded($dateAdded);
        }
        if($message){
            $ticketReply->setMessage($message);
        }
        if($ticket instanceof Ticket OR is_numeric($ticket)) {
            $ticket_reference = ($ticket instanceof Ticket) ? $ticket : $em->getRepository('KdrmklabsTicketBundle:Ticket')->find($ticket);
            $ticketReply->setTicket($ticket_reference);
            if($dateAdded){
                $ticket_reference->setDateUpd($dateAdded);
            } else {
                $ticket_reference->setDateUpd(time());
            }
        }
        if($user){
            $user_reference = ($user instanceof UserInterface) ? $user : (is_integer($user)) ? $this->getServiceContainer()->get('kdrmklabs_ticket.user_repository')->find($user) : $user; 
            $ticketReply->setUser($user_reference);
        }
        
        $em->persist($ticketReply);
        $em->flush();

        return $ticket_reference;
    }
    
    public function deleteTicket($ticket) {
        $em = $this->getDoctrineManager();
        if($ticket instanceof Ticket OR is_numeric($ticket)){
            $ticket = ($ticket instanceof Ticket) ? $ticket : $em->getRepository('KdrmklabsTicketBundle:Ticket')->find($ticket);
            
            if($ticket){
                $em->remove($ticket);
                $em->flush();
                return true;
            }
        }
        
        return false;
    }
    
    public function closeTicket($ticket){
        $em = $this->getDoctrineManager();
        if($ticket instanceof Ticket OR is_numeric($ticket)){
            $ticket = ($ticket instanceof Ticket) ? $ticket : $em->getRepository('KdrmklabsTicketBundle:Ticket')->find($ticket);
            
            if($ticket instanceof Ticket){
                $ticket->setClosed(true);
                $em->flush();
                
                return true;
            }
        }
        
        return false;
    }

    public function getTicket($id){
        $em = $this->getDoctrineManager();
        
        return $em->getRepository('KdrmklabsTicketBundle:Ticket')->find($id);
    }
}