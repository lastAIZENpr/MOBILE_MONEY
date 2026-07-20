<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Retrait</h2>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('client/retrait/store') ?>" method="post">
                    <div class="mb-3">
                        <label for="montant" class="form-label">Montant (Ar)</label>
                        <input type="number" class="form-control" id="montant" name="montant" required min="1" step="1">
                        <div class="form-text">Entrez le montant à retirer</div>
                    </div>
                    <div class="mb-3">
                        <a href="<?= base_url('client') ?>" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-warning">Valider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
