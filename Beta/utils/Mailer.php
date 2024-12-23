<?php
require __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . "/../includes/constants.php";
use \Mailjet\Resources;

function sendMailContactAnnouncer($emailAnnouncer, $type, $name, $firstname, $phone = '', $email, $move, $category, $city, $description, $token)
{
    $typeLabel = $type === 'pro' ? "professionnel" : "particulier";
    $moveLabel = $move == 'yes' ? "oui" : "non";

    $message = "<h4>Urgences - Mise en relation</h4>";
    $message .= "<p>Une personne essaye de prendre contact avec vous pour une annonce postée sur assistance-incendies.fr.</p><br><br>";

    $message .= "<ul>";
    $message .= "Vous trouverez ci-dessous le récapitulatif de votre annonce :";
    $message .= "<li>type: $category</li>";
    $message .= "<li>commune: $city</li>";
    $message .= "<li>description: $description</li>";
    $message .= "</ul><br><br>";

    $message .= "<ul>Voici ces coordonnées pour le contacter :";
    $message .= "<li>Je suis un {$typeLabel}</li>";
    $message .= "<li>Nom : {$name}</li>";
    $message .= "<li>Prénom : {$firstname}</li>";
    $message .= "<li>Téléphone : {$phone}</li>";
    $message .= "<li>Email : {$email}</li>";
    $message .= "<li>Pouvez-vous vous déplacer : {$moveLabel}</li>";
    $message .= "</ul>";

    $message .= "<br><br>";
    $url = SITE_URL . 'delete.php?token=' . $token;
    $message .= "Vous pouvez utiliser ce lien pour supprimer votre annonce : <a href='$url'>Lien de suppression</a>.";
    $message .= "<br><br>";

    $message .= "<table><tr><td><img src='https://assistance-incendies.adista.fr/site/img/logo.png'></td><td><a href='" . SITE_URL . "'>Assistance incendies</a></td></tr></table>";

    sendMail($emailAnnouncer, "Urgences - mise en relation - Demande", $message);
}

function sendMailOffer($target, $token, $category, $city, $description)
{

    $message = "<h4>Urgences - Mise en relation</h4>";
    $message = "<p>Vous avez posté une annonce sur assistance-incendies.adista.fr</p><br><br>";

    $message .= "<ul>";
    $message .= "Vous trouverez ci-dessous le récapitulatif de votre annonce :";
    $message .= "<li>type: $category</li>";
    $message .= "<li>commune: $city</li>";
    $message .= "<li>description: $description</li>";
    $message .= "</ul><br><br>";

    $url = SITE_URL . 'delete.php?token=' . $token;
    $message .= "Vous pouvez utiliser ce lien pour supprimer votre annonce : <a href='$url'>Lien de suppression</a>.";
    $message .= "<br><br>";

    $message .= "<table><tr><td><img src='https://assistance-incendies.adista.fr/site/img/logo.png'></td><td><a href='" . SITE_URL . "'>Assistance incendies</a></td></tr></table>";

    sendMail($target, "Urgences - mise en relation - Confirmation", $message);
}

function sendMail($to, $subject, $body)
{
    if (!strstr($_SERVER['HTTP_HOST'], "assistance-incendies.adista.fr")){
        return;
    }

    $apikey = '';
    $apisecret = '';
//    in-v3.mailjet.com // Port 25 ou 587
    $from = '';

    $mj = new \Mailjet\Client($apikey, $apisecret,true,['version' => 'v3.1']);

    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => $from
                ],
                'To' => [
                    [
                        'Email' => $to
                    ]
                ],
                'Subject' => $subject,
                'TextPart' => $subject,
                'HTMLPart' => $body
            ]
        ]
    ];

    $response = $mj->post(Resources::$Email, ['body' => $body]);

//    $response->success() && var_dump($response->getData());
    $response->success();
}
