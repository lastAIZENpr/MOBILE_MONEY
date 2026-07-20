<?php

namespace App\Controllers;

use App\Models\CompteClientModel;
use App\Models\PrefixModel;

class Login extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function authenticate()
    {
        $numero = $this->request->getPost('numero');
        
        // Validation
        if (empty($numero)) {
            return redirect()->back()->with('error', 'Veuillez entrer un numéro de téléphone');
        }

        // Vérifier que le numéro commence par un préfixe valide
        $prefixModel = new PrefixModel();
        $prefixes = $prefixModel->where('actif', 1)->findAll();
        
        $prefixeValide = false;
        foreach ($prefixes as $prefix) {
            if (strpos($numero, $prefix['prefixe']) === 0) {
                $prefixeValide = true;
                break;
            }
        }

        if (!$prefixeValide) {
            return redirect()->back()->with('error', 'Numéro invalide : le préfixe n\'est pas reconnu par l\'opérateur');
        }

        // Chercher ou créer le compte
        $compteModel = new CompteClientModel();
        $compte = $compteModel->where('numero_telephone', $numero)->first();

        if (!$compte) {
            // Créer le compte avec solde = 0
            $compteModel->insert([
                'numero_telephone' => $numero,
                'solde' => 0,
                'date_creation' => date('Y-m-d H:i:s')
            ]);
            $compte = $compteModel->where('numero_telephone', $numero)->first();
        }

        // Créer la session
        session()->set('client_id', $compte['id']);
        session()->set('numero_telephone', $compte['numero_telephone']);
        session()->set('solde', $compte['solde']);

        return redirect()->to('/client');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
