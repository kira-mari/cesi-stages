<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Models\User;

/**
 * Tests unitaires pour le modèle User
 */
class UserTest extends TestCase
{
    /**
     * Test de la méthode findByEmail
     */
    public function testFindByEmail()
    {
        $userModel = new User();
        
        // Test avec un email qui existe
        $user = $userModel->findByEmail('admin@cesi.fr');
        $this->assertNotFalse($user);
        $this->assertEquals('admin@cesi.fr', $user['email']);
        $this->assertEquals('admin', $user['role']);
        
        // Test avec un email qui n'existe pas
        $user = $userModel->findByEmail('inexistant@cesi.fr');
        $this->assertFalse($user);
    }
    
    /**
     * Test de la méthode countByRole
     */
    public function testCountByRole()
    {
        $userModel = new User();
        
        // Test comptage des étudiants
        $count = $userModel->countByRole('etudiant');
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
        
        // Test comptage des pilotes
        $count = $userModel->countByRole('pilote');
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
        
        // Test comptage des admins
        $count = $userModel->countByRole('admin');
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }
    
    /**
     * Test de la méthode getByRolePaginated
     */
    public function testGetByRolePaginated()
    {
        $userModel = new User();
        
        // Test récupération des étudiants
        $etudiants = $userModel->getByRolePaginated('etudiant', 1, 10);
        $this->assertIsArray($etudiants);
        
        // Vérifier que tous les éléments sont bien des étudiants
        foreach ($etudiants as $etudiant) {
            $this->assertEquals('etudiant', $etudiant['role']);
        }
    }
    
    /**
     * Test de la méthode searchByRole
     */
    public function testSearchByRole()
    {
        $userModel = new User();
        
        // Test recherche par nom
        $results = $userModel->searchByRole('etudiant', 'Doe');
        $this->assertIsArray($results);
        
        // Test recherche par prénom
        $results = $userModel->searchByRole('etudiant', 'John');
        $this->assertIsArray($results);
        
        // Test recherche sans résultat
        $results = $userModel->searchByRole('etudiant', 'XYZ123');
        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }
    
    /**
     * Test de la création d'un utilisateur
     */
    public function testCreate()
    {
        $userModel = new User();
        
        $userData = [
            'nom' => 'Test',
            'prenom' => 'Utilisateur',
            'email' => 'test.utilisateur' . time() . '@cesi.fr',
            'password' => password_hash('test123', PASSWORD_BCRYPT),
            'role' => 'etudiant',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $userId = $userModel->create($userData);
        
        $this->assertIsInt((int)$userId);
        $this->assertGreaterThan(0, (int)$userId);
        
        // Vérifier que l'utilisateur a bien été créé
        $user = $userModel->find($userId);
        $this->assertNotFalse($user);
        $this->assertEquals($userData['nom'], $user['nom']);
        $this->assertEquals($userData['email'], $user['email']);
        
        // Nettoyage
        $userModel->delete($userId);
    }
    
    /**
     * Test de la mise à jour d'un utilisateur
     */
    public function testUpdate()
    {
        $userModel = new User();
        
        // Créer un utilisateur temporaire
        $userData = [
            'nom' => 'Test',
            'prenom' => 'Update',
            'email' => 'test.update' . time() . '@cesi.fr',
            'password' => password_hash('test123', PASSWORD_BCRYPT),
            'role' => 'etudiant',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $userId = $userModel->create($userData);
        
        // Mettre à jour l'utilisateur
        $updateData = [
            'nom' => 'TestModifié',
            'prenom' => 'UpdateModifié',
            'email' => $userData['email'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $userModel->update($userId, $updateData);
        $this->assertTrue($result);
        
        // Vérifier la mise à jour
        $user = $userModel->find($userId);
        $this->assertEquals('TestModifié', $user['nom']);
        $this->assertEquals('UpdateModifié', $user['prenom']);
        
        // Nettoyage
        $userModel->delete($userId);
    }
    
    /**
     * Test de la suppression d'un utilisateur
     */
    public function testDelete()
    {
        $userModel = new User();
        
        // Créer un utilisateur temporaire
        $userData = [
            'nom' => 'Test',
            'prenom' => 'Delete',
            'email' => 'test.delete' . time() . '@cesi.fr',
            'password' => password_hash('test123', PASSWORD_BCRYPT),
            'role' => 'etudiant',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $userId = $userModel->create($userData);
        
        // Supprimer l'utilisateur
        $result = $userModel->delete($userId);
        $this->assertTrue($result);
        
        // Vérifier que l'utilisateur n'existe plus
        $user = $userModel->find($userId);
        $this->assertFalse($user);
    }
}
