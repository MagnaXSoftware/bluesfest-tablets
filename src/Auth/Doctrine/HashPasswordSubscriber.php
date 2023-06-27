<?php

declare(strict_types=1);

namespace App\Auth\Doctrine;

use App\Auth\UserPasswordInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class HashPasswordSubscriber implements EventSubscriber
{
    public function prePersist(PrePersistEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof UserPasswordInterface) return;

        $this->hashAndUpdatePassword($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof UserPasswordInterface) return;

        $this->hashAndUpdatePassword($entity);

        /** @var EntityManager $em */
        $em = $args->getObjectManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }

    /**
     * @param UserPasswordInterface $entity
     * @return void
     */
    private function hashAndUpdatePassword(UserPasswordInterface $entity): void
    {
        if (($plainPassword = $entity->getPlainPassword()) !== null) {
            $hashedPassword = (string)password_hash($plainPassword, PASSWORD_ARGON2I);
            $entity->setPassword($hashedPassword);
            $entity->eraseCredentials();
        }
    }
}