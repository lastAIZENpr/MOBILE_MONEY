<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['compte_id', 'type_operation_id', 'montant', 'frais', 'solde_apres', 'date_transaction', 'compte_destination_id'];
    protected $useTimestamps = false;
}
