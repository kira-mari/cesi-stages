<?php
namespace Core;

use PDO;
use PDOException;

/**
 * Classe de base pour tous les modèles
 */
abstract class Model
{
    /**
     * @var PDO Instance de connexion à la base de données
     */
    protected static $db = null;

    /**
     * @var string Nom de la table
     */
    protected $table;

    /**
     * @var string Clé primaire
     */
    protected $primaryKey = 'id';

    /**
     * @var array Attributs du modèle
     */
    protected $attributes = [];

    /**
     * Constructeur
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->attributes = $data;
    }

    /**
     * Récupère la connexion à la base de données
     *
     * @return PDO
     */
    protected static function getDB()
    {
        if (self::$db === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                self::$db = new PDO($dsn, DB_USER, DB_PASS);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new \Exception("Erreur de connexion à la base de données: " . $e->getMessage());
            }
        }
        return self::$db;
    }

    /**
     * Récupère tous les enregistrements
     *
     * @return array
     */
    public function all()
    {
        $stmt = self::getDB()->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /**
     * Récupère un enregistrement par son ID
     *
     * @param int $id
     * @return array|false
     */
    public function find($id)
    {
        $stmt = self::getDB()->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Récupère des enregistrements avec une clause WHERE
     *
     * @param string $column
     * @param mixed $value
     * @return array
     */
    public function where($column, $value)
    {
        $stmt = self::getDB()->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value");
        $stmt->execute([':value' => $value]);
        return $stmt->fetchAll();
    }

    /**
     * Crée un nouvel enregistrement
     *
     * @param array $data
     * @return int ID de l'enregistrement créé
     */
    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute($data);

        return self::getDB()->lastInsertId();
    }

    /**
     * Met à jour un enregistrement
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }
        $set = implode(', ', $set);

        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;

        $stmt = self::getDB()->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Supprime un enregistrement
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = self::getDB()->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Récupère des enregistrements avec pagination
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function paginate($page = 1, $perPage = ITEMS_PER_PAGE)
    {
        $offset = ($page - 1) * $perPage;
        $stmt = self::getDB()->prepare("SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Compte le nombre total d'enregistrements
     *
     * @return int
     */
    public function count()
    {
        $stmt = self::getDB()->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }

    /**
     * Recherche des enregistrements
     *
     * @param string $column
     * @param string $search
     * @return array
     */
    public function search($column, $search)
    {
        $stmt = self::getDB()->prepare("SELECT * FROM {$this->table} WHERE {$column} LIKE :search");
        $stmt->execute([':search' => '%' . $search . '%']);
        return $stmt->fetchAll();
    }

    /**
     * Magic getter pour accéder aux attributs
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Magic setter pour définir les attributs
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }
}
