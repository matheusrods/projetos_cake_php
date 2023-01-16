<?php
class ProdutoServicoFixture extends CakeTestFixture {
  public $name = 'ProdutoServico';
  public $table = 'produto_servico';
  
  public $fields = array(
    'codigo' => 
    array(
      'type' => 'integer',
      'null' => false,
      'default' => NULL,
      'length' => NULL,
      'key' => 'primary',
      ),
    'codigo_produto' => 
    array(
      'type' => 'integer',
      'null' => false,
      'default' => NULL,
      'length' => NULL,
      ),
    'codigo_servico' => 
    array(
      'type' => 'integer',
      'null' => false,
      'default' => NULL,
      'length' => NULL,
      ),
    'data_inclusao' => 
    array(
      'type' => 'datetime',
      'null' => false,
      'default' => '(getdate())',
      'length' => NULL,
      ),
    'codigo_usuario_inclusao' => 
    array(
      'type' => 'integer',
      'null' => false,
      'default' => NULL,
      'length' => NULL,
      ),
    'ativo' => 
    array(
      'type' => 'integer',
      'null' => false,
      'default' => '((1))',
      'length' => NULL,
      ),
    'codigo_empresa' => 
    array(
      'type' => 'integer',
      'null' => true,
      'default' => NULL,
      'length' => NULL,
      ),
    'codigo_antigo' => 
    array(
      'type' => 'integer',
      'null' => true,
      'default' => NULL,
      'length' => NULL,
      ),
    );

  var $records = array(
    array(
      'codigo_produto' => 118,
      'codigo_servico' => 6466,
      'codigo' => 4519,
      'codigo_usuario_inclusao' => 2,
      'codigo_empresa' => 2,
      'codigo_antigo' => NULL,
      'data_inclusao' => '2017-05-31 18:07:21',
      'ativo' => 1,
      ),
    array(
      'codigo_produto' => 118,
      'codigo_servico' => 6465,
      'codigo' => 4518,
      'codigo_usuario_inclusao' => 2,
      'codigo_empresa' => 2,
      'codigo_antigo' => NULL,
      'data_inclusao' => '2017-05-31 18:07:09',
      'ativo' => 1,
      ),
    array(
      'codigo_produto' => 118,
      'codigo_servico' => 6464,
      'codigo' => 4517,
      'codigo_usuario_inclusao' => 2,
      'codigo_empresa' => 2,
      'codigo_antigo' => NULL,
      'data_inclusao' => '2017-05-31 18:06:52',
      'ativo' => 1,
      ),
    array(
      'codigo_produto' => 109,
      'codigo_servico' => 4334,
      'codigo' => 4496,
      'codigo_usuario_inclusao' => 61913,
      'codigo_empresa' => 2,
      'codigo_antigo' => NULL,
      'data_inclusao' => '2017-05-03 15:46:33',
      'ativo' => 1,
      ),
    array(
      'codigo_produto' => 110,
      'codigo_servico' => 4239,
      'codigo' => 3504,
      'codigo_usuario_inclusao' => 61913,
      'codigo_empresa' => 2,
      'codigo_antigo' => NULL,
      'data_inclusao' => '2017-05-02 15:14:33',
      'ativo' => 1,
      ),
    array(
      'codigo_produto' => 110,
      'codigo_servico' => 3580,
      'codigo' => 3501,
      'codigo_usuario_inclusao' => 61913,
      'codigo_empresa' => 2,
      'codigo_antigo' => NULL,
      'data_inclusao' => '2017-05-02 15:09:07',
      'ativo' => 1,
      ),
    array(
      'codigo_produto' => 110,
      'codigo_servico' => 3447,
      'codigo' => 3500,
      'codigo_usuario_inclusao' => 61913,
      'codigo_empresa' => 2,
      'codigo_antigo' => NULL,
      'data_inclusao' => '2017-05-02 15:08:25',
      'ativo' => 1,
      ),
    array(
      'codigo_produto' => 110,
      'codigo_servico' => 4236,
      'codigo' => 3499,
      'codigo_usuario_inclusao' => 61913,
      'codigo_empresa' => 2,
      'codigo_antigo' => NULL,
      'data_inclusao' => '2017-05-02 11:34:21',
      'ativo' => 1,
      ),
    array(
      'codigo_produto' => 110,
      'codigo_servico' => 4235,
      'codigo' => 3498,
      'codigo_usuario_inclusao' => 61913,
      'codigo_empresa' => 2,
      'codigo_antigo' => NULL,
      'data_inclusao' => '2017-05-02 11:34:02',
      'ativo' => 1,
      ),
    array(
      'codigo_produto' => 110,
      'codigo_servico' => 4234,
      'codigo' => 3497,
      'codigo_usuario_inclusao' => 61913,
      'codigo_empresa' => 2,
      'codigo_antigo' => NULL,
      'data_inclusao' => '2017-05-02 11:33:37',
      'ativo' => 1,
      ),
    );
}