<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegisterUserListener implements EventSubscriberInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * RegisterUserListener constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted'
        ];
    }

    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $kernel = $this->getContainer()->get('kernel');

        $projectDirectory = $kernel->getProjectDir();

        $mainRepositoriesDirectory = $projectDirectory . '/var/repositories/main';

        $userMainRepositoryDirectory = $mainRepositoriesDirectory . '/' . $user->getUsernameCanonical();

//        $this->getContainer()->get('logger')->log('debug', $projectDirectory);

        if (!file_exists($userMainRepositoryDirectory)) {
            mkdir(
                $userMainRepositoryDirectory,
                0755,
                true
            );
        }

        exec("git init $userMainRepositoryDirectory");

//        $this->getContainer()->get('logger')->log(
//            'debug',
//            passthru("git init $userMainRepositoryDirectory")
//        );
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }
}
