<?php
include("header.php");

include_once __DIR__ . "./includes/constants.php";
include_once __DIR__ . "./includes/classes/event.php";

$events = event::loadAll();

$currenteventId = !empty($_GET['event']) ? intval($_GET['event']) : 0;

$filters = [];
?>
<style>
    .suggestions>li{
        list-style: none;
        padding:3px;
        background-color: #d6d6d6;
        margin-top: 1px;
    }
    .suggestions>li:hover{
        background-color: #a1c7dc;
    }

</style>
<div class="container">
    <div class="row jumbotron jumbotron-fluid">
        <div class="container mt-4">
            <h1 class="display-6">Service de centralisation des propositions et demandes en situation d'urgence</h1>
            
                    <p class="lead">Pour créer un évenement, c'est par ici.</p>             
            </div>
            <hr class="my-1">
        </div>
    </div>
</div>

<div>
    <div class="container">
        <form>
            <input id="page" name="page" type="hidden" value="1"> <!-- Quand on lance une recherche, on revient toujours à la première page -->
            <div class="row mb-1 mt-1">
                <div class="form-group col-5">
                    <?php if (count($events)  > 0): ?>
                        <label for="event" class="p-1">Evenement</label>
                        <select class="form-select" id="event" name="event" aria-label="Default select example">
                            <option value="0" selected>Toutes</option>
                            <?php foreach ($events as $event) : ?>
                                <option value="<?= $event->getId() ?>" <?php if ($currenteventId == $event->getId()): ?>selected<?php endif ?>>
                                    <?= $event->getName() ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    <?php endif ?>
                </div>
            </div>
        </form>
</div>
</div>
<?php
include("footer.php");
?>

