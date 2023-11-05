<?php
include("header.php");
?>
<div class="container">
    <div class="row jumbotron jumbotron-fluid">
        <div class="container mt-4">
            <h1 class="display-6">Service de centralisation des propositions et demandes en situation d'urgence</h1>
                <p class="lead"></p>
            <hr class="my-4">
        </div>
    </div>
</div>

<div class="container">
    <div class="alert alert-success" role="alert">
        <p>Votre demande de mise en relation a bien été enregistrée.</p>
    </div>

    <p>Nous avons transmis vos coordonnées à l'annonceur, il devrait rapidement reprendre contact avec vous.</p>
    <br>

    <button type="button" class="btn btn-secondary mb-4" onclick="location.href='index.php'">Retour à l'accueil</button>
</div>

<?php
include("footer.php");
?>
