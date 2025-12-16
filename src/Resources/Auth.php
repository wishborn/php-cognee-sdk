<?php

declare(strict_types=1);

namespace Cognee\Resources;

use Cognee\Models\User;

/**
 * Auth resource for authentication operations.
 */
class Auth extends AbstractResource
{
    /**
     * Login with email and password.
     *
     * @param string $email User email
     * @param string $password User password
     * @return User
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function login(string $email, string $password): User
    {
        $response = $this->post('api/v1/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);

        return User::fromArray($response['user'] ?? $response);
    }

    /**
     * Logout the current user.
     *
     * @return bool
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function logout(): bool
    {
        $this->post('api/v1/auth/logout');

        return true;
    }

    /**
     * Register a new user.
     *
     * @param array<string, mixed> $data Registration data (email, password, name, etc.)
     * @return User
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function register(array $data): User
    {
        $response = $this->post('api/v1/auth/register', $data);

        return User::fromArray($response['user'] ?? $response);
    }

    /**
     * Request a password reset.
     *
     * @param string $email User email
     * @return bool
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function forgotPassword(string $email): bool
    {
        $this->post('api/v1/auth/forgot-password', ['email' => $email]);

        return true;
    }

    /**
     * Verify email with token.
     *
     * @param string $token Verification token
     * @return bool
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function verify(string $token): bool
    {
        $this->post('api/v1/auth/verify', ['token' => $token]);

        return true;
    }
}
