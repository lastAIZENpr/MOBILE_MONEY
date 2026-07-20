<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Liste des Comptes Clients</h2>
            </div>
            <div class="card-body">
                <?php if (empty($comptes)): ?>
                    <div class="alert alert-info">
                        Aucun compte client enregistré.
                    </div>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Numéro</th>
                                <th>Solde (Ar)</th>
                                <th>Date de création</th>
                                <th>Transactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comptes as $compte): ?>
                            <tr>
                                <td><?= $compte['id'] ?></td>
                                <td><?= $compte['numero_telephone'] ?></td>
                                <td><?= number_format($compte['solde'], 0, ',', ' ') ?></td>
                                <td><?= $compte['date_creation'] ?></td>
                                <td><?= $compte['nb_transactions'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <a href="<?= base_url('operateur') ?>" class="btn btn-secondary">Retour</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
