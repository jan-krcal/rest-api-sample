<?php

declare(strict_types=1);

namespace App\Model\Enums;

use App\Model\Entities\Article;
use App\Model\Entities\User;

enum UserRole: string
{
    case ADMIN = 'admin';
    case AUTHOR = 'author';
    case READER = 'reader';

    public function canCreateArticle(): bool
    {
        return match ($this) {
            self::ADMIN, self::AUTHOR => true,
            self::READER => false,
        };
    }

    public function canEditArticle(User $user, Article $article): bool
    {
        return match ($this) {
            self::ADMIN => true,
            self::AUTHOR => $article->getAuthor() === $user,
            self::READER => false,
        };
    }

    public function canDeleteArticle(User $user, Article $article): bool
    {
        return match ($this) {
            self::ADMIN => true,
            self::AUTHOR => $article->getAuthor() === $user,
            self::READER => false,
        };
    }

    public function canAccessUsers(): bool
    {
        return match ($this) {
            self::ADMIN => true,
            self::AUTHOR, self::READER => false,
        };
    }
}
