<?php
include("header.php");

include_once "./includes/classes/Category.php";
include_once "./includes/classes/City.php";
include_once "./includes/classes/Offer.php";
include_once "./includes/classes/Contact.php";

$categories = Category::loadAll();
$cities = City::loadAll();
$offers = Offer::loadAll();

?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col d-flex justify-content-center p-2"><h2>Stock disponible à ce jour</h2></div>
    </div>
    <div class="row">
        <div class="col-6">
            <?php if (count($categories)  > 0) : ?>
                <label for="category"></label>
                <select class="form-select" id="category" aria-label="Default select example">
                    <option value="" selected>Catégorie..</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category->getId() ?>"><?= $category->getName() ?></option>
                    <?php endforeach ?>
                </select>
            <?php endif ?>
        </div>
        <div class="col-6">
            <?php if (count($cities) > 0) : ?>
                <label for="commune"></label>
                <select class="form-select" id="commune" aria-label="Default select example">
                    <option value="" selected>Commune..</option>
                    <?php foreach ($cities as $city) : ?>
                        <option value="<?= $city->getId() ?>"><?= $city->getName() ?></option>
                    <?php endforeach ?>
                </select>
            <?php endif ?>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Catégorie</th>
                        <th>Commune</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody id="list-stock">
                    <?php if (count($offers) > 0) : ?>
                        <?php foreach ($offers as $offer) : ?>
                            <tr
                                class="row-stock"
                                data-category="<?= $offer->getCategory()->getId() ?>"
                                data-commune="<?= $offer->getCity()->getId() ?>"
                                data-url="contact-announcer.php?offer=<?= $offer->getId() ?>"
                                style="cursor: pointer;"
                            >
                                <td><?= $offer->getCategory()->getName() ?></td>
                                <td><?= $offer->getCity()->getName() ?></td>
                                <td><?= $offer->getDescription() ?></td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="application/javascript">
    const rows = document.querySelectorAll('tr.row-stock');
    const selectCategory = document.querySelector('#category');
    const selectCommune = document.querySelector('#commune');

    rows.forEach(row => row.addEventListener('click', () => {
        window.location = row.dataset.url;
    }));

    selectCategory.addEventListener('change', event => {
        const categoryId = event.target.value;
        const communeId = selectCommune.selectedOptions[0].value;

        filterRows(categoryId, communeId);
    });

    selectCommune.addEventListener('change', event => {
        const communeId = event.target.value;
        const categoryId = selectCategory.selectedOptions[0].value;

        filterRows(categoryId, communeId);
    });

    function filterRows(categoryId, communeId) {
        const rowsShow = Array.from(rows).filter(n => (categoryId ? n.dataset.category === categoryId : true) && (communeId ? n.dataset.commune === communeId : true));
        const rowsHide = Array.from(rows).filter(r => !rowsShow.includes(r));

        rowsShow.forEach(function (rh) {
            rh.style = 'display:table-row'
        });
        rowsHide.forEach(function (rh) {
            rh.style = 'display:none'
        })
    }
</script>

<?php
include("footer.php");
?>

