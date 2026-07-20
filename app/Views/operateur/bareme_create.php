<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Ajouter une Tranche de Frais - <?= $type['libelle'] ?></h2>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('operateur/bareme/store') ?>" method="post">
                    <input type="hidden" name="type_operation_id" value="<?= $type['id'] ?>">
                    <div class="mb-3">
                        <label for="montant_min" class="form-label">Montant Minimum (Ar)</label>
                        <input type="number" class="form-control" id="montant_min" name="montant_min" required min="0">
                    </div>
                    <div class="mb-3">
                        <label for="montant_max" class="form-label">Montant Maximum (Ar)</label>
                        <input type="number" class="form-control" id="montant_max" name="montant_max" required min="0">
                    </div>
                    <div class="mb-3">
                        <label for="frais" class="form-label">Frais (Ar)</label>
                        <input type="number" class="form-control" id="frais" name="frais" required min="0">
                    </div>
                    <div class="mb-3">
                        <a href="<?= base_url('operateur/types') ?>" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
