<?php
class ActivityController {
    public function list() {
        $activityModel = new Activity();
        $activities = $activityModel->getAll();
        
        $pageTitle = "Nos Activités";
        require 'views/activities/list.php';
    }

    public function details($id) {
        $activityModel = new Activity();
        $activity = $activityModel->getById($id);
        
        if(!$activity) {
            header("Location: " . BASE_URL . "/?action=activities");
            exit;
        }

        $pageTitle = $activity['nom'];
        require 'views/activities/details.php';
    }
}
?>
