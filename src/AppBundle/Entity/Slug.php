<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Base\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Slug
 *
 * @ORM\Table(name="slug")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SlugRepository")
 */
class Slug extends Entity
{
    /**
     * @var string
     *
     * @ORM\Column(name="body", type="string", length=255)
     */
    private $body;

    /**
     * @var Text
     *
     * @ORM\ManyToOne(targetEntity="Text")
     */
    private $text;

    public function __toString()
    {
        return $this->getBody();
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Slug
     */
    public function setBody(string $body): Slug
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @return null|Text
     */
    public function getText(): ?Text
    {
        return $this->text;
    }

    /**
     * @param Text $text
     * @return Slug
     */
    public function setText(Text $text): Slug
    {
        $this->text = $text;

        return $this;
    }
}

