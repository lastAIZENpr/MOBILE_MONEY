<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Situation des Gains</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-header">
                                <h4>Gains via notre opérateur</h4>
                            </div>
                            <div class="card-body">
                                <h1><?= number_format($gains_notre_operateur, 0, ',', ' ') ?> Ar</h1>
                                <p class="mb-0">Frais collectés sur les transferts internes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-header">
                                <h4>Gains via autres opérateurs (commissions)</h4>
                            </div>
                            <div class="card-body">
                                <h1><?= number_format(array_sum($gains_autres_operateurs), 0, ',', ' ') ?> Ar</h1>
                                <p class="mb-0">Commissions collectées sur les transferts externes</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($gains_autres_operateurs)): ?>
                    <div class="mt-4">
                        <h4>Détail par opérateur externe</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Opérateur</th>
                                    <th>Commissions collectées</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gains_autres_operateurs as $operateur => $montant): ?>
                                    <tr>
                                        <td><?= esc($operateur) ?></td>
                                        <td><?= number_format($montant, 0, ',', ' ') ?> Ar</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
