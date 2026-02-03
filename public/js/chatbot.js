/**
 * Chatbot flottant - gestion de l'UI et des appels AJAX
 * URL cible : https://cesi-site.local/chatbot/ask
 */

(function () {
    const CHATBOT_ENDPOINT = 'https://cesi-site.local/chatbot/ask';
    // Clé d'historique unique par utilisateur
    const getHistoryKey = () => {
        const userId = window.CHATBOT_USER_ID || 'guest';
        return `cesistages_chatbot_history_v1_user_${userId}`;
    };
    let chatbotHistory = [];

    /**
     * Crée le markup du widget si absent (injection non intrusive)
     */
    function ensureChatbotDOM() {
        if (document.querySelector('.chatbot-widget')) {
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'chatbot-widget';
        wrapper.innerHTML = `
            <div class="chatbot-panel" id="chatbotPanel" aria-hidden="true">
                <div class="chatbot-header">
                    <div class="chatbot-header-main">
                        <div class="chatbot-avatar">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div>
                            <div class="chatbot-title">Assistant stages</div>
                            <div class="chatbot-subtitle">
                                <span class="chatbot-status-dot"></span>
                                Disponible pour vos questions
                            </div>
                        </div>
                    </div>
                    <button class="chatbot-close-btn" type="button" aria-label="Fermer le chatbot">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="chatbot-messages" id="chatbotMessages" aria-live="polite">
                </div>
                <div class="chatbot-footer">
                    <form class="chatbot-form" id="chatbotForm">
                        <textarea
                            class="chatbot-input"
                            id="chatbotInput"
                            rows="1"
                            placeholder="Posez votre question..."
                        ></textarea>
                        <button class="chatbot-send-btn" id="chatbotSendBtn" type="submit" aria-label="Envoyer">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4L20 12L4 20L4 13L11 12L4 11L4 4Z" fill="currentColor"/>
                            </svg>
                        </button>
                    </form>
                    <div class="chatbot-error" id="chatbotError" style="display:none;"></div>
                </div>
            </div>

            <button class="chatbot-toggle-btn" id="chatbotToggleBtn" type="button" aria-label="Ouvrir le chatbot">
                <i class="fa-solid fa-comments chatbot-icon-open"></i>
                <i class="fa-solid fa-xmark chatbot-icon-close"></i>
            </button>
        `;

        document.body.appendChild(wrapper);
    }

    /**
     * Ajoute un message dans le flux
     */
    function appendMessage(text, from = 'bot', options) {
        const opts = options || {};
        const messagesEl = document.getElementById('chatbotMessages');
        if (!messagesEl) return;

        const msg = document.createElement('div');
        msg.className = 'chatbot-message ' + (from === 'user' ? 'chatbot-message--user' : 'chatbot-message--bot');
        msg.innerHTML = `${escapeHtml(text)}<div class="chatbot-message-meta">${from === 'user' ? 'Vous' : 'Bot'} · ${formatTime(
            new Date()
        )}</div>`;

        messagesEl.appendChild(msg);
        messagesEl.scrollTop = messagesEl.scrollHeight;

        if (opts.store !== false) {
            chatbotHistory.push({ type: from, text: text });
            saveHistory();
        }
    }

    /**
     * Ajoute un message du bot avec éventuels liens vers des offres
     * payload = { answer: string, offers?: [{title, url}] }
     */
    function appendBotAnswer(payload, options) {
        const opts = options || {};
        const messagesEl = document.getElementById('chatbotMessages');
        if (!messagesEl || !payload) return;

        const msg = document.createElement('div');
        msg.className = 'chatbot-message chatbot-message--bot';

        const textWrapper = document.createElement('div');
        const safeAnswer = typeof payload.answer === 'string' ? payload.answer : '';
        textWrapper.innerHTML = escapeHtml(safeAnswer).replace(/\n/g, '<br>');
        msg.appendChild(textWrapper);

        if (Array.isArray(payload.offers) && payload.offers.length > 0) {
            const list = document.createElement('ul');
            list.style.marginTop = '0.5rem';
            list.style.paddingLeft = '1.1rem';

            payload.offers.forEach(function (offer) {
                if (!offer || !offer.url || !offer.title) return;
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = offer.url;
                a.textContent = offer.title;
                a.style.color = 'hsl(var(--primary))';
                a.style.textDecoration = 'underline';
                li.appendChild(a);
                list.appendChild(li);
            });

            msg.appendChild(list);
        }

        // Afficher la liste des étudiants (pour les réponses admin sur les candidatures)
        if (Array.isArray(payload.etudiants) && payload.etudiants.length > 0) {
            const list = document.createElement('ul');
            list.style.marginTop = '0.5rem';
            list.style.paddingLeft = '1.1rem';
            list.style.listStyle = 'disc';

            payload.etudiants.forEach(function (etudiant) {
                if (!etudiant || !etudiant.nom) return;
                const li = document.createElement('li');
                li.style.marginBottom = '0.25rem';
                
                const nomSpan = document.createElement('span');
                nomSpan.textContent = etudiant.nom;
                nomSpan.style.fontWeight = '500';
                li.appendChild(nomSpan);

                if (etudiant.email) {
                    const emailSpan = document.createElement('span');
                    emailSpan.textContent = ' (' + etudiant.email + ')';
                    emailSpan.style.color = 'hsl(var(--muted-foreground))';
                    emailSpan.style.fontSize = '0.9em';
                    li.appendChild(emailSpan);
                }

                if (etudiant.statut) {
                    const statutSpan = document.createElement('span');
                    statutSpan.textContent = ' - ' + etudiant.statut;
                    const statutColor = etudiant.statut === 'Acceptée' ? '#4ade80' : 
                                       etudiant.statut === 'Refusée' ? '#f87171' : 
                                       'hsl(var(--muted-foreground))';
                    statutSpan.style.color = statutColor;
                    statutSpan.style.fontSize = '0.9em';
                    statutSpan.style.fontWeight = '500';
                    li.appendChild(statutSpan);
                }

                list.appendChild(li);
            });

            msg.appendChild(list);
        }

        // Afficher un tableau des étudiants (pour les réponses admin sur tous les étudiants)
        if (Array.isArray(payload.etudiants_table) && payload.etudiants_table.length > 0) {
            const tableWrapper = document.createElement('div');
            tableWrapper.style.marginTop = '1rem';
            tableWrapper.style.overflowX = 'auto';
            tableWrapper.style.maxHeight = '400px';
            tableWrapper.style.overflowY = 'auto';

            const table = document.createElement('table');
            table.style.width = '100%';
            table.style.borderCollapse = 'collapse';
            table.style.fontSize = '0.875rem';
            table.style.border = '1px solid hsl(var(--border))';
            table.style.borderRadius = '0.5rem';
            table.style.overflow = 'hidden';

            // En-tête du tableau
            const thead = document.createElement('thead');
            thead.style.backgroundColor = 'hsl(var(--muted))';
            const headerRow = document.createElement('tr');
            
            ['ID', 'Nom', 'Email', 'Date création'].forEach(function (headerText) {
                const th = document.createElement('th');
                th.textContent = headerText;
                th.style.padding = '0.75rem';
                th.style.textAlign = 'left';
                th.style.borderBottom = '1px solid hsl(var(--border))';
                th.style.fontWeight = '600';
                headerRow.appendChild(th);
            });
            
            thead.appendChild(headerRow);
            table.appendChild(thead);

            // Corps du tableau
            const tbody = document.createElement('tbody');
            payload.etudiants_table.forEach(function (etudiant, index) {
                const row = document.createElement('tr');
                row.style.borderBottom = index < payload.etudiants_table.length - 1 ? '1px solid hsl(var(--border))' : 'none';
                row.style.backgroundColor = index % 2 === 0 ? 'transparent' : 'hsl(var(--muted) / 0.3)';

                [etudiant.id || 'N/A', etudiant.nom || 'N/A', etudiant.email || 'N/A', etudiant.date_creation || 'N/A'].forEach(function (cellText) {
                    const td = document.createElement('td');
                    td.textContent = cellText;
                    td.style.padding = '0.75rem';
                    td.style.borderRight = '1px solid hsl(var(--border))';
                    if (cellText === etudiant.email) {
                        td.style.color = 'hsl(var(--primary))';
                    }
                    row.appendChild(td);
                });

                tbody.appendChild(row);
            });
            
            table.appendChild(tbody);
            tableWrapper.appendChild(table);
            msg.appendChild(tableWrapper);
        }

        const meta = document.createElement('div');
        meta.className = 'chatbot-message-meta';
        meta.textContent = 'Bot · ' + formatTime(new Date());
        msg.appendChild(meta);

        messagesEl.appendChild(msg);
        messagesEl.scrollTop = messagesEl.scrollHeight;

        if (opts.store !== false) {
            chatbotHistory.push({
                type: 'bot',
                payload: payload, // Sauvegarder le payload complet
                text: safeAnswer, // Garder pour compatibilité
                offers: Array.isArray(payload.offers) ? payload.offers : []
            });
            saveHistory();
        }
    }

    /**
     * Ajoute l'indicateur de saisie du bot
     */
    function showTyping() {
        const messagesEl = document.getElementById('chatbotMessages');
        if (!messagesEl) return;
        if (messagesEl.querySelector('.chatbot-typing')) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'chatbot-message chatbot-message--bot';
        wrapper.innerHTML = `
            <span class="chatbot-typing">
                <span class="chatbot-typing-dot"></span>
                <span class="chatbot-typing-dot"></span>
                <span class="chatbot-typing-dot"></span>
            </span>
        `;
        wrapper.dataset.typing = 'true';

        messagesEl.appendChild(wrapper);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function hideTyping() {
        const messagesEl = document.getElementById('chatbotMessages');
        if (!messagesEl) return;
        const typing = messagesEl.querySelector('[data-typing="true"]');
        if (typing) typing.remove();
    }

    /**
     * Gestion des erreurs front
     */
    function showError(msg) {
        const el = document.getElementById('chatbotError');
        if (!el) return;
        el.textContent = msg;
        el.style.display = 'block';
    }

    function clearError() {
        const el = document.getElementById('chatbotError');
        if (!el) return;
        el.textContent = '';
        el.style.display = 'none';
    }

    /**
     * Helpers
     */
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
            .replace(/\n/g, '<br>');
    }

    function formatTime(date) {
        return date.toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function saveHistory() {
        try {
            const historyKey = getHistoryKey();
            localStorage.setItem(historyKey, JSON.stringify(chatbotHistory));
        } catch (e) {
            // ignore
        }
    }

    function loadHistory() {
        try {
            const historyKey = getHistoryKey();
            const raw = localStorage.getItem(historyKey);
            if (!raw) return [];
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }
    
    // Fonction pour détecter le changement d'utilisateur et recharger l'historique
    function checkUserChange() {
        const currentUserId = window.CHATBOT_USER_ID || 'guest';
        const lastUserId = window.CHATBOT_LAST_USER_ID;
        
        // Si l'utilisateur a changé, on va recharger l'historique dans initChatbot
        // On sauvegarde juste l'ID actuel pour la prochaine vérification
        if (lastUserId !== undefined && lastUserId !== currentUserId) {
            // L'utilisateur a changé, on va recharger l'historique dans initChatbot
            // Ne rien faire ici, laissez initChatbot gérer le chargement
        }
        
        window.CHATBOT_LAST_USER_ID = currentUserId;
    }

    /**
     * Envoi de la requête au backend
     */
    async function sendMessageToBackend(message) {
        const sendBtn = document.getElementById('chatbotSendBtn');
        const input = document.getElementById('chatbotInput');

        if (sendBtn) sendBtn.disabled = true;
        clearError();
        showTyping();

        try {
            const response = await fetch(CHATBOT_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ message: message })
            });

            let payload;
            try {
                payload = await response.json();
            } catch (e) {
                payload = null;
            }

            hideTyping();

            if (!response.ok || !payload || typeof payload.answer === 'undefined') {
                showError('Une erreur est survenue lors de la réponse du chatbot.');
                return;
            }
            appendBotAnswer(payload);
        } catch (error) {
            hideTyping();
            showError("Impossible de joindre le serveur du chatbot.");
        } finally {
            if (sendBtn) sendBtn.disabled = false;
            if (input) input.focus();
        }
    }

    /**
     * Initialisation des évènements
     */
    function initChatbot() {
        ensureChatbotDOM();

        const panel = document.getElementById('chatbotPanel');
        const toggleBtn = document.getElementById('chatbotToggleBtn');
        const closeBtn = panel ? panel.querySelector('.chatbot-close-btn') : null;
        const form = document.getElementById('chatbotForm');
        const input = document.getElementById('chatbotInput');

        if (!panel || !toggleBtn || !form || !input) {
            return;
        }

        // Vérifier le changement d'utilisateur et charger l'historique approprié
        checkUserChange();
        
        // Charger l'historique de l'utilisateur actuel
        chatbotHistory = loadHistory();
        const messagesEl = document.getElementById('chatbotMessages');
        if (messagesEl) {
            // Toujours remplacer le contenu initial par l'historique
            messagesEl.innerHTML = '';
            
            if (Array.isArray(chatbotHistory) && chatbotHistory.length > 0) {
                // Charger l'historique existant
                chatbotHistory.forEach(function (entry) {
                    if (!entry) return;
                    if (entry.type === 'user') {
                        appendMessage(entry.text, 'user', { store: false });
                    } else if (entry.type === 'bot') {
                        appendBotAnswer(entry.payload || { answer: entry.text, offers: entry.offers || [], etudiants: entry.etudiants || [], etudiants_table: entry.etudiants_table || [] }, { store: false });
                    }
                });
                messagesEl.scrollTop = messagesEl.scrollHeight;
            } else {
                // Si pas d'historique, initialiser avec le message d'accueil et le sauvegarder
                chatbotHistory = [];
                const welcomeAnswer = 'Bonjour 👋 Je suis l\'assistant CesiStages.\n\nVoici des exemples de questions que vous pouvez me poser :\n• « Je cherche un stage à Lyon »\n• « Stage de 6 mois »\n• « Comment postuler ? »\n\nDites « aide » pour voir plus d\'exemples !';
                
                // Ajouter le message d'accueil à l'historique
                chatbotHistory.push({
                    type: 'bot',
                    payload: {
                        answer: welcomeAnswer,
                        needs_admin: false,
                        offers: []
                    }
                });
                
                // Afficher le message d'accueil
                appendBotAnswer({
                    answer: welcomeAnswer,
                    needs_admin: false,
                    offers: []
                }, { store: false }); // Ne pas sauvegarder deux fois
                
                // Sauvegarder l'historique
                saveHistory();
            }
        }

        function togglePanel() {
            const widget = document.querySelector('.chatbot-widget');
            const isActive = widget.classList.toggle('active');
            
            panel.setAttribute('aria-hidden', !isActive);
            
            if (isActive) {
                input.focus();
            }
        }

        function closePanel() {
            const widget = document.querySelector('.chatbot-widget');
            widget.classList.remove('active');
            panel.setAttribute('aria-hidden', 'true');
        }

        toggleBtn.addEventListener('click', togglePanel);
        if (closeBtn) {
            closeBtn.addEventListener('click', closePanel);
        }

        // Fermer avec Echap
        document.addEventListener('keydown', function (e) {
            const widget = document.querySelector('.chatbot-widget');
            if (e.key === 'Escape' && widget.classList.contains('active')) {
                closePanel();
            }
        });

        // Auto-resize du textarea avec limite max-height
        function autoResizeTextarea() {
            input.style.height = 'auto';
            const maxHeight = 100; // Correspond à max-height en CSS (100px)
            const newHeight = Math.min(input.scrollHeight, maxHeight);
            input.style.height = newHeight + 'px';
            input.style.overflowY = input.scrollHeight > maxHeight ? 'auto' : 'hidden';
        }

        input.addEventListener('input', autoResizeTextarea);
        
        // Reset la hauteur au focus si vide
        input.addEventListener('focus', function() {
            if (!this.value.trim()) {
                this.style.height = 'auto';
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const rawMessage = input.value.trim();
            if (!rawMessage) {
                return;
            }

            appendMessage(rawMessage, 'user');
            input.value = '';
            input.style.height = 'auto';
            input.style.overflowY = 'hidden';

            sendMessageToBackend(rawMessage);
        });
    }

    document.addEventListener('DOMContentLoaded', initChatbot);
})();

