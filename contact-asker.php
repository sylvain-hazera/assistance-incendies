<?php
require_once( __DIR__ . "/utils/Mailer.php");
include_once __DIR__ . "/includes/classes/Contact.php";
include_once __DIR__ . "/includes/classes/Ask.php";

if (empty($_GET['ask'])) {
    header('Location: list-ask.php');
    exit();
}

$ask = Ask::load($_GET['ask']);
if (!$ask) {
    header('Location: list-ask.php');
    exit();
}

if (!empty($_POST)) {
    if (empty($_POST['type'])) {
        echo 'Veuillez sélectionner le type.';
    }
    if (empty($_POST['name'])) {
        echo 'Veuillez saisir un nom.';
    }
    if (empty($_POST['firstname'])) {
        echo 'Veuillez saisir un prénom.';
    }
    if (empty($_POST['email'])) {
        echo 'Veuillez saisir un email.';
    }
    if (empty($_POST['move'])) {
        echo 'Veuillez indiquer si vous pouvez vous déplacer.';
    }

    $emailAnnouncer = $ask->getContact()->getEmail();
    sendMailContactAnnouncer(
        $emailAnnouncer,
        htmlspecialchars($_POST['type']),
        htmlspecialchars($_POST['name']),
        htmlspecialchars($_POST['firstname']),
        (isset($_POST['phone']) && !empty($_POST['phone'])) ? htmlspecialchars($_POST['phone']) : '',
        htmlspecialchars($_POST['email']),
        htmlspecialchars($_POST['move']),
        $ask->getCategory()->getName(),
        $ask->getCity()->getName(),
        $ask->getDescription(),
        $ask->getToken()
    );

    header('Location: contact-announcer-confirmation.php');
    exit();
}

include("header.php");
?>
<div class="container">
    <div class="row jumbotron jumbotron-fluid">
        <div class="container mt-4">
            <h1 class="display-6">Service de centralisation des propositions et demandes en situation d'urgence</h1>
            <?php if ($ask->getContact()->isVisible()): ?>
                <p class="lead">Vous pouvez contacter la personne ci-dessous pour être mis en relation.</p>
            <?php else: ?>
                <p class="lead">Saisissez vos coordonnées pour être mis en relation avec l'annonceur.</p>
            <?php endif; ?>
            <hr class="my-4">
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <?= $ask->getCategory()->getName() . ' | ' . $ask->getCity()->getName() ?>
        </div>
        <div class="card-body">
            <p class="card-text"><?= $ask->getDescription() ?></p>
        </div>
    </div>
</div>

<hr class="my-4">

<div class="container">
    <?php if ($ask->getContact()->isVisible()): ?>
        <div class="row">
            <h4>Personne à contacter</h4>
            <ul style="margin-left: 1rem;">
                <li><?= $ask->getContact()->getFirstName() . ' ' . $ask->getContact()->getName() ?></li>
                <li>Téléphone : <a href="tel:<?= $ask->getContact()->getPhone() ?>"><?= $ask->getContact()->getPhone() ?></a></li>
                <li>Email : <a href="mailto:<?= $ask->getContact()->getEmail() ?>"><?= $ask->getContact()->getEmail() ?></a></li>
            </ul>
            <br>
        </div>
    <?php else: ?>
        <div class="row justify-content-center">
            <form method="post" id="contact-announcer" name="contact-announcer" action="contact-announcer.php?ask=<?= $ask->getId() ?>">
                <h4 class="mb-4">Vos informations personnelles</h4>

                <div class="form-group mb-4">
                    <label for="type">Je suis</label>
                    <select id="type" name="type" class="form-control">
                        <option value="particular">Un particulier</option>
                        <option value="pro">Un professionnel</option>
                    </select>
                </div>
                <div class="form-group mb-4">
                    <label for="name">Nom *</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group mb-4">
                    <label for="firstname">Prénom *</label>
                    <input type="text" id="firstname" name="firstname" class="form-control" required>
                </div>
                <div class="form-group mb-4">
                    <label for="phone">Téléphone</label>
                    <input type="tel" id="phone" name="phone" class="form-control">
                </div>
                <div class="form-group mb-4">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="move">Je peux me déplacer ?</label>
                    <select id="move" name="move" class="form-control">
                        <option value="no">Non</option>
                        <option value="yes">Oui</option>
                    </select>
                </div>
                <br>
                <p class="text-muted">* Champs obligatoires</p>
                <div style="text-align: center;"><button type="submit" class="btn btn-primary mb-4"><h4>Envoyer la demande</h4></button></div>
            </form>
        </div>
    <?php endif; ?>
    <div style="text-align: center;"><button type="button" class="btn btn-secondary mb-4" onclick="history.back();"><h5>Retour à la liste</h5></button></div>
    <?php if (!$ask->getContact()->isVisible()): ?>
    <hr class="my-4">
    <span class="text-muted" style="font-size: 12px">
Le site d'assistance traite les données recueillies uniquement pour vous mettre en contact avec l'annonceur.

Pour en savoir plus sur la gestion de vos données personnelles et pour exercer vos droits, reportez-vous à la notice ci-jointe.
    </span>
    <?php endif ?>
</div>

<?php
include("footer.php");
?>
