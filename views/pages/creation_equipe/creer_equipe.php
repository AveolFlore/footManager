<!-- Importation de la police Inter (style moderne de la capture) -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<style>
    body { font-family: 'Inter', sans-serif; }
</style>

<div class="flex h-screen bg-[#f8fafc]"> <!-- Gris très léger pour le fond -->
    <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">
        <?php include_once __DIR__ . '/../../partials/header.php'; ?>

        <main class="flex-1 p-8 overflow-y-auto">
            <!-- Notification de succès pour création -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
        <div id="success-notification" class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 font-medium rounded shadow-sm animate-bounce">
            ✅ L'équipe a été créée avec succès !
        </div>
    <?php endif; ?>
    
    <!-- Notification de succès pour modification -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
        <div id="success-notification" class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 font-medium rounded shadow-sm animate-bounce">
            ✅ L'équipe a été modifiée avec succès !
        </div>
    <?php endif; ?>
    
    <!-- Script pour faire disparaître la notification après 3 secondes -->
    <script>
        // Récupère l'élément de notification
        const notification = document.getElementById('success-notification');
        
        // Si la notification existe
        if (notification) {
            // Attend 3 secondes (3000 millisecondes)
            setTimeout(() => {
                // Ajoute une transition de fondu
                notification.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                
                // Après la transition, supprime l'élément
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }
    </script>

    <div class="flex justify-between items-center mb-8">
        <!-- <h1 class="text-2xl font-bold text-[#1e293b]">Gestion des Équipes</h1> -->
        <!-- CORRECTION DU LIEN : admin-createequipe -->
        <!-- <a href="/admin-createequipe" class="bg-[#10b981] hover:bg-[#059669] text-white text-sm font-semibold py-2 px-6 rounded-lg transition shadow-md">
            + Ajouter une équipe
        </a> -->
    </div>
            <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                
                <!-- Header : Vert foncé avec texte blanc en gras -->
                <div class="bg-[#064e3b] p-6 text-center">
                    <h2 class="text-white text-xl font-semibold tracking-tight">
                        <?php 
                        // Vérifie si on est en mode modification (si $equipe existe)
                        echo isset($equipe) ? 'Modifier l\'Équipe' : 'Créer l\'Équipe'; 
                        ?>
                    </h2>
                </div>

                <!-- Formulaire : action change selon le mode -->
                <form action="<?= isset($equipe) ? '/admin-teamupdate?id=' . $equipe['id'] : '/admin-storeequipe' ?>" method="POST" class="p-8 space-y-6">
                    
                    <!-- Label : Gris foncé, graisse moyenne (500) -->
                    <div>
                        <label class="block text-sm font-medium text-[#334155] mb-2">Nom de l'équipe</label>
                        <!-- Valeur préremplie si en mode modification -->
                        <input type="text" name="nom" placeholder="Ex: Les Lions du Green" required 
                               value="<?= isset($equipe) ? htmlspecialchars($equipe['nom']) : '' ?>"
                               class="w-full px-4 py-2.5 border border-[#e2e8f0] rounded-lg focus:ring-2 focus:ring-[#10b981] focus:border-transparent outline-none text-[#1e293b] placeholder:text-gray-300 transition">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-[#334155] mb-2">Couleur Distinctive</label>
                            <!-- Valeur préremplie si en mode modification -->
                            <input type="color" name="couleur" 
                                   value="<?= isset($equipe) ? htmlspecialchars($equipe['couleur']) : '#000000' ?>"
                                   class="w-full h-11 p-1 border border-[#e2e8f0] rounded-lg cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#334155] mb-2">Catégorie officielle</label>
                            <select name="categorie" 
                                    class="w-full px-4 py-2.5 border border-[#e2e8f0] rounded-lg focus:ring-2 focus:ring-[#10b981] outline-none text-[#1e293b] bg-white transition">
                                <!-- Options sélectionnées selon la catégorie existante -->
                                <option value="Senior" <?= (isset($equipe) && $equipe['categorie'] === 'Senior') ? 'selected' : '' ?>>Senior</option>
                                <option value="Reserve" <?= (isset($equipe) && $equipe['categorie'] === 'Reserve') ? 'selected' : '' ?>>Reserve</option>
                                <option value="Jeune" <?= (isset($equipe) && $equipe['categorie'] === 'Jeune') ? 'selected' : '' ?>>Jeune</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#334155] mb-2">Date de Fondation officielle</label>
                        <!-- Valeur préremplie si en mode modification, sinon date du jour -->
                        <input type="date" name="date_creation" value="<?= isset($equipe) ? htmlspecialchars($equipe['date_creation']) : date('Y-m-d') ?>" 
                               class="w-full px-4 py-2.5 border border-[#e2e8f0] rounded-lg focus:ring-2 focus:ring-[#10b981] outline-none text-[#1e293b] transition">
                    </div>

                    <!-- Actions : Texte gris pour annuler, bouton vert vif pour enregistrer -->
                    <div class="flex items-center justify-end space-x-6 pt-6 border-t border-[#f1f5f9]">
                        <a href="/admin-team" class="text-sm font-medium text-[#64748b] hover:text-[#1e293b] transition">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="bg-[#10b981] hover:bg-[#059669] text-white text-sm font-semibold py-2.5 px-8 rounded-lg shadow-sm transition-all active:scale-95">
                            <?php 
                            // Change le texte du bouton selon le mode
                            echo isset($equipe) ? 'Modifier l\'équipe' : 'Créer l\'équipe'; 
                            ?>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>