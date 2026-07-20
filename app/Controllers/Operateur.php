<?php

namespace App\Controllers;

use App\Models\PrefixModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use App\Models\CompteClientModel;
use App\Models\TransactionModel;
use App\Models\OperateurExterneModel;

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
        $operateurExterneModel = new OperateurExterneModel();
        
        $prefixes = $prefixModel->findAll();
        
        // Enrichir avec le nom de l'opérateur externe
        foreach ($prefixes as &$prefix) {
            if ($prefix['operateur_externe_id']) {
                $operateur = $operateurExterneModel->find($prefix['operateur_externe_id']);
                $prefix['operateur_nom'] = $operateur ? $operateur['nom'] : 'Inconnu';
            } else {
                $prefix['operateur_nom'] = null;
            }
        }
        
        return view('operateur/prefixes', ['prefixes' => $prefixes]);
    }

    public function prefixCreate()
    {
        $operateurExterneModel = new OperateurExterneModel();
        $operateurs = $operateurExterneModel->findAll();
        
        return view('operateur/prefix_create', ['operateurs' => $operateurs]);
    }

    public function prefixStore()
    {
        $prefixModel = new PrefixModel();
        
        $prefixe = $this->request->getPost('prefixe');
        $operateurExterneId = $this->request->getPost('operateur_externe_id');
        
        // Validation : format 2-3 chiffres
        if (!preg_match('/^\d{2,3}$/', $prefixe)) {
            return redirect()->back()->with('error', 'Le préfixe doit contenir 2 ou 3 chiffres uniquement');
        }
        
        // Validation : unicité
        $existing = $prefixModel->where('prefixe', $prefixe)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Ce préfixe existe déjà');
        }
        
        // Si opérateur_externe_id est vide, le mettre à NULL (notre opérateur)
        $operateurExterneId = !empty($operateurExterneId) ? $operateurExterneId : null;
        
        $prefixModel->insert([
            'prefixe' => $prefixe,
            'actif' => 1,
            'operateur_externe_id' => $operateurExterneId
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

    // Situation des gains
    public function gains()
    {
        $transactionModel = new TransactionModel();
        $typeOperationModel = new TypeOperationModel();
        $compteModel = new CompteClientModel();
        $prefixModel = new PrefixModel();
        $operateurExterneModel = new OperateurExterneModel();
        
        $transactions = $transactionModel->where('type_operation_id', 3)->findAll(); // Transferts
        
        $gainsNotreOperateur = 0;
        $gainsAutresOperateurs = [];
        
        foreach ($transactions as $transaction) {
            if ($transaction['compte_destination_id']) {
                $compteDestinataire = $compteModel->find($transaction['compte_destination_id']);
                if ($compteDestinataire) {
                    $prefixeDestinataire = substr($compteDestinataire['numero_telephone'], 0, 3);
                    $prefix = $prefixModel->where('prefixe', $prefixeDestinataire)->first();
                    
                    if ($prefix && $prefix['operateur_externe_id']) {
                        $operateurExterne = $operateurExterneModel->find($prefix['operateur_externe_id']);
                        if ($operateurExterne) {
                            $nomOperateur = $operateurExterne['nom'];
                            // Calculer la commission (frais - frais de base)
                            $fraisBase = $transaction['frais'];
                            // On estime que la commission est une partie des frais
                            // Pour simplifier, on considère que tous les frais de transfert externe sont des commissions
                            if (!isset($gainsAutresOperateurs[$nomOperateur])) {
                                $gainsAutresOperateurs[$nomOperateur] = 0;
                            }
                            $gainsAutresOperateurs[$nomOperateur] += $fraisBase;
                        }
                    } else {
                        // Transfert interne - gains pour notre opérateur
                        $gainsNotreOperateur += $transaction['frais'];
                    }
                }
            }
        }
        
        $data = [
            'gains_notre_operateur' => $gainsNotreOperateur,
            'gains_autres_operateurs' => $gainsAutresOperateurs
        ];
        
        return view('operateur/gains', $data);
    }

    // Situation des montants à envoyer aux opérateurs externes
    public function montantsOperateurs()
    {
        $transactionModel = new TransactionModel();
        $typeOperationModel = new TypeOperationModel();
        $compteModel = new CompteClientModel();
        $prefixModel = new PrefixModel();
        $operateurExterneModel = new OperateurExterneModel();
        
        $transactions = $transactionModel->where('type_operation_id', 3)->findAll(); // Transferts
        
        $montantsParOperateur = [];
        
        foreach ($transactions as $transaction) {
            if ($transaction['compte_destination_id']) {
                $compteDestinataire = $compteModel->find($transaction['compte_destination_id']);
                if ($compteDestinataire) {
                    $prefixeDestinataire = substr($compteDestinataire['numero_telephone'], 0, 3);
                    $prefix = $prefixModel->where('prefixe', $prefixeDestinataire)->first();
                    
                    if ($prefix && $prefix['operateur_externe_id']) {
                        $operateurExterne = $operateurExterneModel->find($prefix['operateur_externe_id']);
                        if ($operateurExterne) {
                            $nomOperateur = $operateurExterne['nom'];
                            // Ajouter le montant principal (sans les frais)
                            if (!isset($montantsParOperateur[$nomOperateur])) {
                                $montantsParOperateur[$nomOperateur] = 0;
                            }
                            $montantsParOperateur[$nomOperateur] += $transaction['montant'];
                        }
                    }
                }
            }
        }
        
        $data = [
            'montants_par_operateur' => $montantsParOperateur
        ];
        
        return view('operateur/montants_operateurs', $data);
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

    // CRUD des opérateurs externes
    public function operateurs()
    {
        $operateurModel = new OperateurExterneModel();
        $operateurs = $operateurModel->findAll();
        
        return view('operateur/operateurs', ['operateurs' => $operateurs]);
    }

    public function operateurCreate()
    {
        return view('operateur/operateur_create');
    }

    public function operateurStore()
    {
        $operateurModel = new OperateurExterneModel();
        
        $nom = $this->request->getPost('nom');
        $tauxCommission = $this->request->getPost('taux_commission');
        
        if (!$operateurModel->insert([
            'nom' => $nom,
            'taux_commission' => $tauxCommission
        ])) {
            return redirect()->back()->with('error', 'Erreur lors de la création de l\'opérateur: ' . implode(', ', $operateurModel->errors()));
        }
        
        return redirect()->to('/operateur/operateurs')->with('success', 'Opérateur externe ajouté avec succès');
    }

    public function operateurEdit($id)
    {
        $operateurModel = new OperateurExterneModel();
        $operateur = $operateurModel->find($id);
        
        if (!$operateur) {
            return redirect()->to('/operateur/operateurs')->with('error', 'Opérateur non trouvé');
        }
        
        return view('operateur/operateur_edit', ['operateur' => $operateur]);
    }

    public function operateurUpdate($id)
    {
        $operateurModel = new OperateurExterneModel();
        
        $nom = $this->request->getPost('nom');
        $tauxCommission = $this->request->getPost('taux_commission');
        
        if (!$operateurModel->update($id, [
            'nom' => $nom,
            'taux_commission' => $tauxCommission
        ])) {
            return redirect()->back()->with('error', 'Erreur lors de la modification de l\'opérateur: ' . implode(', ', $operateurModel->errors()));
        }
        
        return redirect()->to('/operateur/operateurs')->with('success', 'Opérateur externe modifié avec succès');
    }

    public function operateurDelete($id)
    {
        $operateurModel = new OperateurExterneModel();
        $prefixModel = new PrefixModel();
        
        // Vérifier si des préfixes sont associés à cet opérateur
        $prefixesAssocies = $prefixModel->where('operateur_externe_id', $id)->countAllResults();
        if ($prefixesAssocies > 0) {
            return redirect()->to('/operateur/operateurs')->with('error', 'Impossible de supprimer: des préfixes sont associés à cet opérateur');
        }
        
        $operateurModel->delete($id);
        
        return redirect()->to('/operateur/operateurs')->with('success', 'Opérateur externe supprimé avec succès');
    }
}
