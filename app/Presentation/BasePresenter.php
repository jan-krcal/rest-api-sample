<?php

declare(strict_types=1);

namespace App\Presentation;

use App\Model\Entities\User;
use App\Model\Services\AuthService;
use App\Traits\ResponseTrait;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;

abstract class BasePresenter extends Presenter
{
    use ResponseTrait;

    private AuthService $authService;

    private User $currentUser;

    public function startup(): void
    {
        parent::startup();

        if ($user = $this->authService->verify($this->getHttpRequest()->getHeaders())) {
            $this->currentUser = $user;
        } else {
            $this->sendError('Unauthorized', IResponse::S401_Unauthorized);
        }
    }

    protected function getCurrentUser(): User
    {
        return $this->currentUser;
    }

    public function injectAuthService(
        AuthService $authService,
    ): void {
        $this->authService = $authService;
    }
}
