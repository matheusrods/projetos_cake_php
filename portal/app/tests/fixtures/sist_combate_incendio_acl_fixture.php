<?php

class SistCombateIncendioFixture extends CakeTestFixture {

    var $name = 'SistCombateIncendio';
    var $table = 'sist_combate_incendio';
    
    var $fields = array(
		'localizacao' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'tipo_esguico' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'tipo_engate' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'medida_engate' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'tipo_acionamento_sprinklers' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'acessorios' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ), 
		'codigo_sistema' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'ativo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'tipo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'recarga_meses' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'verificacao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'pesagem' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'tempo_restante' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'codigo_unidade' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'codigo_setor' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'data_fabricacao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'peso_liquido_kg' => array( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 9, ), 
		'revisor' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'fabricante' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'composicao' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'n_serie' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 100, ), 
		'n_ativo_fixo' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 100, ), 
		'diametro_mangueira' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'classe_fogo' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 10, )
    );
    
    var $records = array(
		array (
	      'localizacao' => 'endereco',
	      'tipo_esguico' => 'sad6asd564',
	      'tipo_engate' => 'dasd4as56d4',
	      'medida_engate' => 'asdas',
	      'tipo_acionamento_sprinklers' => '54d5as6d45a6s',
	      'acessorios' => '56456das4d56a4s',
	      'codigo' => 1,
	      'codigo_sistema' => 44,
	      'ativo' => 1,
	      'tipo' => 1,
	      'recarga_meses' => 213,
	      'verificacao' => 2132,
	      'pesagem' => 1323,
	      'tempo_restante' => 3213,
	      'codigo_unidade' => NULL,
	      'codigo_setor' => 12,
	      'data_fabricacao' => NULL,
	      'data_inclusao' => '24/02/2016 10:57:41',
	      'peso_liquido_kg' => '21321.00',
	      'revisor' => 'Resposavel pela Inspeção',
	      'fabricante' => 'Fabricante',
	      'composicao' => 'Composição',
	      'n_serie' => 'asdasd46546',
	      'n_ativo_fixo' => 'asdasd56456',
	      'diametro_mangueira' => 'sd54a4sd56',
	      'classe_fogo' => NULL,
	    ),
	    array (
	      'localizacao' => 'endereco',
	      'tipo_esguico' => 'sad6asd564',
	      'tipo_engate' => 'dasd4as56d4',
	      'medida_engate' => 'asdas',
	      'tipo_acionamento_sprinklers' => '54d5as6d45a6s',
	      'acessorios' => '56456das4d56a4s',
	      'codigo' => 2,
	      'codigo_sistema' => 45,
	      'ativo' => 0,
	      'tipo' => 1,
	      'recarga_meses' => 213,
	      'verificacao' => 2132,
	      'pesagem' => 1323,
	      'tempo_restante' => 3213,
	      'codigo_unidade' => 5,
	      'codigo_setor' => 12,
	      'data_fabricacao' => '01/01/2015 00:00:00',
	      'data_inclusao' => '24/02/2016 11:00:43',
	      'peso_liquido_kg' => '21321.00',
	      'revisor' => 'Resposavel pela Inspeção (alterado)',
	      'fabricante' => 'Fabricante',
	      'composicao' => 'Composição',
	      'n_serie' => 'asdasd46546',
	      'n_ativo_fixo' => 'asdasd56456',
	      'diametro_mangueira' => 'sd54a4sd56',
	      'classe_fogo' => NULL,
	    ),
	    array (
	      'localizacao' => 'asdasd',
	      'tipo_esguico' => 'asdasd',
	      'tipo_engate' => 'asdasd',
	      'medida_engate' => 'sadasd',
	      'tipo_acionamento_sprinklers' => 'asdasd',
	      'acessorios' => 'asdasd',
	      'codigo' => 3,
	      'codigo_sistema' => 46,
	      'ativo' => 1,
	      'tipo' => 2,
	      'recarga_meses' => 213,
	      'verificacao' => 2132,
	      'pesagem' => 1323,
	      'tempo_restante' => 3213,
	      'codigo_unidade' => 5,
	      'codigo_setor' => 1,
	      'data_fabricacao' => '01/01/2001 00:00:00',
	      'data_inclusao' => '24/02/2016 11:28:52',
	      'peso_liquido_kg' => '21321.00',
	      'revisor' => 'Resposavel -- teste',
	      'fabricante' => 'asdasd',
	      'composicao' => 'Composição',
	      'n_serie' => 'asdasd',
	      'n_ativo_fixo' => 'asdasdqa',
	      'diametro_mangueira' => 'asdasd',
	      'classe_fogo' => NULL,
	    ),
    );
}


