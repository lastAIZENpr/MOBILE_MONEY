<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Épargne</h2>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="<?= base_url('client/epargne/store') ?>">
                    <div class="form-group">
                        <label for="pourcentage_epargne">Pourcentage d'épargne (0-100%)</label>
                        <input type="number" class="form-control" id="pourcentage_epargne" name="pourcentage_epargne" 
                               value="<?= $pourcentage_epargne ?>" min="0" max="100" step="0.1" required>
                        <small class="form-text text-muted">Ce pourcentage sera automatiquement épargné lors de chaque dépôt.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="<?= base_url('client') ?>" class="btn btn-secondary">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
