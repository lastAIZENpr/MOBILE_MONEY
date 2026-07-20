<?php

namespace App\Controllers;

use App\Models\PrefixModel;

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
}
