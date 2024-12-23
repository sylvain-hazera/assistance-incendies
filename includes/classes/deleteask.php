<?php
require_once __DIR__ . "/includes/classes/Ask.php";
include("header.php");

if (!isset($_GET['token'])){
    header("location: index.php");
    exit();
}

$ask = ask::loadByToken($_GET['token']);
if (!$ask){
    header("location: index.php");
    exit();
}

?>
<div class="container">
    <div class="row jumbotron jumbotron-fluid">
        <div class="container mt-4">
            <h1 class="display-6">Urgences - Mise en relation</h1>
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
    <br>
</div>


<div class="container">
    <div class="alert alert-success" role="warning">
        <p>Voulez-vous supprimer votre annonce ?</p>
    </div>
    <br>
    <button type="button" class="btn btn-danger mb-4" onclick="location.href='index.php'"><h2>Non</h2></button>
    <button type="button" class="btn btn-success mb-4" onclick="location.href='delete-confirmation.php?token=<?= $_GET['token']?>'"><h2>Oui</h2></button>
</div>

<?php
include("footer.php");
?>
