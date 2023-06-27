<?php

declare(strict_types=1);

namespace App\Doctrine;

use DI\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Proxy\Proxy;
use Doctrine\Persistence\ManagerRegistry as ManagerRegistryInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class Registry implements ManagerRegistryInterface
{

    private ContainerInterface $container;
    private Connection $connection;
    private EntityManager $entityManager;

    public function __construct(ContainerInterface $container, Connection $connection, EntityManager $entityManager)
    {
        $this->container = $container;
        $this->connection = $connection;
        $this->entityManager = $entityManager;
    }

    public function getDefaultConnectionName()
    {
        return 'default';
    }

    public function getConnection(?string $name = null)
    {
        if ($name === null or $name == 'default') {
            return $this->connection;
        }
        throw new NotFoundException("No connection named {$name} exist");
    }

    public function getConnections()
    {
        return [
            'default' => $this->connection,
        ];
    }

    public function getConnectionNames()
    {
        return array_combine(array_keys($this->getConnections()), array_keys($this->getConnections()));
    }

    public function getDefaultManagerName()
    {
        return 'default';
    }

    public function getManager(?string $name = null)
    {
        if ($name === null or $name == 'default') {
            return $this->connection;
        }
        throw new NotFoundException("No connection named {$name} exist");
    }

    public function getManagers()
    {
        return [
            'default' => $this->entityManager,
        ];
    }

    public function resetManager(?string $name = null)
    {
        return $this->getManager($name);
    }

    public function getManagerNames()
    {
        return array_combine(array_keys($this->getConnections()), array_keys($this->getConnections()));
    }


    /**
     * {@inheritDoc}
     */
    public function getManagerForClass(string $class)
    {
        $proxyClass = new ReflectionClass($class);
        if ($proxyClass->isAnonymous()) {
            return null;
        }

        if ($proxyClass->implementsInterface(Proxy::class)) {
            $parentClass = $proxyClass->getParentClass();

            if ($parentClass === false) {
                return null;
            }

            $class = $parentClass->getName();
        }

        if (!$this->entityManager->getMetadataFactory()->isTransient($class)) {
            return $this->entityManager;
        }
        return null;
    }

    public function getRepository(string $persistentObject, ?string $persistentManagerName = null)
    {
        $this->getManagerForClass($persistentObject)->getRepository($persistentObject);
    }
}