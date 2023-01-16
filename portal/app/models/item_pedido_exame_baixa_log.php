<?php

class ItemPedidoExameBaixaLog extends AppModel {

	public $name = 'ItemPedidoExameBaixaLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'itens_pedidos_exames_baixa_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_itens_pedidos_exames_baixa';
	public $actsAs = array('Secure');
    public $validate = array(
        'codigo_itens_pedidos_exames_baixa' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}