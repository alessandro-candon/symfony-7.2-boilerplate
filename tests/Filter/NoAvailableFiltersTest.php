<?php
// phpcs:ignoreFile

namespace App\Tests\Filter;

use App\Exception\System\BadFilterDataException;
use App\Filter\FilterApplier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NoAvailableFiltersTest extends KernelTestCase
{
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testErrorFilterClass(): void
    {
        $this->expectException(BadFilterDataException::class);
        $this->expectExceptionMessage('Not static::AVAILABLE_FILTERS are defined');
        $errorFilterClass = new ErrorFilterClass([]);
        $errorFilterClass->apply($this->em->createQueryBuilder(), 'alias');
    }
}

class ErrorFilterClass extends FilterApplier {}