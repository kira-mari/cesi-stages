<?php
namespace Models;

use Core\Model;
use PDO;

/**
 * Modèle des candidatures
 */
class Candidature extends Model
{
    protected $table = 'candidatures';

    /**
     * Récupère toutes les candidatures avec détails
     *
     * @return array
     */
    public function getAllWithDetails()
    {
        $stmt = self::getDB()->query(
            "SELECT c.*, o.titre as offre_titre, e.nom as entreprise_nom,
                    u.nom as etudiant_nom, u.prenom as etudiant_prenom, u.email as etudiant_email
             FROM {$this->table} c
             JOIN offres o ON c.offre_id = o.id
             JOIN entreprises e ON o.entreprise_id = e.id
             JOIN users u ON c.etudiant_id = u.id
             ORDER BY c.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Récupère les candidatures d'un étudiant
     *
     * @param int $etudiantId
     * @return array
     */
    public function getByEtudiant($etudiantId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT c.*, o.titre as offre_titre, e.nom as entreprise_nom
             FROM {$this->table} c
             JOIN offres o ON c.offre_id = o.id
             JOIN entreprises e ON o.entreprise_id = e.id
             WHERE c.etudiant_id = :etudiant_id
             ORDER BY c.created_at DESC"
        );
        $stmt->execute([':etudiant_id' => $etudiantId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les candidatures d'un étudiant avec détails complets
     *
     * @param int $etudiantId
     * @return array
     */
    public function getByEtudiantWithDetails($etudiantId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT c.*, o.titre as offre_titre, o.description as offre_description,
                    e.nom as entreprise_nom, e.email as entreprise_email
             FROM {$this->table} c
             JOIN offres o ON c.offre_id = o.id
             JOIN entreprises e ON o.entreprise_id = e.id
             WHERE c.etudiant_id = :etudiant_id
             ORDER BY c.created_at DESC"
        );
        $stmt->execute([':etudiant_id' => $etudiantId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les candidatures des étudiants d'un pilote
     *
     * @param int $piloteId
     * @return array
     */
    public function getByPiloteWithDetails($piloteId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT c.*, o.titre as offre_titre, e.nom as entreprise_nom,
                    u.nom as etudiant_nom, u.prenom as etudiant_prenom, u.email as etudiant_email
             FROM {$this->table} c
             JOIN offres o ON c.offre_id = o.id
             JOIN entreprises e ON o.entreprise_id = e.id
             JOIN users u ON c.etudiant_id = u.id
             JOIN pilote_etudiant pe ON u.id = pe.etudiant_id
             WHERE pe.pilote_id = :pilote_id
             ORDER BY c.created_at DESC"
        );
        $stmt->execute([':pilote_id' => $piloteId]);
        return $stmt->fetchAll();
    }

    /**
     * Compte les candidatures d'un étudiant
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

    /**
     * Compte les candidatures des étudiants d'un pilote
     *
     * @param int $piloteId
     * @return int
     */
    public function countByPilote($piloteId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT COUNT(*) FROM {$this->table} c
             JOIN users u ON c.etudiant_id = u.id
             JOIN pilote_etudiant pe ON u.id = pe.etudiant_id
             WHERE pe.pilote_id = :pilote_id"
        );
        $stmt->execute([':pilote_id' => $piloteId]);
        return $stmt->fetchColumn();
    }

    /**
     * Compte les candidatures pour une offre
     *
     * @param int $offreId
     * @return int
     */
    public function countByOffre($offreId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE offre_id = :offre_id"
        );
        $stmt->execute([':offre_id' => $offreId]);
        return $stmt->fetchColumn();
    }

    /**
     * Récupère les candidatures d'une offre avec les détails des étudiants
     *
     * @param int $offreId
     * @return array
     */
    public function getByOffreWithEtudiants($offreId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT c.*, u.id as etudiant_id, u.nom as etudiant_nom, u.prenom as etudiant_prenom, 
                    u.email as etudiant_email, o.titre as offre_titre
             FROM {$this->table} c
             JOIN users u ON c.etudiant_id = u.id
             JOIN offres o ON c.offre_id = o.id
             WHERE c.offre_id = :offre_id
             ORDER BY c.created_at DESC"
        );
        $stmt->execute([':offre_id' => $offreId]);
        return $stmt->fetchAll();
    }

    /**
     * Vérifie si un étudiant a déjà postulé à une offre
     *
     * @param int $etudiantId
     * @param int $offreId
     * @return bool
     */
    public function aPostule($etudiantId, $offreId)
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
     * Calcule la moyenne de candidatures par offre
     *
     * @return float
     */
    public function getMoyenneParOffre()
    {
        $stmt = self::getDB()->query(
            "SELECT AVG(candidatures_count) as moyenne
             FROM (
                 SELECT COUNT(*) as candidatures_count
                 FROM {$this->table}
                 GROUP BY offre_id
             ) as counts"
        );
        return round($stmt->fetchColumn(), 2);
    }

    /**
     * Récupère le nombre de candidatures par étudiant pour un pilote
     *
     * @param int $piloteId
     * @return array
     */
    public function getStatsByEtudiantsPilote($piloteId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT u.id, u.nom, u.prenom, COUNT(c.id) as nb_candidatures
             FROM users u
             JOIN pilote_etudiant pe ON u.id = pe.etudiant_id
             LEFT JOIN candidatures c ON u.id = c.etudiant_id
             WHERE pe.pilote_id = :pilote_id
             GROUP BY u.id
             ORDER BY nb_candidatures DESC"
        );
        $stmt->execute([':pilote_id' => $piloteId]);
        return $stmt->fetchAll();
    }

    // ============================================================
    // FONCTIONS RECRUTEUR
    // ============================================================

    /**
     * Récupère les candidatures des entreprises d'un recruteur
     *
     * @param int $recruteurId
     * @return array
     */
    public function getByRecruteurWithDetails($recruteurId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT c.id, c.etudiant_id, c.offre_id, c.statut, c.lettre_motivation, c.cv_path, c.created_at,
                    o.titre as offre_titre, o.id as offre_id, e.nom as entreprise_nom, e.id as entreprise_id,
                    u.nom as etudiant_nom, u.prenom as etudiant_prenom, u.email as etudiant_email, u.telephone as etudiant_telephone
             FROM {$this->table} c
             JOIN offres o ON c.offre_id = o.id
             JOIN entreprises e ON o.entreprise_id = e.id
             JOIN users u ON c.etudiant_id = u.id
             JOIN recruteur_entreprise re ON e.id = re.entreprise_id
             WHERE re.recruteur_id = :recruteur_id
             ORDER BY c.created_at DESC"
        );
        $stmt->execute([':recruteur_id' => $recruteurId]);
        return $stmt->fetchAll();
    }

    /**
     * Compte les candidatures des entreprises d'un recruteur
     *
     * @param int $recruteurId
     * @return int
     */
    public function countByRecruteur($recruteurId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT COUNT(*) FROM {$this->table} c
             JOIN offres o ON c.offre_id = o.id
             JOIN recruteur_entreprise re ON o.entreprise_id = re.entreprise_id
             WHERE re.recruteur_id = :recruteur_id"
        );
        $stmt->execute([':recruteur_id' => $recruteurId]);
        return $stmt->fetchColumn();
    }

    /**
     * Compte les candidatures par statut pour un recruteur
     *
     * @param int $recruteurId
     * @return array
     */
    public function countByStatutForRecruteur($recruteurId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT c.statut, COUNT(*) as count FROM {$this->table} c
             JOIN offres o ON c.offre_id = o.id
             JOIN recruteur_entreprise re ON o.entreprise_id = re.entreprise_id
             WHERE re.recruteur_id = :recruteur_id
             GROUP BY c.statut"
        );
        $stmt->execute([':recruteur_id' => $recruteurId]);
        $results = $stmt->fetchAll();
        
        $stats = ['en_attente' => 0, 'acceptee' => 0, 'refusee' => 0];
        foreach ($results as $row) {
            $stats[$row['statut']] = (int) $row['count'];
        }
        return $stats;
    }

    /**
     * Vérifie si une candidature appartient à un recruteur (via ses entreprises)
     *
     * @param int $candidatureId
     * @param int $recruteurId
     * @return bool
     */
    public function belongsToRecruteur($candidatureId, $recruteurId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT COUNT(*) FROM {$this->table} c
             JOIN offres o ON c.offre_id = o.id
             JOIN recruteur_entreprise re ON o.entreprise_id = re.entreprise_id
             WHERE c.id = :candidature_id AND re.recruteur_id = :recruteur_id"
        );
        $stmt->execute([
            ':candidature_id' => $candidatureId,
            ':recruteur_id' => $recruteurId
        ]);
        return $stmt->fetchColumn() > 0;
    }
}
