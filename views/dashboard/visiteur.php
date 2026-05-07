<?php require 'views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header Dashboard -->
        <div class="bg-gradient-to-r from-brand-600 to-indigo-700 px-6 py-8 sm:p-10">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white sm:text-3xl">Espace Utilisateur</h1>
                    <p class="mt-2 text-brand-100">
                        Bienvenue, <span class="font-semibold"><?= htmlspecialchars($_SESSION['user_nom'] ?? 'Cher Client') ?></span>
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-brand-100 text-brand-800">
                        <i class="fa-solid fa-user-tag mr-2"></i> Visiteur
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            <!-- Bouton Historique -->
            <div class="mb-6 flex justify-end gap-3">
              <a href="<?= BASE_URL ?>/?action=my_event_tickets"
                 class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow transition text-sm">
                <i class="fa-solid fa-ticket"></i> Mes Tickets
              </a>
              <a href="<?= BASE_URL ?>/?action=mon_historique"
                 class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition text-sm">
                <i class="fa-solid fa-clock-rotate-left"></i> Mon Historique
              </a>
            </div>
            <h2 class="text-lg font-medium text-gray-900 mb-6">Vos informations</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                
                <!-- Carte Réservations -->
                <div class="bg-gray-50 overflow-hidden rounded-xl border border-gray-100 p-6 flex items-center hover:bg-gray-100 transition-colors">
                    <div class="bg-blue-100 rounded-lg p-3 text-brand-600">
                        <i class="fa-solid fa-calendar-check text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Mes Réservations</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= count($reservations) ?></p>
                    </div>
                </div>

                <!-- Carte Statut -->
                <div class="bg-gray-50 overflow-hidden rounded-xl border border-gray-100 p-6 flex items-center hover:bg-gray-100 transition-colors">
                    <div class="bg-green-100 rounded-lg p-3 text-green-600">
                        <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">En attente de paiement</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            <?php 
                                $attente = 0;
                                foreach($reservations as $r) { if($r['statut'] == 'ATTENTE') $attente++; }
                                echo $attente;
                            ?>
                        </p>
                    </div>
                </div>
                
            </div>

            <!-- Section Tickets d'Événements -->
            <?php
                try {
                    require_once 'models/EventTicket.php';
                    $ticketModel      = new EventTicket();
                    $myTickets        = $ticketModel->getByUser($_SESSION['user_id']);
                    $ticketsCount     = count($myTickets);
                } catch (Exception $e) {
                    $myTickets = []; $ticketsCount = 0;
                }
            ?>
            <?php if ($ticketsCount > 0): ?>
            <div class="mt-8 border-t border-gray-200 pt-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-ticket text-brand-500"></i> Mes Tickets d'Événements
                    </h3>
                    <a href="<?= BASE_URL ?>/?action=my_event_tickets"
                       class="text-sm text-brand-600 hover:text-brand-800 font-medium">
                        Voir tout <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach (array_slice($myTickets, 0, 4) as $tk): ?>
                    <div class="bg-gradient-to-br from-brand-50 to-indigo-50 border border-brand-200 rounded-xl p-4 flex items-center gap-3">
                        <div class="bg-brand-100 rounded-lg p-2 flex-shrink-0">
                            <i class="fa-solid fa-ticket text-brand-600"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-mono text-xs font-bold text-brand-700"><?= htmlspecialchars($tk['numero_ticket']) ?></p>
                            <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($tk['event_titre']) ?></p>
                            <p class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($tk['date_debut'])) ?> · <?= $tk['nombre_places'] ?> place(s)</p>
                        </div>
                        <span class="ml-auto flex-shrink-0 px-2 py-0.5 rounded-full text-xs font-semibold
                            <?= $tk['statut'] === 'CONFIRME' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                            <?= $tk['statut'] ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($ticketsCount > 4): ?>
                    <p class="text-center mt-3 text-sm text-gray-500">
                        et <?= $ticketsCount - 4 ?> autre(s) ticket(s) —
                        <a href="<?= BASE_URL ?>/?action=my_event_tickets" class="text-brand-600 font-medium">Voir tous</a>
                    </p>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="mt-6 border-t border-gray-200 pt-6">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500 flex items-center gap-2">
                        <i class="fa-solid fa-ticket text-gray-300 text-lg"></i>
                        Pas encore de ticket d'événement.
                    </p>
                    <a href="<?= BASE_URL ?>/?action=events"
                       class="text-sm text-brand-600 hover:text-brand-800 font-medium">
                        <i class="fa-solid fa-calendar-star mr-1"></i> Voir les événements
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <div class="mt-10 border-t border-gray-200 pt-10">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Historique des Réservations</h3>

                <?php if(empty($reservations)): ?>
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="p-12 text-center text-gray-500">
                            <i class="fa-regular fa-folder-open text-4xl mb-3 text-gray-300"></i>
                            <p>Vous n'avez pas encore fait de réservation.</p>
                            <a href="<?= BASE_URL ?>/?action=activities" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-brand-600 hover:bg-brand-700">
                                Explorer les activités
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-white shadow overflow-hidden sm:rounded-md">
                        <ul role="list" class="divide-y divide-gray-200">
                            <?php foreach($reservations as $r): ?>
                                <li>
                                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-brand-600 truncate"><?= htmlspecialchars($r['activite_nom']) ?></p>
                                            <div class="ml-2 flex-shrink-0 flex">
                                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?php 
                                                        if($r['statut'] == 'ATTENTE') echo 'bg-yellow-100 text-yellow-800';
                                                        elseif($r['statut'] == 'CONFIRMEE' || $r['statut'] == 'PAYEE') echo 'bg-green-100 text-green-800';
                                                        else echo 'bg-red-100 text-red-800';
                                                    ?>">
                                                    <?= htmlspecialchars($r['statut']) ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-2 sm:flex sm:justify-between items-center">
                                            <div class="sm:flex">
                                                <p class="flex items-center text-sm text-gray-500">
                                                    <i class="fa-solid fa-calendar mr-1.5 text-gray-400"></i>
                                                    Prévu le <?= date('d/m/Y H:i', strtotime($r['date_reservation'])) ?>
                                                </p>
                                            </div>
                                            <div class="mt-2 flex items-center gap-4 text-sm text-gray-500 sm:mt-0">
                                                <p class="font-bold text-gray-900 text-lg"><?= number_format($r['montant_total'], 2, ',', ' ') ?> FC</p>
                                                <?php if($r['statut'] == 'ATTENTE'): ?>
                                                    <a href="<?= BASE_URL ?>/?action=online_checkout&id=<?= $r['id'] ?>" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-xs font-semibold shadow-sm transition">
                                                        <i class="fa-solid fa-credit-card mr-1"></i> Payer en Ligne
                                                    </a>
                                                <?php endif; ?>
                                                <?php if($r['statut'] == 'PAYEE'): ?>
                                                    <a href="<?= BASE_URL ?>/?action=receipt&id=<?= $r['id'] ?>" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-md text-xs font-semibold shadow-sm transition">
                                                        <i class="fa-solid fa-receipt mr-1"></i> Reçu
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Module de Réclamation -->
            <div class="mt-10 border-t border-gray-200 pt-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Service Client / Réclamations</h3>
                    <p class="text-gray-500 text-sm mb-6">Une remarque, un objet perdu ou une suggestion ? Notre équipe est à votre écoute.</p>
                    
                    <?php if(isset($_SESSION['flash_success'])): ?>
                        <div class="mb-4 bg-green-50 p-4 border-l-4 border-green-500 text-sm text-green-700">
                            <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/?action=submit_reclamation" method="POST" class="space-y-4">
                        <div>
                            <label for="sujet" class="block text-sm font-medium text-gray-700 mb-1">Sujet</label>
                            <input type="text" id="sujet" name="sujet" required class="focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border outline-none">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Votre message</label>
                            <textarea id="message" name="message" rows="4" required class="focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border outline-none"></textarea>
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Envoyer le message
                        </button>
                    </form>
                </div>
                
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Mes précédents messages</h3>
                    <?php if(empty($reclamations)): ?>
                        <div class="bg-gray-50 rounded-lg p-6 text-center text-gray-500">
                            Aucun message envoyé.
                        </div>
                    <?php else: ?>
                        <div class="space-y-4 max-h-80 overflow-y-auto pr-2">
                            <?php foreach($reclamations as $rec): ?>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium text-gray-900 text-sm"><?= htmlspecialchars($rec['sujet']) ?></h4>
                                        <span class="px-2 py-0.5 text-xs rounded-full font-medium <?php echo $rec['statut'] == 'RESOLUE' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                            <?= htmlspecialchars($rec['statut']) ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($rec['message']) ?></p>
                                    <p class="text-xs text-gray-400 mt-2"><?= date('d/m/Y H:i', strtotime($rec['date_creation'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
