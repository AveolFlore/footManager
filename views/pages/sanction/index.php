<?php
$pageTitle = "Gestion des Sanctions";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-teams {
            background-image: url('/assets/images/teams2.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .bg-overlay { background: rgba(255, 255, 255, 0.85); }
    </style>
</head>

<body class="bg-teams min-h-screen">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    <div class="pt-20 bg-overlay min-h-screen">
        <main>
            <div class="p-6 md:p-8 lg:p-10">
                    
                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-8 flex items-center gap-4">
                        <i class="fas fa-gavel text-orange-600 text-4xl"></i>
                        Gestion des Sanctions
                    </h1>

                    <?php if (isset($_GET['msg'])): ?>
                        <?php 
                            $msg = $_GET['msg'];
                            $isSuccess = ($msg === 'sanction_payee');
                            $alertClass = $isSuccess ? 'bg-green-50 text-green-700 border-green-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200';
                            $icon = $isSuccess ? 'fa-check-circle text-green-600' : 'fa-exclamation-triangle text-yellow-600';
                            $messages = ['sanction_payee' => "Sanction marquée comme payée !", 'sanction_introuvable' => "Sanction introuvable.", 'deja_payee' => "Sanction déjà payée.", 'requete_invalide' => "Requête invalide."];
                        ?>
                        <div class="mb-8 p-5 rounded-2xl flex items-center gap-4 shadow-sm border <?= $alertClass ?>">
                            <i class="fas <?= $icon ?> text-3xl"></i>
                            <p class="text-lg font-semibold"><?= $messages[$msg] ?? htmlspecialchars($msg) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50">
                            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                                <i class="fas fa-clock text-yellow-600"></i>
                                Sanctions en attente de paiement
                            </h2>
                        </div>

                        <?php if (empty($sanctions)): ?>
                            <div class="text-center py-16 text-slate-500">
                                <i class="fas fa-inbox text-6xl mb-4 text-slate-300"></i>
                                <p class="text-xl font-medium">Aucune sanction en attente pour le moment.</p>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-slate-50 border-b border-slate-100">
                                        <tr>
                                            <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase">Joueur</th>
                                            <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase">Motif</th>
                                            <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase">Montant</th>
                                            <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase">Date</th>
                                            <th class="text-right px-8 py-5 text-xs font-bold text-slate-400 uppercase">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php foreach ($sanctions as $sanction): ?>
                                            <tr class="hover:bg-slate-50 transition-all">
                                                <td class="px-8 py-6">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-lg shadow-sm">
                                                            <?= strtoupper(substr($sanction['nom'], 0, 1) . substr($sanction['prenom'], 0, 1)) ?>
                                                        </div>
                                                        <p class="font-bold text-slate-800"><?= htmlspecialchars($sanction['nom'] . ' ' . $sanction['prenom']) ?></p>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-6">
                                                    <span class="px-3 py-1 rounded-xl text-xs font-black bg-yellow-50 text-yellow-700 border border-yellow-100"><?= htmlspecialchars($sanction['titre']) ?></span>
                                                    <p class="text-sm text-slate-600 mt-1"><?= htmlspecialchars($sanction['motif']) ?></p>
                                                </td>
                                                <td class="px-8 py-6 font-black text-orange-600"><?= number_format($sanction['montant'], 0, ',', ' ') ?> FCFA</td>
                                                <td class="px-8 py-6 text-sm text-slate-600"><?= date('d/m/Y', strtotime($sanction['date_sanction'])) ?></td>
                                                <td class="px-8 py-6 text-right">
                                                    <form method="POST" action="/sanction-marquerpayee">
                                                        <input type="hidden" name="sanction_id" value="<?= (int)$sanction['id'] ?>">
                                                        <button type="submit" class="px-5 py-3 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-2xl shadow-md transition-all">
                                                            Marquer payée
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                        <?php include __DIR__ . '/../../partials/pagination.php'; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>