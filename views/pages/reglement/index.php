<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/reglement/Reglement.php';
require_once __DIR__ . '/../../../models/vote/Vote.php';
require_once __DIR__ . '/../../../middleware/Role.php';

use Config\Database;
use Models\Reglement\Reglement;
use Models\Vote\Vote;

requireLogin();

$db = (new Database())->connect();
$reglementModel = new Reglement($db);
$voteModel = new Vote($db);

// Vérifier et mettre à jour les règlements expirés avant d'afficher la page
$reglementModel->checkAndUpdateExpiredVotes();

$reglements = $reglementModel->readAll();
$user_id = $_SESSION['user']['id'];

$pageTitle = "Règlements du Club";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link class="rounded-full" rel="icon" type="image/png" href="/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 font-sans">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php include __DIR__ . '/../../partials/header.php'; ?>
            <div class="p-6 md:p-8 lg:p-10">
                
                <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800">Règlement Intérieur</h1>
                    </div>
                    <button onclick="toggleModal('modal-propose', true)" class="flex items-center gap-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-green-200">
                        <i class="fas fa-plus"></i>Proposer un règlement
                    </button>
                </header>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 font-semibold">
                        <?php
                        $msg = $_GET['msg'];
                        $isSuccess = in_array($msg, ['proposition_envoyee', 'vote_enregistre']);
                        $alertClass = $isSuccess 
                            ? 'from-green-50 to-emerald-50 text-green-700 border-green-200' 
                            : 'from-yellow-50 to-orange-50 text-yellow-700 border-yellow-200';
                        $icon = $isSuccess ? 'fa-check-circle text-green-600' : 'fa-info-circle text-yellow-600';
                        
                        $messages = [
                            'proposition_envoyee' => 'Votre proposition de règlement a été envoyée !',
                            'vote_enregistre' => 'Votre vote a bien été enregistré !',
                            'deja_vote' => 'Vous avez déjà voté pour ce règlement.',
                            'delai_depasse' => 'Le délai de vote pour ce règlement est dépassé.',
                            'reglement_introuvable' => 'Ce règlement n\'existe pas.',
                        ];
                        $textToShow = $messages[$msg] ?? htmlspecialchars($msg);
                        ?>
                        <div class="bg-gradient-to-r <?= $alertClass ?> border rounded-2xl p-4 flex items-center gap-3">
                            <i class="fas <?= $icon ?> text-2xl"></i>
                            <span><?= $textToShow ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($reglements as $r): 
                        $hasVoted = $voteModel->hasVoted($r['id'], $user_id);
                        $voteResults = $voteModel->getResults($r['id']);
                        
                        $totalVotes = 0; $ouiVotes = 0; $nonVotes = 0;
                        foreach ($voteResults as $vr) {
                            $totalVotes += $vr['total'];
                            if ($vr['choix'] === 'oui') $ouiVotes = $vr['total'];
                            if ($vr['choix'] === 'non') $nonVotes = $vr['total'];
                        }
                        $ouiPercent = $totalVotes > 0 ? round(($ouiVotes / $totalVotes) * 100) : 0;
                        $nonPercent = $totalVotes > 0 ? round(($nonVotes / $totalVotes) * 100) : 0;

                        $tempsRestant = null; $delaiDepasse = false;

                        if ($r['statut'] === 'reflexion') {
                            $dateDebut = new DateTime($r['date_debut_vote']);
                            $dateFin = (clone $dateDebut)->modify('+ ' . (int)$r['duree_vote_heures'] . ' hours');
                            $maintenant = new DateTime();
                            
                            if ($maintenant > $dateFin) {
                                $delaiDepasse = true;
                            } else {
                                $tempsRestant = $maintenant->diff($dateFin);
                            }
                        }

                        switch ($r['statut']) {
                            case 'actif':
                                $badgeClass = 'from-green-100 to-emerald-100 text-green-700 border-green-200'; $badgeLabel = 'En vigueur'; break;
                            case 'rejete':
                                $badgeClass = 'from-red-100 to-pink-100 text-red-700 border-red-200'; $badgeLabel = 'Rejeté'; break;
                            default:
                                $badgeClass = 'from-yellow-100 to-orange-100 text-yellow-700 border-yellow-200'; $badgeLabel = 'En vote';
                        }
                    ?>
                        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden flex flex-col justify-between">
                            <div class="p-8 pb-4">
                                <div class="flex justify-between items-start mb-6 gap-2">
                                    <div>
                                        <h2 class="text-2xl font-extrabold text-slate-800 mb-3"><?= htmlspecialchars($r['titre']) ?></h2>
                                        <span class="inline-block text-sm px-4 py-2 rounded-2xl font-bold border bg-gradient-to-r <?= $badgeClass ?>">
                                            <i class="fas fa-tag mr-2"></i><?= $badgeLabel ?>
                                        </span>
                                    </div>
                                    <p class="text-3xl font-extrabold text-orange-600 whitespace-nowrap">
                                        <?= number_format($r['montant_amende'], 0, ',', ' ') ?>
                                        <span class="text-sm text-slate-500 font-semibold">FCFA</span>
                                    </p>
                                </div>
                                <p class="text-slate-600 mb-6 text-lg leading-relaxed"><?= htmlspecialchars($r['description']) ?></p>
                            </div>

                            <div class="p-8 pt-0">
                                <?php if ($r['statut'] === 'reflexion'): ?>
                                    <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200">
                                        <div class="flex items-center gap-3 text-blue-700 font-semibold">
                                            <i class="fas fa-clock text-2xl"></i>
                                            <span>
                                                <?= $delaiDepasse ? "Délai de vote dépassé, traitement..." : "Temps restant : " . ($tempsRestant->days > 0 ? $tempsRestant->days . 'j ' : '') . $tempsRestant->h . 'h ' . $tempsRestant->i . 'm' ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($r['statut'] === 'reflexion' && !$delaiDepasse): ?>
                                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                                        <?php if ($hasVoted): ?>
                                            <div class="text-center py-2">
                                                <i class="fas fa-check-circle text-green-600 text-4xl mb-3"></i>
                                                <p class="text-sm font-semibold text-slate-700">Votre vote : 
                                                    <span class="font-extrabold text-xl <?= $hasVoted['choix'] === 'oui' ? 'text-green-700' : 'text-red-700' ?>">
                                                        <?= strtoupper($hasVoted['choix']) ?>
                                                    </span>
                                                </p>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-sm font-semibold text-slate-700 mb-4 text-center">Exprimez votre choix :</p>
                                            <div class="flex gap-4">
                                                <form action="/reglement-voter" method="POST" class="flex-1">
                                                    <input type="hidden" name="reglement_id" value="<?= (int)$r['id'] ?>">
                                                    <input type="hidden" name="choix" value="oui">
                                                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-4 rounded-2xl font-extrabold transition-all shadow-lg shadow-green-200 text-lg">
                                                        <i class="fas fa-thumbs-up mr-2"></i> OUI
                                                    </button>
                                                </form>
                                                <form action="/reglement-voter" method="POST" class="flex-1">
                                                    <input type="hidden" name="reglement_id" value="<?= (int)$r['id'] ?>">
                                                    <input type="hidden" name="choix" value="non">
                                                    <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white py-4 rounded-2xl font-extrabold transition-all shadow-lg shadow-red-200 text-lg">
                                                        <i class="fas fa-thumbs-down mr-2"></i> NON
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($totalVotes > 0): ?>
                                            <div class="mt-6 border-t border-slate-200 pt-4">
                                                <p class="text-xs text-slate-500 mb-2 text-center font-bold uppercase tracking-wider">Tendance (<?= $totalVotes ?> vote<?= $totalVotes > 1 ? 's' : '' ?>)</p>
                                                <div class="flex h-3 rounded-full overflow-hidden bg-slate-200">
                                                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 transition-all duration-700" style="width: <?= $ouiPercent ?>%"></div>
                                                    <div class="bg-gradient-to-r from-red-500 to-pink-600 transition-all duration-700" style="width: <?= $nonPercent ?>%"></div>
                                                </div>
                                                <div class="flex justify-between text-xs font-bold mt-2">
                                                    <span class="text-green-700">OUI (<?= $ouiPercent ?>%)</span>
                                                    <span class="text-red-700">NON (<?= $nonPercent ?>%)</span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif (in_array($r['statut'], ['actif', 'rejete'])): ?>
                                    <div class="p-6 rounded-2xl border border-slate-100 <?= $r['statut'] === 'actif' ? 'bg-gradient-to-r from-green-50 to-emerald-50 border-green-100' : 'bg-gradient-to-r from-red-50 to-pink-50 border-red-100' ?>">
                                        <h4 class="text-xs font-extrabold mb-3 text-slate-700 uppercase tracking-wide">Scrutin Clôturé :</h4>
                                        <div class="flex h-4 rounded-full overflow-hidden bg-slate-200 mb-3">
                                            <div class="bg-gradient-to-r from-green-500 to-emerald-600" style="width: <?= $ouiPercent ?>%"></div>
                                            <div class="bg-gradient-to-r from-red-500 to-pink-600" style="width: <?= $nonPercent ?>%"></div>
                                        </div>
                                        <div class="flex justify-between text-base font-black">
                                            <span class="text-green-700">OUI : <?= $ouiVotes ?> (<?= $ouiPercent ?>%)</span>
                                            <span class="text-red-700">NON : <?= $nonVotes ?> (<?= $nonPercent ?>%)</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <div id="modal-propose" onclick="handleOutsideClick(event)" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex justify-center items-center hidden p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden transform transition-all">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6 flex justify-between items-center">
                <h2 class="text-2xl font-extrabold text-white flex items-center gap-3">
                    <i class="fas fa-plus-circle"></i>Proposer un nouveau règlement
                </h2>
                <button onclick="toggleModal('modal-propose', false)" class="text-white/80 hover:text-white text-2xl"><i class="fas fa-times"></i></button>
            </div>
            <form action="/reglement-proposer" method="POST" class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Titre du règlement</label>
                    <input type="text" name="titre" required class="w-full border-2 border-slate-200 rounded-2xl px-4 py-3.5 text-base focus:outline-none focus:border-green-500 transition-all bg-slate-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                    <textarea name="description" rows="4" required class="w-full border-2 border-slate-200 rounded-2xl px-4 py-3.5 text-base focus:outline-none focus:border-green-500 transition-all bg-slate-50 focus:bg-white"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Montant amende (FCFA)</label>
                        <input type="number" name="montant_amende" required min="0" class="w-full border-2 border-slate-200 rounded-2xl px-4 py-3.5 text-base focus:outline-none focus:border-green-500 transition-all bg-slate-50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Type d'infraction</label>
                        <select name="type_infraction" class="w-full border-2 border-slate-200 rounded-2xl px-4 py-3.5 text-base focus:outline-none focus:border-green-500 transition-all bg-slate-50 focus:bg-white">
                            <option value="retard">Retard</option>
                            <option value="absence">Absence</option>
                            <option value="comportement">Comportement</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Durée d'ouverture du scrutin (heures)</label>
                    <input type="number" name="duree_vote_heures" required min="1" value="24" class="w-full border-2 border-slate-200 rounded-2xl px-4 py-3.5 text-base focus:outline-none focus:border-green-500 transition-all bg-slate-50 focus:bg-white">
                </div>
                <div class="flex justify-end gap-4 pt-4 border-t border-slate-100">
                    <button type="button" onclick="toggleModal('modal-propose', false)" class="px-6 py-3 text-slate-700 hover:bg-slate-100 font-bold transition-all rounded-2xl border border-slate-200">
                        Annuler
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-8 py-3 rounded-2xl font-extrabold shadow-lg shadow-green-200 transition-all">
                        Soumettre la proposition
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id, show) {
            const modal = document.getElementById(id);
            if (show) {
                modal.classList.remove('hidden');
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
</body>
</html>