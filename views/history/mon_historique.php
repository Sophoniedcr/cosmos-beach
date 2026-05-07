<?php
// ============================================================
// COSMOS BEACH — views/history/mon_historique.php
// Page d'historique adaptée selon le rôle de l'utilisateur :
//   VISITEUR   → Ses réservations + paiements
//   AGENT      → Ses actions + réservations qu'il a traitées
//   CAISSIER   → Ses encaissements + historique connexions
//   DIRECTEUR  → Historique global équipe + stats
//   SUPER_ADMIN → Tout (audit complet)
// ============================================================
require 'views/layout/header.php';

$role = $_SESSION['user_role'] ?? 'VISITEUR';

// Couleurs et labels par rôle
$role_config = [
    'VISITEUR'    => ['color' => 'from-sky-500 to-indigo-600',   'label' => 'Mon Historique',             'icon' => 'fa-clock-rotate-left'],
    'AGENT'       => ['color' => 'from-blue-600 to-cyan-700',    'label' => 'Historique Agent',            'icon' => 'fa-bell-concierge'],
    'CAISSIER'    => ['color' => 'from-green-600 to-teal-700',   'label' => 'Historique Caisse',           'icon' => 'fa-cash-register'],
    'DIRECTEUR'   => ['color' => 'from-purple-700 to-indigo-800','label' => 'Historique Équipe & Activités','icon' => 'fa-chart-line'],
    'SUPER_ADMIN' => ['color' => 'from-rose-700 to-red-800',     'label' => 'Historique Complet (Admin)',  'icon' => 'fa-shield-halved'],
];
$cfg = $role_config[$role] ?? $role_config['VISITEUR'];
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

  <!-- Header -->
  <div class="bg-gradient-to-r <?= $cfg['color'] ?> rounded-2xl px-6 py-8 mb-6 text-white">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold flex items-center gap-3">
          <i class="fa-solid <?= $cfg['icon'] ?>"></i>
          <?= $cfg['label'] ?>
        </h1>
        <p class="mt-1 opacity-80 text-sm">
          <?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?>
          &nbsp;·&nbsp; <?= date('d/m/Y H:i') ?>
        </p>
      </div>
      <a href="<?= BASE_URL ?>/?action=dashboard"
         class="flex items-center gap-2 bg-white/20 hover:bg-white/30 transition px-4 py-2 rounded-lg text-sm font-medium">
        <i class="fa-solid fa-arrow-left"></i> Retour
      </a>
    </div>
  </div>

  <!-- Onglets selon le rôle -->
  <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

    <!-- Navigation onglets -->
    <div class="border-b border-gray-200 bg-gray-50 flex gap-1 p-2 overflow-x-auto" id="hist-tabs">

      <?php if (in_array($role, ['VISITEUR','AGENT','CAISSIER','DIRECTEUR','SUPER_ADMIN'])): ?>
      <button onclick="showTab('tab-reservations')"
              class="tab-btn active px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 whitespace-nowrap"
              id="btn-reservations">
        <i class="fa-solid fa-calendar-check"></i>
        <?= $role === 'VISITEUR' ? 'Mes Réservations' : 'Réservations' ?>
      </button>
      <?php endif; ?>

      <?php if (in_array($role, ['CAISSIER','DIRECTEUR','SUPER_ADMIN'])): ?>
      <button onclick="showTab('tab-paiements')"
              class="tab-btn px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 whitespace-nowrap"
              id="btn-paiements">
        <i class="fa-solid fa-money-bill-wave"></i>
        <?= $role === 'CAISSIER' ? 'Mes Encaissements' : 'Paiements' ?>
      </button>
      <?php endif; ?>

      <?php if (in_array($role, ['DIRECTEUR','SUPER_ADMIN'])): ?>
      <button onclick="showTab('tab-activites')"
              class="tab-btn px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 whitespace-nowrap"
              id="btn-activites">
        <i class="fa-solid fa-water-ladder"></i> Activités
      </button>
      <?php endif; ?>

      <?php if (in_array($role, ['DIRECTEUR','SUPER_ADMIN'])): ?>
      <button onclick="showTab('tab-connexions')"
              class="tab-btn px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 whitespace-nowrap"
              id="btn-connexions">
        <i class="fa-solid fa-right-to-bracket"></i> Connexions
      </button>
      <?php endif; ?>

      <?php if ($role === 'SUPER_ADMIN'): ?>
      <button onclick="showTab('tab-audit')"
              class="tab-btn px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 whitespace-nowrap"
              id="btn-audit">
        <i class="fa-solid fa-shield-halved"></i> Journal d'Audit
      </button>
      <?php endif; ?>

    </div>

    <div class="p-6">

      <!-- ════════════════════════════════════════════
           ONGLET 1 — RÉSERVATIONS
      ════════════════════════════════════════════ -->
      <div id="tab-reservations" class="tab-content">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-gray-900">
            <i class="fa-solid fa-calendar-check text-indigo-500 mr-2"></i>
            <?= $role === 'VISITEUR' ? 'Mes Réservations' : 'Toutes les Réservations' ?>
          </h2>
          <span class="text-sm text-gray-500"><?= count($reservations ?? []) ?> entrées</span>
        </div>

        <?php if (empty($reservations)): ?>
          <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-calendar-xmark text-5xl mb-3"></i>
            <p>Aucune réservation trouvée.</p>
          </div>
        <?php else: ?>
        <div class="overflow-x-auto rounded-xl border border-gray-200">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">#</th>
                <?php if ($role !== 'VISITEUR'): ?>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Client</th>
                <?php endif; ?>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Activité</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Date réservation</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Créée le</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Montant</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Statut</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <?php foreach ($reservations as $r): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 font-mono text-indigo-600 font-bold">#<?= $r['id'] ?></td>
                <?php if ($role !== 'VISITEUR'): ?>
                <td class="px-4 py-3">
                  <div class="font-medium text-gray-900"><?= htmlspecialchars(($r['client_prenom'] ?? '') . ' ' . ($r['client_nom'] ?? '')) ?></div>
                  <div class="text-xs text-gray-400"><?= htmlspecialchars($r['client_email'] ?? '') ?></div>
                </td>
                <?php endif; ?>
                <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($r['activite_nom'] ?? '') ?></td>
                <td class="px-4 py-3 text-gray-600">
                  <i class="fa-solid fa-calendar-day mr-1 text-gray-400"></i>
                  <?= date('d/m/Y', strtotime($r['date_reservation'])) ?>
                  <span class="text-xs text-gray-400 ml-1"><?= date('H:i', strtotime($r['date_reservation'])) ?></span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                  <?= date('d/m/Y H:i', strtotime($r['date_creation'])) ?>
                </td>
                <td class="px-4 py-3 font-bold text-gray-900"><?= number_format($r['montant_total'], 0, ',', ' ') ?> FC</td>
                <td class="px-4 py-3">
                  <?php
                  $badges = [
                    'ATTENTE'   => 'bg-yellow-100 text-yellow-800',
                    'PAYEE'     => 'bg-green-100 text-green-800',
                    'CONFIRMEE' => 'bg-blue-100 text-blue-800',
                    'ANNULEE'   => 'bg-red-100 text-red-800',
                  ];
                  $bc = $badges[$r['statut']] ?? 'bg-gray-100 text-gray-700';
                  ?>
                  <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $bc ?>">
                    <?= htmlspecialchars($r['statut']) ?>
                  </span>
                </td>
                <td class="px-4 py-3">
                  <?php if ($r['statut'] === 'PAYEE'): ?>
                  <a href="<?= BASE_URL ?>/?action=receipt&id=<?= $r['id'] ?>"
                     class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium border border-indigo-200 transition">
                    <i class="fa-solid fa-receipt"></i> Reçu
                  </a>
                  <?php elseif ($r['statut'] === 'ATTENTE'): ?>
                  <a href="<?= BASE_URL ?>/?action=online_checkout&id=<?= $r['id'] ?>"
                     class="inline-flex items-center gap-1 px-3 py-1 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg text-xs font-medium border border-green-200 transition">
                    <i class="fa-solid fa-credit-card"></i> Payer
                  </a>
                  <?php else: ?>
                  <span class="text-gray-300 text-xs">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>

      <!-- ════════════════════════════════════════════
           ONGLET 2 — PAIEMENTS (Caissier, Directeur, Admin)
      ════════════════════════════════════════════ -->
      <?php if (in_array($role, ['CAISSIER','DIRECTEUR','SUPER_ADMIN'])): ?>
      <div id="tab-paiements" class="tab-content hidden">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-gray-900">
            <i class="fa-solid fa-money-bill-wave text-green-500 mr-2"></i>
            <?= $role === 'CAISSIER' ? 'Mes Encaissements' : 'Tous les Paiements' ?>
          </h2>
          <a href="<?= BASE_URL ?>/?action=search_payments"
             class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
            <i class="fa-solid fa-magnifying-glass"></i> Recherche avancée
          </a>
        </div>

        <?php if (empty($paiements)): ?>
          <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-receipt text-5xl mb-3"></i>
            <p>Aucun paiement enregistré.</p>
          </div>
        <?php else: ?>
        <div class="overflow-x-auto rounded-xl border border-gray-200">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">#</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Client</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Activité</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Date & Heure</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Montant</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Méthode</th>
                <?php if ($role !== 'CAISSIER'): ?>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Caissier</th>
                <?php endif; ?>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Reçu</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <?php foreach ($paiements as $p): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 font-mono text-green-600 font-bold">#<?= $p['id'] ?></td>
                <td class="px-4 py-3">
                  <div class="font-medium text-gray-900"><?= htmlspecialchars(($p['client_prenom'] ?? '') . ' ' . ($p['client_nom'] ?? '')) ?></div>
                </td>
                <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($p['activite_nom'] ?? '') ?></td>
                <td class="px-4 py-3">
                  <div class="text-gray-800"><?= date('d/m/Y', strtotime($p['date_paiement'])) ?></div>
                  <div class="text-xs text-gray-400"><?= date('H:i:s', strtotime($p['date_paiement'])) ?></div>
                </td>
                <td class="px-4 py-3 font-bold text-green-700"><?= number_format($p['montant'], 0, ',', ' ') ?> FC</td>
                <td class="px-4 py-3">
                  <?php
                  $m_icons = ['ESPECES'=>'fa-money-bill','CARTE'=>'fa-credit-card','MOBILE_MONEY'=>'fa-mobile'];
                  $m_colors = ['ESPECES'=>'bg-green-100 text-green-800','CARTE'=>'bg-purple-100 text-purple-800','MOBILE_MONEY'=>'bg-orange-100 text-orange-800'];
                  $mi = $m_icons[$p['methode']] ?? 'fa-question';
                  $mc = $m_colors[$p['methode']] ?? 'bg-gray-100 text-gray-700';
                  ?>
                  <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold <?= $mc ?>">
                    <i class="fa-solid <?= $mi ?>"></i> <?= htmlspecialchars($p['methode']) ?>
                  </span>
                </td>
                <?php if ($role !== 'CAISSIER'): ?>
                <td class="px-4 py-3 text-gray-600 text-xs">
                  <?= htmlspecialchars(($p['caissier_prenom'] ?? '') . ' ' . ($p['caissier_nom'] ?? '—')) ?>
                </td>
                <?php endif; ?>
                <td class="px-4 py-3">
                  <a href="<?= BASE_URL ?>/?action=receipt&id=<?= $p['reservation_id'] ?>"
                     class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs border border-indigo-200 transition">
                    <i class="fa-solid fa-file-invoice"></i> Voir
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- ════════════════════════════════════════════
           ONGLET 3 — ACTIVITÉS (Directeur, Admin)
      ════════════════════════════════════════════ -->
      <?php if (in_array($role, ['DIRECTEUR','SUPER_ADMIN'])): ?>
      <div id="tab-activites" class="tab-content hidden">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-gray-900">
            <i class="fa-solid fa-water-ladder text-teal-500 mr-2"></i>
            Historique des Activités
          </h2>
          <a href="<?= BASE_URL ?>/?action=activity_history"
             class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
            <i class="fa-solid fa-expand"></i> Vue complète
          </a>
        </div>

        <?php if (empty($activite_history)): ?>
          <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-water-ladder text-5xl mb-3"></i>
            <p>Aucun historique d'activité.</p>
          </div>
        <?php else: ?>
        <div class="space-y-3">
          <?php foreach ($activite_history as $ah):
            $ah_colors = ['CREATE'=>'bg-green-100 text-green-800','UPDATE'=>'bg-blue-100 text-blue-800','DELETE'=>'bg-red-100 text-red-800'];
            $ah_icons  = ['CREATE'=>'fa-plus','UPDATE'=>'fa-pen','DELETE'=>'fa-trash'];
            $ah_c = $ah_colors[$ah['action']] ?? 'bg-gray-100 text-gray-700';
            $ah_i = $ah_icons[$ah['action']]  ?? 'fa-circle';
          ?>
          <div class="flex gap-4 items-start p-4 border border-gray-200 rounded-xl hover:shadow-sm transition">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 <?= $ah_c ?>">
              <i class="fa-solid <?= $ah_i ?>"></i>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-2">
                <div>
                  <span class="font-semibold text-gray-900"><?= htmlspecialchars($ah['activite_nom'] ?? 'Activité #'.$ah['activity_id']) ?></span>
                  <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium <?= $ah_c ?>"><?= $ah['action'] ?></span>
                </div>
                <div class="text-right flex-shrink-0">
                  <div class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($ah['created_at'])) ?></div>
                  <div class="text-xs font-bold text-gray-700"><?= date('H:i:s', strtotime($ah['created_at'])) ?></div>
                </div>
              </div>
              <div class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold">
                  <?= strtoupper(substr($ah['prenom'] ?? '?', 0, 1)) ?>
                </div>
                <span class="font-medium"><?= htmlspecialchars(($ah['prenom'] ?? '') . ' ' . ($ah['nom'] ?? '')) ?></span>
                <span class="text-gray-400">·</span>
                <span class="text-xs text-gray-400"><?= htmlspecialchars($ah['email'] ?? '') ?></span>
                <span class="ml-auto px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">
                  <?= htmlspecialchars($ah['role'] ?? '') ?>
                </span>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- ════════════════════════════════════════════
           ONGLET 4 — CONNEXIONS (Directeur, Admin)
      ════════════════════════════════════════════ -->
      <?php if (in_array($role, ['DIRECTEUR','SUPER_ADMIN'])): ?>
      <div id="tab-connexions" class="tab-content hidden">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-gray-900">
            <i class="fa-solid fa-right-to-bracket text-purple-500 mr-2"></i>
            Historique des Connexions
          </h2>
          <a href="<?= BASE_URL ?>/?action=admin_login_history"
             class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
            <i class="fa-solid fa-expand"></i> Vue complète
          </a>
        </div>

        <?php if (empty($connexions)): ?>
          <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-right-to-bracket text-5xl mb-3"></i>
            <p>Aucune connexion enregistrée.</p>
          </div>
        <?php else: ?>
        <div class="overflow-x-auto rounded-xl border border-gray-200">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Utilisateur</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Rôle</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Date & Heure</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">IP</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Navigateur</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Statut</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <?php foreach ($connexions as $c): ?>
              <tr class="hover:bg-gray-50 transition <?= $c['is_suspicious'] ? 'bg-red-50' : '' ?>">
                <td class="px-4 py-3">
                  <div class="font-medium text-gray-900">
                    <?= htmlspecialchars(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '')) ?>
                  </div>
                  <div class="text-xs text-gray-400"><?= htmlspecialchars($c['email']) ?></div>
                </td>
                <td class="px-4 py-3">
                  <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                    <?= htmlspecialchars($c['role'] ?? 'N/A') ?>
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="text-gray-800"><?= date('d/m/Y', strtotime($c['login_time'])) ?></div>
                  <div class="text-xs font-bold text-gray-700"><?= date('H:i:s', strtotime($c['login_time'])) ?></div>
                </td>
                <td class="px-4 py-3 font-mono text-xs text-gray-600"><?= htmlspecialchars($c['ip_address']) ?></td>
                <td class="px-4 py-3 text-xs text-gray-500">
                  <?= htmlspecialchars($c['browser'] ?? '') ?> / <?= htmlspecialchars($c['os'] ?? '') ?>
                </td>
                <td class="px-4 py-3">
                  <?php if ($c['is_suspicious']): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                      <i class="fa-solid fa-triangle-exclamation"></i> Suspect
                    </span>
                  <?php elseif ($c['status'] === 'success'): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                      <i class="fa-solid fa-check"></i> Succès
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                      <i class="fa-solid fa-xmark"></i> Échec
                    </span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- ════════════════════════════════════════════
           ONGLET 5 — JOURNAL D'AUDIT (Admin uniquement)
      ════════════════════════════════════════════ -->
      <?php if ($role === 'SUPER_ADMIN'): ?>
      <div id="tab-audit" class="tab-content hidden">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-gray-900">
            <i class="fa-solid fa-shield-halved text-rose-500 mr-2"></i>
            Journal d'Audit Complet
          </h2>
          <a href="<?= BASE_URL ?>/?action=admin_audit_logs"
             class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
            <i class="fa-solid fa-expand"></i> Vue complète
          </a>
        </div>

        <?php if (empty($audit_logs)): ?>
          <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-shield-halved text-5xl mb-3"></i>
            <p>Aucune entrée d'audit.</p>
          </div>
        <?php else: ?>
        <div class="space-y-2">
          <?php foreach ($audit_logs as $log): ?>
          <div class="flex items-center gap-3 p-3 border border-gray-100 rounded-lg hover:bg-gray-50 text-sm">
            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0">
              <i class="fa-solid fa-shield-halved text-rose-500 text-xs"></i>
            </div>
            <div class="flex-1 min-w-0">
              <span class="font-semibold text-gray-900"><?= htmlspecialchars($log['action']) ?></span>
              <span class="text-gray-400 mx-1">·</span>
              <span class="text-gray-600"><?= htmlspecialchars($log['user_name'] ?? 'Système') ?></span>
              <?php if ($log['description']): ?>
              <span class="text-gray-400 mx-1">·</span>
              <span class="text-gray-500 text-xs"><?= htmlspecialchars($log['description']) ?></span>
              <?php endif; ?>
            </div>
            <div class="text-right flex-shrink-0">
              <div class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($log['timestamp'])) ?></div>
              <div class="text-xs font-bold text-gray-700"><?= date('H:i:s', strtotime($log['timestamp'])) ?></div>
            </div>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $log['status'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
              <?= $log['status'] ?>
            </span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

    </div><!-- /p-6 -->
  </div><!-- /card -->
</div>

<style>
.tab-btn { color: #6b7280; background: transparent; border: none; cursor: pointer; transition: all .15s; }
.tab-btn:hover { background: #e5e7eb; color: #111827; }
.tab-btn.active { background: #ffffff; color: #4f46e5; font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
.tab-content.hidden { display: none; }
</style>

<script>
function showTab(id) {
  document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
  document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
  document.getElementById(id).classList.remove('hidden');
  const btnId = 'btn-' + id.replace('tab-', '');
  const btn = document.getElementById(btnId);
  if (btn) btn.classList.add('active');
}
</script>

<?php require 'views/layout/footer.php'; ?>
