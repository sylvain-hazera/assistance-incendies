<?php
include("header.php");
?>
<div class="container">
    <div class="row jumbotron jumbotron-fluid">
        <div class="container mt-4">

            <h1 class="display-6">Service de centralisation des propositions et demandes en situation d'urgence</h1>
            <hr class="my-1">
        </div>
    </div>
</div>

<div class="container">
    <div class="d-grid gap-2">
        <a href="list-cards.php" class="btn btn-danger" type="button"><h1 style="margin:30px;">J'ai un besoin</h1></a>
        <a href="add.php" class="btn btn-success" type="button"><h1 style="margin:30px;">J'ai une solution</h1></a>
	<a href="preevac.php" class="btn btn-warning" type="button"><h1 style="margin:30px;">Je vais etre évacué => Consignes de bases</h1></a>
    </div>
    <div style="text-align: center;padding-top: 50px"><img src="img/logo.png"></div>
    <div class="d-grid gap-2">
	<a href="/test/index.php" class="btn btn-outline-primary" type="button"><h1 style="margin:30px;">Espace Test / Nouvelles fonctions en développement</h1></a>
    </div>
<div>
<?php
include("footer.php");
?>
