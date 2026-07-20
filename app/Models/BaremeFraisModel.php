<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table = 'baremes_frais';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['type_operation_id', 'montant_min', 'montant_max', 'frais'];
    protected $useTimestamps = false;
}
