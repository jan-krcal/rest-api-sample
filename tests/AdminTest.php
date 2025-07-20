<?php

/**
 * @phpIni extension=mysqli
 */

declare(strict_types=1);

use App\Model\Services\UserService;
use Nette\Bootstrap\Configurator;
use Nette\Http\IResponse;
use Nette\Http\UrlScript;
use Nette\Utils\Json;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

final class AdminTest extends TestCase
{
    private UrlScript $baseUrl;

    private UserService $userService;

    private int $userId;

    private int $articleId;

    private string $token;

    private string $time;

    public function setUp(): void
    {
        $this->baseUrl = new UrlScript('http://localhost:8000');
        $this->time = (string) time();

        $configurator = new Configurator();
        $configurator->setTempDirectory(__DIR__ . '/../temp');
        $configurator->addConfig(__DIR__ . '/../config/common.neon');
        $configurator->addConfig(__DIR__ . '/../config/services.neon');

        $container = $configurator->createContainer();

        $this->userService = $container->getByType(UserService::class);
    }

    public function tearDown(): void
    {
        $user = $this->userService->get($this->userId);
        if ($user) {
            $this->userService->delete($user);
        }
    }

    public function testAdmin(): void
    {
        $this->register();
        $this->login();
        $this->readUsers();
        $this->createArticle();
        $this->deleteArticle();
        $this->readArticles();
    }

    private function register(): void
    {
        $ch = curl_init((string) $this->baseUrl->withPath('/auth/register'));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'email' => $this->time . 'admin@example.com',
                'password' => 'password',
                'role' => 'admin',
                'name' => 'Tester',
            ],
        ]);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        Assert::same(IResponse::S201_Created, $code);
        $data = Json::decode($response, true);
        Assert::hasKey('id', $data);
        $this->userId = (int) $data['id'];
    }

    private function login(): void
    {
        $ch = curl_init((string) $this->baseUrl->withPath('/auth/login'));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'email' => $this->time . 'admin@example.com',
                'password' => 'password',
            ],
        ]);

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        Assert::same(IResponse::S200_OK, $code);
        $data = Json::decode($response, true);
        Assert::hasKey('token', $data);
        $this->token = $data['token'];
    }

    private function readUsers(): void
    {
        $auth = 'Authorization: Bearer ' . $this->token;
        $ch = curl_init((string) $this->baseUrl->withPath('/users/'));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                $auth,
            ],
        ]);

        $response = curl_exec($ch);
        error_log($response);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        Assert::same(IResponse::S200_OK, $code);
    }

    private function readArticles(): void
    {
        $auth = 'Authorization: Bearer ' . $this->token;
        $ch = curl_init((string) $this->baseUrl->withPath('/articles/'));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                $auth,
            ],
        ]);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        Assert::same(IResponse::S200_OK, $code);
    }

    private function createArticle(): void
    {
        $auth = 'Authorization: Bearer ' . $this->token;
        $ch = curl_init((string) $this->baseUrl->withPath('/articles/'));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'title' => 'Title admin article',
                'content' => 'Content admin article',
            ],
            CURLOPT_HTTPHEADER => [
                $auth,
            ],
        ]);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        Assert::same(IResponse::S201_Created, $code);
        $data = Json::decode($response, true);
        Assert::hasKey('id', $data);
        $this->articleId = $data['id'];
    }

    private function deleteArticle(): void
    {
        $auth = 'Authorization: Bearer ' . $this->token;
        $ch = curl_init((string) $this->baseUrl->withPath('/articles/' . $this->articleId));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => [
                $auth,
            ],
        ]);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        Assert::same(IResponse::S204_NoContent, $code);
    }
}

(new AdminTest())->run();
