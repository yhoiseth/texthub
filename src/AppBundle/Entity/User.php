<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="`user`")
 */
class User extends BaseUser
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(
     *     type="string",
     *     nullable=true
     * )
     */
    protected $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?: $this->getUsername();
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }
}
