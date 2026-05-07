<?php
class HomeController {
    public function index() {
        // Optionnel : Récupérer des activités phares depuis le modèle Activity
        // $activityModel = new Activity();
        // $featuredActivities = $activityModel->getFeatured();

        // Afficher la vue
        require 'views/home.php';
    }
}
?>
