<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();

        $client = static::createClient();

        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        $this->em->getConnection()->beginTransaction();
        $this->em->clear();

        $this->client = $client;
    }

    protected function tearDown(): void
    {
        // Rollback the transaction after each test
        if ($this->em->getConnection()->isTransactionActive()) {
            $this->em->getConnection()->rollBack();
        }

        parent::tearDown();
    }
}
