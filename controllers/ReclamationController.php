<?php
class ReclamationController {
    public function submit() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rec = new Reclamation();
            $rec->user_id = $_SESSION['user_id'];
            $rec->sujet = $_POST['sujet'];
            $rec->message = $_POST['message'];

            if ($rec->create()) {
                $_SESSION['flash_success'] = "Votre réclamation a bien été envoyée. Notre équipe la traitera dans les plus brefs délais.";
            } else {
                $_SESSION['flash_error'] = "Erreur lors de l'envoi de la réclamation.";
            }
        }
        
        header("Location: " . BASE_URL . "/?action=dashboard");
        exit;
    }
}
?>
