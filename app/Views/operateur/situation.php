<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Situation Globale</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card text-center mb-3 bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Soldes</h5>
                                <h3><?= number_format($total_solde, 0, ',', ' ') ?> Ar</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center mb-3 bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Frais</h5>
                                <h3><?= number_format($total_frais, 0, ',', ' ') ?> Ar</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center mb-3 bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Nombre de Comptes</h5>
                                <h3><?= $nb_comptes ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center mb-3 bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Transactions</h5>
                                <h3><?= $nb_transactions ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h4 class="mt-4">Liste des Comptes</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Numéro</th>
                            <th>Solde (Ar)</th>
                            <th>Date de création</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comptes as $compte): ?>
                        <tr>
                            <td><?= $compte['id'] ?></td>
                            <td><?= $compte['numero_telephone'] ?></td>
                            <td><?= number_format($compte['solde'], 0, ',', ' ') ?></td>
                            <td><?= $compte['date_creation'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
