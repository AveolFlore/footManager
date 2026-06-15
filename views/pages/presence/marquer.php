<script>
    // --- Gestion du Dark Mode ---
    // Fonction pour appliquer le thème global
    function appliquerTheme() {
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        // Rafraîchir les labels de présence pour appliquer les bonnes classes inactives
        document.querySelectorAll('.presence-radio').forEach(radio => {
            if (!radio.checked) changerType(radio);
        });
    }

    // Appeler au chargement de la page
    appliquerTheme();

    // --- Logique d'interaction des Présences ---
    function marquerTous(type) {
        document.querySelectorAll(`input[value="${type}"]`).forEach(radio => {
            radio.checked = true;
            changerType(radio);
        });
    }

    function changerType(radio) {
        const container = radio.closest('.flex.flex-wrap');
        const labels = container.querySelectorAll('label');
        const isDark = document.documentElement.classList.contains('dark');

        labels.forEach(label => {
            const input = label.querySelector('input');
            const type = label.getAttribute('data-type');
            
            // Récupération des classes de base définies dans le PHP
            const activeClasses = label.getAttribute('data-active').split(' ');
            let inactiveClasses = label.getAttribute('data-inactive').split(' ');

            // Surcharge dynamique des classes inactives si on est en Dark Mode
            if (isDark) {
                const darkInactiveThemes = {
                    'present': ['bg-green-950/40', 'text-green-400', 'border', 'border-green-800/50', 'hover:bg-green-900/40'],
                    'retard':  ['bg-orange-950/40', 'text-orange-400', 'border', 'border-orange-800/50', 'hover:bg-orange-900/40'],
                    'absent':  ['bg-red-950/40', 'text-red-400', 'border', 'border-red-800/50', 'hover:bg-red-900/40'],
                    'excuse':  ['bg-blue-950/40', 'text-blue-400', 'border', 'border-blue-800/50', 'hover:bg-blue-900/40']
                };
                inactiveClasses = darkInactiveThemes[type];
            }

            // Nettoyage de tous les états possibles pour éviter les conflits
            const allPossibleClasses = [
                ...activeClasses, 
                ...label.getAttribute('data-inactive').split(' '),
                'bg-green-950/40', 'text-green-400', 'border-green-800/50', 'hover:bg-green-900/40',
                'bg-orange-950/40', 'text-orange-400', 'border-orange-800/50', 'hover:bg-orange-900/40',
                'bg-red-950/40', 'text-red-400', 'border-red-800/50', 'hover:bg-red-900/40',
                'bg-blue-950/40', 'text-blue-400', 'border-blue-800/50', 'hover:bg-blue-900/40',
                'border'
            ];
            label.classList.remove(...allPossibleClasses);

            // Application du bon état
            if (input.checked) {
                label.classList.add(...activeClasses);
            } else {
                label.classList.add(...inactiveClasses);
            }
        });
    }
</script>