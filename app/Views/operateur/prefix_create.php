<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Ajouter un Préfixe</h2>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('operateur/prefix/store') ?>" method="post">
                    <div class="mb-3">
                        <label for="prefixe" class="form-label">Préfixe</label>
                        <input type="text" class="form-control" id="prefixe" name="prefixe" required placeholder="Ex: 033 ou 037">
                        <div class="form-text">Le préfixe doit contenir 2 ou 3 chiffres uniquement</div>
                    </div>
                    <div class="mb-3">
                        <label for="operateur_externe_id" class="form-label">Opérateur</label>
                        <select class="form-select" id="operateur_externe_id" name="operateur_externe_id">
                            <option value="">Notre opérateur (interne)</option>
                            <?php foreach ($operateurs as $operateur): ?>
                                <option value="<?= $operateur['id'] ?>"><?= esc($operateur['nom']) ?> (<?= number_format($operateur['taux_commission'], 2) ?>%)</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Sélectionnez un opérateur externe si ce préfixe appartient à un autre opérateur</div>
                    </div>
                    <div class="mb-3">
                        <a href="<?= base_url('operateur/prefixes') ?>" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
