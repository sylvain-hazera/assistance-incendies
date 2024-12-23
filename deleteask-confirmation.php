<?php

include_once __DIR__ . "/includes/classes/Ask.php";

if (isset($_GET['token'])){
    $result = Ask::deleteByToken($_GET['token']);
    if ($result > 0){
        $message = "Votre annonce a bien été supprimée";
        $class = 'alert alert-success';
    }else{
        $message = "Annonce invalide";
        $class = 'alert alert-danger';
    }
}else{
    header("location: index.php");
    exit();
}

include("header.php");
?>
<div class="container">
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="<?php echo $class ?>" role="alert">
                <h2><?php echo $message; ?></h2>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mb-4" onclick="location.href='index.php'">Retour à l'accueil</button>
    </div>
</div>
<?php

include("footer.php");

?>
