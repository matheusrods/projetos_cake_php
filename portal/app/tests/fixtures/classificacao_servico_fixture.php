<?php
class ClassificacaoServicoFixture extends CakeTestFixture {
	public $name    = 'ClassificacaoServico';
	public $table   = 'classificacao_servico';
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
        'type' => 'datetime',
        'null' => false,
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
      'codigo_empresa' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
        )
      );
	
    public $records = array(
      array (
        'codigo' => 3,
        'codigo_usuario_inclusao' => 1,
        'codigo_empresa' => 2,
        'data_inclusao' => '2017-05-31 17:57:47',
        'descricao' => 'Planos de Saúde',
        ),
      array (
        'codigo' => 2,
        'codigo_usuario_inclusao' => 1,
        'codigo_empresa' => 2,
        'data_inclusao' => '2017-05-31 17:57:47',
        'descricao' => 'Exames',
        ),
      array (
        'codigo' => 1,
        'codigo_usuario_inclusao' => 1,
        'codigo_empresa' => 2,
        'data_inclusao' => '2017-05-31 17:57:47',
        'descricao' => 'Consultas',
        ),
      );
}