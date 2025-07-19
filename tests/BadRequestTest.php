<?php

declare(strict_types=1);

use Nette\Http\IResponse;
use Nette\Http\UrlScript;
use Nette\Utils\Json;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

final class BadRequestTest extends TestCase
{
    private UrlScript $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = new UrlScript('http://localhost:8000');
    }

    public function testBadRequest(): void
    {
        $ch = curl_init((string) $this->baseUrl->withPath('/bad/'));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($ch);
        error_log($response);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        Assert::same(IResponse::S404_NotFound, $code);
    }
}

(new BadRequestTest())->run();
