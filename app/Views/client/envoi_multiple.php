<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Envoi Multiple</h2>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('client/envoi_multiple/store') ?>" method="post" id="envoiMultipleForm">
                    <div class="mb-3">
                        <label for="montant_total" class="form-label">Montant total à envoyer (Ar)</label>
                        <input type="number" class="form-control" id="montant_total" name="montant_total" required min="1" step="1">
                        <div class="form-text">Ce montant sera divisé équitablement entre tous les destinataires</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Destinataires</label>
                        <div id="destinatairesContainer">
                            <div class="row mb-2 destinataire-row">
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="destinataires[]" required placeholder="Numéro de téléphone" pattern="\d{10}" title="Le numéro doit contenir 10 chiffres">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger w-100" onclick="removeDestinataire(this)" disabled>Supprimer</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" onclick="addDestinataire()">Ajouter un destinataire</button>
                        <div class="form-text mt-2">Minimum 2 destinataires requis</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <strong>Montant par destinataire :</strong> <span id="montantParDestinataire">0</span> Ar
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <a href="<?= base_url('client') ?>" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-info">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function addDestinataire() {
    const container = document.getElementById('destinatairesContainer');
    const newRow = document.createElement('div');
    newRow.className = 'row mb-2 destinataire-row';
    newRow.innerHTML = `
        <div class="col-md-10">
            <input type="text" class="form-control" name="destinataires[]" required placeholder="Numéro de téléphone" pattern="\d{10}" title="Le numéro doit contenir 10 chiffres">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger w-100" onclick="removeDestinataire(this)">Supprimer</button>
        </div>
    `;
    container.appendChild(newRow);
    updateMontantParDestinataire();
    updateRemoveButtons();
}

function removeDestinataire(button) {
    const row = button.closest('.destinataire-row');
    row.remove();
    updateMontantParDestinataire();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.destinataire-row');
    const buttons = document.querySelectorAll('.destinataire-row .btn-danger');
    buttons.forEach(btn => {
        btn.disabled = rows.length <= 1;
    });
}

function updateMontantParDestinataire() {
    const montantTotal = parseFloat(document.getElementById('montant_total').value) || 0;
    const destinataires = document.querySelectorAll('.destinataire-row').length;
    const montantParDestinataire = destinataires > 0 ? montantTotal / destinataires : 0;
    document.getElementById('montantParDestinataire').textContent = montantParDestinataire.toFixed(0);
}

document.getElementById('montant_total').addEventListener('input', updateMontantParDestinataire);
</script>
<?= $this->endSection() ?>
