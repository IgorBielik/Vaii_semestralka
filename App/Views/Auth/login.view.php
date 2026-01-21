<?php
/*vypracované pomocou AI*/
/** @var string|null $message */
/** @var string $email */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('auth');
$email = $email ?? '';
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
                    <h5 class="card-title text-center">Prihlásenie</h5>

                    <!-- Chybová správa -->
                    <?php if ($message): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Chyba!</strong> <?= htmlspecialchars($message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form class="form-signin" method="post" action="<?= $link->url("login") ?>">
                        <div class="form-label-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input name="email" type="email" id="email" class="form-control"
                                   placeholder="Email" value="<?= htmlspecialchars($email) ?>" autofocus>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="password" class="form-label">Heslo</label>
                            <input name="password" type="password" id="password" class="form-control"
                                   placeholder="Heslo">
                        </div>
                        <div class="text-center mb-3">
                            <button class="btn btn-primary" type="submit" name="submit">Prihlásiť sa</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <small>
                            Nemáš ešte účet?
                            <a href="<?= $link->url('register') ?>" class="text-decoration-none">Zaregistruj sa</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
