<?php

class ItemPedidoExameBaixaFixture extends CakeTestFixture {

    var $name = 'ItemPedidoExameBaixa';
    var $table = 'itens_pedidos_exames_baixa';
    
    var $fields = array( 
    'descricao' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
    'data_validade' => array ( 'type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
    'data_realizacao_exame' => array ( 'type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
    'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
    'codigo_itens_pedidos_exames' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'resultado' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_aparelho_audiometrico' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_usuario_alteracao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
    'fornecedor_particular' => array ( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
    'pedido_importado' => array ( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
    );

    var $records = array(

            array(
              'descricao' => NULL,
              'data_validade' => NULL,
              'data_realizacao_exame' => '2017-07-01',
              'codigo' => 791,
              'codigo_itens_pedidos_exames' => 4592,
              'resultado' => 1,
              'codigo_aparelho_audiometrico' => NULL,
              'codigo_usuario_inclusao' => 64915,
              'data_inclusao' => '2017-07-20 11:56:35',
            ),
          
            array(
              'descricao' => NULL,
              'data_validade' => NULL,
              'data_realizacao_exame' => '2017-07-17',
              'codigo' => 790,
              'codigo_itens_pedidos_exames' => 4511,
              'resultado' => 1,
              'codigo_aparelho_audiometrico' => NULL,
              'codigo_usuario_inclusao' => 63085,
              'data_inclusao' => NULL,
            ),
          
            array(
              'descricao' => 'Apareceu número alto no exame',
              'data_validade' => NULL,
              'data_realizacao_exame' => '2017-07-14',
              'codigo' => 789,
              'codigo_itens_pedidos_exames' => 4593,
              'resultado' => 2,
              'codigo_aparelho_audiometrico' => NULL,
              'codigo_usuario_inclusao' => 2,
              'data_inclusao' => NULL,
            ),
          
            array(
              'descricao' => NULL,
              'data_validade' => NULL,
              'data_realizacao_exame' => '2017-07-11',
              'codigo' => 788,
              'codigo_itens_pedidos_exames' => 4588,
              'resultado' => 1,
              'codigo_aparelho_audiometrico' => NULL,
              'codigo_usuario_inclusao' => 64915,
              'data_inclusao' => NULL,
            ),
          
            array(
              'descricao' => NULL,
              'data_validade' => NULL,
              'data_realizacao_exame' => '2017-07-11',
              'codigo' => 787,
              'codigo_itens_pedidos_exames' => 4587,
              'resultado' => 1,
              'codigo_aparelho_audiometrico' => NULL,
              'codigo_usuario_inclusao' => 63085,
              'data_inclusao' => NULL,
            ),
          
            array(
              'descricao' => NULL,
              'data_validade' => NULL,
              'data_realizacao_exame' => '2017-07-11',
              'codigo' => 786,
              'codigo_itens_pedidos_exames' => 4586,
              'resultado' => 1,
              'codigo_aparelho_audiometrico' => NULL,
              'codigo_usuario_inclusao' => 63085,
              'data_inclusao' => NULL,
            ),
          
            array(
              'descricao' => NULL,
              'data_validade' => NULL,
              'data_realizacao_exame' => '2017-07-10',
              'codigo' => 785,
              'codigo_itens_pedidos_exames' => 4579,
              'resultado' => 1,
              'codigo_aparelho_audiometrico' => NULL,
              'codigo_usuario_inclusao' => 63085,
              'data_inclusao' => NULL,
            ),
          
            array(
              'descricao' => NULL,
              'data_validade' => NULL,
              'data_realizacao_exame' => '2017-06-28',
              'codigo' => 784,
              'codigo_itens_pedidos_exames' => 4517,
              'resultado' => 1,
              'codigo_aparelho_audiometrico' => NULL,
              'codigo_usuario_inclusao' => 63085,
              'data_inclusao' => NULL,
            ),
          
            array(
              'descricao' => NULL,
              'data_validade' => NULL,
              'data_realizacao_exame' => '2017-06-23',
              'codigo' => 783,
              'codigo_itens_pedidos_exames' => 117,
              'resultado' => 1,
              'codigo_aparelho_audiometrico' => NULL,
              'codigo_usuario_inclusao' => 2,
              'data_inclusao' => NULL,
            ),
          
            array(
              'descricao' => NULL,
              'data_validade' => NULL,
              'data_realizacao_exame' => '2017-06-23',
              'codigo' => 782,
              'codigo_itens_pedidos_exames' => 116,
              'resultado' => 1,
              'codigo_aparelho_audiometrico' => NULL,
              'codigo_usuario_inclusao' => 2,
              'data_inclusao' => NULL,
            ),
            array(
      'descricao' => NULL,
      'data_validade' => NULL,
      'data_realizacao_exame' => '2017-05-17',
      'codigo' => 777,
      'codigo_itens_pedidos_exames' => 2407,
      'resultado' => 1,
      'codigo_aparelho_audiometrico' => NULL,
      'codigo_usuario_inclusao' => 64918,
      'data_inclusao' => NULL,
    ),

          array( 
          'codigo' =>33134 , 
          'codigo_itens_pedidos_exames' =>42860 , 
          'resultado' =>1 , 
          'data_validade' =>'' , 
          'descricao' =>'' , 
          'data_realizacao_exame' =>'2018-05-02 00:00:00' , 
          'codigo_aparelho_audiometrico' =>'' , 
          'codigo_usuario_inclusao' =>67487 , 
          'data_inclusao' =>'2018-05-23 08:10:51' , 
          'fornecedor_particular' =>0 , 
          'pedido_importado' =>0 , 
          'codigo_usuario_alteracao' =>67487  ),
      
      /*****************************/
      array( 
    'codigo' => 33145 , 
    'codigo_itens_pedidos_exames' => 46991 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33147 , 
    'codigo_itens_pedidos_exames' => 46992 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33148 , 
    'codigo_itens_pedidos_exames' => 46993 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33144 , 
    'codigo_itens_pedidos_exames' => 46994 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33146 , 
    'codigo_itens_pedidos_exames' => 46995 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33150 , 
    'codigo_itens_pedidos_exames' => 46996 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33152 , 
    'codigo_itens_pedidos_exames' => 46997 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33153 , 
    'codigo_itens_pedidos_exames' => 46998 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33149 , 
    'codigo_itens_pedidos_exames' => 46999 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33151 , 
    'codigo_itens_pedidos_exames' => 47000 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33155 , 
    'codigo_itens_pedidos_exames' => 47001 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33157 , 
    'codigo_itens_pedidos_exames' => 47002 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33158 , 
    'codigo_itens_pedidos_exames' => 47003 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33154 , 
    'codigo_itens_pedidos_exames' => 47004 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  ),
  array( 
    'codigo' => 33156 , 
    'codigo_itens_pedidos_exames' => 47005 , 
    'resultado' => 1 , 
    'data_validade' => '' , 
    'descricao' => '' , 
    'data_realizacao_exame' => '2018-06-04' , 
    'codigo_aparelho_audiometrico' => NULL , 
    'codigo_usuario_inclusao' => 67487 , 
    'data_inclusao' => '2018-06-05' , 
    'fornecedor_particular' => 0 , 
    'pedido_importado' => 0 , 
    'codigo_usuario_alteracao' => 67487  )

      );
}