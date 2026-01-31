<div class="page-section">
    <div class="container">
        <div class="page-header">
            <h1>Statistiques</h1>
            <p>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    Vue d'ensemble et pilotage de la plateforme.
                <?php elseif ($_SESSION['user_role'] === 'pilote'): ?>
                    Suivi de l'activité de vos étudiants.
                <?php else: ?>
                    Suivi de votre recherche de stage.
                <?php endif; ?>
            </p>
        </div>

        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <!-- STATISTIQUES ADMINISTRATEUR -->
            <div class="stats-grid mb-5">
                <!-- Offres et Entreprises -->
                <div class="stat-card stat-primary">
                    <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="stat-info">
                        <span class="stat-number"><?= $totalOffres ?></span>
                        <span class="stat-label">Offres disponibles</span>
                    </div>
                </div>
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                     <div class="stat-info">
                        <span class="stat-number"><?= $totalEntreprises ?></span>
                        <span class="stat-label">Entreprises</span>
                    </div>
                </div>
                
                <!-- Utilisateurs -->
                <div class="stat-card stat-warning">
                    <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
                     <div class="stat-info">
                        <span class="stat-number"><?= $totalPilotes ?></span>
                        <span class="stat-label">Pilotes</span>
                    </div>
                </div>
                <div class="stat-card stat-info">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                     <div class="stat-info">
                        <span class="stat-number"><?= $totalEtudiants ?></span>
                        <span class="stat-label">Étudiants</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                     <div class="stat-info">
                        <span class="stat-number"><?= $totalAdmins ?></span>
                        <span class="stat-label">Administrateurs</span>
                    </div>
                </div>
            </div>

        <?php elseif ($_SESSION['user_role'] === 'pilote'): ?>
            <!-- STATISTIQUES PILOTE -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3><i class="fas fa-user-graduate"></i> Activité par étudiant</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($statsEtudiants)): ?>
                                <p class="text-muted">Aucune donnée disponible.</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($statsEtudiants as $etudiant): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></span>
                                            <span class="badge badge-primary rounded-pill"><?= $etudiant['nb_candidatures'] ?> candidatures</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3><i class="fas fa-heart"></i> Top Wishlist Promotion</h3>
                        </div>
                        <div class="card-body">
                             <?php if (empty($topWishlistPromo)): ?>
                                <p class="text-muted">Aucun article en wishlist.</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($topWishlistPromo as $wish): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><?= htmlspecialchars($wish['titre']) ?></span>
                                            <span class="badge badge-warning rounded-pill"><?= $wish['wishlist_count'] ?> likes</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Dernières candidatures des étudiants</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Offre</th>
                                    <th>Entreprise</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($offresEtudiants)): ?>
                                    <tr><td colspan="4" class="text-center py-3">Aucune candidature trouvée.</td></tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($offresEtudiants, 0, 10) as $cand): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($cand['etudiant_prenom'] . ' ' . $cand['etudiant_nom']) ?></td>
                                            <td><?= htmlspecialchars($cand['offre_titre']) ?></td>
                                            <td><?= htmlspecialchars($cand['entreprise_nom']) ?></td>
                                            <td class="text-muted"><?= date('d/m/Y', strtotime($cand['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php elseif ($_SESSION['user_role'] === 'etudiant'): ?>
            <!-- STATISTIQUES ETUDIANT -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-pie"></i> Compétences ciblées</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($repartitionCompetences)): ?>
                                <p class="text-muted">Postulez à des offres pour voir vos statistiques.</p>
                            <?php else: ?>
                                <div class="skills-list">
                                    <?php foreach ($repartitionCompetences as $comp => $count): ?>
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span><?= htmlspecialchars($comp) ?></span>
                                                <small class="text-muted"><?= $count ?> offres</small>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: <?= ($count / count($offresPostulees)) * 100 ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3><i class="fas fa-list-check"></i> Mes candidatures</h3>
                        </div>
                        <div class="card-body p-0">
                             <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Offre</th>
                                            <th>Date</th>
                                            <th>État</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($offresPostulees)): ?>
                                            <tr><td colspan="3" class="text-center py-3">Vous n'avez pas encore postulé.</td></tr>
                                        <?php else: ?>
                                            <?php foreach (array_slice($offresPostulees, 0, 5) as $cand): ?>
                                                <tr>
                                                    <td>
                                                        <div class="font-weight-bold"><?= htmlspecialchars($cand['offre_titre']) ?></div>
                                                        <small class="text-muted"><?= htmlspecialchars($cand['entreprise_nom']) ?></small>
                                                    </td>
                                                    <td><?= date('d/m/Y', strtotime($cand['created_at'])) ?></td>
                                                    <td><span class="badge badge-success rounded-pill">Envoyé</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="<?= BASE_URL ?>/candidatures/etudiant" class="btn btn-sm btn-outline-primary">Voir tout</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
