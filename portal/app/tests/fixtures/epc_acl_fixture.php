<?php

class EpcFixture extends CakeTestFixture {

    var $name = 'Epc';
    var $table = 'epc';
    
    var $fields = array(
		'metodo_avaliacao_atenuacao' =>  array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'observacao' =>  array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'codigo' =>  array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ), 
		'ativo' =>  array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'atenuacao_qtd' =>  array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'atenuacao_medida' =>  array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'quantidade' =>  array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'instalacao' =>  array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'revisao' =>  array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'data_inclusao' =>  array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'custo' =>  array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 9, ), 
		'nome' =>  array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'validade_meses' =>  array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'riscos_selecionados' =>  array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, )
    );
    
    var $records = array(
	    array (
	      'metodo_avaliacao_atenuacao' => 'metodo',
	      'observacao' => 'observação (alterado)',
	      'codigo' => 1,
	      'ativo' => 1,
	      'atenuacao_qtd' => 10,
	      'atenuacao_medida' => 1,
	      'quantidade' => 10,
	      'instalacao' => '01/02/2016 00:00:00',
	      'revisao' => '23/02/2016 00:00:00',
	      'data_inclusao' => '23/02/2016 16:59:59',
	      'custo' => '20.00',
	      'nome' => 'nome EPC  (alterado)',
	      'validade_meses' => '2',
	      'riscos_selecionados' => '4',
	    ),
	    array (
	      'metodo_avaliacao_atenuacao' => 'metodo de avaliação',
	      'observacao' => 'observação (alterado)',
	      'codigo' => 1,
	      'ativo' => 1,
	      'atenuacao_qtd' => 10,
	      'atenuacao_medida' => 1,
	      'quantidade' => 10,
	      'instalacao' => '01/02/2016 00:00:00',
	      'revisao' => '23/02/2016 00:00:00',
	      'data_inclusao' => '23/02/2016 16:59:59',
	      'custo' => '20.00',
	      'nome' => 'nome EPC  (alterado)',
	      'validade_meses' => '2',
	      'riscos_selecionados' => '4',
	    ),
	    array (
	      'metodo_avaliacao_atenuacao' => 'metodo de avaliação da atenuação',
	      'observacao' => 'observação (alterado)',
	      'codigo' => 1,
	      'ativo' => 1,
	      'atenuacao_qtd' => 10,
	      'atenuacao_medida' => 1,
	      'quantidade' => 10,
	      'instalacao' => '01/02/2016 00:00:00',
	      'revisao' => '23/02/2016 00:00:00',
	      'data_inclusao' => '23/02/2016 16:59:59',
	      'custo' => '20.00',
	      'nome' => 'nome EPC  (alterado)',
	      'validade_meses' => '2',
	      'riscos_selecionados' => '4',
	    ),            
    );
}



