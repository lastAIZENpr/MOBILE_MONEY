<?php

namespace App\Controllers;

use App\Models\CompteClientModel;
use App\Models\TypeOperationModel;
use App\Models\TransactionModel;

class Client extends BaseController
{
    public function index()
    {
        // Vérifier si le client est connecté
        if (!session()->get('client_id')) {
            return redirect()->to('/login');
        }

        $compteModel = new CompteClientModel();
        $compte = $compteModel->find(session()->get('client_id'));

        $data = [
            'numero' => $compte['numero_telephone'],
            'solde' => $compte['solde']
        ];

        return view('client/dashboard', $data);
    }

    public function depot()
    {
        if (!session()->get('client_id')) {
            return redirect()->to('/login');
        }
        return view('client/depot');
    }

    public function depotStore()
    {
        $montant = $this->request->getPost('montant');
        
        // Validation : montant > 0
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à 0');
        }
        
        $compteModel = new CompteClientModel();
        $typeOperationModel = new TypeOperationModel();
        $transactionModel = new TransactionModel();
        
        $clientId = session()->get('client_id');
        $compte = $compteModel->find($clientId);
        
        // Récupérer le type d'opération "depot"
        $typeDepot = $typeOperationModel->where('code', 'depot')->first();
        
        // Calculer le nouveau solde (dépôt = pas de frais)
        $nouveauSolde = $compte['solde'] + $montant;
        $frais = 0;
        
        // Mettre à jour le solde du compte
        $compteModel->update($clientId, ['solde' => $nouveauSolde]);
        
        // Créer la transaction
        $transactionModel->insert([
            'compte_id' => $clientId,
            'type_operation_id' => $typeDepot['id'],
            'montant' => $montant,
            'frais' => $frais,
            'solde_apres' => $nouveauSolde,
            'date_transaction' => date('Y-m-d H:i:s')
        ]);
        
        // Mettre à jour la session
        session()->set('solde', $nouveauSolde);
        
        return redirect()->to('/client')->with('success', 'Dépôt de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué avec succès');
    }

    public function retrait()
    {
        if (!session()->get('client_id')) {
            return redirect()->to('/login');
        }
        return view('client/retrait');
    }

    public function retraitStore()
    {
        $montant = $this->request->getPost('montant');
        
        // Validation basique : montant > 0
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à 0');
        }
        
        // TODO : Implémentation complète dans une tâche ultérieure
        return redirect()->to('/client')->with('success', 'Retrait effectué (squelette)');
    }

    public function transfert()
    {
        if (!session()->get('client_id')) {
            return redirect()->to('/login');
        }
        return view('client/transfert');
    }

    public function transfertStore()
    {
        $montant = $this->request->getPost('montant');
        $numeroDestinataire = $this->request->getPost('numero_destinataire');
        
        // Validation basique : montant > 0
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à 0');
        }
        
        // Validation basique : numéro destinataire non vide
        if (empty($numeroDestinataire)) {
            return redirect()->back()->with('error', 'Le numéro du destinataire est requis');
        }
        
        // TODO : Implémentation complète dans une tâche ultérieure
        return redirect()->to('/client')->with('success', 'Transfert effectué (squelette)');
    }
}
