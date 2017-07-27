<?php

namespace AppBundle\Service;


use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class VersionControlSystem
{
    /** @var string $collectionsDirectory */
    private $collectionsDirectory;

    /** @var TokenStorageInterface $tokenStorage */
    private $tokenStorage;

    public function __construct(string $collectionsDirectory, TokenStorageInterface $tokenStorage)
    {
        $this->setCollectionsDirectory($collectionsDirectory);
        $this->setTokenStorage($tokenStorage);
    }

    public function initializeRepository(string $username)
    {
        $collectionsDirectory = $this->getCollectionsDirectory();
//        $username = $this->getTokenStorage()->getToken()->getUsername();

//        dump($collectionsDirectory);
//        dump($username);
//        die;


        exec("git init $collectionsDirectory/$username");
    }

    /**
     * @return string
     */
    public function getCollectionsDirectory(): string
    {
        return $this->collectionsDirectory;
    }

    /**
     * @param string $collectionsDirectory
     */
    public function setCollectionsDirectory(string $collectionsDirectory)
    {
        $this->collectionsDirectory = $collectionsDirectory;
    }

    /**
     * @return TokenStorageInterface
     */
    public function getTokenStorage(): TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
}
