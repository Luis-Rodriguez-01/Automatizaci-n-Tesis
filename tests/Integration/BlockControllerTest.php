<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface;
use PHPUnit\Framework\TestCase;
use Application\Services\BlockService;

class BlockControllerTest extends TestCase
{
    private $app;
    private $blockService;

    protected function setUp(): void {
        parent::setUp();

        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); 
        $dotenv->load();

        if (!isset($_ENV['DB_HOST'])) {
            throw new \Exception('La variable de entorno DB_HOST no está definida.');
        }
        
        $database = new Infrastructure\Database\Database(
            $_ENV['DB_HOST'],
            $_ENV['DB_DATABASE'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD']
        );
    
        $blockRepository = new Infrastructure\Repositories\BlockRepository($database);
        $this->blockService = new BlockService($blockRepository);
        $this->blockController = new Infrastructure\Http\Controllers\BlockController($this->blockService);

        $this->app = AppFactory::create();
        $this->app->post('/api/blocks/create', [$this->blockController, 'createNewBlock']);
    }

    public function testCreateNewBlock()
    {
        $data = [
            'name' => 'Nuevo Bloque',
            'description' => 'Descripción del nuevo bloque',
            'activated' => true,
            'extraInformation' => false,
            'selectedVariables' => []
        ];

        $existingBlock = $this->blockService->getBlockByName($data['name']);
        if ($existingBlock) {
            $this->blockService->deleteBlock($existingBlock['id']);
        }

        $request = $this->createRequest('POST', '/api/blocks/create', $data);
        $response = $this->app->handle($request);
        $this->assertEquals(201, $response->getStatusCode());

        $responseBody = json_decode((string)$response->getBody(), true);
        print_r($responseBody); // Verifica la respuesta

        if (isset($responseBody['id'])) {
            print_r("ID del bloque creado: " . $responseBody['id']); // Verifica el ID
        } else {
            $this->fail('No se pudo obtener el ID del bloque creado.');
        }
    }

    private function createRequest(string $method, string $uri, array $data = []): ServerRequestInterface
    {
        $request = (new \Slim\Psr7\Factory\ServerRequestFactory())->createServerRequest($method, $uri);
        if (!empty($data)) {
            $request = $request->withParsedBody($data);
        }
        return $request;
    }
}
