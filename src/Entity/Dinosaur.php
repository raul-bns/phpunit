<?php

namespace App\Entity;

use App\Enum\HealthStatus;

/**
 * @package App\Entity
 */
class Dinosaur
{
    private string $name;
    private string $genus;
    private int $length;
    private string $enclosure;
    private HealthStatus $health = HealthStatus::HEALTHY;

    /**
     * @param string $name
     * @param string $genus
     * @param int $length
     * @param string $enclosure
     *
     * @return void
     */
    public function __construct(
        string $name,
        string $genus = 'Unknown',
        int $length = 0,
        string $enclosure = 'Unknown'
    ) {
        $this->name = $name;
        $this->genus = $genus;
        $this->length = $length;
        $this->enclosure = $enclosure;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getGenus(): string
    {
        return $this->genus;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @return string
     */
    public function getEnclosure(): string
    {
        return $this->enclosure;
    }

    /**
     * @return string
     */
    public function getSizeDescription(): string
    {
        if ($this->getLength() >= 10)
            return 'Large';

        if ($this->getLength() >= 5)
            return 'Medium';

        return 'Small';
    }

    /**
     * @return bool
     */
    public function isAcceptingVisitors(): bool
    {
        return $this->health !== HealthStatus::SICK;
    }

    /**
     * @param string $health
     *
     * @return void
     */
    public function setHealth(HealthStatus $health): void
    {
        $this->health = $health;
    }
}