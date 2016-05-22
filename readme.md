# Getting started with KdrmklabsTicketBundle in Symfony2.

This bundle allows you to integrate in your Symfony2 project a basic system of customer support by tickets. You can create, edit and respond tickets instantly  after installing this bundle .

Needs a minimum configuration and the whole procedure is very simple.

## Installation.

### I. Installing the bundle in two different ways.

#### a) Automatically.


You can install the last version of this bundle with composer running the command from your command line:

```
$ composer require kdrmklabs/ticket-bundle
```

#### b) By composer.json .

Or you can install this bundle by adding next code line to your project in the composer.json file and after update it with the command `composer update`

file: /composer.json

```json
{
    "require": {
        "kdrmklabs/ticket-bundle": "dev-master",
    }
}
``` 

Now, update the bundle with composer:

```
$ composer update kdrmklabs/ticket-bundle
```

### II. Enable and register the Bundle in the AppKernel

```php
// file: app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Kdrmklabs\Bundle\TicketBundle\KdrmklabsTicketBundle(),
        // ...
        // Your application bundles
    );
}
```

### III. Install Gedmon Doctrine2 extensions

#### Requirements:

- [Gedmo Doctrine2 extensions](https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/symfony2.md)

Check if gedmo doctrine extensions are downloaded.

`/vendor/gedmo/doctrine-extensions/lib/Gedmo`

if not download it:

a. Add dependency to composer.json

*file: /composer.json*

```json
{
    "require": {
        ....
        "gedmo/doctrine-extensions": "dev-master"
    }
}
```

b. Update with composer in the command line:

```
$ php composer.phar update gedmo/doctrine-extensions
```

#### Configure Gedmo:

III.I. Create a DoctrineExtensionListener

```php
// file: src/AppBundle/Listener/DoctrineExtensionListener.php

namespace AppBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctrineExtensionListener implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function onLateKernelRequest(GetResponseEvent $event)
    {
        $translatable = $this->container->get('gedmo.listener.translatable');
        $translatable->setTranslatableLocale($event->getRequest()->getLocale());
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $securityContext = $this->container->get('security.context', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        if (null !== $securityContext && null !== $securityContext->getToken() && $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $loggable = $this->container->get('gedmo.listener.loggable');
            $loggable->setUsername($securityContext->getToken()->getUsername());
        }
    }
}
```

III.II. Create file `doctrine_extensions.yml` and locate it in your `app/config` directory

```yml
# file: app/config/doctrine_extensions.yml

doctrine:
    orm:
        auto_generate_proxy_classes: %kernel.debug%
        auto_mapping: true
# only these lines are added additionally
        mappings:
            translatable:
                type: annotation
                alias: Gedmo
                prefix: Gedmo\Translatable\Entity
                # make sure vendor library location is correct
                dir: "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable/Entity"
    # services to handle doctrine extensions
    # import it in config.yml
services:
    # KernelRequest listener
    extension.listener:
        # change this to your DoctrineExtensionListener namespace
        class: AppBundle\Listener\DoctrineExtensionListener
        calls:
            - [ setContainer, [ @service_container ] ]
        tags:
            # translatable sets locale after router processing
            - { name: kernel.event_listener, event: kernel.request, method: onLateKernelRequest, priority: -10 }
            # loggable hooks user username if one is in security context
            - { name: kernel.event_listener, event: kernel.request, method: onKernelRequest }

    # Doctrine Extension listeners to handle behaviors
    gedmo.listener.tree:
        class: Gedmo\Tree\TreeListener
        tags:
            - { name: doctrine.event_subscriber, connection: default }
        calls:
            - [ setAnnotationReader, [ @annotation_reader ] ]

    gedmo.listener.translatable:
        class: Gedmo\Translatable\TranslatableListener
        tags:
            - { name: doctrine.event_subscriber, connection: default }
        calls:
            - [ setAnnotationReader, [ @annotation_reader ] ]
            - [ setDefaultLocale, [ %locale% ] ]
            - [ setTranslatableLocale, [ %locale% ]]
            - [ setTranslationFallback, [ false ] ]

    gedmo.listener.timestampable:
        class: Gedmo\Timestampable\TimestampableListener
        tags:
            - { name: doctrine.event_subscriber, connection: default }
        calls:
            - [ setAnnotationReader, [ @annotation_reader ] ]

    gedmo.listener.sluggable:
        class: Gedmo\Sluggable\SluggableListener
        tags:
            - { name: doctrine.event_subscriber, connection: default }
        calls:
            - [ setAnnotationReader, [ @annotation_reader ] ]

    gedmo.listener.sortable:
        class: Gedmo\Sortable\SortableListener
        tags:
            - { name: doctrine.event_subscriber, connection: default }
        calls:
            - [ setAnnotationReader, [ @annotation_reader ] ]

    gedmo.listener.loggable:
        class: Gedmo\Loggable\LoggableListener
        tags:
            - { name: doctrine.event_subscriber, connection: default }
        calls:
            - [ setAnnotationReader, [ @annotation_reader ] ]
```

> Don't forget change 'AppBundle\Listener\DoctrineExtensionListener' to your DoctrineExtensionListener namespace.

Finally, Do not forget to import **doctrine_extensions.yml** in your **app/config/config.yml** :

```yml
# file: app/config/config.yml
imports:
    - { resource: parameters.yml }
    - { resource: security.yml }
    - { resource: doctrine_extensions.yml }

```

More details of Gedmo Doctrine2 extensions in Symfony2: https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/symfony2.md


### IV. (optional) If you want paginate results with KNPPaginatorBundle you can install and configure it.

More details: https://github.com/KnpLabs/KnpPaginatorBundle#configuration-example

### V. Configure the bundle.

V.I. Add `kdrmklabs_ticket` configuration to you config.yml

```yml
# file: app/config/config.yml

kdrmklabs_ticket:
    user_class: AppBundle\Entity\User
    user_primay_key: id
```

Where `user_primay_key` is the name of your primary key in the user table.

V.II. Add `resolve_target_entities` to your doctrine configuration

```yml
# file: app/config/config.yml

# Doctrine Configuration
doctrine:
    orm:
        auto_mapping: true
        resolve_target_entities:
            Kdrmklabs\Bundle\TicketBundle\Model\UserInterface: AppBundle\Entity\User
```

> Do not forget change 'AppBundle\Entity\User' to your User Entity namespace

More details about resolve_target_entities: http://symfony.com/doc/current/cookbook/doctrine/resolve_target_entity.html

V.III. Implements `Kdrmklabs\Bundle\TicketBundle\Model\UserInterface` from your User entity.

```php
// file: 

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User implements \Kdrmklabs\Bundle\TicketBundle\Model\UserInterface
{
    public function getId(){
        
    }
}

```

### VII. Finally, create database tables and update the schema

Update your database schema with the command:

```
$ php app/console doctrine:schema:update --force
```

> Now, you need to populete the database tables `kdrmklabs_ticket_status` and `kdrmklabs_ticket_category`

Example of SQL to populete the tables:

```sql
INSERT INTO `kdrmklabs_ticket_category` (`id`, `name`, `active`) VALUES ('1', 'Billing', '1');
INSERT INTO `kdrmklabs_ticket_category` (`id`, `name`, `active`) VALUES ('2', 'Cancellations and refunds', '1');
INSERT INTO `kdrmklabs_ticket_category` (`id`, `name`, `active`) VALUES ('3', 'Report a scam or offer false', '1');
INSERT INTO `kdrmklabs_ticket_category` (`id`, `name`, `active`) VALUES ('4', 'Report inappropriate or illegal content', '1');
INSERT INTO `kdrmklabs_ticket_category` (`id`, `name`, `active`) VALUES ('5', 'Security Center and user protection', '1');

INSERT INTO `kdrmklabs_ticket_status` (`id`, `name`, `active`) VALUES ('1', 'Pending', '1');
INSERT INTO `kdrmklabs_ticket_status` (`id`, `name`, `active`) VALUES ('2', 'Invalid', '1');
INSERT INTO `kdrmklabs_ticket_status` (`id`, `name`, `active`) VALUES ('3', 'Solved', '1');
``` 


## Statuses

You can add to the database table `kdrmklabs_ticket_status` all states that you want to use to identify the tickets, for example:

![image](https://cloud.githubusercontent.com/assets/5240279/15417885/18df2e22-1e20-11e6-9d22-55f182e3cc27.png)

## Categories

You can add to the database table `kdrmklabs_ticket_category` all categories that you want to use to classify the tickets, for example:

![image](https://cloud.githubusercontent.com/assets/5240279/15417940/c288fe08-1e20-11e6-973b-070883d98421.png)

## Available services

**Examples of implementation of kdrmklabs_ticket.ticket_service from a controller**

### Create a ticket

```php
/**
* @Route("/create")
*/
public function createAction() {
   $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
   $ticket = $kdrmklabs_ticket_service->createTicket("message", "subject", 1, 1, 1);

   return $this->redirectToRoute('kdrmklabs_ticket_show', array('id' => $ticket->getId()));
}
```

**createTicket** -> Return a `Kdrmklabs\Bundle\TicketBundle\Entity\Ticket` object

Description

```php
createTicket(
    string $initial_message, 
    string $subject, 
    int|Kdrmklabs\Bundle\TicketBundle\Model\UserInterface|AppBundle\Entity\User $user, 
    int|Kdrmklabs\Bundle\TicketBundle\Entity\TicketCategory $category,
    int|Kdrmklabs\Bundle\TicketBundle\Entity\TicketStatus $status 
    [, int $dateAdded])
```


### Delete

```php
/**
* @Route("/delete/{id}")
*/
public function deleteAction($id) {
   $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
   $kdrmklabs_ticket_service->deleteTicket($id);

   return $this->redirectToRoute('kdrmklabs_ticket_index');
}
```

**deleteTicket** -> Return boolean

Description:

```php
deleteTicket( int|Kdrmklabs\Bundle\TicketBundle\Entity\Ticket $ticket)
```

### Reply

```php
/**
* @Route("/reply/{id}")
*/
public function replyAction($id) {
   $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
   $ticket = $kdrmklabs_ticket_service->replyTicket($id, 1, "reply");

   return $this->redirectToRoute('kdrmklabs_ticket_show', array('id' => $ticket->getId()));
}
```

**replyTicket** -> Return a `Kdrmklabs\Bundle\TicketBundle\Entity\Ticket` object

Description:

```php
replyTicket(
    int|Kdrmklabs\Bundle\TicketBundle\Entity\Ticket $ticket, 
    int|Kdrmklabs\Bundle\TicketBundle\Model\UserInterface|AppBundle\Entity\User $user, 
    string $message
    [, int $dateAdded])
```

### get a ticket

```php
/**
* @Route("/show/{id}", name="kdrmklabs_ticket_show")
* @Template()
*/
public function showAction($id) {
   $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
   $ticket = $kdrmklabs_ticket_service->getTicket($id);

   return array('ticket' => $ticket);
}
```

**getTicket** -> Return a `Kdrmklabs\Bundle\TicketBundle\Entity\Ticket` object

Description:

```php
getTicket( int|string $id_ticket )
```

### list tickets

```php
/**
* @Route("/", name="kdrmklabs_ticket_index")
* @Template()
*/
public function indexAction(Request $request) {
   $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
   $query = $kdrmklabs_ticket_service->getTickets(); // get all tickets

   $paginator = $this->get('knp_paginator');
   $pagination = $paginator->paginate(
       $query->getQuery(),
       $request->query->get('page', 1),
       10
   );

   return array('pagination' => $pagination);
}
```

**getTickets** -> return DQL QUERY

Description:

```php
getTickets(
        [ int|Kdrmklabs\Bundle\TicketBundle\Model\UserInterface|AppBundle\Entity\User $author,
        int|Kdrmklabs\Bundle\TicketBundle\Entity\TicketCategory $category,
        int|Kdrmklabs\Bundle\TicketBundle\Entity\TicketStatus $status 
        int $from_datetime,
        int $to_datetime,
        boolean $closed ]
    )
```

### close ticket

```php
/**
* @Route("/close/{id}")
*/
public function closeAction($id){
   $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
   $kdrmklabs_ticket_service->closeTicket($id);

   return $this->redirectToRoute('kdrmklabs_ticket_index');
}
```

**closeTicket** -> Return boolean

Description:

```php
closeTicket( id|string $id_ticket )
```

### User repository from UserInterface

```php
// access to UserInterface from controller
$this->get('kdrmklabs_ticket.user_repository')->find($id_user);
```

### Example complete of Controller implementation

```php
namespace Kdrmklabs\Bundle\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

    /**
     * @Route("/", name="kdrmklabs_ticket_index")
     * @Template()
     */
    public function indexAction(Request $request) {
        $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
        $query = $kdrmklabs_ticket_service->getTickets();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(),
            $request->query->get('page', 1),
            10
        );
        
        return array('pagination' => $pagination);
    }

    /**
     * @Route("/create")
     */
    public function createAction() {
        $id_user = 1;
        $id_category = 1;
        $id_status = 1;
        $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
        $ticket = $kdrmklabs_ticket_service->createTicket("message", "subject", $id_user, $id_category, $id_status);
        
        return $this->redirectToRoute('kdrmklabs_ticket_show', array('id' => $ticket->getId()));
    }

    /**
     * @Route("/show/{id}", name="kdrmklabs_ticket_show")
     * @Template()
     */
    public function showAction($id) {
        $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
        $ticket = $kdrmklabs_ticket_service->getTicket($id);

        return array('ticket' => $ticket);
    }

    /**
     * @Route("/reply/{id}")
     */
    public function replyAction($id) {
        $id_user = 1;
        $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
        $ticket = $kdrmklabs_ticket_service->replyTicket($id, $id_user, "reply");
        
        return $this->redirectToRoute('kdrmklabs_ticket_show', array('id' => $ticket->getId()));
    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id) {
        $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
        $kdrmklabs_ticket_service->deleteTicket($id);
        
        return $this->redirectToRoute('kdrmklabs_ticket_index');
    }

    /**
     * @Route("/close/{id}")
     */
    public function closeAction($id){
        $kdrmklabs_ticket_service = $this->get('kdrmklabs_ticket.ticket_service');
        $kdrmklabs_ticket_service->closeTicket($id);
        
        return $this->redirectToRoute('kdrmklabs_ticket_index');
    }
}
```
