<?php
/*vypracované pomocou AI*/
/** @var array $errors */
/** @var string|null $name */
/** @var string|null $email */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('auth');
?>
<div class="container">
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="card card-signin my-5 position-relative">
                <!-- Back to home icon in the corner -->
                <a href="<?= $link->url('home.index') ?>" class="position-absolute"
                   style="top: 0.5rem; right: 0.75rem; text-decoration: none; font-size: 1.3rem;">
                    &#x2190;
                </a>
                <div class="card-body">
                    <h5 class="card-title text-center">Registrácia</h5>

                    <?php if (!empty($errors)): ?>
                        <div class="text-danger mb-3">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= $link->url('register') ?>">
                        <div class="form-label-group mb-3">
                            <label for="name" class="form-label">Meno</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control"
                                required
                                value="<?= htmlspecialchars($name ?? '') ?>"
                            >
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                required
                                value="<?= htmlspecialchars($email ?? '') ?>"
                            >
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="password" class="form-label">Heslo</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="form-label-group mb-4">
                            <label for="password_confirm" class="form-label">Potvrdenie hesla</label>
                            <input
                                type="password"
                                id="password_confirm"
                                name="password_confirm"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="text-center mb-2">
                            <button class="btn btn-primary" type="submit" name="submit">
                                Registrovať sa
                            </button>
                        </div>
                    </form>

                    <div class="mt-2 text-center">
                        <a href="<?= $link->url('login') ?>">Už máš účet? Prihlás sa.</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

