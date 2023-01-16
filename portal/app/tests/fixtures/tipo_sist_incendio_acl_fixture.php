<?php

class TipoSistIncendioFixture extends CakeTestFixture {

    var $name = 'TipoSistIncendio';
    var $table = 'tipos_sist_incendio';
    
    var $fields = array(
		'descricao_detalhada' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'fabricante' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'tipo_esguico' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'tipo_engate' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'medida_engate' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'tipo_acionamento_sprinklers' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'acessorios' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'observacao' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => 16, ), 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ), 
		'periodicidade_recarga' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'periodicidade_verificacao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'tempo_restante' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'nome' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'composicao' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'peso_liquido' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'diametro_mangueira' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
		'classe_fogo' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, )
    );
    
    var $records = array(
	    array (
	      'descricao_detalhada' => 'bla bla bla bla bla',
	      'fabricante' => 'Fabricante',
	      'tipo_esguico' => 'tipo d eesguiço',
	      'tipo_engate' => 'engate',
	      'medida_engate' => 'engate',
	      'tipo_acionamento_sprinklers' => 'ancionamento sprinklers',
	      'acessorios' => 'acessorios',
	      'observacao' => 'obs..............',
	      'codigo' => 1,
	      'periodicidade_recarga' => 12,
	      'periodicidade_verificacao' => 12,
	      'tempo_restante' => 12,
	      'data_inclusao' => '24/02/2016 09:51:50',
	      'nome' => 'Sistema de Incêndio',
	      'composicao' => 'Composição',
	      'peso_liquido' => '12',
	      'diametro_mangueira' => 'diametro mangueira',
	      'classe_fogo' => '0,1',
	    ),
	    array (
	      'descricao_detalhada' => 'dasd',
	      'fabricante' => 'asdasd',
	      'tipo_esguico' => '4',
	      'tipo_engate' => '564',
	      'medida_engate' => '564',
	      'tipo_acionamento_sprinklers' => '564',
	      'acessorios' => '654',
	      'observacao' => '564',
	      'codigo' => 2,
	      'periodicidade_recarga' => 456,
	      'periodicidade_verificacao' => 4564,
	      'tempo_restante' => 55,
	      'data_inclusao' => '24/02/2016 10:05:14',
	      'nome' => 'Sistema de DASD',
	      'composicao' => '564',
	      'peso_liquido' => '56465',
	      'diametro_mangueira' => '564',
	      'classe_fogo' => '0,3',
	    ),
	    array (
	      'descricao_detalhada' => 'asasdasd',
	      'fabricante' => ' ',
	      'tipo_esguico' => ' ',
	      'tipo_engate' => ' ',
	      'medida_engate' => ' ',
	      'tipo_acionamento_sprinklers' => ' ',
	      'acessorios' => ' ',
	      'observacao' => ' ',
	      'codigo' => 3,
	      'periodicidade_recarga' => NULL,
	      'periodicidade_verificacao' => NULL,
	      'tempo_restante' => NULL,
	      'data_inclusao' => '24/02/2016 10:13:29',
	      'nome' => 'Sistema Teste',
	      'composicao' => ' ',
	      'peso_liquido' => ' ',
	      'diametro_mangueira' => ' ',
	      'classe_fogo' => '2,3',
	    ),
    );
}



