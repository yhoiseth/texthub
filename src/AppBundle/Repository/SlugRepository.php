<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Slug;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use FOS\UserBundle\Model\UserInterface;

/**
 * SlugRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SlugRepository extends EntityRepository
{
    public function findSlugByUserAndSlugBody(UserInterface $user, string $slugBody): ?Slug
    {
        $query = $this->createQueryBuilder('slug')
            ->where('slug.body = :slugBody')
            ->setParameter('slugBody', $slugBody)
            ->join(
                'slug.text',
                'text',
                'WITH',
                'text.createdBy = :user'
            )
            ->setParameter('user', $user)
            ->getQuery()
        ;

        try {
            /** @var Slug $slug */
            $slug = $query->getSingleResult();

            return $slug;
        } catch (NoResultException $exception) {
            return null;
        }
    }
}
