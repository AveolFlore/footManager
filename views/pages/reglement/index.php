<script>
    // --- Gestion du Dark Mode ---
    function appliquerThemeGlobal() {
        const isDark = localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
        
        if (isDark) {
            document.documentElement.classList.add('dark');
            surchargerStylesDark(true);
        } else {
            document.documentElement.classList.remove('dark');
            surchargerStylesDark(false);
        }
    }

    function surchargerStylesDark(active) {
        // Sélection de tous les éléments principaux à adapter
        const body = document.body;
        const cards = document.querySelectorAll('.bg-white');
        const alertBoxes = document.querySelectorAll('.bg-gradient-to-r');
        const inputs = document.querySelectorAll('input, textarea, select');
        const textMuted = document.querySelectorAll('.text-slate-600, .text-slate-700');

        if (active) {
            // Fond global de l'application
            body.classList.remove('from-slate-50', 'to-slate-100');
            body.classList.add('from-slate-900', 'to-slate-950', 'text-slate-100');

            // Transformation des cartes et du modal en Glassmorphism sombre
            cards.forEach(card => {
                card.classList.remove('bg-white', 'border-slate-100');
                card.classList.add('bg-slate-900/60', 'backdrop-blur-md', 'border-slate-800/80', 'shadow-black/40');
            });

            // Ajustement des champs de formulaire du modal
            inputs.forEach(input => {
                input.classList.remove('bg-slate-50', 'border-slate-200', 'text-slate-800');
                input.classList.add('bg-slate-950/50', 'border-slate-800', 'text-slate-200', 'focus:border-green-500');
            });

            // Lisibilité des textes secondaires
            textMuted.forEach(txt => {
                if (txt.classList.contains('text-slate-600')) {
                    txt.classList.remove('text-slate-600');
                    txt.classList.add('text-slate-400');
                }
                if (txt.classList.contains('text-slate-700')) {
                    txt.classList.remove('text-slate-700');
                    txt.classList.add('text-slate-300');
                }
            });

            // Adaptation des blocs de tendance neutres / alertes
            document.querySelectorAll('.bg-slate-50').forEach(b => {
                b.classList.remove('bg-slate-50', 'border-slate-100');
                b.classList.add('bg-slate-950/40', 'border-slate-800/60');
            });
            document.querySelectorAll('.bg-slate-200').forEach(bar => {
                bar.classList.remove('bg-slate-200');
                bar.classList.add('bg-slate-800');
            });

        } else {
            // Rétablissement des styles clairs d'origine si le mode change
            body.classList.add('from-slate-50', 'to-slate-100');
            body.classList.remove('from-slate-900', 'to-slate-950', 'text-slate-100');

            cards.forEach(card => {
                card.classList.add('bg-white', 'border-slate-100');
                card.classList.remove('bg-slate-900/60', 'backdrop-blur-md', 'border-slate-800/80', 'shadow-black/40');
            });

            inputs.forEach(input => {
                input.classList.add('bg-slate-50', 'border-slate-200');
                input.classList.remove('bg-slate-950/50', 'border-slate-800', 'text-slate-200');
            });
        }
    }

    // Écouteur de changement d'état du système en temps réel
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', appliquerThemeGlobal);
    
    // Initialisation immédiate du thème au chargement
    appliquerThemeGlobal();


    // --- Logique d'interaction du Modal ---
    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) {
            modal.classList.remove('hidden');
            // Animation fluide d'apparition du conteneur interne
            setTimeout(() => {
                modal.firstElementChild.classList.remove('scale-95', 'opacity-0');
            }, 10);
        } else {
            modal.classList.add('hidden');
        }
    }

    function handleOutsideClick(e) {
        if (e.target.id === 'modal-propose') {
            toggleModal('modal-propose', false);
        }
    }
</script>