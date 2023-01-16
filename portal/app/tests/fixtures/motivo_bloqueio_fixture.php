<?php
class MotivoBloqueioFixture extends CakeTestFixture {
	var $name = 'MotivoBloqueio';
	var $table = 'motivo_bloqueio';
	
	var $fields = array (
		  'codigo' => array ('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,),
		  'descricao' => array ('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 255,),
		  'data_inclusao' => array ('type' => 'datetime', 'null' => false, 'default' => NULL, 'length' => NULL,),
		  'codigo_usuario_inclusao' => array ('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,),
	);
	
	var $records = array(
	    array ('codigo' => 1, 'descricao' => 'OK', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 2, 'descricao' => 'PENDÊNCIA FINANCEIRA', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 3, 'descricao' => 'SOLICITAÇÃO DO CLIENTE', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 4, 'descricao' => 'SOLICITAÇÃO DA SEGURADORA', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 5, 'descricao' => 'SOLICITAÇÃO DA CORRETORA', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 6, 'descricao' => 'NÃO ENVIO DO CONTRATO', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 7, 'descricao' => 'CONTRATO EXPIRADO', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 8, 'descricao' => 'EMPRESA BLOQUEADA', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 9, 'descricao' => 'EMPRESA INATIVA C/ PEND.FINANC.', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 10, 'descricao' => 'SOLICIT.CLIENTE C/ PEND.FINANC.', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 11, 'descricao' => 'DADOS CADASTRAIS DESATUALIZADOS', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 12, 'descricao' => 'CÓDIGO COBRADOR C/ PENDÊNCIA FINANCEIRA', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 13, 'descricao' => 'PENDÊNCIA FINANCEIRA / NÃO ENVIO DE CONTRATO (1/5)', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 14, 'descricao' => 'CÓDIGO COBRADOR COM PENDÊNCIA FINANCEIRA / NÃO ENVIO DE CONTRATO (11/5)', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 15, 'descricao' => 'EMPRESA INATIVA C/ PEND.FINANC / NÃO ENVIO DO CONTRATO', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 16, 'descricao' => 'DADOS CADASTRAIS DESATUALIZADOS / NÃO ENVIO DO CONTRATO', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 17, 'descricao' => 'CANCELAMENTO', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
	    array ('codigo' => 18, 'descricao' => 'IMPLANTACAO', 'data_inclusao' => '2009-04-03 18:08:43', 'codigo_usuario_inclusao' => 1,),
			
    );
}
?>