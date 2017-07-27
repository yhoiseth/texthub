<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Text
 *
 * @ORM\Table(name="text")
 * @ORM\Entity(
 *     repositoryClass="AppBundle\Repository\TextRepository"
 * )
 */
class Text
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255, nullable=false, unique=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=false)
     */
    private $slug;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $createdBy;

    /**
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Text
     */
    public function setTitle(string $title): Text
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return Text
     */
    public function setSlug(string $slug): Text
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     * @return Text
     */
    public function setCreatedBy(User $createdBy): Text
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
