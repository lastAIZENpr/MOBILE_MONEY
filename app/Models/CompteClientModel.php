<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteClientModel extends Model
{
    protected $table = 'comptes_clients';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['numero_telephone', 'solde', 'date_creation','pourcentage_epargne','solde_epargne'];
    protected $useTimestamps = false;
    
}
