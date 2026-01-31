<?php
namespace Models;

use Core\Model;
use PDO;

/**
 * Modèle des entreprises
 */
class Entreprise extends Model
{
    protected $table = 'entreprises';

    /**
     * Récupère les offres d'une entreprise
     *
     * @param int $entrepriseId
     * @return array
     */
    public function getOffres($entrepriseId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT * FROM offres WHERE entreprise_id = :entreprise_id ORDER BY created_at DESC"
        );
        $stmt->execute([':entreprise_id' => $entrepriseId]);
        return $stmt->fetchAll();
    }

    /**
     * Compte le nombre de candidatures pour une entreprise
     *
     * @param int $entrepriseId
     * @return int
     */
    public function countCandidatures($entrepriseId)
    {
        $stmt = self::getDB()->prepare(
            "SELECT COUNT(*) FROM candidatures c
             JOIN offres o ON c.offre_id = o.id
             WHERE o.entreprise_id = :entreprise_id"
        );
        $stmt->execute([':entreprise_id' => $entrepriseId]);
        return $stmt->fetchColumn();
    }

    /**
     * Recherche avancée d'entreprises
     *
     * @param string $search
     * @param string $ville
     * @return array
     */
    public function searchWithFilter($search = '', $ville = '')
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nom LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($ville)) {
            // Recherche la ville à la fin de l'adresse (format "... Zip Ville")
            $sql .= " AND adresse LIKE :ville";
            $params[':ville'] = '%' . $ville;
        }

        $sql .= " ORDER BY nom";

        $stmt = self::getDB()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère toutes les villes distinctes extraites des adresses
     *
     * @return array
     */
    public function getAllVilles()
    {
        $stmt = self::getDB()->query("SELECT DISTINCT adresse FROM {$this->table} WHERE adresse IS NOT NULL AND adresse != ''");
        $adresses = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $villes = [];
        foreach ($adresses as $adresse) {
            // Suppose format "Rue, CP Ville" -> Split comma -> Last part -> Remove digits
            $parts = explode(',', $adresse);
            $lastPart = trim(end($parts));
            $ville = trim(preg_replace('/[0-9]+/', '', $lastPart));
            
            if (!empty($ville)) {
                $villes[] = ucfirst(strtolower($ville));
            }
        }
        
        $villes = array_unique($villes);
        sort($villes);
        
        return $villes;
    }

    /**
     * Récupère tous les secteurs distincts
     *
     * @return array
     */
    public function getAllSecteurs()
    {
        $stmt = self::getDB()->query("SELECT DISTINCT secteur FROM {$this->table} WHERE secteur IS NOT NULL ORDER BY secteur");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
