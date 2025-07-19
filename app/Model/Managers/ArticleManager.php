<?php

declare(strict_types=1);

namespace App\Model\Managers;

use App\Model\Entities\Article;
use Doctrine\ORM\EntityManagerInterface;

final class ArticleManager
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

    public function find(int $id): ?Article
    {
        return $this->em->getRepository(Article::class)->find($id);
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Article::class)->findAll();
    }

    public function create(Article $article): Article
    {
        $this->em->persist($article);
        $this->em->flush();
        return $article;
    }

    public function update(Article $article): Article
    {
        $this->em->flush();
        return $article;
    }

    public function delete(Article $article): void
    {
        $this->em->remove($article);
        $this->em->flush();
    }
}
