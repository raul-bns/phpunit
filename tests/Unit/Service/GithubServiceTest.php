<?php

namespace App\Tests\Unit\Service;

use App\Enum\HealthStatus;
use App\Service\GithubService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @package App\Tests\Unit\Service
 */
class GithubServiceTest extends TestCase
{
    /**
     * @dataProvider dinoNameProvider
     *
     * @param HealthStatus $expectedStatus
     * @param string $dinoName
     *
     * @return void
     */
    public function testGetHealthReportReturnsCorrectHealthStatusForDino(
        HealthStatus $expectedStatus,
        string $dinoName
    ): void {
        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockClient = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockResponse
            ->method('toArray')
            ->willReturn([
                [
                    'title' => 'Daisy',
                    'labels' => [['name' => 'Status: Sick']]
                ],
                [
                    'title' => 'Maverick',
                    'labels' => [['name' => 'Status: Healthy']]
                ],
            ]);

        $mockClient
            ->expects(self::once())
            ->method('request')
            ->with('GET', 'https://api.github.com/repos/SymfonyCasts/dino-park/issues')
            ->willReturn($mockResponse);

        $service = new GithubService($mockClient, $mockLogger);

        self::assertSame($expectedStatus, $service->getHealthReport($dinoName));
    }

    public function dinoNameProvider(): \Generator
    {
        yield 'Sick Dino' => [
            HealthStatus::SICK,
            'Daisy',
        ];
        yield 'Healthy Dino' => [
            HealthStatus::HEALTHY,
            'Maverick',
        ];
    }
}
