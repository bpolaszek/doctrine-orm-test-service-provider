<?php

namespace Noback\PHPUnitTestServiceContainer\PHPUnit;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Noback\PHPUnitTestServiceContainer\PHPUnit\Entity\User;
use PHPUnit\Framework\TestCase;

final class TestCaseWithEntityManagerTest extends TestCase
{
    use TestCaseWithEntityManager;

    protected function getEntityDirectories(): array
    {
        return array(__DIR__ . '/Entity');
    }

    public function test_it_persists_an_entity()
    {
        $user = new User();
        $user->setName('Matthias');

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $this->getEntityManager()->clear();

        $retrievedUser = $this->getEntityManager()->find(get_class($user), $user->getId());
        $this->assertSame($retrievedUser->getName(), $user->getName());
    }

    public function test_a_new_test_has_a_fresh_database()
    {
        $count = $this
            ->getEntityManager()
            ->getRepository(User::class)
            ->createQueryBuilder('u')
            ->select('COUNT(u.id) as number_of_users')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertSame(0, (int) $count);
    }

    public function test_the_dbal_connection_can_be_retrieved()
    {
        $this->assertInstanceOf(Connection::class, $this->getConnection());
    }

    public function test_the_event_manager_can_be_retrieved()
    {
        $this->assertInstanceOf(EventManager::class, $this->getEventManager());
    }
}
