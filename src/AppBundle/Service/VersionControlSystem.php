<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class VersionControlSystem
{
    /** @var string $collectionsDirectory */
    private $collectionsDirectory;

    /** @var TokenStorageInterface $tokenStorage */
    private $tokenStorage;

    public function __construct(
        string $collectionsDirectory,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->setCollectionsDirectory($collectionsDirectory);
        $this->setTokenStorage($tokenStorage);
    }

    public function initializeRepository(string $username)
    {
        $collectionsDirectory = $this->getCollectionsDirectory();

        exec("git init $collectionsDirectory/$username");
    }

    public function commit(string $filename, string $message)
    {
        /** @var User $user */
        $user = $this->getTokenStorage()->getToken()->getUser();
        $username = $user->getUsername();
        $userName = $user->getName();
        $email = $user->getEmail();

        $collectionsDirectory = $this->getCollectionsDirectory();

        $navigationCommand = "cd $collectionsDirectory/$username";
        $stageCommand = "git add $filename";
        $commitCommand = "git commit --author='$userName <$email>' -m '$message'";

        $completeCommand = "$navigationCommand && $stageCommand && $commitCommand";

        shell_exec($completeCommand);
    }

    public function commitNewFilename(string $old, string $new): void
    {
        /** @var User $user */
        $user = $this->getTokenStorage()->getToken()->getUser();
        $username = $user->getUsername();
        $userName = $user->getName();
        $email = $user->getEmail();

        $collectionsDirectory = $this->getCollectionsDirectory();

        $navigationCommand = "cd $collectionsDirectory/$username";
        $removeOldCommand = "git add --all $old";
        $addNewCommand = "git add $new";
        $commitCommand = "git commit --author='$userName <$email>' -m 'Update filename'";

        $completeCommand = "$navigationCommand && $removeOldCommand && $addNewCommand && $commitCommand";

        shell_exec($completeCommand);
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
