<?php
namespace Models;

use Core\Model;
use PDO;

/**
 * Modèle de la wishlist
 */
class Wishlist extends Model
{
    protected $table = 'wishlist';

    /**
     * Récupère les offres de la wishlist d'un étudiant
     *
     * @param int $etudiantId
     * @return array
     */
    public function getByEtudiant($etudiantId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT * FROM {$this->table} WHERE etudiant_id = :etudiant_id ORDER BY created_at DESC"
        );
        $stmt->execute([':etudiant_id' => $etudiantId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les offres de la wishlist avec détails
     *
     * @param int $etudiantId
     * @return array
     */
    public function getByEtudiantWithDetails($etudiantId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT w.*, o.id, o.titre, o.description, o.remuneration, o.duree,
                    e.nom as entreprise_nom, e.adresse
             FROM {$this->table} w
             JOIN offres o ON w.offre_id = o.id
             JOIN entreprises e ON o.entreprise_id = e.id
             WHERE w.etudiant_id = :etudiant_id
             ORDER BY w.created_at DESC"
        );
        $stmt->execute([':etudiant_id' => $etudiantId]);
        return $stmt->fetchAll();
    }

    /**
     * Vérifie si une offre est dans la wishlist
     *
     * @param int $etudiantId
     * @param int $offreId
     * @return bool
     */
    public function isInWishlist($etudiantId, $offreId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT COUNT(*) FROM {$this->table} 
             WHERE etudiant_id = :etudiant_id AND offre_id = :offre_id"
        );
        $stmt->execute([
            ':etudiant_id' => $etudiantId,
            ':offre_id' => $offreId
        ]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Retire une offre de la wishlist
     *
     * @param int $etudiantId
     * @param int $offreId
     * @return bool
     */
    public function remove($etudiantId, $offreId)
    {
        $stmt = self::getDB()->prepare(
            "DELETE FROM {$this->table} 
             WHERE etudiant_id = :etudiant_id AND offre_id = :offre_id"
        );
        return $stmt->execute([
            ':etudiant_id' => $etudiantId,
            ':offre_id' => $offreId
        ]);
    }

    /**
     * Compte les offres dans la wishlist d'un étudiant
     *
     * @param int $etudiantId
     * @return int
     */
    public function countByEtudiant($etudiantId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE etudiant_id = :etudiant_id"
        );
        $stmt->execute([':etudiant_id' => $etudiantId]);
        return $stmt->fetchColumn();
    }
}
