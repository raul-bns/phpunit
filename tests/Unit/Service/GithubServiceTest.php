<?php

namespace App\Tests\Unit\Service;

use App\Enum\HealthStatus;
use App\Service\GithubService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\MockHttpClient;
use Generator;

/**
 * @package App\Tests\Unit\Service
 */
class GithubServiceTest extends TestCase
{
    private LoggerInterface $mockLogger;
    private MockHttpClient $mockHttpClient;
    private MockResponse $mockResponse;

    protected function setUp(): void
    {
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockHttpClient = new MockHttpClient();
    }

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
        $service = $this->createGithubService([
            [
                'title' => 'Daisy',
                'labels' => [['name' => 'Status: Sick']]
            ],
            [
                'title' => 'Maverick',
                'labels' => [['name' => 'Status: Healthy']]
            ],
        ]);

        self::assertSame($expectedStatus, $service->getHealthReport($dinoName));
        self::assertSame(1, $this->mockHttpClient->getRequestsCount());
        self::assertSame('GET', $this->mockResponse->getRequestMethod());
        self::assertSame(
            'https://api.github.com/repos/SymfonyCasts/dino-park/issues',
            $this->mockResponse->getRequestUrl()
        );
    }

    /**
     * @return Generator<string,array{enum(App\Enum\HealthStatus::SICK),string}|array{enum(App\Enum\HealthStatus::HEALTHY),string}>
     */
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

    /**
     * @return void
     */
    public function testExceptionThrownWithUnknownLabel(): void
    {
        $service = $this->createGithubService([
            [
                'title' => 'Maverick',
                'labels' => [['name' => 'Status: Drowsy']]
            ],
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Drowsy is an unknown status label');

        $service->getHealthReport('Maverick');
    }

    /**
     * @param array $responseData
     *
     * @return GithubService
     */
    public function createGithubService(array $responseData): GithubService
    {
        $this->mockResponse = new MockResponse(json_encode($responseData));

        $this->mockHttpClient->setResponseFactory($this->mockResponse);

        return new GithubService($this->mockHttpClient, $this->mockLogger);
    }
}