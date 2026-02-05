<div class="page-section">
    <div class="container">
        
        <div class="page-header">
            <h1>Offres de Stage</h1>
            <p>Découvrez les dernières opportunités et trouvez le stage qui vous correspond.</p>
        </div>

        <!-- MapLibre GL JS CSS & JS -->
        <link href='https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.css' rel='stylesheet' />
        <script src='https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js'></script>

        <div class="filters-form">
            <form action="<?= BASE_URL ?>/offres" method="GET">
                <div class="filters-row">
                    <div class="filter-group">
                        <label class="form-label" for="search">Rechercher</label>
                        <div class="input-group">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search" name="search" placeholder="Titre, description, entreprise..." value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <label class="form-label" for="competence">Compétence</label>
                        <div class="input-group">
                            <i class="fas fa-code"></i>
                            <select id="competence" name="competence">
                                <option value="">Toutes les compétences</option>
                                <?php if (!empty($competences)): ?>
                                    <?php foreach ($competences as $comp): ?>
                                        <option value="<?= htmlspecialchars($comp) ?>" <?= (isset($competence) && $competence === $comp) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($comp) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-group" style="max-width: 150px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; height: 3rem;">Filtrer</button>
                    </div>

                    <div class="filter-group" style="max-width: 150px;">
                        <button type="button" id="toggleMapBtn" class="btn btn-outline gap-2" style="width: 100%; height: 3rem; background: white; border-color: #e2e8f0;">
                            <i class="fas fa-map-marked-alt"></i> Carte
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Map Container -->
        <div id="map-container" style="display: none; position: relative; width: 100%; height: 500px; margin-bottom: 2rem; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0,0,0,0.05);">
            <div style="position: absolute; top: 15px; right: 50px; z-index: 10; background: white; padding: 5px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <select id="map-style-select" style="border: none; outline: none; font-size: 13px; padding: 5px; color: #333; font-weight: 500; cursor: pointer; min-width: 120px;">
                    <option value="https://basemaps.cartocdn.com/gl/voyager-gl-style/style.json">Voyager (Défaut)</option>
                    <option value="https://basemaps.cartocdn.com/gl/positron-gl-style/style.json">Positron (Clair)</option>
                    <option value="https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json">Dark Matter (Sombre)</option>
                    <option value="satellite">Satellite (ESRI)</option>
                </select>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mapBtn = document.getElementById('toggleMapBtn');
            const mapContainer = document.getElementById('map-container');
            const styleSelect = document.getElementById('map-style-select');
            let map = null;
            let markers = []; // Keep track of markers to re-add them if needed

            // City coordinates mapping (approximate)
            const cityCoords = {
                'Paris': [48.8566, 2.3522],
                'Lyon': [45.7640, 4.8357],
                'Marseille': [43.2965, 5.3698],
                'Bordeaux': [44.8378, -0.5792],
                'Lille': [50.6292, 3.0573],
                'Toulouse': [43.6047, 1.4442],
                'Nantes': [47.2184, -1.5536],
                'Strasbourg': [48.5734, 7.7521],
                'Montpellier': [43.6108, 3.8767],
                'Rennes': [48.1173, -1.6778],
                'Nice': [43.7102, 7.2620],
                'Grenoble': [45.1885, 5.7245],
                'Rouen': [49.4432, 1.0999],
                'Toulon': [43.1242, 5.928],
                'Angers': [47.4784, -0.5632],
                'Dijon': [47.3220, 5.0415],
                'Brest': [48.3904, -4.4861],
                'Le Mans': [48.0061, 0.1996],
                'Clermont-Ferrand': [45.7772, 3.0870],
                'Amiens': [49.8941, 2.2957],
                'Aix-en-Provence': [43.5297, 5.4474],
                'Limoges': [45.8336, 1.2611],
                'Tours': [47.3941, 0.6848],
                'Metz': [49.1193, 6.1757],
                'Besançon': [47.2378, 6.0241],
                'Perpignan': [42.6886, 2.8948],
                'Orléans': [47.9029, 1.9090],
                'Caen': [49.1828, -0.3706],
                'Mulhouse': [47.7508, 7.3358],
                'Nancy': [48.6921, 6.1844]
            };

            // Offers data from PHP
            // Note: We use $offresMap here which contains ALL offers (for the map),
            // whereas $offres in the view loop only contains the current page.
            const offres = <?= json_encode(array_map(function($o) {
                return [
                    'id' => $o['id'],
                    'titre' => $o['titre'],
                    'entreprise' => $o['entreprise_nom'],
                    'ville' => $o['entreprise_adresse'] ?? 'Paris', // Use full address
                    'desc' => substr($o['description'], 0, 100) . '...'
                ];
            }, $offresMap ?? [])) ?>;

            // Define styles
            const styles = {
                'voyager': 'https://basemaps.cartocdn.com/gl/voyager-gl-style/style.json',
                'positron': 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
                'dark': 'https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json',
                'satellite': {
                    "version": 8,
                    "sources": {
                        "raster-tiles": {
                            "type": "raster",
                            "tiles": ["https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}"],
                            "tileSize": 256,
                            "attribution": "Esri, DigitalGlobe, GeoEye, Earthstar Geographics, CNES/Airbus DS, USDA, USGS, AeroGRID, IGN, and the GIS User Community"
                        }
                    },
                    "layers": [{
                        "id": "simple-tiles",
                        "type": "raster",
                        "source": "raster-tiles",
                        "minzoom": 0,
                        "maxzoom": 22
                    }]
                }
            };

            // Function to determine map style based on site theme
            function getThemeBasedStyle() {
                const isLight = document.documentElement.getAttribute('data-theme') === 'light';
                // If user selected satellite manually in the dropdown, we might want to keep it? 
                // The prompt says "if dark puts dark, if light puts voyager", implying auto-switch unless maybe satellite is forced.
                // For now, let's just return the style URL.
                return isLight ? styles.voyager : styles.dark;
            }

            mapBtn.addEventListener('click', function() {
                if (mapContainer.style.display === 'none') {
                    // Show map with slide down effect
                    mapContainer.style.display = 'block';
                    mapContainer.style.opacity = 0;
                    mapContainer.style.transform = 'translateY(-20px)';
                    mapContainer.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    
                    setTimeout(() => {
                        mapContainer.style.opacity = 1;
                        mapContainer.style.transform = 'translateY(0)';
                    }, 10);

                    // Initialize map if not exists
                    if (!map) {
                        const initialStyle = getThemeBasedStyle();
                        
                        // Update select to match initial auto-detected style
                        if (initialStyle === styles.voyager) styleSelect.value = styles.voyager;
                        else if (initialStyle === styles.dark) styleSelect.value = styles.dark;
                        else styleSelect.value = initialStyle; // fallback

                        map = new maplibregl.Map({
                            container: 'map-container',
                            style: initialStyle, 
                            center: [2, 46],
                            zoom: 5
                        });

                        // Add navigation controls
                        map.addControl(new maplibregl.NavigationControl());

                        map.on('load', () => {
                            addMarkers();
                        });

                        // Style switcher logic (Manual)
                        styleSelect.addEventListener('change', (e) => {
                            const val = e.target.value;
                            if (val === 'satellite') {
                                map.setStyle(styles.satellite);
                            } else {
                                map.setStyle(val);
                            }
                        });
                        
                        // Auto-theme observer
                        const observer = new MutationObserver((mutations) => {
                            mutations.forEach((mutation) => {
                                if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
                                    // Only switch if the user hasn't explicitly chosen "satellite"
                                    // or if we want to force the theme. 
                                    // Let's force theme match unless currently on satellite which is special.
                                    if (styleSelect.value !== 'satellite') {
                                        const newStyle = getThemeBasedStyle();
                                        map.setStyle(newStyle);
                                        // Update dropdown
                                        if (newStyle === styles.voyager) styleSelect.value = styles.voyager;
                                        else if (newStyle === styles.dark) styleSelect.value = styles.dark;
                                    }
                                }
                            });
                        });
                        
                        observer.observe(document.documentElement, {
                            attributes: true,
                            attributeFilter: ['data-theme']
                        });

                    } else {
                        setTimeout(() => map.resize(), 300); // MapLibre resize
                    }
                } else {
                    // Hide map with smoother animation
                    mapContainer.style.opacity = 0;
                    mapContainer.style.transform = 'translateY(-20px) scale(0.98)';
                    // Ensure transition is applied if it wasn't already or was overwritten
                    mapContainer.style.transition = 'opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                    
                    setTimeout(() => {
                        mapContainer.style.display = 'none';
                        // Reset transform/opacity for next open, but keep display none
                         mapContainer.style.transform = 'translateY(-20px) scale(0.98)';
                    }, 400); // Match transition duration
                }
            });

            async function addMarkers() {
                const bounds = new maplibregl.LngLatBounds();
                let hasMarkers = false;

                // 1. Identification des adresses uniques pour optimiser les appels API
                const uniqueAddresses = [...new Set(offres.map(o => o.ville))];
                const addressCoordsMap = new Map();

                // 2. Géocodage en parallèle via l'API Adresse.data.gouv.fr (Très rapide, pas de restrictions lourdes)
                await Promise.all(uniqueAddresses.map(async (address) => {
                    let coords = null;
                    
                    // A. Essai API BAN en PREMIER (Précision maximale)
                    // On ne check PAS les mots clés "Paris", etc. avant, sinon "10 Rue de Paris" finit au centre du village "Paris" au lieu de la rue.
                    try {
                        const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(address)}&limit=1`);
                        const data = await response.json();
                        if (data.features && data.features.length > 0) {
                            coords = data.features[0].geometry.coordinates; // [lon, lat] natif API
                        }
                    } catch (e) {
                        console.warn("Erreur géocodage BAN pour:", address, e);
                    }

                    // B. Fallback : Liste codée en dur (Uniquement si l'API n'a rien trouvé)
                    if (!coords) {
                        for (const [city, c] of Object.entries(cityCoords)) {
                            if (address.toLowerCase().includes(city.toLowerCase())) {
                                coords = [c[1], c[0]]; // Conversion [lat, lon] -> [lon, lat]
                                break;
                            }
                        }
                    }

                    if (coords) {
                        addressCoordsMap.set(address, coords);
                    }
                }));

                // 3. Création des marqueurs pour CHAQUE offre (avec léger décalage pour éviter les superpositions)
                for (const offre of offres) {
                    let originalCoords = addressCoordsMap.get(offre.ville);
                    
                    // Fallback Paris si échec total
                    if (!originalCoords) {
                        originalCoords = [cityCoords['Paris'][1], cityCoords['Paris'][0]];
                    }
                    
                    // Ajout d'un "Jitter" (bruit aléatoire) pour séparer les marqueurs au même endroit
                    // Environ 10-20 mètres de décalage max
                    const jitterLon = (Math.random() - 0.5) * 0.0003;
                    const jitterLat = (Math.random() - 0.5) * 0.0003;
                    const finalLngLat = [originalCoords[0] + jitterLon, originalCoords[1] + jitterLat];
                    
                    hasMarkers = true;
                    bounds.extend(finalLngLat);

                    // Create popup
                    const popup = new maplibregl.Popup({ offset: 25 }).setHTML(`
                        <div style="min-width: 200px; font-family: 'Inter', sans-serif;">
                            <h6 style="margin: 0 0 5px 0; color: #1e293b; font-weight: 700; font-size: 1rem;">${offre.titre}</h6>
                            <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 8px;">
                                <i class="fas fa-building" style="margin-right: 4px;"></i> ${offre.entreprise}
                            </div>
                            <div style="color: #64748b; font-size: 0.8rem; margin-bottom: 12px; display: flex; align-items: center;">
                                <i class="fas fa-map-marker-alt" style="margin-right: 6px; color: #ef4444;"></i> ${offre.ville}
                            </div>
                            <a href="<?= BASE_URL ?>/offres/show/${offre.id}" 
                               style="display: block; text-align: center; background-color: #3b82f6; color: white; text-decoration: none; padding: 8px 12px; border-radius: 6px; font-weight: 500; font-size: 0.875rem; transition: background-color 0.2s;">
                                Voir l'offre
                            </a>
                        </div>
                    `);

                    // Create marker
                    const marker = new maplibregl.Marker()
                        .setLngLat(finalLngLat)
                        .setPopup(popup)
                        .addTo(map);
                    
                    markers.push(marker);
                }

                 // Fit bounds
                if (hasMarkers) {
                    map.fitBounds(bounds, {
                        padding: 50,
                        maxZoom: 14 // Higher max zoom for vector maps
                    });
                }
            }
        });
        </script>

        <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'pilote'])): ?>
            <div class="d-flex justify-content-end mb-4">
                <a href="<?= BASE_URL ?>/offres/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Créer une offre
                </a>
            </div>
        <?php endif; ?>

        <div class="offers-grid">
            <?php if (empty($offres)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                    <h3>Aucune offre trouvée</h3>
                    <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                </div>
            <?php else: ?>
                <?php foreach ($offres as $offre): ?>
                    <div class="offer-card position-relative pt-5">
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'etudiant'): ?>
                            <?php 
                                $inWishlist = in_array($offre['id'], $wishlistIds ?? []);
                                $wishlistAction = $inWishlist ? 'removeFromWishlist' : 'addToWishlist';
                                $wishlistTitle = $inWishlist ? 'Retirer de ma liste' : 'Ajouter à ma liste';
                                // Theme color (primary) instead of warning
                                $btnClass = $inWishlist ? 'btn-primary text-white' : 'btn-outline-secondary';
                                $iconClass = $inWishlist ? 'fas fa-star' : 'far fa-star';
                            ?>
                            <a href="<?= BASE_URL ?>/offres/<?= $wishlistAction ?>/<?= $offre['id'] ?>" 
                               class="btn btn-icon <?= $btnClass ?> rounded-circle position-absolute top-0 end-0 m-3 wishlist-btn" 
                               data-id="<?= $offre['id'] ?>"
                               data-in-wishlist="<?= $inWishlist ? 'true' : 'false' ?>"
                               data-bs-toggle="tooltip" 
                               title="<?= $wishlistTitle ?>" 
                               style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; z-index: 5;">
                                <i class="<?= $iconClass ?>"></i>
                            </a>
                        <?php endif; ?>

                        <div class="offer-header d-block mb-3">
                            <h3 class="mb-2"><?= htmlspecialchars($offre['titre']) ?></h3>
                            <div class="d-flex align-items-center text-primary small">
                                <i class="fas fa-building me-2 opacity-75"></i>
                                <span class="offer-company fw-bold"><?= htmlspecialchars($offre['entreprise_nom'] ?? 'Entreprise') ?></span>
                            </div>
                        </div>
                        
                        <div class="offer-body">
                            <div class="offer-skills">
                                <?php 
                                    if (!empty($offre['competences'])) {
                                        $creationParams = json_decode($offre['competences'], true);
                                        if (is_array($creationParams)) {
                                            foreach (array_slice($creationParams, 0, 3) as $comp) {
                                                echo '<span class="skill-tag">' . htmlspecialchars($comp) . '</span>';
                                            }
                                            if (count($creationParams) > 3) {
                                                echo '<span class="skill-tag">+' . (count($creationParams) - 3) . '</span>';
                                            }
                                        }
                                    }
                                ?>
                            </div>
                            
                            <p>
                                <?= nl2br(htmlspecialchars(substr($offre['description'], 0, 150) . '..')) ?>
                            </p>

                            <div class="offer-meta">
                                <span><i class="far fa-clock"></i> <?= htmlspecialchars($offre['duree'] ?? 'N/C') ?> mois</span>
                                <span><i class="fas fa-coins"></i> <?= htmlspecialchars(number_format($offre['remuneration'] ?? 0, 0, ',', ' ')) ?> €/mois</span>
                                <span><i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($offre['created_at'] ?? 'now')) ?></span>
                            </div>
                        </div>

                        <div class="offer-footer d-flex justify-content-between align-items-center">
                            <a href="<?= BASE_URL ?>/offres/<?= $offre['id'] ?>" class="btn btn-outline w-100">Voir l'offre</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination-container mt-4 text-center">
            <div class="btn-group">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&competence=<?= urlencode($competence ?? '') ?>" 
                       class="btn btn-sm <?= ($page == $i) ? 'btn-primary' : 'btn-outline' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Select all wishlist buttons
    const wishlistBtns = document.querySelectorAll('.wishlist-btn');
    const BASE_URL = '<?= BASE_URL ?>';

    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault(); // Prevent default link navigation
            e.stopPropagation(); // Stop bubbling

            const id = btn.getAttribute('data-id');
            const isInWishlist = btn.getAttribute('data-in-wishlist') === 'true';
            
            // Determine action URL
            // If currently in wishlist => remove, else => add
            // But we can also just use the href if it's correctly set by PHP, 
            // OR use hardcoded logic. Let's rely on current state.
            const action = isInWishlist ? 'removeFromWishlist' : 'addToWishlist';
            const url = `${BASE_URL}/offres/${action}/${id}`;

            try {
                // Add loading state visuals? (Optional)
                btn.style.opacity = '0.7';

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    
                    if (data.success) {
                        // Toggle state
                        const newState = !isInWishlist;
                        btn.setAttribute('data-in-wishlist', newState ? 'true' : 'false');
                        
                        // Update visual
                        const icon = btn.querySelector('i');
                        if (newState) {
                            // Added
                            btn.classList.remove('btn-outline-secondary');
                            btn.classList.add('btn-primary', 'text-white');
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                            btn.setAttribute('title', 'Retirer de ma liste');
                            
                            // Show toast/flash (Create simple toast manually)
                            showToast("Offre ajoutée à votre liste", "success");
                        } else {
                            // Removed
                            btn.classList.remove('btn-primary', 'text-white');
                            btn.classList.add('btn-outline-secondary');
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                            btn.setAttribute('title', 'Ajouter à ma liste');
                            
                            showToast("Offre retirée de votre liste", "info");
                        }
                    }
                }
            } catch (err) {
                console.error("Erreur wishlist:", err);
            } finally {
                btn.style.opacity = '1';
                // Update href for fallback
                const nextAction = btn.getAttribute('data-in-wishlist') === 'true' ? 'removeFromWishlist' : 'addToWishlist';
                btn.setAttribute('href', `${BASE_URL}/offres/${nextAction}/${id}`);
            }
        });
    });

    // Helper for simple toast notification matching the system flash messages
    function showToast(message, type = 'info') {
        // Enforce max 3 toasts limit
        const existingToasts = document.querySelectorAll('.flash-message');
        if (existingToasts.length >= 3) {
            // Remove the oldest one (first in the DOM)
            existingToasts[0].remove();
        }

        const toast = document.createElement('div');
        toast.className = `flash-message flash-${type}`;
        
        // Recalculate top offset based on currently visible toasts (re-query after removal)
        const currentToasts = document.querySelectorAll('.flash-message');
        let topOffset = 90; // Default start from CSS
        if (currentToasts.length > 0) {
            topOffset += (currentToasts.length * 70); 
        }
        
        toast.style.top = `${topOffset}px`;
        
        let icon = type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
        
        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            ${message}
            <button class="flash-close" onclick="this.parentElement.remove()">&times;</button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove
        setTimeout(() => {
            toast.style.transition = 'opacity 0.3s, transform 0.3s';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
</script>
