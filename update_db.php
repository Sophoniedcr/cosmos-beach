<?php
// Script de mise à jour de la base de données
// À exécuter une seule fois pour ajouter les colonnes manquantes

require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    echo "<h2>Mise à jour de la base de données Cosmos Beach</h2>";
    echo "<ul>";

    // 1. Ajouter la colonne prenom
    try {
        $conn->exec("ALTER TABLE users ADD COLUMN prenom VARCHAR(100) DEFAULT '' AFTER nom");
        echo "<li><span style='color:green'>OK</span> : Colonne 'prenom' ajoutée.</li>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<li><span style='color:gray'>INFO</span> : La colonne 'prenom' existe déjà.</li>";
        } else {
            echo "<li><span style='color:red'>ERREUR</span> : " . $e->getMessage() . "</li>";
        }
    }

    // 2. Ajouter is_active
    try {
        $conn->exec("ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1");
        echo "<li><span style='color:green'>OK</span> : Colonne 'is_active' ajoutée.</li>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<li><span style='color:gray'>INFO</span> : La colonne 'is_active' existe déjà.</li>";
        } else {
            echo "<li><span style='color:red'>ERREUR</span> : " . $e->getMessage() . "</li>";
        }
    }

    // 3. Ajouter last_login
    try {
        $conn->exec("ALTER TABLE users ADD COLUMN last_login DATETIME DEFAULT NULL");
        echo "<li><span style='color:green'>OK</span> : Colonne 'last_login' ajoutée.</li>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<li><span style='color:gray'>INFO</span> : La colonne 'last_login' existe déjà.</li>";
        } else {
            echo "<li><span style='color:red'>ERREUR</span> : " . $e->getMessage() . "</li>";
        }
    }

    // 4. Ajouter disabled_at
    try {
        $conn->exec("ALTER TABLE users ADD COLUMN disabled_at DATETIME DEFAULT NULL");
        echo "<li><span style='color:green'>OK</span> : Colonne 'disabled_at' ajoutée.</li>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<li><span style='color:gray'>INFO</span> : La colonne 'disabled_at' existe déjà.</li>";
        } else {
            echo "<li><span style='color:red'>ERREUR</span> : " . $e->getMessage() . "</li>";
        }
    }

    // 5. Ajouter disabled_reason
    try {
        $conn->exec("ALTER TABLE users ADD COLUMN disabled_reason TEXT DEFAULT NULL");
        echo "<li><span style='color:green'>OK</span> : Colonne 'disabled_reason' ajoutée.</li>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<li><span style='color:gray'>INFO</span> : La colonne 'disabled_reason' existe déjà.</li>";
        } else {
            echo "<li><span style='color:red'>ERREUR</span> : " . $e->getMessage() . "</li>";
        }
    }

    // 6. Mettre à jour l'enum des rôles
    try {
        $conn->exec("ALTER TABLE users MODIFY COLUMN role ENUM('VISITEUR', 'RECEPTIONNISTE', 'AGENT_RESERVATION', 'CAISSIER', 'DIRECTEUR', 'ADMIN', 'SUPER_ADMIN', 'AGENT') DEFAULT 'VISITEUR'");
        echo "<li><span style='color:green'>OK</span> : Rôles mis à jour (SUPER_ADMIN et AGENT ajoutés).</li>";
    } catch (PDOException $e) {
        echo "<li><span style='color:red'>ERREUR</span> : " . $e->getMessage() . "</li>";
    }

    echo "</ul>";
    echo "<br><b>La mise à jour de la base de données est terminée ! Vous pouvez maintenant supprimer ce fichier et essayer de vous connecter.</b>";
    echo "<br><br><a href='index.php'>Retour à l'accueil</a>";

} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
