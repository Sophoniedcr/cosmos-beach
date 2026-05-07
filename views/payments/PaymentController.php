<?php
// ============================================================
// COSMOS BEACH — controllers/PaymentController.php — v3.1
// Corrections :
//   - process() : CSRF ajouté, reservation_id intval()
//   - process_online() : CSRF ajouté, reservation_id intval()
//   - search_payments() : count total corrigé (plus de bug count(search()))
//   - search_payments() accessible CAISSIER + DIRECTEUR + SUPER_ADMIN
//   - cashier_search() : CSRF ajouté
// ============================================================

class PaymentController
{
    // ──────────────────────────────────────────────────────────
    // Encaissement caisse physique
    // ──────────────────────────────────────────────────────────
    public function process(): void
    {
        if (!isset($_SESSION['user_role'])
            || !in_array($_SESSION['user_role'], ['CAISSIER', 'DIRECTEUR', 'SUPER_ADMIN'], true)) {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        // CSRF
        if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
            $_SESSION['flash_error'] = "Requête invalide.";
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        $reservation_id = intval($_POST['reservation_id'] ?? 0);
        if ($reservation_id <= 0) {
            $_SESSION['flash_error'] = "Numéro de réservation invalide.";
            header("Location: " . BASE_URL . "/?action=cashier_search");
            exit;
        }

        $resModel   = new Reservation();
        $reservation = $resModel->getById($reservation_id);

        if ($reservation && $reservation['statut'] === 'ATTENTE') {
            $payment               = new Payment();
            $payment->reservation_id = $reservation['id'];
            $payment->caissier_id  = $_SESSION['user_id'];
            $payment->montant      = $reservation['montant_total'];
            $payment->methode      = 'ESPECES';

            $paymentId = $payment->create();
            if ($paymentId) {
                $resModel->updateStatus($reservation_id, 'PAYEE');
                AuditLog::log('process_payment', 'payments', $reservation_id,
                    'Paiement espèces encaissé — Réservation #' . $reservation_id);
                header("Location: " . BASE_URL . "/?action=receipt&id=" . $reservation_id);
                exit;
            }
        }

        $_SESSION['flash_error'] = "Impossible d'encaisser cette réservation.";
        header("Location: " . BASE_URL . "/?action=cashier_search");
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // Checkout en ligne
    // ──────────────────────────────────────────────────────────
    public function online_checkout(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }

        $reservation_id = intval($_GET['id'] ?? 0);
        $resModel       = new Reservation();
        $reservation    = $resModel->getById($reservation_id);

        if (!$reservation
            || (int)$reservation['user_id'] !== (int)$_SESSION['user_id']
            || $reservation['statut'] !== 'ATTENTE') {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        $pageTitle = "Paiement en Ligne";
        require 'views/payment/online_checkout.php';
    }

    // ──────────────────────────────────────────────────────────
    // Traitement paiement en ligne
    // ──────────────────────────────────────────────────────────
    public function process_online(): void
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }

        // CSRF
        if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
            $_SESSION['flash_error'] = "Requête invalide.";
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        $reservation_id = intval($_POST['reservation_id'] ?? 0);
        $methode = in_array($_POST['methode'] ?? '', ['CARTE', 'MOBILE_MONEY'], true)
            ? $_POST['methode'] : 'MOBILE_MONEY';

        $resModel    = new Reservation();
        $reservation = $resModel->getById($reservation_id);

        if ($reservation
            && (int)$reservation['user_id'] === (int)$_SESSION['user_id']
            && $reservation['statut'] === 'ATTENTE') {

            $payment               = new Payment();
            $payment->reservation_id = $reservation['id'];
            $payment->caissier_id  = null;
            $payment->montant      = $reservation['montant_total'];
            $payment->methode      = $methode;

            if ($payment->create()) {
                $resModel->updateStatus($reservation_id, 'PAYEE');
                AuditLog::log('online_payment', 'payments', $reservation_id,
                    "Paiement en ligne ($methode) — Réservation #$reservation_id");
                $_SESSION['flash_success'] = "Paiement réussi via $methode ! Votre reçu est disponible.";
                header("Location: " . BASE_URL . "/?action=receipt&id=" . $reservation_id);
                exit;
            }
        }

        $_SESSION['flash_error'] = "Erreur lors du traitement du paiement.";
        header("Location: " . BASE_URL . "/?action=dashboard");
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // Reçu
    // ──────────────────────────────────────────────────────────
    public function receipt(int $reservation_id): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }

        $resModel    = new Reservation();
        $reservation = $resModel->getById($reservation_id);
        $payModel    = new Payment();
        $payment     = $payModel->getByReservationId($reservation_id);

        // Seul le propriétaire, un caissier, directeur ou admin peut voir le reçu
        $allowed = in_array($_SESSION['user_role'], ['CAISSIER', 'DIRECTEUR', 'SUPER_ADMIN'], true)
            || (isset($reservation['user_id']) && (int)$reservation['user_id'] === (int)$_SESSION['user_id']);

        if (!$reservation || !$payment || !$allowed) {
            $_SESSION['flash_error'] = "Reçu introuvable ou accès refusé.";
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        require 'views/receipt.php';
    }

    // ──────────────────────────────────────────────────────────
    // Recherche réservation par le caissier
    // ──────────────────────────────────────────────────────────
    public function cashier_search(): void
    {
        if (!isset($_SESSION['user_role'])
            || !in_array($_SESSION['user_role'], ['CAISSIER', 'DIRECTEUR', 'SUPER_ADMIN'], true)) {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        $search_result       = null;
        $error_message       = '';
        $search_id           = '';
        $resModel            = new Reservation();
        $pending_reservations = $resModel->getPending();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $error_message = "Requête invalide.";
            } else {
                $search_id = intval($_POST['reservation_id'] ?? 0);
                if ($search_id > 0) {
                    $reservation = $resModel->getById($search_id);
                    if ($reservation) {
                        $search_result = $reservation;
                    } else {
                        $error_message = "Réservation #$search_id introuvable.";
                    }
                } else {
                    $error_message = "Veuillez entrer un numéro valide.";
                }
            }
        }

        $pageTitle = "Recherche Réservation";
        require 'views/dashboard/caissier.php';
    }

    // ──────────────────────────────────────────────────────────
    // Recherche des paiements encaissés
    // Accessible : CAISSIER, DIRECTEUR, SUPER_ADMIN
    // ──────────────────────────────────────────────────────────
    public function search_payments(): void
    {
        if (!isset($_SESSION['user_role'])
            || !in_array($_SESSION['user_role'], ['CAISSIER', 'DIRECTEUR', 'SUPER_ADMIN'], true)) {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        $paymentModel = new Payment();
        $filters      = [];
        $page         = max(1, intval($_GET['page'] ?? 1));
        $limit        = 20;
        $offset       = ($page - 1) * $limit;

        // Récupérer les filtres (GET ou POST)
        $src = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;

        if (!empty($src['reservation_id'])) $filters['reservation_id'] = intval($src['reservation_id']);
        if (!empty($src['client_name']))    $filters['client_name']    = trim($src['client_name']);
        if (!empty($src['methode']))        $filters['methode']        = $src['methode'];
        if (!empty($src['date_from']))      $filters['date_from']      = $src['date_from'];
        if (!empty($src['date_to']))        $filters['date_to']        = $src['date_to'];

        // CAISSIER ne voit que ses propres encaissements
        if ($_SESSION['user_role'] === 'CAISSIER') {
            $filters['caissier_id'] = $_SESSION['user_id'];
        }

        // Récupérer avec pagination
        $filters_with_pagination = array_merge($filters, ['limit' => $limit, 'offset' => $offset]);
        $payments = $paymentModel->search($filters_with_pagination);

        // Compter le total sans pagination (requête dédiée)
        $total       = $paymentModel->countSearch($filters);
        $total_pages = max(1, ceil($total / $limit));

        $pageTitle = "Paiements Encaissés";
        require 'views/payments/search_payments.php';
    }
}
