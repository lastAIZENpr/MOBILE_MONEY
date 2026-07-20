<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Gestion des Types d'Opérations et Barèmes de Frais</h2>
                <a href="<?= base_url('operateur/type/create') ?>" class="btn btn-primary">Ajouter un type d'opération</a>
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
                
                <?php foreach ($types as $type): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><?= $type['libelle'] ?> (<?= $type['code'] ?>)</h5>
                        <div>
                            <a href="<?= base_url('operateur/bareme/create/' . $type['id']) ?>" class="btn btn-sm btn-success">Ajouter une tranche</a>
                            <a href="<?= base_url('operateur/type/delete/' . $type['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce type d\'opération et tous ses barèmes ?')">Supprimer</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($type['baremes'])): ?>
                            <p class="text-muted">Aucun barème défini</p>
                        <?php else: ?>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Montant Min</th>
                                        <th>Montant Max</th>
                                        <th>Frais (Ar)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($type['baremes'] as $bareme): ?>
                                    <tr>
                                        <td><?= number_format($bareme['montant_min'], 0, ',', ' ') ?></td>
                                        <td><?= number_format($bareme['montant_max'], 0, ',', ' ') ?></td>
                                        <td><?= number_format($bareme['frais'], 0, ',', ' ') ?></td>
                                        <td>
                                            <a href="<?= base_url('operateur/bareme/delete/' . $bareme['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce barème ?')">Supprimer</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
