<?php
namespace Models;

use Core\Model;
use PDO;

/**
 * Modèle des utilisateurs
 */
class User extends Model
{
    protected $table = 'users';

    /**
     * Trouve un utilisateur par son email
     *
     * @param string $email
     * @return array|false
     */
    public function findByEmail($email)
    {
        $stmt = self::getDB()->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Compte les utilisateurs par rôle
     *
     * @param string $role
     * @return int
     */
    public function countByRole($role)
    {
        $stmt = self::getDB()->prepare("SELECT COUNT(*) FROM {$this->table} WHERE role = :role");
        $stmt->execute([':role' => $role]);
        return $stmt->fetchColumn();
    }

    /**
     * Récupère les utilisateurs par rôle avec pagination
     *
     * @param string $role
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getByRolePaginated($role, $page = 1, $perPage = ITEMS_PER_PAGE)
    {
        $offset = ($page - 1) * $perPage;
        $stmt = self::getDB()->prepare(
            "SELECT * FROM {$this->table} WHERE role = :role ORDER BY nom, prenom LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':role', $role);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Recherche des utilisateurs par rôle
     *
     * @param string $role
     * @param string $search
     * @return array
     */
    public function searchByRole($role, $search)
    {
        $stmt = self::getDB()->prepare(
            "SELECT * FROM {$this->table} 
             WHERE role = :role 
             AND (nom LIKE :search OR prenom LIKE :search OR email LIKE :search)
             ORDER BY nom, prenom"
        );
        $stmt->execute([
            ':role' => $role,
            ':search' => '%' . $search . '%'
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les étudiants gérés par un pilote (avec pagination)
     *
     * @param int $piloteId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getEtudiantsByPilotePaginated($piloteId, $page = 1, $perPage = ITEMS_PER_PAGE)
    {
        $offset = ($page - 1) * $perPage;
        $stmt = self::getDB()->prepare(
            "SELECT u.* FROM {$this->table} u
             JOIN pilote_etudiant pe ON u.id = pe.etudiant_id
             WHERE pe.pilote_id = :pilote_id
             ORDER BY u.nom, u.prenom
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':pilote_id', $piloteId);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Compte les étudiants gérés par un pilote
     *
     * @param int $piloteId
     * @return int
     */
    public function countEtudiantsByPilote($piloteId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT COUNT(*) FROM pilote_etudiant WHERE pilote_id = :pilote_id"
        );
        $stmt->execute([':pilote_id' => $piloteId]);
        return $stmt->fetchColumn();
    }

    /**
     * Recherche des étudiants gérés par un pilote
     *
     * @param int $piloteId
     * @param string $search
     * @return array
     */
    public function searchEtudiantsByPilote($piloteId, $search)
    {
        $stmt = self::getDB()->prepare(
            "SELECT u.* FROM {$this->table} u
             JOIN pilote_etudiant pe ON u.id = pe.etudiant_id
             WHERE pe.pilote_id = :pilote_id
             AND (u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search)
             ORDER BY u.nom, u.prenom"
        );
        $stmt->execute([
            ':pilote_id' => $piloteId,
            ':search' => '%' . $search . '%'
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les étudiants gérés par un pilote
     *
     * @param int $piloteId
     * @return array
     */
    public function getEtudiantsByPilote($piloteId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT u.* FROM {$this->table} u
             JOIN pilote_etudiant pe ON u.id = pe.etudiant_id
             WHERE pe.pilote_id = :pilote_id
             ORDER BY u.nom, u.prenom"
        );
        $stmt->execute([':pilote_id' => $piloteId]);
        return $stmt->fetchAll();
    }

    /**
     * Met à jour le jeton "Se souvenir de moi"
     * 
     * @param int $userId
     * @param string|null $token
     * @return bool
     */
    public function updateRememberToken($userId, $token)
    {
        // On suppose que la colonne remember_token existe (voir migration 002)
        try {
            $stmt = self::getDB()->prepare("UPDATE {$this->table} SET remember_token = :token WHERE id = :id");
            return $stmt->execute([':token' => $token, ':id' => $userId]);
        } catch (\PDOException $e) {
            // Si la colonne n'existe pas encore, on ignore silencieusement ou on log
            return false;
        }
    }

    /**
     * Met à jour le mot de passe d'un utilisateur
     * 
     * @param int $userId
     * @param string $hashedPassword
     * @return bool
     */
    public function updatePassword($userId, $hashedPassword)
    {
        $stmt = self::getDB()->prepare("UPDATE {$this->table} SET password = :password WHERE id = :id");
        return $stmt->execute([':password' => $hashedPassword, ':id' => $userId]);
    }

    /**
     * Récupère tous les pilotes
     * 
     * @return array
     */
    public function getAllPilotes()
    {
        $stmt = self::getDB()->prepare("SELECT * FROM {$this->table} WHERE role = 'pilote' ORDER BY nom, prenom");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère le pilote assigné à un étudiant
     * 
     * @param int $etudiantId
     * @return array|false
     */
    public function getPiloteByEtudiant($etudiantId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT u.* FROM {$this->table} u
             JOIN pilote_etudiant pe ON u.id = pe.pilote_id
             WHERE pe.etudiant_id = :etudiant_id"
        );
        $stmt->execute([':etudiant_id' => $etudiantId]);
        return $stmt->fetch();
    }

    /**
     * Assigne un pilote à un étudiant
     * 
     * @param int $etudiantId
     * @param int $piloteId
     * @return bool
     */
    public function assignPiloteToEtudiant($etudiantId, $piloteId)
    {
        // Vérifie si une relation existe déjà
        $current = $this->getPiloteByEtudiant($etudiantId);
        
        if ($current) {
            // Mise à jour
            $stmt = self::getDB()->prepare(
                "UPDATE pilote_etudiant SET pilote_id = :pilote_id WHERE etudiant_id = :etudiant_id"
            );
        } else {
            // Création
            $stmt = self::getDB()->prepare(
                "INSERT INTO pilote_etudiant (etudiant_id, pilote_id) VALUES (:etudiant_id, :pilote_id)"
            );
        }
        
        return $stmt->execute([
            ':etudiant_id' => $etudiantId,
            ':pilote_id' => $piloteId
        ]);
    }

    /**
     * Supprime l'assignation pilote pour un étudiant
     * 
     * @param int $etudiantId
     * @return bool
     */
    public function removePiloteFromEtudiant($etudiantId)
    {
        $stmt = self::getDB()->prepare("DELETE FROM pilote_etudiant WHERE etudiant_id = :etudiant_id");
        return $stmt->execute([':etudiant_id' => $etudiantId]);
    }
}
