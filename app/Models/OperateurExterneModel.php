<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurExterneModel extends Model
{
    protected $table = 'operateurs_externes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['nom', 'taux_commission'];
    protected $useTimestamps = false;
    protected $validationRules = [
        'nom' => 'required|min_length[2]|max_length[100]',
        'taux_commission' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
    ];
    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom de l\'opérateur est requis',
            'min_length' => 'Le nom doit contenir au moins 2 caractères',
            'max_length' => 'Le nom ne peut pas dépasser 100 caractères'
        ],
        'taux_commission' => [
            'required' => 'Le taux de commission est requis',
            'numeric' => 'Le taux de commission doit être un nombre',
            'greater_than_equal_to' => 'Le taux de commission doit être positif',
            'less_than_equal_to' => 'Le taux de commission ne peut pas dépasser 100%'
        ]
    ];
}
