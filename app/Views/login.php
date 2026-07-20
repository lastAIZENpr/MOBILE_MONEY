<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money - Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Mobile Money</h2>
                        <p class="mb-0">Connexion par numéro de téléphone</p>
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
                        
                        <form action="<?= base_url('login/authenticate') ?>" method="post">
                            <div class="mb-3">
                                <label for="numero" class="form-label">Numéro de téléphone</label>
                                <input type="text" class="form-control" id="numero" name="numero" required placeholder="Ex: 0341234567">
                                <div class="form-text">Entrez votre numéro de téléphone (avec préfixe valide)</div>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
