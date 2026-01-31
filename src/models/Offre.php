<?php
namespace Models;

use Core\Model;
use PDO;

/**
 * Modèle des offres de stage
 */
class Offre extends Model
{
    protected $table = 'offres';

    /**
     * Récupère toutes les offres avec les informations de l'entreprise
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAllWithEntreprise($page = 1, $perPage = ITEMS_PER_PAGE)
    {
        $offset = ($page - 1) * $perPage;
        $stmt = self::getDB()->prepare(
            "SELECT o.*, e.nom as entreprise_nom, e.email as entreprise_email
             FROM {$this->table} o
             JOIN entreprises e ON o.entreprise_id = e.id
             ORDER BY o.created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère une offre avec les informations de l'entreprise
     *
     * @param int $id
     * @return array|false
     */
    public function getWithEntreprise($id)
    {
        $stmt = self::getDB()->prepare(
            "SELECT o.*, e.nom as entreprise_nom, e.email as entreprise_email, 
                    e.telephone as entreprise_telephone, e.adresse as entreprise_adresse
             FROM {$this->table} o
             JOIN entreprises e ON o.entreprise_id = e.id
             WHERE o.id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Récupère les offres récentes
     *
     * @param int $limit
     * @return array
     */
    public function getRecentes($limit = 5)
    {
        $stmt = self::getDB()->prepare(
            "SELECT o.*, e.nom as entreprise_nom
             FROM {$this->table} o
             JOIN entreprises e ON o.entreprise_id = e.id
             ORDER BY o.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Recherche avancée d'offres
     *
     * @param string $search
     * @param string $competence
     * @return array
     */
    public function searchAdvanced($search = '', $competence = '')
    {
        $sql = "SELECT o.*, e.nom as entreprise_nom 
                FROM {$this->table} o
                JOIN entreprises e ON o.entreprise_id = e.id
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (o.titre LIKE :search OR o.description LIKE :search OR e.nom LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($competence)) {
            $sql .= " AND o.competences LIKE :competence";
            $params[':competence'] = '%"' . $competence . '"%';
        }

        $sql .= " ORDER BY o.created_at DESC";

        $stmt = self::getDB()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère toutes les compétences uniques
     *
     * @return array
     */
    public function getAllCompetences()
    {
        $stmt = self::getDB()->query("SELECT competences FROM {$this->table}");
        $allCompetences = [];

        while ($row = $stmt->fetch()) {
            $comps = json_decode($row['competences'], true);
            if (is_array($comps)) {
                $allCompetences = array_merge($allCompetences, $comps);
            }
        }

        return array_unique($allCompetences);
    }

    /**
     * Récupère la répartition des offres par durée
     *
     * @return array
     */
    public function getRepartitionParDuree()
    {
        $stmt = self::getDB()->query(
            "SELECT 
                CASE 
                    WHEN duree <= 1 THEN '1 mois'
                    WHEN duree <= 2 THEN '2 mois'
                    WHEN duree <= 3 THEN '3 mois'
                    WHEN duree <= 4 THEN '4 mois'
                    WHEN duree <= 6 THEN '6 mois'
                    ELSE '6+ mois'
                END as duree_categorie,
                COUNT(*) as nombre
             FROM {$this->table}
             GROUP BY duree_categorie
             ORDER BY duree_categorie"
        );
        return $stmt->fetchAll();
    }

    /**
     * Récupère le top des offres en wishlist
     *
     * @param int $limit
     * @return array
     */
    public function getTopWishlist($limit = 5)
    {
        $stmt = self::getDB()->prepare(
            "SELECT o.*, e.nom as entreprise_nom, COUNT(w.id) as wishlist_count
             FROM {$this->table} o
             JOIN entreprises e ON o.entreprise_id = e.id
             JOIN wishlist w ON o.id = w.offre_id
             GROUP BY o.id
             ORDER BY wishlist_count DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère le top des offres en wishlist pour un pilote
     *
     * @param int $piloteId
     * @param int $limit
     * @return array
     */
    public function getTopWishlistByPilote($piloteId, $limit = 5)
    {
        $stmt = self::getDB()->prepare(
            "SELECT o.titre, COUNT(w.id) as wishlist_count
             FROM {$this->table} o
             JOIN wishlist w ON o.id = w.offre_id
             JOIN pilote_etudiant pe ON w.etudiant_id = pe.etudiant_id
             WHERE pe.pilote_id = :pilote_id
             GROUP BY o.id
             ORDER BY wishlist_count DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':pilote_id', $piloteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
