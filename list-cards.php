<?php
include("header.php");

include_once __DIR__ . "/includes/constants.php";
include_once __DIR__ . "/includes/classes/Category.php";
include_once __DIR__ . "/includes/classes/City.php";
include_once __DIR__ . "/includes/classes/Offer.php";
include_once __DIR__ . "/includes/classes/Contact.php";
include_once __DIR__ . "/includes/classes/event.php";

$categories = Category::loadAll();
$cities = City::loadCitiesWithOffer();
$events = event::loadAll();

$currentPage = !empty($_GET['page']) ? intval($_GET['page']) : 1;
$currentCityId = !empty($_GET['city']) ? intval($_GET['city']) : 0;
$currentCategoryId = !empty($_GET['category']) ? intval($_GET['category']) : 0;
$currenteventId = !empty($_GET['event']) ? intval($_GET['event']) : 0;

$filters = [];
$filters['page'] = $currentPage;
$filters['city'] = $currentCityId;
$filters['category'] = $currentCategoryId;
$filters['event'] = $currenteventId;

$result = Offer::loadWithFilters($filters);
$offers = $result['offers'];
$nbOffers = $result['count'];

$nbPages = ceil($nbOffers / NB_OFFERS_PER_PAGE);
if ($currentPage == 0){
    $currentPage = 1;
}
if ($currentPage > $nbPages){
    $currentPage = $nbPages;
}
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
            
                    <p class="lead">Vous avez besoin d'aide ? Cherchez dans les annonces ci-dessous celles qui pourraient vous aider.</p>
                
<a href="list-cards.php" class="btn btn-outline-primary margin" role="button">Consulter les solutions</a>
<a href="list-ask.php" class="btn btn-outline-primary margin" role="button">Consulter les demandes</a>
<a href="add.php" class="btn btn-outline-primary margin" role="button">Proposer une solution</a>
<a href="addask.php" class="btn btn-outline-primary margin" role="button">Demander de l'aide</a>
                
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
                    <label for="cityName" class="p-1">Commune</label>
                    <input class="form-control" id="city" type="hidden" name="city" required value="<?php if(!empty($_GET['city'])) { echo htmlspecialchars($_GET['city'], ENT_QUOTES); } ?>"/>
                    <div class="input-group mb-3">
                        <input class="form-control" id="cityName" type="text" name="cityName" value="<?php if(!empty($_GET['cityName'])) { echo htmlspecialchars($_GET['cityName'], ENT_QUOTES); } ?>"/>
                        <button class="btn btn-outline-secondary" type="button" onclick="clearCity()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </button>
                    </div>
                    <ul class="suggestions" id="suggestions"></ul>
                </div>

                <div class="form-group col-5">
                    <?php if (count($categories)  > 0): ?>
                        <label for="category" class="p-1">Catégorie</label>
                        <select class="form-select" id="category" name="category" aria-label="Default select example">
                            <option value="0" selected>Toutes</option>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category->getId() ?>" <?php if ($currentCategoryId == $category->getId()): ?>selected<?php endif ?>>
                                    <?= $category->getName() ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    <?php endif ?>
                </div>
                <div class="form-group col-5">
                    <?php if (count($events)  > 0): ?>
                        <label for="event" class="p-1">Evenement</label>
                        <select class="form-select" id="event" name="event" aria-label="Default select example">
                            <option value="0" selected>Tous</option>
                            <?php foreach ($events as $event) : ?>
                                <option value="<?= $event->getId() ?>" <?php if ($currenteventId == $event->getId()): ?>selected<?php endif ?>>
                                    <?= $event->getName() ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    <?php endif ?>
                </div>

                <div class="form-group col-2">
                    <button type="submit" class="btn btn-primary mb-2" style="margin-top: 32px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

        </form>

        <?php foreach($offers as $offer): ?>
        <div class="card mb-4">
            <div class="card-header">
                <?= $offer->getCategory()->getName() . ' | ' . $offer->getCity()->getName() ?> | Publiée le : <?= date("d/m/Y", strtotime($offer->getDateCreation()) ); ?>
            </div>
            <div class="card-body">
                <p class="card-text"><?= $offer->getDescription() ?></p>
                <div style="display: flex; justify-content: right">
                    <a href="contact-announcer.php?offer=<?= $offer->getId() ?>" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if ($nbPages > 0):
            $params = $_GET;
            unset($params['page']);
        ?>
        <nav aria-label="Page navigation example" style="justify-content: right; display: flex">
            <ul class="pagination">
                <li class="page-item">
                    <a class="page-link" href="list-cards.php?<?=http_build_query($params)?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $nbPages; $i++): ?>
                <li class="page-item <?php if ($i == $currentPage): ?>active<?php endif ?>">
                    <a class="page-link" href="list-cards.php?<?=http_build_query(['page' => $i] + $params)?>"><?=$i?></a>
                </li>
                <?php endfor ?>
                <li class="page-item">
                    <a class="page-link" href="list-cards.php?<?=http_build_query(['page' => $nbPages] + $params)?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <?php else: ?>
        Aucun résultat pour cette recherche
        <?php endif ?>
    </form>
</div>
<script>
    <?php
    $citiesObj = [];
    $citiesIds = [];
    foreach ($cities as $city) {
        $citiesObj[] = $city->getName();
        $citiesIds[] = ['id' => $city->getId(), 'name' => $city->getName()];
    }

    $citiesFormatted = json_encode($citiesObj);
    $citiesIdsFormatted = json_encode($citiesIds);

    echo "const cities = JSON.parse('$citiesFormatted');";
    echo "const citiesIds = JSON.parse('$citiesIdsFormatted');";
    ?>

    (function () {
        "use strict";
        let inputField = document.getElementById('cityName');
        let inputFieldId = document.getElementById('city');
        let ulField = document.getElementById('suggestions');
        inputField.addEventListener('input', changeAutoComplete);
        ulField.addEventListener('click', selectItem);

        function changeAutoComplete({ target }) {
            let data = target.value;
            ulField.innerHTML = ``;
            inputFieldId.value = 0;
            if (data.length > 1) {
                let autoCompleteValues = autoComplete(data);
                autoCompleteValues.forEach(value => { addItem(value); });
            }
        }

        function autoComplete(inputValue) {
            let destination = cities;
            return destination.filter(
                (value) => value.toLowerCase().includes(inputValue.toLowerCase())
            );
        }

        function addItem(value) {
            ulField.innerHTML = ulField.innerHTML + `<li>${value}</li>`;
        }

        function selectItem({ target }) {
            if (target.tagName === 'LI') {
                inputField.value = target.textContent;
                const index = citiesIds.findIndex((element) => {
                    return element.name === target.textContent;
                });

                inputFieldId.value = citiesIds[index].id;
                ulField.innerHTML = ``;
            }
        }
    })();

    function clearCity(){
        document.getElementById('cityName').setAttribute('value', '');
        document.getElementById('city').setAttribute('value', '0');
    }
</script>
<?php
include("footer.php");
?>

