<?php
namespace Models;

use Core\Model;
use PDO;

/**
 * Modèle des messages internes
 */
class Message extends Model
{
    protected $table = 'messages';

    /**
     * Envoie un message
     */
    public function envoyer($expediteurId, $destinataireId, $sujet, $contenu)
    {
        return $this->create([
            'expediteur_id' => $expediteurId,
            'destinataire_id' => $destinataireId,
            'sujet' => $sujet,
            'contenu' => $contenu,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Récupère les messages reçus par un utilisateur
     */
    public function getRecus($userId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT m.*, 
                    u.nom as expediteur_nom, 
                    u.prenom as expediteur_prenom,
                    u.email as expediteur_email,
                    u.role as expediteur_role
             FROM {$this->table} m
             JOIN users u ON m.expediteur_id = u.id
             WHERE m.destinataire_id = :user_id
             ORDER BY m.created_at DESC"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les messages envoyés par un utilisateur
     */
    public function getEnvoyes($userId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT m.*, 
                    u.nom as destinataire_nom, 
                    u.prenom as destinataire_prenom,
                    u.email as destinataire_email,
                    u.role as destinataire_role
             FROM {$this->table} m
             JOIN users u ON m.destinataire_id = u.id
             WHERE m.expediteur_id = :user_id
             ORDER BY m.created_at DESC"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère un message avec les détails
     */
    public function getWithDetails($messageId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT m.*, 
                    exp.nom as expediteur_nom, 
                    exp.prenom as expediteur_prenom,
                    exp.email as expediteur_email,
                    exp.role as expediteur_role,
                    dest.nom as destinataire_nom, 
                    dest.prenom as destinataire_prenom,
                    dest.email as destinataire_email,
                    dest.role as destinataire_role
             FROM {$this->table} m
             JOIN users exp ON m.expediteur_id = exp.id
             JOIN users dest ON m.destinataire_id = dest.id
             WHERE m.id = :id"
        );
        $stmt->execute([':id' => $messageId]);
        return $stmt->fetch();
    }

    /**
     * Marque un message comme lu
     */
    public function marquerLu($messageId)
    {
        $stmt = self::getDB()->prepare(
            "UPDATE {$this->table} SET lu = 1, lu_at = NOW() WHERE id = :id"
        );
        return $stmt->execute([':id' => $messageId]);
    }

    /**
     * Compte les messages non lus
     */
    public function countNonLus($userId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT COUNT(*) FROM {$this->table} 
             WHERE destinataire_id = :user_id AND lu = 0"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchColumn();
    }

    /**
     * Vérifie si un utilisateur peut accéder à un message
     */
    public function peutAcceder($messageId, $userId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT COUNT(*) FROM {$this->table} 
             WHERE id = :id AND (expediteur_id = :user_id OR destinataire_id = :user_id)"
        );
        $stmt->execute([':id' => $messageId, ':user_id' => $userId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Supprime un message (pour l'utilisateur)
     */
    public function supprimer($messageId)
    {
        return $this->delete($messageId);
    }

    /**
     * Statistiques de messagerie pour un utilisateur
     */
    public function getStats($userId)
    {
        $db = self::getDB();
        
        // Messages reçus
        $stmt = $db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE destinataire_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $recus = $stmt->fetchColumn();
        
        // Messages envoyés
        $stmt = $db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE expediteur_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $envoyes = $stmt->fetchColumn();
        
        // Messages non lus
        $stmt = $db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE destinataire_id = :user_id AND lu = 0");
        $stmt->execute([':user_id' => $userId]);
        $nonLus = $stmt->fetchColumn();
        
        return [
            'recus' => (int) $recus,
            'envoyes' => (int) $envoyes,
            'non_lus' => (int) $nonLus
        ];
    }

    /**
     * Compte total de messages envoyés (pour admin)
     */
    public function countTotal()
    {
        $stmt = self::getDB()->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }
}
