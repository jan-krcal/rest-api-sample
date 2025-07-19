<?php

declare(strict_types=1);

namespace App\Model\Services;

use App\Model\Entities\Article;
use App\Model\Entities\User;
use App\Model\Managers\ArticleManager;

final class ArticleService extends BaseService
{
    public function __construct(
        private readonly ArticleManager $articleManager
    ) {
    }

    public function get(int $id): ?Article
    {
        return $this->articleManager->find($id);
    }

    public function getAll(): array
    {
        return $this->articleManager->findAll();
    }

    public function delete(Article $article): void
    {
        $this->articleManager->delete($article);
    }

    public function create(array $data, User $user): ?Article
    {
        if ($this->validateData($data)) {
            $article = new Article();
            $article->setTitle($data['title']);
            $article->setContent($data['content']);
            $article->setAuthor($user);
            $article = $this->articleManager->create($article);
            return $article;
        }
        $this->error = 'Article insert failed,' . $this->error;
        return null;
    }

    public function update(?string $json, ?int $id): ?Article
    {
        if (!$this->isJsonValid($json)) {
            $this->error = 'Data format error';
            return null;
        }

        $article = $this->get($id);
        if (!$article) {
            $this->error = 'Article not found';
            return null;
        }

        $data = json_decode($json, true);
        if (isset($data['title']) && $data['title']) {
            $article->setTitle($data['title']);
        }

        if (isset($data['content']) && $data['content']) {
            $article->setTitle($data['content']);
        }

        $article = $this->articleManager->update($article);
        return $article;
    }

    private function validateData(array $data): bool
    {
        if (!isset($data['title']) || empty($data['title'])) {
            $this->error = ' Title is required';
            return false;
        }

        if (!isset($data['content']) || empty($data['content'])) {
            $this->error = ' Content is required';
            return false;
        }

        return true;
    }
}
