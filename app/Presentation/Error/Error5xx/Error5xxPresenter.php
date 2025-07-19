<?php

declare(strict_types=1);

namespace App\Presentation\Error\Error5xx;

use Nette\Application\Attributes\Requires;
use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses\JsonResponse;
use Tracy\ILogger;

#[Requires(forward: true)]
final class Error5xxPresenter implements IPresenter
{
    public function __construct(
        private readonly ILogger $logger,
    ) {
    }


    public function run(Request $request): Response
    {
        $exception = $request->getParameter('exception');
        $this->logger->log($exception, ILogger::EXCEPTION);
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Internal server error',
        ]);
    }
}
