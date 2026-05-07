<?php
class AdminActivityController {
    public function manage() {
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['ADMIN', 'DIRECTEUR'])) {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        $activityModel = new Activity();

        // Traitement de l'ajout
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'add') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_error'] = "Action non autorisée.";
                header("Location: " . BASE_URL . "/?action=admin_activities");
                exit;
            }
            $activityModel->nom = $_POST['nom'];
            $activityModel->description = $_POST['description'];
            $activityModel->prix = $_POST['prix'];
            $activityModel->duree = $_POST['duree'];
            $activityModel->capacite_max = $_POST['capacite_max'];
            $activityModel->type = $_POST['type'];
            $activityModel->type = $_POST['type'];
            
            // Gestion de l'upload de l'image
            $image_url = 'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&q=80&w=800'; // Image par défaut
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'img/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES['image_file']['name']);
                $uploadFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadFile)) {
                    $image_url = BASE_URL . '/' . $uploadFile;
                }
            }
            $activityModel->image_url = $image_url;

            if ($activityModel->create()) {
                $_SESSION['flash_success'] = "Activité ajoutée avec succès.";
            } else {
                $_SESSION['flash_error'] = "Erreur lors de l'ajout de l'activité.";
            }
            header("Location: " . BASE_URL . "/?action=admin_activities");
            exit;
        }

        // Traitement de la modification
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'update' && isset($_POST['update_id'])) {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_error'] = "Action non autorisée.";
                header("Location: " . BASE_URL . "/?action=admin_activities");
                exit;
            }
            $activityModel->nom = $_POST['nom'];
            $activityModel->description = $_POST['description'];
            $activityModel->prix = $_POST['prix'];
            $activityModel->duree = $_POST['duree'];
            $activityModel->capacite_max = $_POST['capacite_max'];
            $activityModel->type = $_POST['type'];
            $activityModel->type = $_POST['type'];
            
            // Gestion de l'upload de l'image lors de la modification
            $image_url = $_POST['existing_image'] ?? 'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&q=80&w=800';
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'img/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES['image_file']['name']);
                $uploadFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadFile)) {
                    $image_url = BASE_URL . '/' . $uploadFile;
                }
            }
            $activityModel->image_url = $image_url;

            if ($activityModel->update($_POST['update_id'])) {
                $_SESSION['flash_success'] = "Activité modifiée avec succès.";
            } else {
                $_SESSION['flash_error'] = "Erreur lors de la modification de l'activité.";
            }
            header("Location: " . BASE_URL . "/?action=admin_activities");
            exit;
        }

        // Traitement suppression
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'delete' && isset($_POST['delete_id'])) {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_error'] = "Action non autorisée.";
                header("Location: " . BASE_URL . "/?action=admin_activities");
                exit;
            }
            $activityModel->delete($_POST['delete_id']);
            $_SESSION['flash_success'] = "Activité supprimée avec succès.";
            header("Location: " . BASE_URL . "/?action=admin_activities");
            exit;
        }

        $activities = $activityModel->getAll();
        $pageTitle = "Gérer les Acitvités";
        require 'views/dashboard/activites_crud.php';
    }
}
?>
