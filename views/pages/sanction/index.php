<?php
$pageTitle = "Gestion des Sanctions";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 font-sans">
    
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <div class="p-6 md:p-8 lg:p-10">
                
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-8 flex items-center gap-4">
                    <i class="fas fa-gavel text-orange-600 text-4xl"></i>
                    Gestion des Sanctions
                </h1>

                <?php if (isset($_GET['msg'])): ?>
                    <?php 
                        $msg = $_GET['msg'];
                        $isSuccess = ($msg === 'sanction_payee');
                        $alertClass = $isSuccess 
                            ? 'from-green-50 to-emerald-50 text-green-700 border-green-200' 
                            : 'from-yellow-50 to-orange-50 text-yellow-700 border-yellow-200';
                        $icon = $isSuccess ? 'fa-check-circle text-green-600' : 'fa-exclamation-triangle text-yellow-600';
                        
                        $messages = [
                            'sanction_payee' => "Sanction marquée comme payée !",
                            'sanction_introuvable' => "Sanction introuvable.",
                            'deja_payee' => "Sanction déjà payée.",
                            'requete_invalide' => "Requête invalide."
                        ];
                    ?>
                    <div class="mb-8 p-5 rounded-2xl flex items-center gap-4 shadow-md bg-gradient-to-r border <?= $alertClass ?>">
                        <i class="fas <?= $icon ?> text-3xl"></i>
                        <p class="text-lg font-semibold"><?= $messages[$msg] ?? htmlspecialchars($msg) ?></p>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-white to-slate-50">
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
                                <thead class="bg-slate-50 border-b-2 border-slate-100">
                                    <tr>
                                        <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Joueur</th>
                                        <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Motif</th>
                                        <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Montant</th>
                                        <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Date</th>
                                        <th class="text-right px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($sanctions as $sanction): ?>
                                        <tr class="hover:bg-slate-50 transition-all duration-200">
                                            <td class="px-8 py-6 whitespace-nowrap">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-500 to-yellow-600 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-orange-200">
                                                        <?= strtoupper(substr($sanction['nom'], 0, 1) . substr($sanction['prenom'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-800 text-lg">
                                                            <?= htmlspecialchars($sanction['nom'] . ' ' . $sanction['prenom']) ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td class="px-8 py-6">
                                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl text-xs font-black bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-800 mb-1 border border-yellow-200">
                                                    <?= htmlspecialchars($sanction['titre']) ?>
                                                </span>
                                                <p class="text-sm text-slate-600 leading-relaxed"><?= htmlspecialchars($sanction['motif']) ?></p>
                                            </td>
                                            
                                            <td class="px-8 py-6 whitespace-nowrap text-2xl font-black text-orange-600">
                                                <?= number_format($sanction['montant'], 0, ',', ' ') ?>
                                                <span class="text-xs text-slate-500 font-bold uppercase">FCFA</span>
                                            </td>
                                            
                                            <td class="px-8 py-6 whitespace-nowrap text-sm text-slate-600 font-semibold">
                                                <?= date('d/m/Y', strtotime($sanction['date_sanction'])) ?>
                                            </td>
                                            
                                            <td class="px-8 py-6 text-right whitespace-nowrap">
                                                <form method="POST" action="/sanction-marquerpayee" class="inline">
                                                    <input type="hidden" name="sanction_id" value="<?= (int)$sanction['id'] ?>">
                                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-bold rounded-2xl transition-all shadow-md shadow-green-200 active:scale-95">
                                                        <i class="fas fa-check-circle text-base"></i>
                                                        Marquer comme payée
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

</body>
</html>