<?php
session_start(); // Démarrer la session pour stocker les jetons

include("header.php");

include_once "./includes/classes/Category.php";
include_once "./includes/classes/City.php";
include_once "./includes/classes/Offer.php";
include_once "./includes/classes/Contact.php";
include_once "./includes/constants.php";
include_once "./includes/classes/event.php";

require_once("utils/Mailer.php");

$categories = Category::loadAll();
$cities = City::loadAll();
$events = Event::loadAll();

$types = TYPE_CONTACT;

$saved = false;
$error = '';

// Générer un jeton s'il n'existe pas déjà
if (!isset($_SESSION['upload_token'])) {
    $_SESSION['upload_token'] = bin2hex(random_bytes(32)); // Générer un jeton aléatoire de 32 octets
}

if(!empty($_POST))
{
    // Vérifier si le jeton est présent et valide
    if (!isset($_POST['upload_token']) || $_POST['upload_token'] !== $_SESSION['upload_token']) {
        $error = "Jeton invalide. Veuillez réessayer.";
    } else {
        // Le jeton est valide, procéder avec le téléversement de fichiers

        if (empty($_POST['event'])) {
            $error = 'Veuillez indiquer votre Evenement !';
        }
        
        if(empty($_POST['city']) || $_POST['city'] == 0) {
            $error = 'Veuillez indiquer une commune !';
        }

        if (empty($_POST['category'])) {
            $error = 'Veuillez indiquer votre proposition !';
        }

        if (empty($_POST['description'])) {
            $error = 'Veuillez indiquer une description !';
        }

        if(empty($_POST['name'])) {
            $error = 'Veuillez indiquer un nom !';
        }

        if (empty($_POST['email'])) {
            $error = 'Veuillez indiquer un email !';
        }

        if (!$error) {
            $contactObj = new Contact();
            $contactObj->setName(isset($_POST['name']) ? htmlentities($_POST['name']) : '');
            $contactObj->setFirstName(isset($_POST['firstName']) ? htmlentities($_POST['firstName']) : '');
            $contactObj->setType(isset($_POST['type']) ? htmlentities($_POST['type']) : '');
            $contactObj->setPhone(isset($_POST['phone']) ? htmlentities($_POST['phone']) : '');
            $contactObj->setEmail(isset($_POST['email']) ? htmlentities($_POST['email']) : '');
            $contactObj->setIsMovable(isset($_POST['move']) && $_POST['move'] == 1);
            $contactObj->setIsVisible(isset($_POST['visible']) && $_POST['visible'] == 1);
            $contactObj->setAcceptNotification(true);

            $cityObj = City::load(isset($_POST['city']) ? intval($_POST['city']) : 0);
            $categoryObj = Category::load(isset($_POST['category']) ? intval($_POST['category']) : 0);
            $eventObj = Event::load(isset($_POST['event']) ? intval($_POST['event']) : 0);

            if ($cityObj && $contactObj && $categoryObj) {
                $offer = new Offer();
                $offer->setCategory($categoryObj);
                $offer->setEvent($eventObj);
                $offer->setCity($cityObj);
                $offer->setContact($contactObj);
                $offer->setDescription(isset($_POST['description']) ? htmlentities($_POST['description']) : '');
                $offer->setDateCreation(new DateTime());
                $offer->save();

                // Upload des photos si la catégorie est "technique"
                if ($categoryObj->getName() === "technique") {
                    $photo1 = uploadFile($_FILES['photo1']);
                    $photo2 = uploadFile($_FILES['photo2']);
                    $photo3 = uploadFile($_FILES['photo3']);

                    // Si tous les téléversements se sont bien déroulés, enregistrez les noms des fichiers dans la base de données
                    if ($photo1 && $photo2 && $photo3) {
                        $offer->setPhoto1($photo1);
                        $offer->setPhoto2($photo2);
                        $offer->setPhoto3($photo3);
                        $offer->save();
                    } else {
                        // En cas d'échec, vous pouvez gérer les erreurs ici
                        $error = "Une erreur s'est produite lors du téléversement des photos.";
                    }
                }

                $saved = true;

                sendMailOffer(
                    $contactObj->getEmail(),
                    $offer->getToken(),
                    $offer->getCategory()->getName(),
                    $offer->getEvent()->getName(),
                    $offer->getCity()->getName(),
                    $offer->getDescription()
                );

                header("Location: add-confirmation.php");
                exit();
            }
        }
    }
}

/**
 * Fonction pour téléverser un fichier.
 * @param array $file Le tableau représentant le fichier téléversé ($_FILES)
 * @return string|bool Le nom du fichier téléversé ou false en cas d'erreur
 */
function uploadFile($file) {
    // Vérifiez si un fichier a été téléversé et s'il n'y a pas d'erreur
    if ($file['error'] === UPLOAD_ERR_OK && is_uploaded_file($file['tmp_name'])) {
        $filename = basename($file['name']);

        // Générez un nom de fichier unique pour éviter les collisions
        $filename = uniqid() . '_' . $filename;

        // Déplacez le fichier téléversé vers le dossier de destination (hors de la racine web)
        $destination = '/chemin/vers/le/dossier/de/destination/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }
    }
    return false;
}
?>
