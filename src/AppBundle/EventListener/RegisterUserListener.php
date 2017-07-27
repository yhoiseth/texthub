<?php

namespace AppBundle\EventListener;

use AppBundle\Service\VersionControlSystem;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class RegisterUserListener implements EventSubscriberInterface
{
    /** @var KernelInterface $kernel */
    private $kernel;

    /** @var FilesystemInterface $filesystem */
    private $filesystem;

    /** @var VersionControlSystem $versionControlSystem */
    private $versionControlSystem;

    /**
     * RegisterUserListener constructor.
     * @param KernelInterface $kernel
     * @param FilesystemInterface $filesystem
     * @param VersionControlSystem $versionControlSystem
     */
    public function __construct(
        KernelInterface $kernel,
        FilesystemInterface $filesystem,
        VersionControlSystem $versionControlSystem
    )
    {
        $this->setKernel($kernel);
        $this->setFilesystem($filesystem);
        $this->setVersionControlSystem($versionControlSystem);
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
        $this
            ->getFilesystem()
            ->createDir(
                $event->getUser()->getUsername()
            )
        ;

        $this
            ->getVersionControlSystem()
            ->initializeRepository(
                $event->getUser()->getUsername()
            )
        ;
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

    /**
     * @return FilesystemInterface
     */
    public function getFilesystem(): FilesystemInterface
    {
        return $this->filesystem;
    }

    /**
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return VersionControlSystem
     */
    public function getVersionControlSystem(): VersionControlSystem
    {
        return $this->versionControlSystem;
    }

    /**
     * @param VersionControlSystem $versionControlSystem
     */
    public function setVersionControlSystem(VersionControlSystem $versionControlSystem)
    {
        $this->versionControlSystem = $versionControlSystem;
    }
}
