<?php
namespace Models;

use Core\Model;
use PDO;

/**
 * Modèle pour les inscriptions en attente de validation
 */
class PendingRegistration extends Model
{
    protected $table = 'pending_registrations';

    /**
     * Trouve une inscription en attente par email
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
     * Trouve une inscription en attente par code de validation
     *
     * @param string $code
     * @return array|false
     */
    public function findByCode($code)
    {
        $stmt = self::getDB()->prepare("SELECT * FROM {$this->table} WHERE validation_code = :code LIMIT 1");
        $stmt->execute([':code' => $code]);
        return $stmt->fetch();
    }

    /**
     * Nettoie les inscriptions expirées
     *
     * @return int Nombre de lignes supprimées
     */
    public function deleteExpired()
    {
        $stmt = self::getDB()->prepare("DELETE FROM {$this->table} WHERE validation_expires_at < NOW()");
        return $stmt->execute() ? $stmt->rowCount() : 0;
    }
}
