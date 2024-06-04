<?php
include("header.php");
?>
<div class="container">
    <div class="row jumbotron jumbotron-fluid">
        <div class="container mt-4">
            <h1 class="display-6">Environnement de tests</h1>
            <h3>Vous pouvez utiliser ces catégories pour découvrir le site</h3>
            <hr class="my-4">
        </div>
    </div>

</div>

<div class="container">
    <div class="d-grid gap-2">
        <a href="list-cards.php" class="btn btn-outline-danger" type="button"><h1 style="margin:30px;">J'ai un besoin</h1></a>
        <a href="add.php" class="btn btn-outline-success" type="button"><h1 style="margin:30px;">J'ai une solution</h1></a>
	<a href="preevac.php" class="btn btn-outline-warning" type="button"><h1 style="margin:30px;">Je vais etre évacué => Consignes de bases</h1></a>
	<a href="sig.php" class="btn btn-outline-warning" type="button"><h1 style="margin:30px;">"J'ai pu évacuer seul et tout va bien" ou "je suis absent" => Je me signale à ma Mairie, ils seront rassurés.</h1></a>
	<a href="sig.php" class="btn btn-outline-warning" type="button"><h1 style="margin:30px;">Je suis un particulier/une entreprise, j'ai besoin d'une prise en charge spécifique => Je me signale à ma Mairie</h1></a>
	<a href="login.php" class="btn btn-outline-info" type="button"><h1 style="margin:30px;">Espace Mairies et Associations Agréées.</h1></a>
    </div>
    <div style="text-align: center;padding-top: 50px"><img src="img/logo.png"></div>
    <div class="d-grid gap-2">
	<a href="/test/index.php" class="btn btn-outline-primary" type="button"><h1 style="margin:30px;">Espace Démonstration / apprentissage d'utilisation de l'outil</h1></a>
    </div>
<div>
<?php
include("footer.php");
?>
