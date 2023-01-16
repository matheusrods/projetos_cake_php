<?php
class LogsIntegracoesFixture extends CakeTestFixture {
	var $name    = 'LogIntegracao';
	var $table   = 'logs_integracoes';
	
    var $fields = array(

        'codigo' =>array(  'type' => 'integer',    'null' => false,    'default' => NULL,    'length' => NULL,    'key' => 'primary',),
        'codigo_cliente' => array(  'type' => 'integer',    'null' => true,    'default' => NULL,    'length' => NULL,  ),
        'arquivo' => array(  'type' => 'string',    'null' => false,    'default' => NULL,    'length' => 255,  ),
        'conteudo' => array(  'type' => 'text',    'null' => false,    'default' => NULL,    'length' => NULL,  ),
        'retorno' => array(  'type' => 'text',    'null' => false,    'default' => NULL,    'length' => NULL,  ),
        'data_inclusao' => array(  'type' => 'datetime',    'null' => false,    'default' => NULL,    'length' => NULL,  ),
        'sistema_origem' => array(  'type' => 'string',    'null' => false,    'default' => NULL,    'length' => 255,  ),
        'status' => array(  'type' => 'string',    'null' => true,    'default' => NULL,    'length' => 255,  ),
        'descricao' => array(  'type' => 'string',    'null' => true,    'default' => NULL,    'length' => 255,  ),
        'tipo_operacao' => array(  'type' => 'string',    'null' => true,    'default' => NULL,    'length' => 255,  ),
        'reprocessado' => array(  'type' => 'datetime',    'null' => true,    'default' => NULL,    'length' => NULL,  ),
        'finalizado' => array(  'type' => 'datetime',    'null' => true,    'default' => NULL,    'length' => NULL,  ),
        'data_arquivo' => array(  'type' => 'datetime',    'null' => true,    'default' => NULL,    'length' => NULL,  ),
        'codigo_usuario_inclusao' => array(  'type' => 'integer',    'null' => true,    'default' => NULL,    'length' => NULL,  ),
    );


    /*
    var $records = array(
        array(
          'conteudo' => '10220183726000114036700114410                                 000096020000000000000109                     I01949       30051700000000134423410000006N300517520000000000000040000000000000000000000000000000000000000000000100001589615794Marcio Jose da Silva                    Travessa Maria Ornelas, 85              Porto Novo  24431820Sao Goncalo    RJ                                  00000000 000002
    ',
          'retorno' => '10220183726000114036700114410                                 000096020000000000000109                     I01949       30051700000000134423410000006N300517520000000000000040000000000000000000000000000000000000000000000100001589615794Marcio Jose da Silva                    Travessa Maria Ornelas, 85              Porto Novo  24431820Sao Goncalo    RJ                                  00000000 000002
    ',
          'codigo' => 2334,
          'codigo_cliente' => 6181,
          'codigo_usuario_inclusao' => 64915,
          'data_inclusao' => '23/06/2017 13:46:12',
          'reprocessado' => '23/06/2017 13:46:12',
          'finalizado' => NULL,
          'data_arquivo' => '23/06/2017 13:46:12',
          'arquivo' => 'C170530A.TXT_1.txt',
          'sistema_origem' => 'RHHealth',
          'descricao' => 'SUCESSO',
          'status' => '1',
          'tipo_operacao' => 'I',
        ),
      
        array(
          'conteudo' => '10220183726000114036700114410                                 000096030000000000000109                     I01949       30061700000000134423410000006N300517520000000000000040000000000000000000000000000000000000000000000100001589615794Marcio Jose da Silva                    Travessa Maria Ornelas, 85              Porto Novo  24431820Sao Goncalo    RJ                                  00000000 000003
    ',
          'retorno' => '10220183726000114036700114410                                 000096030000000000000109                     I01949       30061700000000134423410000006N300517520000000000000040000000000000000000000000000000000000000000000100001589615794Marcio Jose da Silva                    Travessa Maria Ornelas, 85              Porto Novo  24431820Sao Goncalo    RJ                                  00000000 000003
    ',
          'codigo' => 2335,
          'codigo_cliente' => 6181,
          'codigo_usuario_inclusao' => 64915,
          'data_inclusao' => '23/06/2017 13:46:14',
          'reprocessado' => '23/06/2017 13:46:14',
          'finalizado' => NULL,
          'data_arquivo' => '23/06/2017 13:46:14',
          'arquivo' => 'C170530A.TXT_1.txt',
          'sistema_origem' => 'RHHealth',
          'descricao' => 'SUCESSO',
          'status' => '1',
          'tipo_operacao' => 'I',
        ),

        array(
          'conteudo' => '10220183726000114036700114410                                 000096040000000000000109                     I01949       30071700000000134423410000006N300517520000000000000040000000000000000000000000000000000000000000000100001589615794Marcio Jose da Silva                    Travessa Maria Ornelas, 85              Porto Novo  24431820Sao Goncalo    RJ                                  00000000 000004
    ',
          'retorno' => '10220183726000114036700114410                                 000096040000000000000109                     I01949       30071700000000134423410000006N300517520000000000000040000000000000000000000000000000000000000000000100001589615794Marcio Jose da Silva                    Travessa Maria Ornelas, 85              Porto Novo  24431820Sao Goncalo    RJ                                  00000000 000004
    ',
          'codigo' => 2336,
          'codigo_cliente' => 6181,
          'codigo_usuario_inclusao' => 64915,
          'data_inclusao' => '23/06/2017 13:46:14',
          'reprocessado' => '23/06/2017 13:46:14',
          'finalizado' => NULL,
          'data_arquivo' => '23/06/2017 13:46:14',
          'arquivo' => 'C170530A.TXT_1.txt',
          'sistema_origem' => 'RHHealth',
          'descricao' => 'SUCESSO',
          'status' => '1',
          'tipo_operacao' => 'I',
        ),
        array(
          'conteudo' => '10220183726000114036700114410                                 000096050000000000000109                     I01949       30081700000000134423410000006N300517520000000000000040000000000000000000000000000000000000000000000100001589615794Marcio Jose da Silva                    Travessa Maria Ornelas, 85              Porto Novo  24431820Sao Goncalo    RJ                                  00000000 000005
    ',
          'retorno' => '10220183726000114036700114410                                 000096050000000000000109                     I01949       30081700000000134423410000006N300517520000000000000040000000000000000000000000000000000000000000000100001589615794Marcio Jose da Silva                    Travessa Maria Ornelas, 85              Porto Novo  24431820Sao Goncalo    RJ                                  00000000 000005
    ',
          'codigo' => 2337,
          'codigo_cliente' => 6181,
          'codigo_usuario_inclusao' => 64915,
          'data_inclusao' => '23/06/2017 13:46:15',
          'reprocessado' => '23/06/2017 13:46:14',
          'finalizado' => NULL,
          'data_arquivo' => '23/06/2017 13:46:15',
          'arquivo' => 'C170530A.TXT_1.txt',
          'sistema_origem' => 'RHHealth',
          'descricao' => 'SUCESSO',
          'status' => '1',
          'tipo_operacao' => 'I',
        ),
    );*/

}

?>