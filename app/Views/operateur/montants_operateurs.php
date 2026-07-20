<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Situation des Montants à Envoyer aux Opérateurs Externes</h2>
            </div>
            <div class="card-body">
                <?php if (empty($montants_par_operateur)): ?>
                    <div class="alert alert-info">
                        Aucun montant à envoyer aux opérateurs externes.
                    </div>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Opérateur Externe</th>
                                <th>Montant Total à Envoyer (Ar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($montants_par_operateur as $operateur => $montant): ?>
                                <tr>
                                    <td><?= esc($operateur) ?></td>
                                    <td><strong><?= number_format($montant, 0, ',', ' ') ?> Ar</strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th><?= number_format(array_sum($montants_par_operateur), 0, ',', ' ') ?> Ar</th>
                            </tr>
                        </tfoot>
                    </table>
                <?php endif; ?>
                <a href="<?= base_url('operateur') ?>" class="btn btn-secondary">Retour</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
