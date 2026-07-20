<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Espace Client</h2>
            </div>
            <div class="card-body">
                <p>Bienvenue dans votre espace client.</p>
                <div class="alert alert-info">
                    <h4>Solde actuel</h4>
                    <h2>0 Ar</h2>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card text-center mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Dépôt</h5>
                                <a href="<?= base_url('client/depot') ?>" class="btn btn-success">Effectuer</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Retrait</h5>
                                <a href="<?= base_url('client/retrait') ?>" class="btn btn-warning">Effectuer</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Transfert</h5>
                                <a href="<?= base_url('client/transfert') ?>" class="btn btn-info">Effectuer</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Historique</h5>
                                <a href="<?= base_url('client/historique') ?>" class="btn btn-secondary">Voir</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
