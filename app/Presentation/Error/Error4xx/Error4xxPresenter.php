<?php

declare(strict_types=1);

namespace App\Presentation\Error\Error4xx;

use App\Traits\ResponseTrait;
use Nette;
use Nette\Application\Attributes\Requires;
use Nette\Application\UI\Presenter;

#[Requires(methods: '*', forward: true)]
final class Error4xxPresenter extends Presenter
{
    use ResponseTrait;

    public function actionDefault(Nette\Application\BadRequestException $exception): void
    {
        $this->sendError($exception->getMessage(), $exception->getCode());
    }
}
