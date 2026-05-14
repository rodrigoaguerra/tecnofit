<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

class ApiIntegrationTest extends TestCase
{
    private const BASE_URL = 'http://127.0.0.1:8002';
    private static $serverProcess;
    private static $serverStarted = false;

    public static function setUpBeforeClass(): void
    {
        $cwd = dirname(__DIR__, 2);
        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['file', '/dev/null', 'a'],
            2 => ['file', '/dev/null', 'a'],
        ];

        self::$serverProcess = proc_open(
            'php -S 127.0.0.1:8002 -t public public/index.php',
            $descriptorSpec,
            $pipes,
            $cwd
        );

        if (!is_resource(self::$serverProcess)) {
            self::fail('Não foi possível iniciar o servidor de testes.');
        }

        $start = microtime(true);
        while (true) {
            if (@file_get_contents(self::BASE_URL . '/') !== false) {
                self::$serverStarted = true;
                break;
            }

            if (microtime(true) - $start > 5) {
                self::tearDownAfterClass();
                self::fail('Tempo esgotado ao iniciar o servidor de testes.');
            }

            usleep(100000);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$serverProcess)) {
            proc_terminate(self::$serverProcess);
            proc_close(self::$serverProcess);
            self::$serverStarted = false;
        }
    }

    private function request(string $path): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true,
            ],
        ]);

        $content = @file_get_contents(self::BASE_URL . $path, false, $context);

        if ($content === false) {
            $this->fail('Não foi possível obter resposta da API para: ' . $path);
        }

        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->fail('Resposta não é JSON válido: ' . json_last_error_msg() . ' - ' . $content);
        }

        return [
            'body' => $decoded,
            'raw' => $content,
        ];
    }

    public function testRootEndpointReturnsApiMetadata(): void
    {
        $response = $this->request('/');

        $this->assertArrayHasKey('api', $response['body']);
        $this->assertSame('API TECNOFIT', $response['body']['api']);
        $this->assertArrayHasKey('endpoints', $response['body']);
        $this->assertIsArray($response['body']['endpoints']);
    }

    public function testRankingByIdReturnsExpectedStructure(): void
    {
        $response = $this->request('/ranking/1?page=1&limit=10');

        $this->assertArrayHasKey('movimento', $response['body']);
        $this->assertSame('Deadlift', $response['body']['movimento']);
        $this->assertArrayHasKey('ranking', $response['body']);
        $this->assertIsArray($response['body']['ranking']);
        $this->assertArrayHasKey('pagination', $response['body']);

        $this->assertSame(1, $response['body']['pagination']['current_page']);
        $this->assertSame(10, $response['body']['pagination']['per_page']);
        $this->assertGreaterThanOrEqual(1, $response['body']['pagination']['total_records']);
    }

    public function testRankingByNameReturnsSameResultAsById(): void
    {
        $responseByName = $this->request('/ranking?name=Deadlift&page=1&limit=10');
        $responseById = $this->request('/ranking/1?page=1&limit=10');

        $this->assertSame($responseById['body']['movimento'], $responseByName['body']['movimento']);
        $this->assertSame($responseById['body']['pagination'], $responseByName['body']['pagination']);
        $this->assertSame($responseById['body']['ranking'][0]['usuario'], $responseByName['body']['ranking'][0]['usuario']);
    }

    public function testRankingWithoutIdentifierReturnsBadRequest(): void
    {
        $response = $this->request('/ranking?page=1&limit=10');

        $this->assertArrayHasKey('error', $response['body']);
        $this->assertSame('Identificador do movimento é obrigatório', $response['body']['error']);
    }
}
