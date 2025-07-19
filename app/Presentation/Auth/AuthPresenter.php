<?php

namespace App\Presentation\Auth;

use App\Model\Services\AuthService;
use App\Model\Services\UserService;
use App\Traits\ResponseTrait;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;

final class AuthPresenter extends Presenter
{
    use ResponseTrait;

    public function __construct(
        private readonly UserService $userService,
        private readonly AuthService $authService
    ) {
    }

    public function actionRegister(): void
    {
        $user = $this->userService->create($this->getHttpRequest()->getPost());
        if ($user) {
            $this->sendJsonResponse($user, IResponse::S201_Created);
        } else {
            $this->sendError($this->userService->getError(), IResponse::S400_BadRequest);
        }
    }

    public function actionLogin(): void
    {
        $request = $this->getRequest()->getPost();
        $token = $this->authService->login($request);
        if ($token) {
            $this->sendJsonResponse(['token' => $token]);
        } else {
            $this->sendError('Invalid credentials', IResponse::S401_Unauthorized);
        }
    }
}
