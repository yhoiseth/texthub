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

    public function initializeRepository(string $directory)
    {
        exec("git init $directory");
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
