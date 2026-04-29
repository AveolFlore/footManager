<?php
require_once __DIR__ . '/../../../middleware/Role.php';
// Initialisation sécurisée
if (!isset($regles_actives)) $regles_actives = [];
if (!isset($regles_en_reflexion)) $regles_en_reflexion = [];
if (!isset($count_actives)) $count_actives = 0;
if (!isset($count_reflexion)) $count_reflexion = 0;
requireLogin();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Règlement</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>
        
        <main class="flex-1 p-4 md:p-6">
            <div class="max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Règlement</h1>
                        <p class="text-sm text-gray-500">Règles du club et votes</p>
                    </div>
                    <button onclick="toggleForm()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition shadow-sm font-medium">
                        <span class="text-xl">+</span> Proposer une règle
                    </button>
                </div>

               <div id="form-proposition" class="hidden mb-8 p-6 bg-white border border-emerald-500 rounded-2xl shadow-sm transition-all">
    <h2 class="text-lg font-bold text-slate-800 mb-4">Proposer une Nouvelle Règle</h2>
    
    <form action="/reglement-storerule" method="POST">
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Titre de la règle</label>
            <input 
                type="text" 
                name="titre" 
                required 
                class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-gray-50/50" 
                placeholder="Ex: Retard aux entraînements"
            >
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Type d'infraction</label>
                <select 
                    name="type_infraction" 
                    required 
                    class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-gray-50/50"
                >
                    <option value="autre">Autre</option>
                    <option value="retard">Retard</option>
                    <option value="absence">Absence</option>
                    <option value="comportement">Comportement</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Montant de l'amende (FCFA)</label>
                <input 
                    type="number" 
                    name="montant_amende" 
                    defaultValue="0" 
                    min="0" 
                    class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-gray-50/50" 
                    placeholder="Ex: 2000"
                >
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Texte de la règle proposée</label>
            <textarea 
                name="description" 
                required 
                rows="4" 
                class="w-full p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-gray-50/50" 
                placeholder="Détaillez la règle ici..."
            ></textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit" name="submit_proposition" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 transition">
                <span>🚀</span> Soumettre
            </button>
            <button type="button" onclick="toggleForm()" class="bg-white border border-gray-200 text-gray-600 px-6 py-2.5 rounded-xl font-bold hover:bg-gray-50 transition">
                Annuler
            </button>
        </div>
    </form>
</div>
                    <!--  -->

                <div class="flex border-b border-gray-200 mb-6 bg-white rounded-t-xl overflow-hidden shadow-sm">
                    <button onclick="switchTab('vigueur')" id="tab-vigueur" class="flex-1 py-4 text-center font-bold border-b-4 border-emerald-600 text-emerald-600 bg-emerald-50/50 transition">
                        En vigueur <span class="ml-2 bg-emerald-600 text-white text-xs px-2 py-0.5 rounded-full"><?= $count_actives ?></span>
                    </button>
                    <button onclick="switchTab('reflexion')" id="tab-reflexion" class="flex-1 py-4 text-center font-bold text-gray-500 hover:text-emerald-600 hover:bg-gray-50 border-b-4 border-transparent transition">
                        En réflexion <span class="ml-2 bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full"><?= $count_reflexion ?></span>
                    </button>
                </div>

                <div id="content-vigueur" class="space-y-4">
                    <?php foreach ($regles_actives as $r): ?>
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative group">
                            <span class="absolute top-6 right-6 bg-emerald-100 text-emerald-700 text-xs px-3 py-1 rounded-full font-bold">Adoptée</span>
                            <h3 class="text-lg font-bold text-slate-800 mb-2"><?= htmlspecialchars($r['titre'] ?? 'Règle') ?></h3>
                            <p class="text-gray-600 text-sm leading-relaxed"><?= htmlspecialchars($r['description']) ?></p>
                            <p class="text-xs text-gray-400 mt-4 italic">Proposée le <?= date('d/m/Y', strtotime($r['date_creation'] ?? 'now')) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="content-reflexion" class="space-y-4 hidden">
                    <?php foreach ($regles_en_reflexion as $r): 
                        $total = ($r['total_pour'] ?? 0) + ($r['total_contre'] ?? 0);
                        $pour_perc = ($total > 0) ? round(($r['total_pour'] / $total) * 100) : 0;
                    ?>
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <h3 class="text-lg font-bold text-slate-800 mb-2"><?= htmlspecialchars($r['titre'] ?? 'Proposition') ?></h3>
                            <p class="text-gray-600 text-sm mb-6"><?= htmlspecialchars($r['description']) ?></p>
                            </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('form-proposition');
            form.classList.toggle('hidden');
            if(!form.classList.contains('hidden')) {
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function switchTab(type) {
            const btnV = document.getElementById('tab-vigueur');
            const btnR = document.getElementById('tab-reflexion');
            const contV = document.getElementById('content-vigueur');
            const contR = document.getElementById('content-reflexion');

            if (type === 'vigueur') {
                contV.classList.remove('hidden');
                contR.classList.add('hidden');
                btnV.className = "flex-1 py-4 text-center font-bold border-b-4 border-emerald-600 text-emerald-600 bg-emerald-50/50 transition";
                btnR.className = "flex-1 py-4 text-center font-bold text-gray-500 hover:text-emerald-600 hover:bg-gray-50 border-b-4 border-transparent transition";
            } else {
                contV.classList.add('hidden');
                contR.classList.remove('hidden');
                btnR.className = "flex-1 py-4 text-center font-bold border-b-4 border-emerald-600 text-emerald-600 bg-emerald-50/50 transition";
                btnV.className = "flex-1 py-4 text-center font-bold text-gray-500 hover:text-emerald-600 hover:bg-gray-50 border-b-4 border-transparent transition";
            }
        }
    </script>
</body>
</html>