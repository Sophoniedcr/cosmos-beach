<?php
// ============================================================
// COSMOS BEACH — controllers/MarketingController.php — v2.0
// Rôle : MARKETEUR (ou SUPER_ADMIN)
// Actions :
//   dashboard()      — tableau de bord du marketeur
//   create()         — créer un événement
//   edit()           — modifier un événement
//   delete()         — supprimer un événement
//   toggle()         — activer/désactiver
//   interactions()   — voir les tickets vendus pour un événement
//   list_public()    — page publique des événements
//   book_ticket()    — réserver un ticket (visiteur)
//   confirm_ticket() — confirmation après paiement
//   my_tickets()     — mes tickets (visiteur)
//   cancel_ticket()  — annuler un ticket (visiteur)
// ============================================================

class MarketingController {

    // ── Vérification accès Marketeur ──────────────────────
    private function checkMarketerAccess(): void {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }
        if (!in_array($_SESSION['user_role'], ['MARKETEUR', 'SUPER_ADMIN', 'DIRECTEUR'], true)) {
            http_response_code(403);
            require 'views/errors/403.php';
            exit;
        }
    }

    // ── Vérification visiteur connecté ────────────────────
    private function checkVisitorAccess(): void {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = "Connectez-vous pour réserver un ticket.";
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }
    }

    // ─────────────────────────────────────────────────────
    // Dashboard Marketeur
    // ─────────────────────────────────────────────────────
    public function dashboard(): void {
        $this->checkMarketerAccess();

        $eventModel  = new EventModel();
        $ticketModel = new EventTicket();

        $events = $eventModel->getByCreator((int)$_SESSION['user_id']);
        $stats  = $ticketModel->getStatsByCreator((int)$_SESSION['user_id']);

        $pageTitle = "Dashboard Marketing";
        require 'views/dashboard/marketing.php';
    }

    // ─────────────────────────────────────────────────────
    // Créer un événement (GET = formulaire, POST = traitement)
    // ─────────────────────────────────────────────────────
    public function create(): void {
        $this->checkMarketerAccess();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_error'] = "Requête invalide.";
                header("Location: " . BASE_URL . "/?action=marketing_create");
                exit;
            }

            $eventModel              = new EventModel();
            $eventModel->titre       = trim($_POST['titre']       ?? '');
            $eventModel->description = trim($_POST['description'] ?? '');
            $eventModel->date_debut  = $_POST['date_debut']       ?? '';
            $eventModel->date_fin    = $_POST['date_fin']         ?? '';
            $eventModel->image_url   = $this->handleImageUpload('event_image') ?? '';
            $eventModel->prix_ticket = floatval($_POST['prix_ticket'] ?? 0);
            $eventModel->capacite_max = intval($_POST['capacite_max'] ?? 100);
            $eventModel->lieu        = trim($_POST['lieu']        ?? '');
            $eventModel->type_event  = $_POST['type_event']       ?? 'autre';
            $eventModel->created_by  = (int)$_SESSION['user_id'];

            if (empty($eventModel->titre) || empty($eventModel->date_debut) || empty($eventModel->date_fin)) {
                $_SESSION['flash_error'] = "Veuillez remplir tous les champs obligatoires.";
                header("Location: " . BASE_URL . "/?action=marketing_create");
                exit;
            }

            if ($eventModel->prix_ticket < 0) {
                $_SESSION['flash_error'] = "Le prix du ticket ne peut pas être négatif.";
                header("Location: " . BASE_URL . "/?action=marketing_create");
                exit;
            }

            if ($eventModel->create()) {
                AuditLog::log('create_event', 'events', null, "Événement créé : " . $eventModel->titre);
                $_SESSION['flash_success'] = "Événement « {$eventModel->titre} » publié avec succès !";
            } else {
                $_SESSION['flash_error'] = "Erreur lors de la création de l'événement.";
            }
            header("Location: " . BASE_URL . "/?action=marketing_dashboard");
            exit;
        }

        $pageTitle = "Créer un Événement";
        require 'views/events/create_event.php';
    }

    // ─────────────────────────────────────────────────────
    // Modifier un événement
    // ─────────────────────────────────────────────────────
    public function edit(): void {
        $this->checkMarketerAccess();

        $id         = intval($_GET['id'] ?? 0);
        $eventModel = new EventModel();
        $event      = $eventModel->getById($id);

        if (!$event) {
            $_SESSION['flash_error'] = "Événement introuvable.";
            header("Location: " . BASE_URL . "/?action=marketing_dashboard");
            exit;
        }

        // Seul le créateur ou SUPER_ADMIN peut modifier
        if ($_SESSION['user_role'] !== 'SUPER_ADMIN'
            && (int)$event['created_by'] !== (int)$_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Vous ne pouvez modifier que vos propres événements.";
            header("Location: " . BASE_URL . "/?action=marketing_dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_error'] = "Requête invalide.";
                header("Location: " . BASE_URL . "/?action=marketing_edit&id=" . $id);
                exit;
            }

            $data = [
                'titre'       => trim($_POST['titre']       ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'date_debut'  => $_POST['date_debut']       ?? '',
                'date_fin'    => $_POST['date_fin']         ?? '',
                'image_url'   => $this->handleImageUpload(
                    'event_image',
                    $_POST['current_image_url'] ?? '',
                    (bool)($_POST['remove_image'] ?? 0)
                ),
                'prix_ticket' => floatval($_POST['prix_ticket'] ?? 0),
                'capacite_max'=> intval($_POST['capacite_max']  ?? 100),
                'lieu'        => trim($_POST['lieu']        ?? ''),
                'type_event'  => $_POST['type_event']       ?? 'autre',
            ];

            if ($eventModel->update($id, $data)) {
                AuditLog::log('update_event', 'events', $id, "Événement modifié : " . $data['titre']);
                $_SESSION['flash_success'] = "Événement mis à jour avec succès.";
            } else {
                $_SESSION['flash_error'] = "Erreur lors de la mise à jour.";
            }
            header("Location: " . BASE_URL . "/?action=marketing_dashboard");
            exit;
        }

        $pageTitle = "Modifier l'Événement";
        require 'views/events/edit_event.php';
    }

    // ─────────────────────────────────────────────────────
    // Supprimer un événement
    // ─────────────────────────────────────────────────────
    public function delete(): void {
        $this->checkMarketerAccess();

        $id         = intval($_GET['id'] ?? 0);
        $eventModel = new EventModel();
        $event      = $eventModel->getById($id);

        if (!$event) {
            $_SESSION['flash_error'] = "Événement introuvable.";
            header("Location: " . BASE_URL . "/?action=marketing_dashboard");
            exit;
        }

        if ($_SESSION['user_role'] !== 'SUPER_ADMIN'
            && (int)$event['created_by'] !== (int)$_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Action non autorisée.";
            header("Location: " . BASE_URL . "/?action=marketing_dashboard");
            exit;
        }

        $eventModel->toggle($id);
        AuditLog::log('toggle_event', 'events', $id, "Événement activé/désactivé : " . $event['titre']);
        $_SESSION['flash_success'] = "Statut de l'événement modifié.";
        header("Location: " . BASE_URL . "/?action=marketing_dashboard");
        exit;
    }

    // ─────────────────────────────────────────────────────
    // Interactions : tickets vendus pour un événement
    // ─────────────────────────────────────────────────────
    public function interactions(): void {
        $this->checkMarketerAccess();

        $event_id    = intval($_GET['id'] ?? 0);
        $eventModel  = new EventModel();
        $ticketModel = new EventTicket();

        $event   = $eventModel->getById($event_id);
        if (!$event) {
            $_SESSION['flash_error'] = "Événement introuvable.";
            header("Location: " . BASE_URL . "/?action=marketing_dashboard");
            exit;
        }

        $tickets   = $ticketModel->getByEvent($event_id);
        $pageTitle = "Interactions — " . $event['titre'];
        require 'views/events/interactions.php';
    }

    // ─────────────────────────────────────────────────────
    // Page publique des événements
    // ─────────────────────────────────────────────────────
    public function list_public(): void {
        $eventModel = new EventModel();
        $events     = $eventModel->getActiveEvents();
        $pageTitle  = "Événements & Promos";
        require 'views/events/list.php';
    }

    // ─────────────────────────────────────────────────────
    // Réserver un ticket (visiteur)
    // ─────────────────────────────────────────────────────
    public function book_ticket(): void {
        $this->checkVisitorAccess();

        $event_id   = intval($_GET['id'] ?? 0);
        $eventModel = new EventModel();
        $event      = $eventModel->getById($event_id);

        if (!$event || !$event['is_active']) {
            $_SESSION['flash_error'] = "Événement introuvable ou inactif.";
            header("Location: " . BASE_URL . "/?action=events");
            exit;
        }

        $available = $eventModel->getAvailableSeats($event_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_error'] = "Requête invalide.";
                header("Location: " . BASE_URL . "/?action=book_ticket_event&id=" . $event_id);
                exit;
            }

            $nombre_places = max(1, intval($_POST['nombre_places'] ?? 1));
            $montant_total = $nombre_places * (float)$event['prix_ticket'];

            if ($nombre_places > $available) {
                $_SESSION['flash_error'] = "Seulement $available place(s) disponible(s).";
                header("Location: " . BASE_URL . "/?action=book_ticket_event&id=" . $event_id);
                exit;
            }

            $ticketModel = new EventTicket();
            $result      = $ticketModel->create($event_id, (int)$_SESSION['user_id'], $nombre_places, $montant_total);

            if ($result['success']) {
                // Confirmer automatiquement (paiement symbolique/enregistrement)
                $ticketModel->confirm($result['ticket_id']);

                // Email de confirmation
                $this->sendTicketConfirmationEmail($result['ticket_id'], $ticketModel);
                $ticketModel->markEmailSent($result['ticket_id']);

                AuditLog::log('book_ticket', 'events', $event_id,
                    "Ticket {$result['numero']} réservé pour l'événement ID $event_id");

                $_SESSION['flash_success'] = "🎟️ Votre ticket {$result['numero']} a été réservé ! Un email de confirmation vous a été envoyé.";
                header("Location: " . BASE_URL . "/?action=my_event_tickets");
                exit;
            } else {
                $_SESSION['flash_error'] = "Erreur lors de la réservation. Veuillez réessayer.";
            }
        }

        $pageTitle = "Réserver — " . $event['titre'];
        require 'views/events/book_ticket.php';
    }

    // ─────────────────────────────────────────────────────
    // Mes tickets (visiteur)
    // ─────────────────────────────────────────────────────
    public function my_tickets(): void {
        $this->checkVisitorAccess();

        $ticketModel = new EventTicket();
        $tickets     = $ticketModel->getByUser((int)$_SESSION['user_id']);
        $pageTitle   = "Mes Tickets d'Événements";
        require 'views/events/my_tickets.php';
    }

    // ─────────────────────────────────────────────────────
    // Annuler un ticket (visiteur)
    // ─────────────────────────────────────────────────────
    public function cancel_ticket(): void {
        $this->checkVisitorAccess();

        $ticket_id   = intval($_GET['id'] ?? 0);
        $ticketModel = new EventTicket();

        if ($ticketModel->cancel($ticket_id, (int)$_SESSION['user_id'])) {
            AuditLog::log('cancel_ticket', 'events', $ticket_id, "Ticket annulé");
            $_SESSION['flash_success'] = "Votre ticket a été annulé.";
        } else {
            $_SESSION['flash_error'] = "Impossible d'annuler ce ticket (déjà confirmé ou introuvable).";
        }
        header("Location: " . BASE_URL . "/?action=my_event_tickets");
        exit;
    }

    // ─────────────────────────────────────────────────────
    // Envoi email de confirmation de ticket
    // ─────────────────────────────────────────────────────
    private function sendTicketConfirmationEmail(int $ticket_id, EventTicket $ticketModel): void {
        try {
            require_once 'backend/config/EmailService.php';
            $emailService = new EmailService();

            if (!$emailService->isConfigured()) {
                // Mode dev : logger le ticket
                $ticket = $ticketModel->getById($ticket_id);
                error_log("[COSMOS][DEV] Ticket confirmé : {$ticket['numero_ticket']} pour {$ticket['client_email']}");
                return;
            }

            $ticket = $ticketModel->getById($ticket_id);
            if (!$ticket) return;

            $subject = "🎟️ Confirmation de votre ticket — " . $ticket['event_titre'];
            $body    = $this->buildTicketEmailBody($ticket);

            $emailService->sendRawEmail(
                $ticket['client_email'],
                $ticket['client_prenom'] . ' ' . $ticket['client_nom'],
                $subject,
                $body
            );
        } catch (Exception $e) {
            error_log("[COSMOS] Email ticket: " . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────
    // Upload image événement
    // ─────────────────────────────────────────────────────
    private function handleImageUpload(string $field, string $current_url = '', bool $remove = false): ?string {
        if ($remove) {
            // Supprimer l'ancien fichier s'il est local
            if ($current_url && file_exists(ltrim($current_url, '/'))) {
                @unlink(ltrim($current_url, '/'));
            }
            return null;
        }

        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return $current_url ?: null; // Garder l'image actuelle
        }

        $file     = $_FILES[$field];
        $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mimeType = mime_content_type($file['tmp_name']);

        if (!in_array($mimeType, $allowed)) {
            $_SESSION['flash_error'] = "Format d'image non supporté. Utilisez JPG, PNG ou WEBP.";
            return $current_url ?: null;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['flash_error'] = "L'image ne doit pas dépasser 5 Mo.";
            return $current_url ?: null;
        }

        // Extension sécurisée
        $ext      = match($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => 'jpg',
        };

        $uploadDir = 'img/events/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest     = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            // Supprimer l'ancienne image locale si elle existe
            if ($current_url && strpos($current_url, 'img/events/') !== false && file_exists($current_url)) {
                @unlink($current_url);
            }
            return $dest; // Ex: img/events/event_1715000000_abcd1234.jpg
        }

        $_SESSION['flash_error'] = "Impossible de sauvegarder l'image. Vérifiez les permissions du dossier.";
        return $current_url ?: null;
    }

    private function buildTicketEmailBody(array $ticket): string {
        $dateDebut = date('d/m/Y à H:i', strtotime($ticket['date_debut']));
        $dateFin   = date('d/m/Y à H:i', strtotime($ticket['date_fin']));
        $montant   = number_format((float)$ticket['montant_total'], 2, ',', ' ');
        $lieu      = $ticket['lieu'] ?: 'Cosmos Beach';

        return "
        <div style='font-family:Inter,sans-serif;max-width:600px;margin:0 auto;background:#f8fafc;padding:24px;border-radius:12px;'>
          <div style='background:linear-gradient(135deg,#0ea5e9,#4f46e5);padding:24px;border-radius:10px;text-align:center;'>
            <h1 style='color:white;margin:0;font-size:24px;'>🎟️ Votre Ticket Cosmos Beach</h1>
          </div>
          <div style='background:white;padding:28px;border-radius:10px;margin-top:16px;border:1px solid #e2e8f0;'>
            <p style='color:#374151;font-size:16px;'>Bonjour <strong>{$ticket['client_prenom']} {$ticket['client_nom']}</strong>,</p>
            <p style='color:#6b7280;'>Votre réservation est confirmée. Voici votre ticket :</p>
            <div style='background:#f0f9ff;border:2px dashed #0ea5e9;border-radius:10px;padding:20px;text-align:center;margin:20px 0;'>
              <p style='font-size:28px;font-weight:bold;color:#0284c7;margin:0;letter-spacing:3px;'>{$ticket['numero_ticket']}</p>
              <p style='color:#64748b;font-size:14px;margin:4px 0 0;'>N° de ticket</p>
            </div>
            <table style='width:100%;border-collapse:collapse;margin:16px 0;'>
              <tr><td style='padding:8px 0;color:#6b7280;font-size:14px;'>📅 Événement</td><td style='padding:8px 0;font-weight:600;color:#1e293b;text-align:right;'>{$ticket['event_titre']}</td></tr>
              <tr><td style='padding:8px 0;color:#6b7280;font-size:14px;'>📍 Lieu</td><td style='padding:8px 0;font-weight:600;color:#1e293b;text-align:right;'>{$lieu}</td></tr>
              <tr><td style='padding:8px 0;color:#6b7280;font-size:14px;'>🕐 Du</td><td style='padding:8px 0;font-weight:600;color:#1e293b;text-align:right;'>{$dateDebut}</td></tr>
              <tr><td style='padding:8px 0;color:#6b7280;font-size:14px;'>🕐 Au</td><td style='padding:8px 0;font-weight:600;color:#1e293b;text-align:right;'>{$dateFin}</td></tr>
              <tr><td style='padding:8px 0;color:#6b7280;font-size:14px;'>🎫 Places</td><td style='padding:8px 0;font-weight:600;color:#1e293b;text-align:right;'>{$ticket['nombre_places']}</td></tr>
              <tr style='border-top:2px solid #e2e8f0;'>
                <td style='padding:12px 0;color:#374151;font-size:16px;font-weight:700;'>💰 Total payé</td>
                <td style='padding:12px 0;font-weight:700;color:#0284c7;font-size:18px;text-align:right;'>{$montant} FC</td>
              </tr>
            </table>
            <p style='color:#6b7280;font-size:13px;text-align:center;margin-top:24px;'>Présentez ce numéro de ticket à l'entrée de l'événement.</p>
          </div>
          <p style='text-align:center;color:#9ca3af;font-size:12px;margin-top:16px;'>© Cosmos Beach — Tous droits réservés</p>
        </div>";
    }
}
?>
