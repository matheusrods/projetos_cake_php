    <?php
    class PropostaCredProdutoFixture extends CakeTestFixture {

    var $name = 'PropostaCredProduto';
    var $table = 'propostas_credenciamento_produto';
 
	var $fields = array (
		'codigo_produto' => array ('type' => 'integer', 'null' => true, 'default' => '', 'length' => 2,),
		'codigo' => array ('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary',),
		'codigo_proposta_credenciamento' => array ('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4,),
		'data_inclusao' => array ('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL,),
	);
    
    var $records = array(
	    array (
	      'codigo_produto' => 1,
	      'codigo' => 1,
	      'codigo_proposta_credenciamento' => 511,
	      'data_inclusao' => '2016-04-26 00:00:00',
	    ),
    );

}

