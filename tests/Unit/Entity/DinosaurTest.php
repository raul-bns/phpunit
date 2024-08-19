<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Dinosaur;
use App\Enum\HealthStatus;
use PHPUnit\Framework\TestCase;
use Generator;

/**
 * @package App\Tests\Unit\Entity
 */
class DinosaurTest extends TestCase
{
    /**
     * @return void
     */
    public function testCanGetAndSetDate(): void
    {
        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: 15,
            enclosure: 'Paddock A'
        );

        self::assertSame('Big Eaty', $dino->getName());
        self::assertSame('Tyrannosaurus', $dino->getGenus());
        self::assertSame(15, $dino->getLength());
        self::assertSame('Paddock A', $dino->getEnclosure());
    }

    /**
     * @dataProvider sizeDescriptionProvider
     *
     * @param int $length
     * @param string $expectedSize
     *
     * @return void
     */
    public function testDinoHasCorrectSizeDescriptionFromLenght(int $length, string $expectedSize): void
    {
        $dino = new Dinosaur(name: 'Big Eaty', length: $length);

        self::assertSame($expectedSize, $dino->getSizeDescription());
    }

    /**
     * @return Generator
     */
    public function sizeDescriptionProvider(): \Generator
    {
        yield '10 Meter Large Dino' => [10, 'Large'];
        yield '5 Meter Medium Dino' => [5, 'Medium'];
        yield '4 Meter Samll Dino' => [4, 'Small'];
    }

    public function testIsAcceptingVisitorsByDefault(): void
    {
        $dino = new Dinosaur(name: 'Paco');

        self::assertTrue($dino->isAcceptingVisitors());
    }

    public function testIsNotAcceptingVisitorsIfSick(): void
    {
        $dino = new Dinosaur(name: 'Juan');
        $dino->setHealth(HealthStatus::SICK);

        self::assertFalse($dino->isAcceptingVisitors());
    }
}
