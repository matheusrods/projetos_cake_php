	<?php

	class RiscoAtributoFixture extends CakeTestFixture {

		public $name = 'RiscoAtributo';
		public $table = 'riscos_atributos';

		public $fields = array (
			'codigo' => 
			array (
				'type' => 'integer',
				'null' => false,
				'default' => NULL,
				'length' => NULL,
				'key' => 'primary',
				),
			'descricao' => 
			array (
				'type' => 'string',
				'null' => false,
				'default' => NULL,
				'length' => 255,
				),
			'data_inclusao' => 
			array (
				'type' => 'date',
				'null' => true,
				'default' => NULL,
				'length' => NULL,
				),
			'codigo_usuario_inclusao' => 
			array (
				'type' => 'integer',
				'null' => false,
				'default' => NULL,
				'length' => NULL,
				),
			);

		public $records = array (
			array (
				'data_inclusao' => '2016-07-19 09:57:56',
				'codigo' => 1,
				'codigo_usuario_inclusao' => 61648,
				'descricao' => 'Classificação Efeito Crítico',
				),
			array (
				'data_inclusao' => '2016-07-18 09:55:56',
				'codigo' => 2,
				'codigo_usuario_inclusao' => 61644,
				'descricao' => 'Meios  de Exposição',
				),
			array (
				'data_inclusao' => '2016-07-17 09:53:56',
				'codigo' => 3,
				'codigo_usuario_inclusao' => 61653,
				'descricao' => 'Catastrofes ambientais',
				),
			array (
				'data_inclusao' => '2016-07-16 09:48:56',
				'codigo' => 4,
				'codigo_usuario_inclusao' => 61442,
				'descricao' => 'Acidentes de trabalho',
				),
			array (
				'data_inclusao' => '2016-07-15 09:45:56',
				'codigo' => 5,
				'codigo_usuario_inclusao' => 61478,
				'descricao' => 'Efeito estufa',
				),
			array (
				'data_inclusao' => '2016-07-14 09:41:56',
				'codigo' => 6,
				'codigo_usuario_inclusao' => 61985,
				'descricao' => 'Efeitos da natureza',
				),
			array (
				'data_inclusao' => '2016-07-13 09:37:56',
				'codigo' => 7,
				'codigo_usuario_inclusao' => 61258,
				'descricao' => 'Campo eletromagnético',
				),
			array (
				'data_inclusao' => '2016-07-12 09:33:56',
				'codigo' => 8,
				'codigo_usuario_inclusao' => 61147,
				'descricao' => 'Acidentes com maquinários',
				),
			array (
				'data_inclusao' => '2016-07-11 09:29:56',
				'codigo' => 9,
				'codigo_usuario_inclusao' => 61369,
				'descricao' => 'Acidente industrial',
				),
			array (
				'data_inclusao' => '2016-07-10 09:25:56',
				'codigo' => 10,
				'codigo_usuario_inclusao' => 61987,
				'descricao' => 'Incêndios',
				),
			);

	}



