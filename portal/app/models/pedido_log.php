<?php

class PedidoLog extends AppModel {

    public $name = 'PedidoLog';
    public $databaseTable = 'RHHealth';
    public $tableSchema = 'dbo';
    public $useTable = 'pedidos_log';
    public $primaryKey = 'codigo';
    public $foreignKeyLog = 'codigo_pedido';
    public $actsAs = array('Secure');
    
}
