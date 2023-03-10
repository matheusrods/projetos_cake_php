<?php

class EpiFixture extends CakeTestFixture {

    var $name = 'Epi';
    var $table = 'epi';
    
    var $fields = array(
		'especificacoes' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'uso' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'higienizacao' =>  array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'conservacao' =>  array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'fornecimento' =>  array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'metodo_avaliacao_atenuacao' =>  array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'ativo' =>  array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'reposicao_qtd' =>  array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'atenuacao_qtd' =>  array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'atenuacao_medida' =>  array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'tamanho_epi_funcionario' =>  array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'data_fabricacao_crf' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'data_importacao_cri' =>  array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'data_validade_ca' =>  array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'data_inclusao' =>  array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'custo' =>  array( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 9, ), 
		'nome' =>  array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'substituicao' =>  array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'reposicao_medida_prazo' =>  array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 10, ), 
		'fabricante' =>  array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'descricao_crf' =>  array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'descricao_cri' =>  array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'descricao_ca' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'riscos_selecionados' =>  array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, )
    );
    
    var $records = array(
	   array (
	      'especificacoes' => 'Especifica????es 1',
	      'uso' => 'Uso',
	      'higienizacao' => 'Hizieniza????o',
	      'conservacao' => 'Conserva????o',
	      'fornecimento' => 'Fornecimento',
	      'metodo_avaliacao_atenuacao' => 'Metodo Avalia????o de Atenua????o',
	      'ativo' => 1,
	      'reposicao_qtd' => 123,
	      'atenuacao_qtd' => 123,
	      'atenuacao_medida' => 1,
	      'tamanho_epi_funcionario' => 1,
	      'data_fabricacao_crf' => '01/01/2016 00:00:00',
	      'data_importacao_cri' => '01/01/2016 00:00:00',
	      'data_validade_ca' => '01/01/2016 00:00:00',
	      'data_inclusao' => '23/02/2016 16:07:10',
	      'custo' => '10.00',
	      'nome' => 'EPI Nome (Alterado)',
	      'substituicao' => 'Substitui????o',
	      'reposicao_medida_prazo' => '2',
	      'fabricante' => 'Fabricante',
	      'descricao_crf' => 'CRF',
	      'descricao_cri' => 'CRI',
	      'descricao_ca' => 'Descri????o CA',
	      'riscos_selecionados' => '5',
	    ),
	    array (
	      'especificacoes' => 'Especifica????es 2',
	      'uso' => 'Uso',
	      'higienizacao' => 'Hizieniza????o',
	      'conservacao' => 'Conserva????o',
	      'fornecimento' => 'Fornecimento',
	      'metodo_avaliacao_atenuacao' => 'Metodo Avalia????o de Atenua????o',
	      'ativo' => 1,
	      'reposicao_qtd' => 123,
	      'atenuacao_qtd' => 123,
	      'atenuacao_medida' => 1,
	      'tamanho_epi_funcionario' => 1,
	      'data_fabricacao_crf' => '01/01/2016 00:00:00',
	      'data_importacao_cri' => '01/01/2016 00:00:00',
	      'data_validade_ca' => '01/01/2016 00:00:00',
	      'data_inclusao' => '23/02/2016 16:07:10',
	      'custo' => '10.00',
	      'nome' => 'EPI Nome (Alterado)',
	      'substituicao' => 'Substitui????o',
	      'reposicao_medida_prazo' => '2',
	      'fabricante' => 'Fabricante',
	      'descricao_crf' => 'CRF',
	      'descricao_cri' => 'CRI',
	      'descricao_ca' => 'Descri????o CA',
	      'riscos_selecionados' => '5',
	    ),
	    array (
	      'especificacoes' => 'Especifica????es 3',
	      'uso' => 'Uso',
	      'higienizacao' => 'Hizieniza????o',
	      'conservacao' => 'Conserva????o',
	      'fornecimento' => 'Fornecimento',
	      'metodo_avaliacao_atenuacao' => 'Metodo Avalia????o de Atenua????o',
	      'ativo' => 1,
	      'reposicao_qtd' => 123,
	      'atenuacao_qtd' => 123,
	      'atenuacao_medida' => 1,
	      'tamanho_epi_funcionario' => 1,
	      'data_fabricacao_crf' => '01/01/2016 00:00:00',
	      'data_importacao_cri' => '01/01/2016 00:00:00',
	      'data_validade_ca' => '01/01/2016 00:00:00',
	      'data_inclusao' => '23/02/2016 16:07:10',
	      'custo' => '10.00',
	      'nome' => 'EPI Nome (Alterado)',
	      'substituicao' => 'Substitui????o',
	      'reposicao_medida_prazo' => '2',
	      'fabricante' => 'Fabricante',
	      'descricao_crf' => 'CRF',
	      'descricao_cri' => 'CRI',
	      'descricao_ca' => 'Descri????o CA',
	      'riscos_selecionados' => '5',
	    ), 
    );
}



