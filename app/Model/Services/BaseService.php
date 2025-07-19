<?php

declare(strict_types=1);

namespace App\Model\Services;

abstract class BaseService
{
    protected string $error = '';

    public function getError(): string
    {
        return $this->error;
    }

    public function isJsonValid(string $json): bool
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
