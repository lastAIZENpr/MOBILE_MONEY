<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Espace Opérateur</h2>
            </div>
            <div class="card-body">
                <p>Bienvenue dans l'espace opérateur. Sélectionnez une action dans le menu.</p>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-center mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Préfixes</h5>
                                <p class="card-text">Gérer les préfixes valides</p>
                                <a href="<?= base_url('operateur/prefixes') ?>" class="btn btn-primary">Gérer</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Types d'opérations</h5>
                                <p class="card-text">Gérer les types et barèmes</p>
                                <a href="<?= base_url('operateur/types') ?>" class="btn btn-primary">Gérer</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Situation</h5>
                                <p class="card-text">Voir les gains et comptes</p>
                                <a href="<?= base_url('operateur/situation') ?>" class="btn btn-primary">Voir</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
