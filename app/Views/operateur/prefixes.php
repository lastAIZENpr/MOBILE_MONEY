<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Gestion des Préfixes</h2>
                <a href="<?= base_url('operateur/prefix/create') ?>" class="btn btn-primary">Ajouter un préfixe</a>
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
                
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Préfixe</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prefixes as $prefix): ?>
                        <tr>
                            <td><?= $prefix['id'] ?></td>
                            <td><?= $prefix['prefixe'] ?></td>
                            <td>
                                <?php if ($prefix['actif']): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('operateur/prefix/toggle/' . $prefix['id']) ?>" class="btn btn-sm btn-warning">
                                    <?= $prefix['actif'] ? 'Désactiver' : 'Activer' ?>
                                </a>
                                <a href="<?= base_url('operateur/prefix/delete/' . $prefix['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce préfixe ?')">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
