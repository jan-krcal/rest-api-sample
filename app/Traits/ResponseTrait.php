<?php

declare(strict_types=1);

namespace App\Traits;

use JsonSerializable;

trait ResponseTrait
{
    protected function getError(string $msg): array
    {
        return [
            'status' => 'error',
            'message' => $msg,
        ];
    }

    protected function sendError(string $msg, int $code): void
    {
        $this->getHttpResponse()->setCode($code);
        $this->sendJson($this->getError($msg));
    }

    protected function sendJsonResponse(JsonSerializable|array $data, ?int $code = null): void
    {
        if ($code) {
            $this->getHttpResponse()->setCode($code);
        }
        $this->sendJson($data);
    }
}
