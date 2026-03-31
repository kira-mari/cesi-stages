<?php
namespace Models;

use Core\Model;
use PDO;

/**
 * Modèle des groupes d'étudiants (créés par les pilotes)
 */
class Groupe extends Model
{
    protected $table = 'groupes';

    /**
     * Récupère tous les groupes d'un pilote
     */
    public function getByPilote(int $piloteId): array
    {
        $stmt = self::getDB()->prepare(
            "SELECT g.*, 
             (SELECT COUNT(*) FROM groupe_etudiant ge WHERE ge.groupe_id = g.id) as nb_etudiants
             FROM {$this->table} g
             WHERE g.pilote_id = :pilote_id
             ORDER BY g.nom"
        );
        $stmt->execute([':pilote_id' => $piloteId]);
        return $stmt->fetchAll();
    }

    /**
     * Vérifie qu'un groupe appartient au pilote
     */
    public function belongsToPilote(int $groupeId, int $piloteId): bool
    {
        $groupe = $this->find($groupeId);
        return $groupe && (int) $groupe['pilote_id'] === $piloteId;
    }

    /**
     * Crée un groupe
     */
    public function createGroupe(int $piloteId, string $nom): int|false
    {
        $stmt = self::getDB()->prepare(
            "INSERT INTO {$this->table} (pilote_id, nom) VALUES (:pilote_id, :nom)"
        );
        if ($stmt->execute([':pilote_id' => $piloteId, ':nom' => $nom])) {
            return (int) self::getDB()->lastInsertId();
        }
        return false;
    }

    /**
     * Met à jour le nom d'un groupe
     */
    public function updateNom(int $groupeId, string $nom): bool
    {
        $stmt = self::getDB()->prepare(
            "UPDATE {$this->table} SET nom = :nom WHERE id = :id"
        );
        return $stmt->execute([':nom' => $nom, ':id' => $groupeId]);
    }

    /**
     * Supprime un groupe
     */
    public function deleteGroupe(int $groupeId): bool
    {
        $stmt = self::getDB()->prepare("DELETE FROM groupe_etudiant WHERE groupe_id = :id");
        $stmt->execute([':id' => $groupeId]);
        $stmt = self::getDB()->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $groupeId]);
    }

    /**
     * Récupère les étudiants d'un groupe
     */
    public function getEtudiants(int $groupeId): array
    {
        $stmt = self::getDB()->prepare(
            "SELECT u.* FROM users u
             JOIN groupe_etudiant ge ON u.id = ge.etudiant_id
             WHERE ge.groupe_id = :groupe_id
             ORDER BY u.nom, u.prenom"
        );
        $stmt->execute([':groupe_id' => $groupeId]);
        return $stmt->fetchAll();
    }

    /**
     * Ajoute un étudiant à un groupe (retire des autres groupes du même pilote)
     */
    public function addEtudiant(int $groupeId, int $etudiantId, int $piloteId): bool
    {
        // Retirer l'étudiant des autres groupes de ce pilote
        $stmt = self::getDB()->prepare(
            "DELETE ge FROM groupe_etudiant ge
             JOIN groupes g ON ge.groupe_id = g.id
             WHERE g.pilote_id = :pilote_id AND ge.etudiant_id = :etudiant_id"
        );
        $stmt->execute([':pilote_id' => $piloteId, ':etudiant_id' => $etudiantId]);

        $stmt = self::getDB()->prepare(
            "INSERT IGNORE INTO groupe_etudiant (groupe_id, etudiant_id) VALUES (:groupe_id, :etudiant_id)"
        );
        return $stmt->execute([':groupe_id' => $groupeId, ':etudiant_id' => $etudiantId]);
    }

    /**
     * Retire un étudiant d'un groupe
     */
    public function removeEtudiant(int $groupeId, int $etudiantId): bool
    {
        $stmt = self::getDB()->prepare(
            "DELETE FROM groupe_etudiant WHERE groupe_id = :groupe_id AND etudiant_id = :etudiant_id"
        );
        return $stmt->execute([':groupe_id' => $groupeId, ':etudiant_id' => $etudiantId]);
    }

    /**
     * Récupère les groupes d'un pilote avec leurs étudiants
     */
    public function getByPiloteWithEtudiants(int $piloteId): array
    {
        $groupes = $this->getByPilote($piloteId);
        foreach ($groupes as &$g) {
            $g['etudiants'] = $this->getEtudiants($g['id']);
        }
        return $groupes;
    }

    /**
     * Récupère les étudiants du pilote qui ne sont dans aucun groupe
     */
    public function getEtudiantsSansGroupe(int $piloteId): array
    {
        $stmt = self::getDB()->prepare(
            "SELECT u.* FROM users u
             JOIN pilote_etudiant pe ON u.id = pe.etudiant_id
             WHERE pe.pilote_id = :pilote_id
             AND u.id NOT IN (
                 SELECT ge.etudiant_id FROM groupe_etudiant ge
                 JOIN groupes g ON ge.groupe_id = g.id
                 WHERE g.pilote_id = :pilote_id2
             )
             ORDER BY u.nom, u.prenom"
        );
        $stmt->execute([':pilote_id' => $piloteId, ':pilote_id2' => $piloteId]);
        return $stmt->fetchAll();
    }

    /**
     * Vérifie qu'un étudiant est assigné au pilote
     */
    public function etudiantAssignedToPilote(int $etudiantId, int $piloteId): bool
    {
        $stmt = self::getDB()->prepare(
            "SELECT 1 FROM pilote_etudiant WHERE etudiant_id = :etudiant_id AND pilote_id = :pilote_id"
        );
        $stmt->execute([':etudiant_id' => $etudiantId, ':pilote_id' => $piloteId]);
        return (bool) $stmt->fetch();
    }
}
