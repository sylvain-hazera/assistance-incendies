<?php
include("header.php");

include_once "./includes/classes/Category.php";
include_once "./includes/classes/City.php";
include_once "./includes/classes/Offer.php";
include_once "./includes/classes/Contact.php";
include_once "./includes/constants.php";

require_once("utils/Mailer.php");

$categories = Category::loadAll();
$cities = City::loadAll();

$types = TYPE_CONTACT;

$saved = false;
$error = '';
if(!empty($_POST))
{
    // part 1
    if(empty($_POST['city']) || $_POST['city'] == 0) {
        $error = 'Veuillez indiquer une commune !';
    }

    if (empty($_POST['category']))
    {
        $error = 'Veuillez indiquer votre proposition !';
    }

    if (empty($_POST['description']))
    {
        $error = 'Veuillez indiquer une description !';
    }

    // part 2
    if(empty($_POST['name']))
    {
        $error = 'Veuillez indiquer un nom !';
    }

    if (empty($_POST['email']))
    {
        $error = 'Veuillez indiquer un email !';
    }

    if (!$error) {
        // add contact
        $contactObj = new Contact();
        $contactObj->setName(isset($_POST['name']) ? htmlentities($_POST['name']) : '');
        $contactObj->setFirstName(isset($_POST['firstName']) ? htmlentities($_POST['firstName']) : '');
        $contactObj->setType(isset($_POST['type']) ? htmlentities($_POST['type']) : '');
        $contactObj->setPhone(isset($_POST['phone']) ? htmlentities($_POST['phone']) : '');
        $contactObj->setEmail(isset($_POST['email']) ? htmlentities($_POST['email']) : '');
        $contactObj->setIsMovable(isset($_POST['move']) && $_POST['move'] == 1);
        $contactObj->setIsVisible(isset($_POST['visible']) && $_POST['visible'] == 1);
        //$contactObj->setAcceptNotification(isset($_POST['notification']) && $_POST['notification'] == 1);
        $contactObj->setAcceptNotification(true);

        // city
        $cityObj = City::load(isset($_POST['city']) ? intval($_POST['city']) : 0);

        // cat
        $categoryObj = Category::load(isset($_POST['category']) ? intval($_POST['category']) : 0);

        if ($cityObj && $contactObj && $categoryObj) {
            // add offer
            $offer = new Offer();
            $offer->setCategory($categoryObj);
            $offer->setCity($cityObj);
            $offer->setContact($contactObj);
            $offer->setDescription(isset($_POST['description']) ? htmlentities($_POST['description']) : '');
            $offer->setDateCreation(new DateTime());
            $offer->save();

            $saved = true;

            sendMailOffer(
                $contactObj->getEmail(),
                $offer->getToken(),
                $offer->getCategory()->getName(),
                $offer->getCity()->getName(),
                $offer->getDescription()
            );

            header("Location: add-confirmation.php");
            exit();
        }
    }
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
            <p class="lead">Veuillez remplir ce formulaire pour décrire votre demande.</p>
             <a href="list-cards.php" class="btn btn-outline-primary margin" role="button">Consulter les solutions</a>
            <hr class="my-4">
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <form action="" method="post" id="offerForm">

            <?php if(!empty($error)) : ?>
                <div class="alert alert-danger" role="alert">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
                <div>
                    <div class="form-group mb-4">
                        <label for="cityName">Commune*</label>
                        <input class="form-control" id="city" type="hidden" name="city" required value="<?php if(!empty($_POST['city'])) { echo htmlspecialchars($_POST['city'], ENT_QUOTES); } ?>"/>
                        <input class="form-control" id="cityName" type="text" name="cityName" required value="<?php if(!empty($_POST['cityName'])) { echo htmlspecialchars($_POST['cityName'], ENT_QUOTES); } ?>"/>
                        <ul class="suggestions" id="suggestions"></ul>
                    </div>

                    <div class="form-group mb-4">
                        <label for="category">Je propose*</label>
                        <select class="form-control" name="category" id="category" required>
                            <?php foreach ($categories as $categoryKey => $category) : ?>
                                <option value="<?php echo $category->getId() ?>" <?php echo isset($_POST['category']) && $_POST['category'] == $category->getId()  ? 'selected' : '' ?>><?php echo $category->getName() ?></option>
                            <?php endforeach ?>
                        </select>
                      </div>

                    <div class="form-group mb-4">
                        <label for="description">Descriptif*</label>
                        <textarea class="form-control" id="description" name="description" rows="5" cols="33" required><?php if(!empty($_POST['description'])) { echo htmlspecialchars($_POST['description'], ENT_QUOTES); } ?></textarea>
                    </div>
                </div>

                <div>
                    <div class="form-group mb-4">
                        <label for="type">Je suis</label>
                        <select class="form-control" name="type" id="type">
                            <?php foreach ($types as $typeKey => $typeName) : ?>
                                <option value="<?php echo $typeKey ?>" <?php echo isset($_POST['type']) && $_POST['type'] == $typeKey  ? 'selected' : '' ?>><?php echo $typeName ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="name">Nom*</label>
                        <input class="form-control" required type="name" name="name" id="name" value="<?php if(!empty($_POST['name'])) { echo htmlspecialchars($_POST['name'], ENT_QUOTES); } ?>" />
                    </div>

                    <div class="form-group mb-4">
                        <label for="firstName">Prénom</label>
                        <input class="form-control" type="firstName" name="firstName" id="firstName" value="<?php if(!empty($_POST['firstName'])) { echo htmlspecialchars($_POST['firstName'], ENT_QUOTES); } ?>" />
                    </div>

                    <div class="form-group mb-4">
                        <label for="phone">Téléphone</label>
                        <input class="form-control" type="phone" name="phone" id="phone" value="<?php if(!empty($_POST['phone'])) { echo htmlspecialchars($_POST['phone'], ENT_QUOTES); } ?>" />
                    </div>

                    <div class="form-group mb-4">
                        <label for="email">Email*</label>
                        <input required class="form-control" type="email" name="email" id="email" value="<?php if(!empty($_POST['email'])) { echo htmlspecialchars($_POST['email'], ENT_QUOTES); } ?>" />
                    </div>

                    <div class="form-group mb-4">
                        <label for="move">Je peux me déplacer ?</label>
                        <select class="form-control" name="move" id="move">
                            <option value="0" <?php echo isset($_POST['move']) && $_POST['move'] == 0  ? 'selected' : '' ?>>Non</option>
                            <option value="1" <?php echo isset($_POST['move']) && $_POST['move'] == 1  ? 'selected' : '' ?>>Oui</option>
                        </select>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="visible" name="visible" value="1" <?php echo isset($_POST['visible']) ? 'checked' : '' ?>>
                        <label for="visible" class="form-check-label">
                            Par défaut, vos coordonnées seront masquées sur l'annonce. Cochez la case pour les rendre visibles.
                        </label><br>
                        <!--<input class="form-check-input" type="checkbox" id="cgu" name="cgu" value="1">
                        <label for="cgu" class="form-check-label" required>
                            J'accepte les conditions générales d'utilisation
                        </label><br>-->
                        <!--<input class="form-check-input" type="checkbox" id="notification" name="notification" value="1" <?php echo isset($_POST['notification']) ? 'checked' : '' ?>><label for="notification" class="form-check-label">Mail ou SMS de contact</label><br>-->
                    </div>
                </div>
                <div style="text-align: center;"><button type="submit" class="btn btn-primary mb-4"><h4>Enregistrer</h4></button></div>
                <p class="text-muted">* Champs obligatoires</p>
                <hr class="my-4">
                <span class="text-muted" style="font-size: 12px">
                    Les informations recueillies font l'objet d'un traitement informatique à but unique de mettre en relation des personnes physiques ou morales en fonction de leurs offres ou demandes.
                    <br/>
                    Aucune donnée ne sera transmise, vendue ou louée à des tiers en dehors du but défini.
                    <br/>
                    Pour faire valoir votre droit d'accès, de modification ou de suppression de vos données au regard de la loi informatique et liberté, contactez le gestionnaire de l'application : ADISTA à <a href="mailto:assistance-incendies@adista.fr">assistance-incendies@adista.fr</a>.
                    <br/>
                    Toute dégradation, préjudices ou sinistres survenant dans le cadre de l'entraide ne peut en aucun cas relever de notre responsabilité.
                </span>
        </form>
    </div>
</div>
<script>
    <?php
    $citiesObj = [];
    $citiesIds = [];
    foreach ($cities as $city) {
        $citiesObj[] = $city->getName();
        $citiesIds[] = ['id' => $city->getId(), 'name' => $city->getName()];
    }

    $citiesFormated = json_encode($citiesObj);
    $citiesIdsFormated = json_encode($citiesIds);

    echo "const cities = JSON.parse('$citiesFormated');";
    echo "const citiesIds = JSON.parse('$citiesIdsFormated');";
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
</script>
<?php
include("footer.php");
?>
