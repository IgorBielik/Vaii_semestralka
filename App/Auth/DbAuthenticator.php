<?php

namespace App\Auth;

use App\Models\User;
use Framework\Auth\SessionAuthenticator;
use Framework\Core\App;
use Framework\Core\IIdentity;

/**
 * Db-backed authenticator that uses the User model for authentication.
 */
class DbAuthenticator extends SessionAuthenticator
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Authenticate user by email and password using the User model.
     */
    protected function authenticate(string $username, string $password): ?IIdentity
    {
        // In this app, username is the email
        $user = User::findByEmail($username);
        if ($user && $user->verifyPassword($password)) {
            // User implements IIdentity, so we can return it directly
            return $user;
        }
        return null;
    }
}