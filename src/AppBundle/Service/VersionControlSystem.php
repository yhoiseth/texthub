<?php

namespace AppBundle\Service;

class VersionControlSystem
{
    /** @var string $collectionsDirectory */
    private $collectionsDirectory;

    public function __construct(string $collectionsDirectory)
    {
        $this->setCollectionsDirectory($collectionsDirectory);
    }

    public function initializeRepository(string $username)
    {
        $collectionsDirectory = $this->getCollectionsDirectory();

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
}
