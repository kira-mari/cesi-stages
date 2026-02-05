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


        <!-- SFx 11: Carrousel Statistiques Offres (Visible pour tous) -->
        <div class="card mb-4 border-0 shadow-sm stats-carousel-card">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h3 class="mb-0"><i class="fas fa-chart-line text-primary"></i> Statistiques des Offres</h3>
            </div>
            <div class="card-body p-0">
                <div id="carouselStatsOffres" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselStatsOffres" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Global"></button>
                        <button type="button" data-bs-target="#carouselStatsOffres" data-bs-slide-to="1" aria-label="Durée"></button>
                        <button type="button" data-bs-target="#carouselStatsOffres" data-bs-slide-to="2" aria-label="Wishlist"></button>
                    </div>
                    <div class="carousel-inner">
                        
                        <!-- Slide 1: Indicateurs Globaux -->
                        <div class="carousel-item active">
                            <div class="d-flex h-100 align-items-center justify-content-center">
                                <div class="row w-100 text-center">
                                    <div class="col-6 border-end border-secondary border-opacity-25">
                                        <div class="display-4 fw-bold text-primary animate-value"><?= $totalOffres ?></div>
                                        <div class="text-muted text-uppercase small ls-1">Offres Disponibles</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="display-4 fw-bold text-info animate-value"><?= $moyenneCandidatures ?></div>
                                        <div class="text-muted text-uppercase small ls-1">Candidatures / Offre</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2: Répartition par Durée -->
                        <div class="carousel-item">
                            <div class="d-flex flex-column h-100 align-items-center justify-content-center p-3 p-md-4">
                                <h5 class="mb-3 text-muted">Répartition par Durée</h5>
                                <div class="w-100 w-md-75">
                                    <ul class="list-group list-group-flush">
                                        <?php if(empty($repartitionDuree)): ?>
                                            <li class="list-group-item bg-transparent text-center">Aucune donnée</li>
                                        <?php else: ?>
                                            <?php foreach ($repartitionDuree as $rep): ?>
                                                <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-10 py-2 px-3">
                                                    <span><?= htmlspecialchars($rep['duree_categorie']) ?></span>
                                                    <span class="badge bg-primary text-white rounded-pill ms-2"><?= $rep['nombre'] ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3: Top Wishlist -->
                        <div class="carousel-item">
                            <div class="d-flex flex-column h-100 align-items-center justify-content-center p-3 p-md-4">
                                <h5 class="mb-3 text-muted">Top 5 Wishlist</h5>
                                <div class="w-100 px-lg-5">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0">
                                            <tbody>
                                            <?php if(empty($topWishlist)): ?>
                                                <tr><td class="text-center">Aucune donnée</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($topWishlist as $index => $top): ?>
                                                    <tr>
                                                        <td style="width: 30px;" class="text-muted">#<?= $index + 1 ?></td>
                                                        <td class="text-truncate" style="max-width: 250px;">
                                                            <strong class="d-block text-truncate"><?= htmlspecialchars($top['titre']) ?></strong>
                                                            <small class="text-muted text-truncate"><?= htmlspecialchars($top['entreprise_nom']) ?></small>
                                                        </td>
                                                        <td class="text-end">
                                                            <span class="badge bg-danger rounded-pill"><i class="fas fa-heart me-1"></i> <?= $top['wishlist_count'] ?></span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselStatsOffres" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-secondary rounded-circle p-2 bg-opacity-25" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselStatsOffres" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-secondary rounded-circle p-2 bg-opacity-25" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
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
                <!-- Compétences Ciblées -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm" style="background-color: hsl(var(--card)); color: hsl(var(--card-foreground));">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <h4 class="mb-0 d-flex align-items-center gap-2">
                                <i class="fas fa-chart-pie text-primary"></i> Compétences ciblées
                            </h4>
                        </div>
                        <div class="card-body p-4">
                            <?php if (empty($repartitionCompetences)): ?>
                                <div class="text-center py-5 opacity-50">
                                    <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                    <p>Postulez à des offres pour voir vos statistiques.</p>
                                </div>
                            <?php else: ?>
                                <div class="skills-list d-flex flex-column gap-4">
                                    <?php foreach ($repartitionCompetences as $comp => $count): ?>
                                        <?php $percent = ($count / count($offresPostulees)) * 100; ?>
                                        <div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="fw-bold"><?= htmlspecialchars($comp) ?></span>
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill"><?= $count ?> offres</span>
                                            </div>
                                            <div class="progress rounded-pill bg-secondary bg-opacity-25" style="height: 10px;">
                                                <div class="progress-bar bg-gradient-primary" role="progressbar" 
                                                     style="width: <?= $percent ?>%; background: linear-gradient(90deg, #4f46e5, #9333ea);"
                                                     aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Mes Candidatures -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm" style="background-color: hsl(var(--card)); color: hsl(var(--card-foreground));">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <h4 class="mb-0 d-flex align-items-center gap-2">
                                <i class="fas fa-list-check text-success"></i> Mes candidatures
                            </h4>
                        </div>
                        <div class="card-body p-0 pt-3">
                             <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0" style="background-color: transparent; --bs-table-bg: transparent; --bs-table-color: inherit; color: inherit;">
                                    <thead class="opacity-75 small text-uppercase fw-bold border-bottom border-secondary border-opacity-10">
                                        <tr>
                                            <th class="ps-4">Offre</th>
                                            <th>Date</th>
                                            <th class="text-end pe-4">État</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($offresPostulees)): ?>
                                            <tr><td colspan="3" class="text-center py-5 opacity-75">Vous n'avez pas encore postulé.</td></tr>
                                        <?php else: ?>
                                            <?php foreach (array_slice($offresPostulees, 0, 5) as $cand): ?>
                                                <tr class="border-bottom border-secondary border-opacity-10 hover-bg-opacity-5">
                                                    <td class="ps-4 py-3">
                                                        <div class="fw-bold"><?= htmlspecialchars($cand['offre_titre']) ?></div>
                                                        <small class="opacity-75"><?= htmlspecialchars($cand['entreprise_nom']) ?></small>
                                                    </td>
                                                    <td class="py-3 opacity-75"><?= date('d/m/Y', strtotime($cand['created_at'])) ?></td>
                                                    <td class="text-end pe-4 py-3">
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                                            <i class="fas fa-check-circle me-1"></i> Envoyé
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center pb-4 pt-3">
                            <a href="<?= BASE_URL ?>/candidatures/etudiant" class="btn btn-outline-primary rounded-pill px-4">
                                Voir toutes mes candidatures
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
