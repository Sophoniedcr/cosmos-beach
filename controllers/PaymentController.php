<?php
class PaymentController {
    public function process() {
        // Seul le caissier ou directeur peut encaisser
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['CAISSIER', 'DIRECTEUR'])) {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'])) {
            $reservation_id = $_POST['reservation_id'];
            $resModel = new Reservation();
            $reservation = $resModel->getById($reservation_id);

            if ($reservation && $reservation['statut'] === 'ATTENTE') {
                // Créer le paiement
                $payment = new Payment();
                $payment->reservation_id = $reservation['id'];
                $payment->caissier_id = $_SESSION['user_id'];
                $payment->montant = $reservation['montant_total'];
                $payment->methode = 'ESPECES'; // Caisse physique
                
                $paymentId = $payment->create();

                if ($paymentId) {
                    // Mettre à jour la réservation
                    $resModel->updateStatus($reservation_id, 'PAYEE');
                    
                    // Rediriger vers le reçu électronique
                    header("Location: " . BASE_URL . "/?action=receipt&id=" . $reservation_id);
                    exit;
                }
            }
        }
        
        header("Location: " . BASE_URL . "/?action=dashboard");
        exit;
    }

    public function online_checkout() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }

        $reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $resModel = new Reservation();
        $reservation = $resModel->getById($reservation_id);

        if (!$reservation || $reservation['user_id'] != $_SESSION['user_id'] || $reservation['statut'] != 'ATTENTE') {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        $pageTitle = "Paiement en ligne";
        require 'views/payment/online_checkout.php';
    }

    public function process_online() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }

        $reservation_id = $_POST['reservation_id'];
        $methode = in_array($_POST['methode'], ['CARTE', 'MOBILE_MONEY']) ? $_POST['methode'] : 'MOBILE_MONEY';

        $resModel = new Reservation();
        $reservation = $resModel->getById($reservation_id);

        if ($reservation && $reservation['user_id'] == $_SESSION['user_id'] && $reservation['statut'] === 'ATTENTE') {
            $payment = new Payment();
            $payment->reservation_id = $reservation['id'];
            $payment->caissier_id = null; // Paiement en ligne, pas de caissier
            $payment->montant = $reservation['montant_total'];
            $payment->methode = $methode;
            
            if ($payment->create()) {
                $resModel->updateStatus($reservation_id, 'PAYEE');
                $_SESSION['flash_success'] = "Paiement réussi via " . $methode . " ! Votre reçu est disponible.";
                header("Location: " . BASE_URL . "/?action=dashboard");
                exit;
            }
        }
        
        $_SESSION['flash_error'] = "Erreur lors du traitement du paiement.";
        header("Location: " . BASE_URL . "/?action=dashboard");
        exit;
    }

    public function receipt($reservation_id) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }

        $resModel = new Reservation();
        $reservation = $resModel->getById($reservation_id);
        
        $paymentModel = new Payment();
        $payment = $paymentModel->getByReservationId($reservation_id);

        if (!$reservation || !$payment) {
            die("Reçu introuvable.");
        }

        require 'views/receipt.php';
    }

    // Recherche de réservation par le caissier
    public function cashier_search() {
        // Vérifier les permissions
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['CAISSIER', 'DIRECTEUR'])) {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        // Initialiser les variables
        $search_result = null;
        $error_message = '';
        $search_id = '';
        $pending_reservations = [];

        // Récupérer les réservations en attente
        $resModel = new Reservation();
        $pending_reservations = $resModel->getPending();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $search_id = isset($_POST['reservation_id']) ? intval($_POST['reservation_id']) : 0;

            if ($search_id > 0) {
                $reservation = $resModel->getById($search_id);

                if ($reservation) {
                    $search_result = $reservation;
                } else {
                    $error_message = "Réservation #" . $search_id . " introuvable.";
                }
            } else {
                $error_message = "Veuillez entrer un numéro de réservation valide.";
            }
        }

        $pageTitle = "Recherche Réservation - Caissier";
        require 'views/dashboard/caissier.php';
    }

    // Afficher les paiements encaissés (caissier, directeur, admin)
    public function search_payments() {
        // Vérifier les permissions
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['CAISSIER', 'DIRECTEUR', 'SUPER_ADMIN'])) {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        $paymentModel = new Payment();
        
        // Initialiser les filtres
        $filters = [];
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Récupérer les filtres du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($_GET['search'])) {
            if (isset($_POST['reservation_id']) && !empty($_POST['reservation_id'])) {
                $filters['reservation_id'] = intval($_POST['reservation_id']);
            }
            if (isset($_POST['client_name']) && !empty($_POST['client_name'])) {
                $filters['client_name'] = trim($_POST['client_name']);
            }
            if (isset($_POST['methode']) && !empty($_POST['methode'])) {
                $filters['methode'] = $_POST['methode'];
            }
            if (isset($_POST['date_from']) && !empty($_POST['date_from'])) {
                $filters['date_from'] = $_POST['date_from'];
            }
            if (isset($_POST['date_to']) && !empty($_POST['date_to'])) {
                $filters['date_to'] = $_POST['date_to'];
            }
        }

        // Récupérer les paiements
        $filters['limit'] = $limit;
        $filters['offset'] = $offset;
        $payments = $paymentModel->search($filters);

        // Compter le total
        $total = count($paymentModel->search(array_diff_key($filters, ['limit' => 1, 'offset' => 1])));
        $total_pages = ceil($total / $limit);

        $pageTitle = "Recherche des Paiements Encaissés";
        require 'views/payments/search_payments.php';
    }
}
?>
