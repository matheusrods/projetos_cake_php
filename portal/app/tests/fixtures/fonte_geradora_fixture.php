<?
class FonteGeradoraFixture extends CakeTestFixture {
	var $name = 'FonteGeradora';
	var $table = 'fontes_geradoras';

	var $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'nome' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
		'ativo' => array( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
		'local' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
		'observacao' => array( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);

	var $records = array( 
		array(
		  'codigo' => 2,
		  'nome' => 'MÁQUINAS E EQUIPAMENTOS',
		  'ativo' => 1,
		  'local' => ' ',
		  'observacao' => ' ',
		  'data_inclusao' => '14/03/2017 11:47:09',
		  'codigo_usuario_inclusao' => 61802,
		),
		array(
		  'codigo' => 3,
		  'nome' => 'Ao realizar limpeza dos setores e banheiros da empresa ',
		  'ativo' => 1,
		  'local' => ' ',
		  'observacao' => ' ',
		  'data_inclusao' => '01/09/2017 10:21:45',
		  'codigo_usuario_inclusao' => 61802,
		),
		array(
		  'codigo' => 4,
		  'nome' => 'Ao realizar limpeza de sanitários e retirada de lixos',
		  'ativo' => 1,
		  'local' => ' ',
		  'observacao' => ' ',
		  'data_inclusao' => '01/09/2017 10:29:20',
		  'codigo_usuario_inclusao' => 61802,
		),
		array(
		  'codigo' => 5,
		  'nome' => 'Ao realizar limpeza da copa/refeitório',
		  'ativo' => 1,
		  'local' => ' ',
		  'observacao' => ' ',
		  'data_inclusao' => '01/09/2017 12:37:48',
		  'codigo_usuario_inclusao' => 61802,
		),
		array(
		  'codigo' => 6,
		  'nome' => 'Ambiente Administrativo (Conversação, telefone, impressora)',
		  'ativo' => 1,
		  'local' => ' ',
		  'observacao' => ' ',
		  'data_inclusao' => '17/01/2018 16:47:02',
		  'codigo_usuario_inclusao' => 61802,
		),
		array(
		  'codigo' => 7,
		  'nome' => 'Ruído de fundo (Operação) / Passagem de veículos na portaria',
		  'ativo' => 1,
		  'local' => ' ',
		  'observacao' => ' ',
		  'data_inclusao' => '17/01/2018 17:27:58',
		  'codigo_usuario_inclusao' => 61802,
		),
		array(
		  'codigo' => 8,
		  'nome' => 'Temperatura Ambiente (Trabalho a céu aberto)',
		  'ativo' => 1,
		  'local' => ' ',
		  'observacao' => ' ',
		  'data_inclusao' => '17/01/2018 17:37:46',
		  'codigo_usuario_inclusao' => 61802,
		),
		array(
		  'codigo' => 9,
		  'nome' => 'Raios Solar (Trabalho a céu aberto)',
		  'ativo' => 1,
		  'local' => ' ',
		  'observacao' => ' ',
		  'data_inclusao' => '17/01/2018 17:59:51',
		  'codigo_usuario_inclusao' => 61802,
		),
		array(
		  'codigo' => 10,
		  'nome' => 'Proximidades do processo produtivo em plataforma e utilização de máquinas e equipamentos.',
		  'ativo' => 1,
		  'local' => ' ',
		  'observacao' => ' ',
		  'data_inclusao' => '19/01/2018 16:53:46',
		  'codigo_usuario_inclusao' => 61802,
		),
		array(
		  'codigo' => 11,
		  'nome' => 'Máquina de Solda (Solda Elétrica)',
		  'ativo' => 1,
		  'local' => ' ',
		  'observacao' => ' ',
		  'data_inclusao' => '19/01/2018 17:04:14',
		  'codigo_usuario_inclusao' => 61802,
		),
		array(
		  'codigo' => 12,
		  'nome' => 'teste importacao exclusao',
		  'ativo' => 1,
		  'local' => ' ',
		  'observacao' => ' ',
		  'data_inclusao' => '01/01/2018 00:00:0',
		  'codigo_usuario_inclusao' => 67111,
		),
	);

}