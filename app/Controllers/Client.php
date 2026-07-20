<?php

namespace App\Controllers;

use App\Models\CompteClientModel;
use App\Models\TypeOperationModel;
use App\Models\TransactionModel;
use App\Models\BaremeFraisModel;

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
        
        // Validation : montant > 0
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à 0');
        }
        
        $compteModel = new CompteClientModel();
        $typeOperationModel = new TypeOperationModel();
        $transactionModel = new TransactionModel();
        $baremeFraisModel = new BaremeFraisModel();
        
        $clientId = session()->get('client_id');
        $compte = $compteModel->find($clientId);
        
        // Récupérer le type d'opération "retrait"
        $typeRetrait = $typeOperationModel->where('code', 'retrait')->first();
        
        // Calculer les frais selon la grille
        $bareme = $baremeFraisModel->where('type_operation_id', $typeRetrait['id'])
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();
        
        $frais = $bareme ? $bareme['frais'] : 0;
        
        // Utiliser le crédit de frais de retrait en priorité
        $creditUtilise = 0;
        $fraisAPrelever = $frais;
        
        if ($compte['credit_frais_retrait'] > 0) {
            // Utiliser le crédit pour couvrir les frais
            if ($compte['credit_frais_retrait'] >= $frais) {
                // Le crédit couvre totalement les frais
                $creditUtilise = $frais;
                $fraisAPrelever = 0;
            } else {
                // Le crédit couvre partiellement les frais
                $creditUtilise = $compte['credit_frais_retrait'];
                $fraisAPrelever = $frais - $creditUtilise;
            }
        }
        
        $montantTotal = $montant + $fraisAPrelever;
        
        // Vérifier si le solde est suffisant
        if ($compte['solde'] < $montantTotal) {
            return redirect()->back()->with('error', 'Solde insuffisant. Solde actuel : ' . number_format($compte['solde'], 0, ',', ' ') . ' Ar, Montant requis : ' . number_format($montantTotal, 0, ',', ' ') . ' Ar');
        }
        
        // Calculer le nouveau solde et le nouveau crédit
        $nouveauSolde = $compte['solde'] - $montantTotal;
        $nouveauCreditFraisRetrait = $compte['credit_frais_retrait'] - $creditUtilise;
        
        // Mettre à jour le solde et le crédit du compte
        $compteModel->update($clientId, [
            'solde' => $nouveauSolde,
            'credit_frais_retrait' => $nouveauCreditFraisRetrait
        ]);
        
        // Créer la transaction
        $transactionModel->insert([
            'compte_id' => $clientId,
            'type_operation_id' => $typeRetrait['id'],
            'montant' => $montant,
            'frais' => $frais,
            'solde_apres' => $nouveauSolde,
            'date_transaction' => date('Y-m-d H:i:s')
        ]);
        
        // Mettre à jour la session
        session()->set('solde', $nouveauSolde);
        
        $message = 'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué avec succès (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar';
        if ($creditUtilise > 0) {
            $message .= ', dont ' . number_format($creditUtilise, 0, ',', ' ') . ' Ar couverts par votre crédit de frais)';
        }
        $message .= ')';
        
        return redirect()->to('/client')->with('success', $message);
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
        $inclureFraisRetrait = $this->request->getPost('inclure_frais_retrait');
        
        // Validation : montant > 0
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à 0');
        }
        
        // Validation : numéro destinataire non vide
        if (empty($numeroDestinataire)) {
            return redirect()->back()->with('error', 'Le numéro du destinataire est requis');
        }
        
        // Validation : ne pas transférer à soi-même
        $clientId = session()->get('client_id');
        $compteModel = new CompteClientModel();
        $compteExpediteur = $compteModel->find($clientId);
        
        if ($numeroDestinataire === $compteExpediteur['numero_telephone']) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas transférer à votre propre compte');
        }
        
        // Vérifier que le destinataire existe
        $compteDestinataire = $compteModel->where('numero_telephone', $numeroDestinataire)->first();
        if (!$compteDestinataire) {
            return redirect()->back()->with('error', 'Le destinataire n\'a pas de compte Mobile Money');
        }
        
        $typeOperationModel = new TypeOperationModel();
        $transactionModel = new TransactionModel();
        $baremeFraisModel = new BaremeFraisModel();
        
        // Récupérer le type d'opération "transfert"
        $typeTransfert = $typeOperationModel->where('code', 'transfert')->first();
        
        // Calculer les frais de transfert selon la grille
        $bareme = $baremeFraisModel->where('type_operation_id', $typeTransfert['id'])
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();
        
        $fraisTransfert = $bareme ? $bareme['frais'] : 0;
        
        // Calculer les frais de retrait si l'option est cochée
        $fraisRetrait = 0;
        if ($inclureFraisRetrait) {
            // Récupérer le type d'opération "retrait"
            $typeRetrait = $typeOperationModel->where('code', 'retrait')->first();
            
            // Calculer les frais de retrait selon la grille
            $baremeRetrait = $baremeFraisModel->where('type_operation_id', $typeRetrait['id'])
                ->where('montant_min <=', $montant)
                ->where('montant_max >=', $montant)
                ->first();
            
            $fraisRetrait = $baremeRetrait ? $baremeRetrait['frais'] : 0;
        }
        
        $fraisTotal = $fraisTransfert + $fraisRetrait;
        $montantTotal = $montant + $fraisTotal;
        
        // Vérifier si le solde de l'expéditeur est suffisant
        if ($compteExpediteur['solde'] < $montantTotal) {
            return redirect()->back()->with('error', 'Solde insuffisant. Solde actuel : ' . number_format($compteExpediteur['solde'], 0, ',', ' ') . ' Ar, Montant requis : ' . number_format($montantTotal, 0, ',', ' ') . ' Ar');
        }
        
        // Calculer les nouveaux soldes
        $nouveauSoldeExpediteur = $compteExpediteur['solde'] - $montantTotal;
        $nouveauSoldeDestinataire = $compteDestinataire['solde'] + $montant;
        $nouveauCreditFraisRetrait = $compteDestinataire['credit_frais_retrait'] + $fraisRetrait;
        
        // Mettre à jour les soldes des deux comptes
        $compteModel->update($clientId, ['solde' => $nouveauSoldeExpediteur]);
        $compteModel->update($compteDestinataire['id'], [
            'solde' => $nouveauSoldeDestinataire,
            'credit_frais_retrait' => $nouveauCreditFraisRetrait
        ]);
        
        // Créer la transaction pour l'expéditeur
        $transactionModel->insert([
            'compte_id' => $clientId,
            'type_operation_id' => $typeTransfert['id'],
            'montant' => $montant,
            'frais' => $fraisTotal,
            'solde_apres' => $nouveauSoldeExpediteur,
            'date_transaction' => date('Y-m-d H:i:s'),
            'compte_destination_id' => $compteDestinataire['id']
        ]);
        
        // Mettre à jour la session de l'expéditeur
        session()->set('solde', $nouveauSoldeExpediteur);
        
        $message = 'Transfert de ' . number_format($montant, 0, ',', ' ') . ' Ar vers ' . $numeroDestinataire . ' effectué avec succès (frais : ' . number_format($fraisTotal, 0, ',', ' ') . ' Ar';
        if ($fraisRetrait > 0) {
            $message .= ', dont ' . number_format($fraisRetrait, 0, ',', ' ') . ' Ar de frais de retrait prépayés pour le destinataire)';
        }
        $message .= ')';
        
        return redirect()->to('/client')->with('success', $message);
    }

    public function historique()
    {
        if (!session()->get('client_id')) {
            return redirect()->to('/login');
        }
        
        $transactionModel = new TransactionModel();
        $typeOperationModel = new TypeOperationModel();
        $compteModel = new CompteClientModel();
        
        $clientId = session()->get('client_id');
        
        // Récupérer les transactions du client
        $transactions = $transactionModel->where('compte_id', $clientId)
            ->orderBy('date_transaction', 'DESC')
            ->findAll();
        
        // Enrichir avec les types d'opération et destinataires
        foreach ($transactions as &$transaction) {
            $typeOperation = $typeOperationModel->find($transaction['type_operation_id']);
            $transaction['type_libelle'] = $typeOperation ? $typeOperation['libelle'] : 'Inconnu';
            $transaction['type_code'] = $typeOperation ? $typeOperation['code'] : 'inconnu';
            
            if ($transaction['compte_destination_id']) {
                $compteDestinataire = $compteModel->find($transaction['compte_destination_id']);
                $transaction['destinataire'] = $compteDestinataire ? $compteDestinataire['numero_telephone'] : 'Inconnu';
            } else {
                $transaction['destinataire'] = null;
            }
        }
        
        return view('client/historique', ['transactions' => $transactions]);
    }
}
