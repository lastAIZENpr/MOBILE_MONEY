<?php

namespace App\Controllers;

use App\Models\CompteClientModel;
use App\Models\TypeOperationModel;
use App\Models\TransactionModel;
use App\Models\BaremeFraisModel;
use App\Models\PrefixModel;
use App\Models\OperateurExterneModel;
use App\Models\PromotionModel;

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
        $prefixModel = new PrefixModel();
        $operateurExterneModel = new OperateurExterneModel();
        
        // Récupérer le type d'opération "transfert"
        $typeTransfert = $typeOperationModel->where('code', 'transfert')->first();
        
        // Calculer les frais de transfert selon la grille
        $bareme = $baremeFraisModel->where('type_operation_id', $typeTransfert['id'])
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();
        
        $fraisTransfert = $bareme ? $bareme['frais'] : 0;
        
        // Vérifier si le destinataire appartient à un opérateur externe
        $commission = 0;
        $prefixeDestinataire = substr($numeroDestinataire, 0, 3);
        $prefix = $prefixModel->where('prefixe', $prefixeDestinataire)->first();
        
        if ($prefix && $prefix['operateur_externe_id']) {
            $operateurExterne = $operateurExterneModel->find($prefix['operateur_externe_id']);
            if ($operateurExterne) {
                // Calculer la commission (montant * taux/100)
                $commission = round($montant * $operateurExterne['taux_commission'] / 100);
            }
        }
        
        // Vérifier si promotion applicable (même opérateur)
        $prefixExpediteur = $prefixModel->where('prefixe', substr($compteExpediteur['numero_telephone'], 0, 3))->first();
        $operateurExpediteurId = $prefixExpediteur ? $prefixExpediteur['operateur_externe_id'] : null;
        $operateurDestinataireId = $prefix ? $prefix['operateur_externe_id'] : null;
        
        if ($operateurExpediteurId == $operateurDestinataireId) {
            $promotionModel = new PromotionModel();
            $promotion = $promotionModel->find(1);
            if ($promotion && $promotion['actif']) {
                $fraisTransfert -= round($fraisTransfert * $promotion['pourcentage'] / 100);
            }
        }
        
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
        
        $fraisTotal = $fraisTransfert + $fraisRetrait + $commission;
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
            $message .= ', dont ' . number_format($fraisRetrait, 0, ',', ' ') . ' Ar de frais de retrait prépayés pour le destinataire';
        }
        if ($commission > 0) {
            $message .= ($fraisRetrait > 0 ? ', ' : ', dont ') . number_format($commission, 0, ',', ' ') . ' Ar de commission (transfert externe)';
        }
        $message .= ')';
        
        return redirect()->to('/client')->with('success', $message);
    }

    public function envoiMultiple()
    {
        if (!session()->get('client_id')) {
            return redirect()->to('/login');
        }
        return view('client/envoi_multiple');
    }

    public function envoiMultipleStore()
    {
        $montantTotal = $this->request->getPost('montant_total');
        $destinataires = $this->request->getPost('destinataires');
        
        // Validation : montant > 0
        if ($montantTotal <= 0) {
            return redirect()->back()->with('error', 'Le montant total doit être supérieur à 0');
        }
        
        // Validation : minimum 2 destinataires
        if (empty($destinataires) || count($destinataires) < 2) {
            return redirect()->back()->with('error', 'Minimum 2 destinataires requis');
        }
        
        // Validation : montant divisible par le nombre de destinataires
        $nbDestinataires = count($destinataires);
        if ($montantTotal % $nbDestinataires !== 0) {
            return redirect()->back()->with('error', 'Le montant total doit être divisible par le nombre de destinataires');
        }
        
        $montantParDestinataire = $montantTotal / $nbDestinataires;
        
        $compteModel = new CompteClientModel();
        $typeOperationModel = new TypeOperationModel();
        $transactionModel = new TransactionModel();
        $baremeFraisModel = new BaremeFraisModel();
        $prefixModel = new PrefixModel();
        $operateurExterneModel = new OperateurExterneModel();
        
        $clientId = session()->get('client_id');
        $compteExpediteur = $compteModel->find($clientId);
        
        // Récupérer le type d'opération "transfert"
        $typeTransfert = $typeOperationModel->where('code', 'transfert')->first();
        
        // Calculer les frais de transfert selon la grille
        $bareme = $baremeFraisModel->where('type_operation_id', $typeTransfert['id'])
            ->where('montant_min <=', $montantParDestinataire)
            ->where('montant_max >=', $montantParDestinataire)
            ->first();
        
        $fraisTransfert = $bareme ? $bareme['frais'] : 0;
        
        // Calculer le total des frais et commissions pour tous les destinataires
        $totalFrais = 0;
        $destinatairesValides = [];
        
        foreach ($destinataires as $numeroDestinataire) {
            // Validation : ne pas transférer à soi-même
            if ($numeroDestinataire === $compteExpediteur['numero_telephone']) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas transférer à votre propre compte');
            }
            
            // Vérifier que le destinataire existe
            $compteDestinataire = $compteModel->where('numero_telephone', $numeroDestinataire)->first();
            if (!$compteDestinataire) {
                return redirect()->back()->with('error', 'Le destinataire ' . $numeroDestinataire . ' n\'a pas de compte Mobile Money');
            }
            
            // Vérifier si le destinataire appartient à un opérateur externe
            $commission = 0;
            $prefixeDestinataire = substr($numeroDestinataire, 0, 3);
            $prefix = $prefixModel->where('prefixe', $prefixeDestinataire)->first();
            
            if ($prefix && $prefix['operateur_externe_id']) {
                $operateurExterne = $operateurExterneModel->find($prefix['operateur_externe_id']);
                if ($operateurExterne) {
                    $commission = round($montantParDestinataire * $operateurExterne['taux_commission'] / 100);
                }
            }
            
            $fraisTotal = $fraisTransfert + $commission;
            $totalFrais += $fraisTotal;
            
            $destinatairesValides[] = [
                'numero' => $numeroDestinataire,
                'compte' => $compteDestinataire,
                'frais' => $fraisTotal
            ];
        }
        
        $montantTotalAPrelever = $montantTotal + $totalFrais;
        
        // Vérifier si le solde de l'expéditeur est suffisant
        if ($compteExpediteur['solde'] < $montantTotalAPrelever) {
            return redirect()->back()->with('error', 'Solde insuffisant. Solde actuel : ' . number_format($compteExpediteur['solde'], 0, ',', ' ') . ' Ar, Montant requis : ' . number_format($montantTotalAPrelever, 0, ',', ' ') . ' Ar');
        }
        
        // Exécuter les transferts
        $nouveauSoldeExpediteur = $compteExpediteur['solde'] - $montantTotalAPrelever;
        
        foreach ($destinatairesValides as $dest) {
            $nouveauSoldeDestinataire = $dest['compte']['solde'] + $montantParDestinataire;
            
            // Mettre à jour le solde du destinataire
            $compteModel->update($dest['compte']['id'], ['solde' => $nouveauSoldeDestinataire]);
            
            // Créer la transaction
            $transactionModel->insert([
                'compte_id' => $clientId,
                'type_operation_id' => $typeTransfert['id'],
                'montant' => $montantParDestinataire,
                'frais' => $dest['frais'],
                'solde_apres' => $nouveauSoldeExpediteur,
                'date_transaction' => date('Y-m-d H:i:s'),
                'compte_destination_id' => $dest['compte']['id']
            ]);
        }
        
        // Mettre à jour le solde de l'expéditeur
        $compteModel->update($clientId, ['solde' => $nouveauSoldeExpediteur]);
        
        // Mettre à jour la session
        session()->set('solde', $nouveauSoldeExpediteur);
        
        return redirect()->to('/client')->with('success', 'Envoi multiple effectué avec succès : ' . $nbDestinataires . ' destinataires ont reçu ' . number_format($montantParDestinataire, 0, ',', ' ') . ' Ar chacun (frais totaux : ' . number_format($totalFrais, 0, ',', ' ') . ' Ar)');
    }

    public function historique()
    {
        if (!session()->get('client_id')) {
            return redirect()->to('/login');
        }
        
        $transactionModel = new TransactionModel();
        $typeOperationModel = new TypeOperationModel();
        $compteModel = new CompteClientModel();
        $prefixModel = new PrefixModel();
        $operateurExterneModel = new OperateurExterneModel();
        
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
                
                // Vérifier si c'est un transfert externe
                if ($compteDestinataire) {
                    $prefixeDestinataire = substr($compteDestinataire['numero_telephone'], 0, 3);
                    $prefix = $prefixModel->where('prefixe', $prefixeDestinataire)->first();
                    
                    if ($prefix && $prefix['operateur_externe_id']) {
                        $operateurExterne = $operateurExterneModel->find($prefix['operateur_externe_id']);
                        $transaction['transfert_externe'] = true;
                        $transaction['operateur_externe'] = $operateurExterne ? $operateurExterne['nom'] : 'Inconnu';
                    } else {
                        $transaction['transfert_externe'] = false;
                        $transaction['operateur_externe'] = null;
                    }
                }
            } else {
                $transaction['destinataire'] = null;
                $transaction['transfert_externe'] = false;
                $transaction['operateur_externe'] = null;
            }
        }
        
        // Récupérer le compte actuel pour afficher le crédit de frais
        $compteActuel = $compteModel->find($clientId);
        
        return view('client/historique', [
            'transactions' => $transactions,
            'credit_frais_retrait' => $compteActuel['credit_frais_retrait']
        ]);
    }
}
