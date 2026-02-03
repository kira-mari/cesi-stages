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
}
