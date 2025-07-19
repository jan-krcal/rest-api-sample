<?php

declare(strict_types=1);

namespace App\Model\Services;

use App\Model\Entities\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Nette\Utils\Strings;

final class AuthService extends BaseService
{
    private const PRIVATE_KEY = 'pASZM4WGWd';
    private const AGL = 'HS256';
    private const ISS = 'Articles RESTAPI';
    private const TIME = 3600;

    public function __construct(
        private readonly UserService $userService
    ) {
    }

    public function login(array $data): ?string
    {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userService->getByEmail($email);
        if ($user && $this->userService->verifyPassword($password, $user->getPasswordHash())) {
            $payload = [
                'iss' => self::ISS,
                'iat' => time(),
                'exp' => time() + self::TIME,
                'sub' => $user->getId(),
            ];
            return JWT::encode($payload, self::PRIVATE_KEY, self::AGL);
        }
        return null;
    }

    public function verify(array $headers): ?User
    {
        try {
            $token = '';
            $authorization = $headers['authorization'] ?? null;
            if ($authorization) {
                $token = Strings::substring($authorization, 7);
            }
            $data = JWT::decode($token, new Key(self::PRIVATE_KEY, self::AGL));
            $userId = (int) $data->sub;
            return $this->userService->get($userId);
        } catch (Exception $e) {
            return null;
        }
    }
}
