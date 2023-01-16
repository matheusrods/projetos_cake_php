<?php
class ProcessamentoPedidoExame extends AppModel {
    public $name            = 'ProcessamentoPedidoExame';
    public $tableSchema     = 'dbo';
    public $databaseTable   = 'RHHealth';
    public $useTable        = 'processamento_pedidos_exames';
    public $primaryKey      = 'codigo';
    public $actsAs          = array('Secure', 'Containable');
    public $recursive       = -1;

}