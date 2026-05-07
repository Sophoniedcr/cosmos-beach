<?php
class ReservationController {
    public function book_activity() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once 'models/Activity.php';
            $activityModel = new Activity();
            $activity = $activityModel->getById($_POST['activite_id']);
            
            if (!$activity) {
                $_SESSION['flash_error'] = "Activité introuvable.";
                header("Location: " . BASE_URL . "/?action=dashboard");
                exit;
            }

            $reservation = new Reservation();
            $reservation->user_id = $_SESSION['user_id'];
            $reservation->activite_id = $activity['id'];
            $reservation->date_reservation = $_POST['date_reservation'];
            
            $type = $_POST['activite_type'] ?? $activity['type'];
            $prixUnitaire = $activity['prix'];
            
            // Initialisation des valeurs par défaut
            $reservation->nombre_personnes = 1;
            $reservation->nombre_chambres = null;
            $reservation->mode_reservation = null;
            $reservation->nombre_tables = null;
            $reservation->nombre_adultes = null;
            $reservation->nombre_enfants = null;
            $reservation->montant_total = 0;

            // Logique métier selon le type
            if ($type === 'chambre') {
                $reservation->nombre_personnes = max(1, (int)($_POST['nombre_personnes'] ?? 1));
                $reservation->mode_reservation = $_POST['mode_reservation'] === 'separe' ? 'separe' : 'partage';
                $reservation->nombre_chambres = max(1, (int)($_POST['nombre_chambres'] ?? 1));
                
                // Validation de cohérence basique
                if ($reservation->mode_reservation === 'separe' && $reservation->nombre_chambres < $reservation->nombre_personnes) {
                    $reservation->nombre_chambres = $reservation->nombre_personnes;
                }
                
                $reservation->montant_total = $prixUnitaire * $reservation->nombre_chambres;

            } elseif ($type === 'restaurant') {
                $reservation->nombre_tables = max(1, (int)($_POST['nombre_tables'] ?? 1));
                // Le montant est de 0 car paiement sur place
                $reservation->montant_total = 0;
                // Pour la vérification de capacité, on stockera temporairement tables dans personnes ou on gérera spécifiquement
                $reservation->nombre_personnes = $reservation->nombre_tables; // Pour l'algo isAvailable

            } elseif (in_array($type, ['piscine_ordinaire', 'piscine_vip'])) {
                $reservation->nombre_adultes = max(0, (int)($_POST['nombre_adultes'] ?? 1));
                $reservation->nombre_enfants = max(0, (int)($_POST['nombre_enfants'] ?? 0));
                
                // Au moins 1 adulte ou 1 enfant requis
                if ($reservation->nombre_adultes + $reservation->nombre_enfants === 0) {
                     $_SESSION['flash_error'] = "Veuillez sélectionner au moins une personne.";
                     header("Location: " . BASE_URL . "/?action=activity_details&id=" . $reservation->activite_id);
                     exit;
                }
                
                $reservation->nombre_personnes = $reservation->nombre_adultes + $reservation->nombre_enfants;
                $reservation->montant_total = ($reservation->nombre_adultes * $prixUnitaire) + ($reservation->nombre_enfants * ($prixUnitaire / 2));

            } else {
                // Défaut (zoo, etc)
                $reservation->nombre_personnes = max(1, (int)($_POST['nombre_personnes'] ?? 1));
                $reservation->montant_total = $prixUnitaire * $reservation->nombre_personnes;
            }

            // Vérification de la disponibilité (Algorithme métier)
            if (!$reservation->isAvailable($reservation->activite_id, $reservation->date_reservation)) {
                $_SESSION['flash_error'] = "Désolé, la capacité maximale pour cette date est atteinte. Veuillez choisir un autre jour.";
                header("Location: " . BASE_URL . "/?action=activity_details&id=" . $reservation->activite_id);
                exit;
            }

            if ($reservation->create()) {
                $_SESSION['flash_success'] = "Votre réservation a été enregistrée avec succès et est en attente de paiement.";
                header("Location: " . BASE_URL . "/?action=dashboard");
                exit;
            } else {
                $_SESSION['flash_error'] = "Erreur lors de la réservation.";
                header("Location: " . BASE_URL . "/?action=activity_details&id=" . $reservation->activite_id);
                exit;
            }
        }
    }
}
?>
