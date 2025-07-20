<?php

declare(strict_types=1);

namespace App\Presentation\Users;

use App\Model\Services\UserService;
use App\Presentation\BasePresenter;
use Nette\Http\IResponse;

final class UsersPresenter extends BasePresenter
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    public function startup(): void
    {
        parent::startup();

        if (!$this->getCurrentUser()->getRole()->canAccessUsers()) {
            $this->getHttpResponse()->setCode(IResponse::S401_Unauthorized);
            $this->sendJson($this->getError('Unauthorized to access users'));
        }
    }

    public function actionDefault(?int $id = null): void
    {
        $method = $this->getHttpRequest()->getMethod();

        switch ($method) {
            case 'GET':
                $this->processGet($id);
                break;

            case 'POST':
                $this->processPost();
                break;

            case 'PUT':
                $this->checkId($id);
                $this->processPut($id);
                break;

            case 'DELETE':
                $this->checkId($id);
                $this->processDelete($id);
                break;

            default:
                $this->sendError('Endpoint not found', IResponse::S404_NotFound);
                break;
        }
    }

    private function processDelete(?int $id): void
    {
        $user = $this->userService->get($id);

        if ($user) {
            $this->getHttpResponse()->setCode(IResponse::S204_NoContent);
            $this->userService->delete($user);
            $this->terminate();
        }

        $this->sendError('User not found', IResponse::S404_NotFound);
    }

    private function processPut(?int $id): void
    {
        $user = $this->userService->update($this->getHttpRequest()->getRawBody(), $id);
        if ($user) {
            $this->sendJsonResponse($user);
        } else {
            $this->sendError($this->userService->getError(), IResponse::S400_BadRequest);
        }
    }

    private function processPost(): void
    {
        $user = $this->userService->create($this->getHttpRequest()->getPost());
        if ($user) {
            $this->sendJsonResponse($user, IResponse::S201_Created);
        } else {
            $this->sendError($this->userService->getError(), IResponse::S400_BadRequest);
        }
    }

    private function processGet(?int $id): void
    {
        if ($id) {
            $user = $this->userService->get($id);
            if ($user) {
                $this->sendJsonResponse($user);
            } else {
                $this->sendError('User not found', IResponse::S404_NotFound);
            }
        }
        $this->sendJsonResponse($this->userService->getAll());
    }
}
