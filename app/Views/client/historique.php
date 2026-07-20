<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Historique des Transactions</h2>
            </div>
            <div class="card-body">
                <?php if (empty($transactions)): ?>
                    <div class="alert alert-info">
                        Aucune transaction effectuée.
                    </div>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Montant (Ar)</th>
                                <th>Frais (Ar)</th>
                                <th>Solde après (Ar)</th>
                                <th>Destinataire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?= $transaction['date_transaction'] ?></td>
                                <td>
                                    <span class="badge 
                                        <?= $transaction['type_code'] === 'depot' ? 'bg-success' : 
                                           ($transaction['type_code'] === 'retrait' ? 'bg-warning' : 'bg-info') ?>">
                                        <?= $transaction['type_libelle'] ?>
                                    </span>
                                </td>
                                <td><?= number_format($transaction['montant'], 0, ',', ' ') ?></td>
                                <td><?= number_format($transaction['frais'], 0, ',', ' ') ?></td>
                                <td><?= number_format($transaction['solde_apres'], 0, ',', ' ') ?></td>
                                <td><?= $transaction['destinataire'] ? $transaction['destinataire'] : '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <a href="<?= base_url('client') ?>" class="btn btn-secondary">Retour</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
