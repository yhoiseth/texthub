<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class RegisterUserListener implements EventSubscriberInterface
{
    /** @var KernelInterface $kernel */
    private $kernel;

    /**
     * RegisterUserListener constructor.
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->setKernel($kernel);
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

        /** @var Kernel $kernel */
        $kernel = $this->getKernel();

        $projectDirectory = $kernel->getProjectDir();

        $mainRepositoriesDirectory = $projectDirectory . '/var/repositories/main';

        $userMainRepositoryDirectory = $mainRepositoriesDirectory . '/' . $user->getUsernameCanonical();

        if (!file_exists($userMainRepositoryDirectory)) {
            mkdir(
                $userMainRepositoryDirectory,
                0755,
                true
            );
        }

        exec("git init $userMainRepositoryDirectory");
    }

    /**
     * @return KernelInterface
     */
    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }

    /**
     * @param KernelInterface $kernel
     * @return $this
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        return $this;
    }
}
