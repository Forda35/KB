<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Récupération des données JSON envoyées depuis contact.html
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
  echo json_encode(["success" => false, "message" => "Données invalides."]);
  exit;
}

// Nettoyage et récupération des champs du formulaire
$name    = htmlspecialchars($data['name'] ?? '');
$email   = htmlspecialchars($data['email'] ?? '');
$message = htmlspecialchars($data['message'] ?? '');
$length  = htmlspecialchars($data['length'] ?? '');
$width   = htmlspecialchars($data['width'] ?? '');
$height  = htmlspecialchars($data['height'] ?? '');
$weight  = htmlspecialchars($data['weight'] ?? '');

// Vérification basique
if (empty($name) || empty($email) || empty($message)) {
  echo json_encode(["success" => false, "message" => "Veuillez remplir tous les champs requis."]);
  exit;
}

// Initialisation de PHPMailer
$mail = new PHPMailer(true);

try {
    // --- CONFIGURATION SMTP ---
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Serveur SMTP de Gmail
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ferdinandotsiremy@gmail.com'; //  Ton adresse Gmail expéditrice
    $mail->Password   = 'gono iqum iuob oeuy'; //  À remplacer (mot de passe d’application Gmail)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // --- EXPÉDITEUR ET DESTINATAIRE ---
    $mail->setFrom('ferdinandotsiremy@gmail.com', 'KB International');
    $mail->addAddress('ferdinandotsiremy@gmail.com', 'Service client KB International');
    $mail->addReplyTo($email, $name); // Le visiteur devient le "Reply-To"

    // --- CONTENU DU MESSAGE ---
    $mail->isHTML(true);
    $mail->Subject = "Nouveau message de $name via le site KB International";

    $body  = "<h3>Vous avez reçu un nouveau message depuis le site <strong>KB International</strong>.</h3>";
    $body .= "<p><strong>Nom :</strong> $name</p>";
    $body .= "<p><strong>Email :</strong> $email</p>";
    $body .= "<p><strong>Message :</strong><br>" . nl2br($message) . "</p>";

    // Si des détails du colis sont fournis, on les ajoute joliment
    if ($length || $width || $height || $weight) {
        $body .= "<h4>Détails de la marchandise</h4><ul>";
        if ($length) $body .= "<li>Longueur : {$length} m</li>";
        if ($width)  $body .= "<li>Largeur : {$width} m</li>";
        if ($height) $body .= "<li>Hauteur : {$height} m</li>";
        if ($weight) $body .= "<li>Poids : {$weight} kg</li>";
        $body .= "</ul>";
    }

    $mail->Body = $body;
    $mail->AltBody = strip_tags($body);

    // --- ENVOI DU MAIL ---
    $mail->send();

    echo json_encode(["success" => true, "message" => "Message envoyé avec succès !"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Erreur d’envoi : " . $mail->ErrorInfo]);
}
?>
