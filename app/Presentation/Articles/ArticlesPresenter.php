<?php

declare(strict_types=1);

namespace App\Presentation\Articles;

use App\Model\Services\ArticleService;
use App\Presentation\BasePresenter;
use Nette\Http\IResponse;

final class ArticlesPresenter extends BasePresenter
{
    public function __construct(
        private readonly ArticleService $articleService
    ) {
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
                $this->processPut($id);
                break;

            case 'DELETE':
                $this->processDelete($id);
                break;

            default:
                $this->sendError('Endpoint not found', IResponse::S404_NotFound);
                break;
        }
    }

    private function processDelete(?int $id): void
    {
        $article = $this->articleService->get($id);

        if ($article && $this->getCurrentUser()->getRole()->canEditArticle($this->getCurrentUser(), $article)) {
            $this->getHttpResponse()->setCode(IResponse::S204_NoContent);
            $this->articleService->delete($article);
            $this->terminate();
        }

        if (!$article) {
            $this->sendError('Article not found', IResponse::S404_NotFound);
        }

        if (!$this->getCurrentUser()->getRole()->canEditArticle($this->getCurrentUser(), $article)) {
            $this->sendError('Unauthorized to update this article', IResponse::S401_Unauthorized);
        }
    }

    private function processPut(?int $id): void
    {
        $article = $this->articleService->update($this->getHttpRequest()->getRawBody(), $id);

        if (!$this->getCurrentUser()->getRole()->canEditArticle($this->getCurrentUser(), $article)) {
            $this->sendError('Unauthorized to update this article', IResponse::S401_Unauthorized);
        }

        if ($article) {
            $this->sendJsonResponse($article);
        } else {
            $this->sendError($this->articleService->getError(), IResponse::S400_BadRequest);
        }
    }

    private function processPost(): void
    {
        if (!$this->getCurrentUser()->getRole()->canCreateArticle()) {
            $this->sendError('Unauthorized to create article', IResponse::S401_Unauthorized);
        }

        $article = $this->articleService->create($this->getHttpRequest()->getPost(), $this->getCurrentUser());
        if ($article) {
            $this->sendJsonResponse($article, IResponse::S201_Created);
        } else {
            $this->sendError($this->articleService->getError(), IResponse::S400_BadRequest);
        }
    }

    private function processGet(?int $id): void
    {
        if ($id) {
            $article = $this->articleService->get($id);
            if ($article) {
                $this->sendJsonResponse($article);
            } else {
                $this->sendError('Article not found', IResponse::S404_NotFound);
            }
        }
        $this->sendJsonResponse($this->articleService->getAll());
    }
}
