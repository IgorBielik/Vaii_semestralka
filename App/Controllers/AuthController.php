<?php

namespace App\Controllers;

use App\Configuration;
use App\Models\User;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;

/**
 * Class AuthController
 *
 * This controller handles authentication actions such as login, logout, and redirection to the login page. It manages
 * user sessions and interactions with the authentication system.
 *
 * @package App\Controllers
 */
class AuthController extends BaseController
{
    /**
     * Redirects to the login page.
     *
     * This action serves as the default landing point for the authentication section of the application, directing
     * users to the login URL specified in the configuration.
     *
     * @return Response The response object for the redirection to the login page.
     */
    public function index(Request $request): Response
    {
        return $this->redirect(Configuration::LOGIN_URL);
    }

    /**
     * Authenticates a user and processes the login request.
     *
     * This action handles user login attempts. If the login form is submitted, it attempts to authenticate the user
     * with the provided credentials. Upon successful login, the user is redirected to the admin dashboard.
     * If authentication fails, an error message is displayed on the login page.
     *
     * @return Response The response object which can either redirect on success or render the login view with
     *                  an error message on failure.
     * @throws Exception If the parameter for the URL generator is invalid throws an exception.
     */
    public function login(Request $request): Response
    {
        $logged = null;
        if ($request->hasValue('submit')) {
            // Treat username field from the form as email for DB-backed authentication
            $email = $request->value('username');
            $password = $request->value('password');
            $logged = $this->app->getAuthenticator()?->login($email, $password) ?? false;
            if ($logged) {
                return $this->redirect($this->url('home.index'));
            }
        }

        $message = $logged === false ? 'Bad username or password' : null;
        return $this->html(compact('message'));
    }

    /**
     * Logs out the current user.
     *
     * This action terminates the user's session and redirects them to a view. It effectively clears any authentication
     * tokens or session data associated with the user.
     *
     * @return ViewResponse The response object that renders the logout view.
     */
    public function logout(Request $request): Response
    {
        $this->app->getAuthenticator()->logout();
        return $this->html();
    }

    /**
     * Registers a new user.
     *
     * This action handles user registration requests. It validates the provided data, creates a new user record in
     * the database, and redirects the user to the login page upon successful registration.
     *
     * @return Response The response object which renders the registration view with error messages or redirects
     *                  to the login page on successful registration.
     * @throws Exception If there is an error during the registration process.
     */
    public function register(Request $request): Response
    {
        $errors = [];
        $name = $request->value('name');
        $email = $request->value('email');
        $password = $request->value('password');
        $passwordConfirm = $request->value('password_confirm');

        if ($request->isPost()) {
            if (!$name || !$email || !$password || !$passwordConfirm) {
                $errors[] = 'Všetky polia sú povinné.';
            }

            if ($password !== $passwordConfirm) {
                $errors[] = 'Heslá sa nezhodujú.';
            }

            // Server-side check: email už existuje v databáze?
            if (empty($errors)) {
                $existingUser = User::findByEmail($email);
                if ($existingUser !== null) {
                    $errors[] = 'Používateľ s týmto emailom už existuje.';
                }
            }

            if (empty($errors)) {
                $user = User::register($email, $password);
                $user->setName($name);
                $user->save();

                return $this->redirect(Configuration::LOGIN_URL);
            }
        }

        return $this->html([
            'errors' => $errors,
            'name' => $name,
            'email' => $email,
        ]);
    }
}
