<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu - Cosmos Beach</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .receipt-container {
            width: 100%;
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
        }
        /* Style spécial pour impression PDF */
        @media print {
            body { background-color: white; }
            .receipt-container { box-shadow: none; margin: 0; padding: 0; }
            .no-print { display: none !important; }
        }
        .border-dashed-custom {
            border-bottom: 2px dashed #e5e7eb;
        }
    </style>
</head>
<body>

    <div class="receipt-container rounded-xl">
        <!-- En-tête -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">COSMOS BEACH</h1>
            <p class="text-sm text-gray-500 mt-1">Complexe Touristique & Loisirs</p>
            <p class="text-sm text-gray-500">Situé A Nsele Quartier kindobo sur l avenue derrière le mausolée</p>
            <p class="text-sm text-gray-500">Tel: +243 81 33 59 689</p>
        </div>

        <div class="border-dashed-custom mb-6"></div>

        <!-- Infos Reçu -->
        <div class="flex justify-between mb-6">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Reçu N°</p>
                <p class="font-bold text-gray-900">#<?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?></p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Date</p>
                <p class="font-bold text-gray-900"><?= date('d/m/Y H:i', strtotime($payment['date_paiement'])) ?></p>
            </div>
        </div>

        <!-- Infos Client -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-500 mb-1">Facturé à :</p>
            <p class="font-bold text-gray-900 text-lg"><?= htmlspecialchars($reservation['client_nom']) ?></p>
            <p class="text-sm text-gray-500">Réservation N° : <?= htmlspecialchars($reservation['id']) ?></p>
        </div>

        <!-- Détails -->
        <table class="w-full mb-8">
            <thead>
                <tr class="border-b border-gray-200 text-left">
                    <th class="py-2 text-sm font-semibold text-gray-700">Description</th>
                    <th class="py-2 text-sm font-semibold text-gray-700 text-right">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-4">
                        <p class="font-bold text-gray-900"><?= htmlspecialchars($reservation['activite_nom']) ?></p>
                        <p class="text-sm text-gray-500">Date prévue : <?= date('d/m/Y H:i', strtotime($reservation['date_reservation'])) ?></p>
                    </td>
                    <td class="py-4 text-right font-medium text-gray-900">
                        <?= number_format($reservation['montant_total'], 2, ',', ' ') ?> FC
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="border-dashed-custom mb-6"></div>

        <!-- Total -->
        <div class="flex justify-between items-center mb-8">
            <span class="text-lg font-bold text-gray-700">TOTAL PAYÉ</span>
            <span class="text-2xl font-extrabold text-gray-900"><?= number_format($payment['montant'], 2, ',', ' ') ?> FC</span>
        </div>
        
        <div class="flex justify-between items-center mb-8 text-sm">
            <span class="text-gray-500">Méthode de paiement :</span>
            <span class="font-semibold text-gray-900"><?= htmlspecialchars($payment['methode']) ?></span>
        </div>

        <!-- Footer signature -->
        <div class="text-center mt-12 mb-4">
            <p class="text-gray-500 italic text-sm">Merci de votre visite et à très bientôt chez Cosmos Beach !</p>
        </div>

        <!-- Boutons d'action (masqués à l'impression) -->
        <div class="no-print flex justify-center space-x-4 mt-8">
            <button onclick="window.print()" class="px-6 py-2 bg-indigo-600 text-white rounded-full font-medium hover:bg-indigo-700 shadow-sm transition-colors">
                Imprimer / PDF
            </button>
            <a href="<?= BASE_URL ?>/?action=dashboard" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-full font-medium hover:bg-gray-300 transition-colors">
                Retour
            </a>
        </div>
    </div>

    <script>
        // Lancer l'impression automatiquement au chargement pour une meilleure UX
        window.onload = function() {
            // setTimeout(() => { window.print(); }, 500); 
        };
    </script>
</body>
</html>
