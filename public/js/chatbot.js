/**
 * Chatbot flottant - gestion de l'UI et des appels AJAX
 * URL cible : https://cesi-site.local/chatbot/ask
 */

(function () {
    const CHATBOT_ENDPOINT = 'https://cesi-site.local/chatbot/ask';

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
                    <div class="chatbot-message chatbot-message--bot">
                        Bonjour 👋<br>
                        Je peux vous aider à trouver une offre, comprendre un statut de candidature ou utiliser la plateforme.
                        <div class="chatbot-message-meta">Bot · maintenant</div>
                    </div>
                </div>
                <div class="chatbot-footer">
                    <form class="chatbot-form" id="chatbotForm">
                        <textarea
                            class="chatbot-input"
                            id="chatbotInput"
                            rows="1"
                            placeholder="Posez votre question sur les stages, les offres, la plateforme..."
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
                <i class="fa-solid fa-comments"></i>
            </button>
        `;

        document.body.appendChild(wrapper);
    }

    /**
     * Ajoute un message dans le flux
     */
    function appendMessage(text, from = 'bot') {
        const messagesEl = document.getElementById('chatbotMessages');
        if (!messagesEl) return;

        const msg = document.createElement('div');
        msg.className = 'chatbot-message ' + (from === 'user' ? 'chatbot-message--user' : 'chatbot-message--bot');
        msg.innerHTML = `${escapeHtml(text)}<div class="chatbot-message-meta">${from === 'user' ? 'Vous' : 'Bot'} · ${formatTime(
            new Date()
        )}</div>`;

        messagesEl.appendChild(msg);
        messagesEl.scrollTop = messagesEl.scrollHeight;
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

            appendMessage(payload.answer, 'bot');
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

        function openPanel() {
            panel.classList.add('is-open');
            panel.setAttribute('aria-hidden', 'false');
            toggleBtn.style.opacity = '0';
            toggleBtn.style.pointerEvents = 'none';
            input.focus();
        }

        function closePanel() {
            panel.classList.remove('is-open');
            panel.setAttribute('aria-hidden', 'true');
            toggleBtn.style.opacity = '1';
            toggleBtn.style.pointerEvents = 'auto';
        }

        toggleBtn.addEventListener('click', openPanel);
        if (closeBtn) {
            closeBtn.addEventListener('click', closePanel);
        }

        // Fermer avec Echap
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && panel.classList.contains('is-open')) {
                closePanel();
            }
        });

        // Auto-resize du textarea
        input.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
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

            sendMessageToBackend(rawMessage);
        });
    }

    document.addEventListener('DOMContentLoaded', initChatbot);
})();

