<?php
namespace Models;

use Core\Model;
use PDO;

/**
 * Modèle des évaluations d'entreprises
 */
class Evaluation extends Model
{
    protected $table = 'evaluations';

    /**
     * Récupère les évaluations d'une entreprise
     *
     * @param int $entrepriseId
     * @return array
     */
    public function getByEntreprise($entrepriseId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT e.*, u.nom as user_nom, u.prenom as user_prenom
             FROM {$this->table} e
             JOIN users u ON e.user_id = u.id
             WHERE e.entreprise_id = :entreprise_id
             ORDER BY e.created_at DESC"
        );
        $stmt->execute([':entreprise_id' => $entrepriseId]);
        return $stmt->fetchAll();
    }

    /**
     * Calcule la moyenne des évaluations d'une entreprise
     *
     * @param int $entrepriseId
     * @return float
     */
    public function getMoyenne($entrepriseId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT AVG(note) FROM {$this->table} WHERE entreprise_id = :entreprise_id"
        );
        $stmt->execute([':entreprise_id' => $entrepriseId]);
        $moyenne = $stmt->fetchColumn();
        return $moyenne ? round($moyenne, 1) : 0;
    }

    /**
     * Compte le nombre d'évaluations d'une entreprise
     *
     * @param int $entrepriseId
     * @return int
     */
    public function countByEntreprise($entrepriseId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE entreprise_id = :entreprise_id"
        );
        $stmt->execute([':entreprise_id' => $entrepriseId]);
        return $stmt->fetchColumn();
    }
}
