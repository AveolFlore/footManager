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
        body {
            /* Linear gradient sombre + image teams2.jpg */
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('/assets/images/teams2.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="min-h-screen text-white">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    <div class="pt-20 min-h-screen">
        <main class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto">
                
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-8 flex items-center gap-4">
                <i class="fas fa-gavel text-orange-500 text-4xl"></i>
                Gestion des Sanctions
            </h1>

            <?php if (isset($_GET['msg'])): ?>
                <?php 
                    $msg = $_GET['msg'];
                    $isSuccess = ($msg === 'sanction_payee');
                    $alertClass = $isSuccess ? 'bg-emerald-900/50 border-emerald-500' : 'bg-amber-900/50 border-amber-500';
                    $messages = ['sanction_payee' => "Sanction marquée comme payée !", 'sanction_introuvable' => "Sanction introuvable.", 'deja_payee' => "Sanction déjà payée.", 'requete_invalide' => "Requête invalide."];
                ?>
                <div class="mb-8 p-5 rounded-2xl glass-panel border flex items-center gap-4 shadow-lg <?= $alertClass ?>">
                    <i class="fas <?= $isSuccess ? 'fa-check-circle text-emerald-400' : 'fa-exclamation-triangle text-amber-400' ?> text-3xl"></i>
                    <p class="text-lg font-semibold"><?= $messages[$msg] ?? htmlspecialchars($msg) ?></p>
                </div>
            <?php endif; ?>

            <div class="glass-panel rounded-3xl overflow-hidden border border-white/10">
                <div class="px-8 py-6 border-b border-white/10 bg-black/20">
                    <h2 class="text-2xl font-extrabold flex items-center gap-3">
                        <i class="fas fa-clock text-yellow-500"></i>
                        Sanctions en attente de paiement
                    </h2>
                </div>

                <?php if (empty($sanctions)): ?>
                    <div class="text-center py-16 text-slate-300">
                        <i class="fas fa-inbox text-6xl mb-4 opacity-50"></i>
                        <p class="text-xl font-medium">Aucune sanction en attente pour le moment.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-black/20 border-b border-white/10">
                                <tr>
                                    <th class="text-left px-8 py-5 text-xs font-bold text-slate-300 uppercase">Joueur</th>
                                    <th class="text-left px-8 py-5 text-xs font-bold text-slate-300 uppercase">Motif</th>
                                    <th class="text-left px-8 py-5 text-xs font-bold text-slate-300 uppercase">Montant</th>
                                    <th class="text-left px-8 py-5 text-xs font-bold text-slate-300 uppercase">Date</th>
                                    <th class="text-right px-8 py-5 text-xs font-bold text-slate-300 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php foreach ($sanctions as $sanction): ?>
                                    <tr class="hover:bg-white/5 transition-all">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-2xl bg-orange-600/20 text-orange-400 flex items-center justify-center font-black text-lg">
                                                    <?= strtoupper(substr($sanction['nom'], 0, 1) . substr($sanction['prenom'], 0, 1)) ?>
                                                </div>
                                                <p class="font-bold"><?= htmlspecialchars($sanction['nom'] . ' ' . $sanction['prenom']) ?></p>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="px-3 py-1 rounded-xl text-xs font-black bg-yellow-900/50 text-yellow-300 border border-yellow-700/50"><?= htmlspecialchars($sanction['titre']) ?></span>
                                            <p class="text-sm text-slate-300 mt-1"><?= htmlspecialchars($sanction['motif']) ?></p>
                                        </td>
                                        <td class="px-8 py-6 font-black text-orange-400"><?= number_format($sanction['montant'], 0, ',', ' ') ?> FCFA</td>
                                        <td class="px-8 py-6 text-sm text-slate-300"><?= date('d/m/Y', strtotime($sanction['date_sanction'])) ?></td>
                                        <td class="px-8 py-6 text-right">
                                            <form method="POST" action="/sanction-marquerpayee">
                                                <input type="hidden" name="sanction_id" value="<?= (int)$sanction['id'] ?>">
                                                <button type="submit" class="px-5 py-3 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-2xl transition-all">
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
        </main>
    </div>
</body>
</html>