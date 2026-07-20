<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Autres Opérateurs</h2>
                <a href="<?= base_url('operateur/operateur/create') ?>" class="btn btn-primary">Ajouter un opérateur</a>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($operateurs)): ?>
                    <div class="alert alert-info">
                        Aucun opérateur externe configuré.
                    </div>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Taux de commission (%)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($operateurs as $operateur): ?>
                                <tr>
                                    <td><?= $operateur['id'] ?></td>
                                    <td><?= esc($operateur['nom']) ?></td>
                                    <td><?= number_format($operateur['taux_commission'], 2) ?>%</td>
                                    <td>
                                        <a href="<?= base_url('operateur/operateur/edit/' . $operateur['id']) ?>" class="btn btn-sm btn-warning">Modifier</a>
                                        <a href="<?= base_url('operateur/operateur/delete/' . $operateur['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet opérateur ?')">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
