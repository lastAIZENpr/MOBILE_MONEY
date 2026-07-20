<?php

namespace App\Controllers;

use App\Models\PrefixModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use App\Models\CompteClientModel;
use App\Models\TransactionModel;

class Operateur extends BaseController
{
    public function index()
    {
        return view('operateur/dashboard');
    }

    // CRUD des préfixes
    public function prefixes()
    {
        $prefixModel = new PrefixModel();
        $prefixes = $prefixModel->findAll();
        
        return view('operateur/prefixes', ['prefixes' => $prefixes]);
    }

    public function prefixCreate()
    {
        return view('operateur/prefix_create');
    }

    public function prefixStore()
    {
        $prefixModel = new PrefixModel();
        
        $prefixe = $this->request->getPost('prefixe');
        
        // Validation : format 2-3 chiffres
        if (!preg_match('/^\d{2,3}$/', $prefixe)) {
            return redirect()->back()->with('error', 'Le préfixe doit contenir 2 ou 3 chiffres uniquement');
        }
        
        // Validation : unicité
        $existing = $prefixModel->where('prefixe', $prefixe)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Ce préfixe existe déjà');
        }
        
        $prefixModel->insert([
            'prefixe' => $prefixe,
            'actif' => 1
        ]);
        
        return redirect()->to('/operateur/prefixes')->with('success', 'Préfixe ajouté avec succès');
    }

    public function prefixToggle($id)
    {
        $prefixModel = new PrefixModel();
        $prefix = $prefixModel->find($id);
        
        if ($prefix) {
            $prefixModel->update($id, ['actif' => $prefix['actif'] ? 0 : 1]);
        }
        
        return redirect()->to('/operateur/prefixes')->with('success', 'Préfixe modifié avec succès');
    }

    public function prefixDelete($id)
    {
        $prefixModel = new PrefixModel();
        $prefixModel->delete($id);
        
        return redirect()->to('/operateur/prefixes')->with('success', 'Préfixe supprimé avec succès');
    }

    // CRUD des types d'opérations et barèmes de frais
    public function types()
    {
        $typeModel = new TypeOperationModel();
        $types = $typeModel->findAll();
        
        $baremeModel = new BaremeFraisModel();
        
        foreach ($types as &$type) {
            $type['baremes'] = $baremeModel->where('type_operation_id', $type['id'])->orderBy('montant_min', 'ASC')->findAll();
        }
        
        return view('operateur/types', ['types' => $types]);
    }

    public function typeCreate()
    {
        return view('operateur/type_create');
    }

    public function typeStore()
    {
        $typeModel = new TypeOperationModel();
        
        $code = $this->request->getPost('code');
        $libelle = $this->request->getPost('libelle');
        
        // Validation : unicité du code
        $existing = $typeModel->where('code', $code)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Ce code existe déjà');
        }
        
        $typeModel->insert([
            'code' => $code,
            'libelle' => $libelle
        ]);
        
        return redirect()->to('/operateur/types')->with('success', 'Type d\'opération ajouté avec succès');
    }

    public function typeDelete($id)
    {
        $typeModel = new TypeOperationModel();
        $baremeModel = new BaremeFraisModel();
        
        // Supprimer d'abord les barèmes associés
        $baremeModel->where('type_operation_id', $id)->delete();
        
        // Puis supprimer le type
        $typeModel->delete($id);
        
        return redirect()->to('/operateur/types')->with('success', 'Type d\'opération supprimé avec succès');
    }

    public function baremeCreate($typeId)
    {
        $typeModel = new TypeOperationModel();
        $type = $typeModel->find($typeId);
        
        return view('operateur/bareme_create', ['type' => $type]);
    }

    public function baremeStore()
    {
        $baremeModel = new BaremeFraisModel();
        
        $typeOperationId = $this->request->getPost('type_operation_id');
        $montantMin = $this->request->getPost('montant_min');
        $montantMax = $this->request->getPost('montant_max');
        $frais = $this->request->getPost('frais');
        
        // Validation : montant_min < montant_max
        if ($montantMin >= $montantMax) {
            return redirect()->back()->with('error', 'Le montant minimum doit être inférieur au montant maximum');
        }
        
        // Validation : pas de chevauchement avec les tranches existantes
        $existing = $baremeModel->where('type_operation_id', $typeOperationId)
            ->groupStart()
                ->where('montant_min <=', $montantMin)
                ->where('montant_max >=', $montantMin)
            ->groupEnd()
            ->orGroupStart()
                ->where('montant_min <=', $montantMax)
                ->where('montant_max >=', $montantMax)
            ->groupEnd()
            ->first();
        
        if ($existing) {
            return redirect()->back()->with('error', 'Cette tranche chevauche une tranche existante');
        }
        
        $baremeModel->insert([
            'type_operation_id' => $typeOperationId,
            'montant_min' => $montantMin,
            'montant_max' => $montantMax,
            'frais' => $frais
        ]);
        
        return redirect()->to('/operateur/types')->with('success', 'Barème ajouté avec succès');
    }

    public function baremeDelete($id)
    {
        $baremeModel = new BaremeFraisModel();
        $baremeModel->delete($id);
        
        return redirect()->to('/operateur/types')->with('success', 'Barème supprimé avec succès');
    }

    // Situation globale
    public function situation()
    {
        $compteModel = new CompteClientModel();
        $transactionModel = new TransactionModel();
        
        // Total des soldes de tous les comptes
        $totalSolde = 0;
        $comptes = $compteModel->findAll();
        foreach ($comptes as $compte) {
            $totalSolde += $compte['solde'];
        }
        
        // Total des frais collectés
        $totalFrais = 0;
        $transactions = $transactionModel->findAll();
        foreach ($transactions as $transaction) {
            $totalFrais += $transaction['frais'];
        }
        
        // Nombre de comptes
        $nbComptes = count($comptes);
        
        // Nombre de transactions
        $nbTransactions = count($transactions);
        
        $data = [
            'total_solde' => $totalSolde,
            'total_frais' => $totalFrais,
            'nb_comptes' => $nbComptes,
            'nb_transactions' => $nbTransactions,
            'comptes' => $comptes
        ];
        
        return view('operateur/situation', $data);
    }

    // Liste des transactions
    public function transactions()
    {
        $transactionModel = new TransactionModel();
        $typeOperationModel = new TypeOperationModel();
        $compteModel = new CompteClientModel();
        
        $transactions = $transactionModel->orderBy('date_transaction', 'DESC')->findAll();
        
        // Enrichir avec les types d'opération et comptes
        foreach ($transactions as &$transaction) {
            $typeOperation = $typeOperationModel->find($transaction['type_operation_id']);
            $transaction['type_libelle'] = $typeOperation ? $typeOperation['libelle'] : 'Inconnu';
            $transaction['type_code'] = $typeOperation ? $typeOperation['code'] : 'inconnu';
            
            $compte = $compteModel->find($transaction['compte_id']);
            $transaction['compte_numero'] = $compte ? $compte['numero_telephone'] : 'Inconnu';
            
            if ($transaction['compte_destination_id']) {
                $compteDestinataire = $compteModel->find($transaction['compte_destination_id']);
                $transaction['destinataire'] = $compteDestinataire ? $compteDestinataire['numero_telephone'] : 'Inconnu';
            } else {
                $transaction['destinataire'] = null;
            }
        }
        
        return view('operateur/transactions', ['transactions' => $transactions]);
    }

    // Liste des comptes clients
    public function comptes()
    {
        $compteModel = new CompteClientModel();
        $transactionModel = new TransactionModel();
        
        $comptes = $compteModel->findAll();
        
        // Enrichir avec le nombre de transactions par compte
        foreach ($comptes as &$compte) {
            $nbTransactions = $transactionModel->where('compte_id', $compte['id'])->countAllResults();
            $compte['nb_transactions'] = $nbTransactions;
        }
        
        return view('operateur/comptes', ['comptes' => $comptes]);
    }
}
