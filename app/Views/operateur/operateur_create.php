<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Ajouter un Opérateur Externe</h2>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('operateur/operateur/store') ?>" method="post">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de l'opérateur</label>
                        <input type="text" class="form-control" id="nom" name="nom" required placeholder="Ex: Orange Money">
                        <div class="form-text">Entrez le nom de l'opérateur externe</div>
                    </div>
                    <div class="mb-3">
                        <label for="taux_commission" class="form-label">Taux de commission (%)</label>
                        <input type="number" class="form-control" id="taux_commission" name="taux_commission" required min="0" max="100" step="0.01" placeholder="Ex: 2.5">
                        <div class="form-text">Entrez le taux de commission (entre 0 et 100)</div>
                    </div>
                    <div class="mb-3">
                        <a href="<?= base_url('operateur/operateurs') ?>" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
