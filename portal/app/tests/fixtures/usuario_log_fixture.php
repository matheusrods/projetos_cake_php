<?php
class UsuarioLogFixture extends CakeTestFixture {

    var $name  = 'UsuarioLog';
    var $table = 'usuario_log';

    var $fields = array(
        'codigo' => array('type' => 'integer', 'null' => true,   'key' => 'primary'),
        'codigo_usuario' => array('type' => 'integer', 'null' => true,  ),
        'codigo_documento' => array('type' => 'string', 'null' => true,  ),
        'codigo_cliente' => array('type' => 'integer', 'null' => true,  ),
        'codigo_departamento' => array('type' => 'integer', 'null' => true,  ),
        'nome' => array('type' => 'string', 'null' => true,  ),
        'apelido' => array('type' => 'string', 'null' => true,  ),
        'senha' => array('type' => 'string', 'null' => true,  ),
        'email' => array('type' => 'string', 'null' => true,  ),
        'ativo' => array('type' => 'boolean', 'null' => true,  ),
        'data_inclusao' => array('type' => 'datetime', 'default' => '(getdate())',  ),
        'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true,  ),
        'codigo_uperfil' => array('type' => 'integer', 'null' => true,  ),
        'codigo_usuario_monitora' => array('type' => 'string', 'null' => true,  ),
        'celular' => array('type'=>'string', 'null' => true, 'default' => null,  ),
        'alerta_portal' => array('type' => 'boolean', 'null' => true,  ),
        'alerta_email' => array('type' => 'boolean', 'null' => true,  ),
        'alerta_sms' => array('type' => 'boolean', 'null' => true,  ),
        'token' => array('type' => 'string', 'null' => true,  ),
        'digital' => array('type' => 'binary', 'null' => true,  ),
        'cracha' => array('type' => 'integer', 'null' => true,  ),
        'fuso_horario' => array('type' => 'integer', 'null' => true,  ),
        'horario_verao' => array('type' => 'boolean', 'null' => true,  ),
        'codigo_seguradora' => array('type' => 'integer', 'null' => true,  ),
        'codigo_corretora' => array('type' => 'integer', 'null' => true,  ),
        'codigo_filial' => array('type' => 'integer', 'null' => true,  ),
        'data_senha_expiracao' => array('type' => 'datetime', 'null' => true,  ),
        'permite_sm_sem_pgr' => array('type' => 'boolean', 'null' => true,  ),
        'admin' => array('type' => 'boolean', 'null' => true,  ),
        'indexes' => array('0' => array())
    );

    var $records = array(
        array( 
            'digital' => NULL, 
            'codigo_departamento' => 9, 
            'codigo' => 5, 
            'codigo_usuario' => 3, 
            'codigo_cliente' => NULL, 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 8, 
            'fuso_horario' => NULL, 
            'cracha' => NULL, 
            'codigo_seguradora' => NULL, 
            'codigo_corretora' => NULL, 
            'codigo_filial' => NULL, 
            'data_inclusao' => '20140328 15:46:11', 
            'data_senha_expiracao' => NULL, 
            'ativo' => 1, 
            'alerta_portal' => 0, 
            'alerta_email' => 0, 
            'alerta_sms' => 0, 
            'horario_verao' => 0, 
            'admin' => 0, 
            'permite_sm_sem_pgr' => 0, 
            'codigo_documento' => '84095820667', 
            'nome' => 'Uzumaki Naruto', 
            'apelido' => 'naruto', 
            'senha' => 'ibqevB8sfeq2VYUP7l7i++/HlOjwHYD53E6sGkSWSJkvOL4RyuD/I8HCnIFgBSClDERflVTMftbhzBARA3VwqRuojt6N0pX2z7BQrqmPd5SeRDiG34Sf5kF3uBFyNJjhkhtkjmwPPVw5Zr8p7X/hJTAqHF9mrXWx3s04iOPrbLE=', 
            'email' => 'uzna@mkt.com', 
            'celular' => NULL, 
            'token' => NULL, 
            'codigo_usuario_monitora' => NULL, 
        ),
        array( 
            'digital' => NULL, 
            'codigo_departamento' => 9, 
            'codigo' => 6, 
            'codigo_usuario' => 3, 
            'codigo_cliente' => NULL, 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 8, 
            'fuso_horario' => NULL, 
            'cracha' => NULL, 
            'codigo_seguradora' => NULL, 
            'codigo_corretora' => NULL, 
            'codigo_filial' => NULL, 
            'data_inclusao' => '20140328 15:47:46', 
            'data_senha_expiracao' => NULL, 
            'ativo' => 1, 
            'alerta_portal' => 0, 
            'alerta_email' => 0, 
            'alerta_sms' => 0, 
            'horario_verao' => 0, 
            'admin' => 0, 
            'permite_sm_sem_pgr' => 0, 
            'codigo_documento' => '84095820667', 
            'nome' => 'Uzumaki Naruto', 
            'apelido' => 'naruto', 
            'senha' => 'ibqevB8sfeq2VYUP7l7i++/HlOjwHYD53E6sGkSWSJkvOL4RyuD/I8HCnIFgBSClDERflVTMftbhzBARA3VwqRuojt6N0pX2z7BQrqmPd5SeRDiG34Sf5kF3uBFyNJjhkhtkjmwPPVw5Zr8p7X/hJTAqHF9mrXWx3s04iOPrbLE=', 
            'email' => 'uzna@mkt.com', 
            'celular' => '11978454545', 
            'token' => NULL, 
            'codigo_usuario_monitora' => NULL, 
        ),
        array( 
            'digital' => NULL, 
            'codigo_departamento' => 11, 
            'codigo' => 7, 
            'codigo_usuario' => 36280, 
            'codigo_cliente' => 2, 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'fuso_horario' => NULL, 
            'cracha' => NULL, 
            'codigo_seguradora' => NULL, 
            'codigo_corretora' => NULL, 
            'codigo_filial' => NULL, 
            'data_inclusao' => '20140328 16:40:00', 
            'data_senha_expiracao' => NULL, 
            'ativo' => 1, 
            'alerta_portal' => 0, 
            'alerta_email' => 0, 
            'alerta_sms' => 0, 
            'horario_verao' => 0, 
            'admin' => 0, 
            'permite_sm_sem_pgr' => 0, 
            'codigo_documento' => '01648034000150', 
            'nome' => 'Marcos Mouse Kombat', 
            'apelido' => 'mmkt', 
            'senha' => 'ZkMFyipdxtgRit7cMoi+t0GLBIBN6HyzJFk2FC5eU3pawbVUb+zuTfrWUH/BY5Rqm2U08YfxZh/0/pR1lBHPDOaZQ+1ekTNvjKEyCE/zvT/xPxY47JV26ZJmUJ5wDzewrVb4/L/FA44GTL8MI8a3zqA1l4Iw+oWdJ8AXlukQS0Y=', 
            'email' => 'kkk@gmail.com', 
            'celular' => '11976545454', 
            'token' => 'e71ef10def763025e84b16c415cd4286', 
            'codigo_usuario_monitora' => '002165', 
        ),
        array( 
            'digital' => NULL, 
            'codigo_departamento' => 11, 
            'codigo' => 8, 
            'codigo_usuario' => 36280, 
            'codigo_cliente' => 2, 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'fuso_horario' => NULL, 
            'cracha' => NULL, 
            'codigo_seguradora' => NULL, 
            'codigo_corretora' => NULL, 
            'codigo_filial' => NULL, 
            'data_inclusao' => '20140328 16:45:26', 
            'data_senha_expiracao' => NULL, 
            'ativo' => 1, 
            'alerta_portal' => 0, 
            'alerta_email' => 0, 
            'alerta_sms' => 0, 
            'horario_verao' => 0, 
            'admin' => 0, 
            'permite_sm_sem_pgr' => 0, 
            'codigo_documento' => '01648034000150', 
            'nome' => 'Marcos Mouse Kombat', 
            'apelido' => 'mmkt', 
            'senha' => 'ZkMFyipdxtgRit7cMoi+t0GLBIBN6HyzJFk2FC5eU3pawbVUb+zuTfrWUH/BY5Rqm2U08YfxZh/0/pR1lBHPDOaZQ+1ekTNvjKEyCE/zvT/xPxY47JV26ZJmUJ5wDzewrVb4/L/FA44GTL8MI8a3zqA1l4Iw+oWdJ8AXlukQS0Y=', 
            'email' => 'kkk@gmail.com', 
            'celular' => '11976545454', 
            'token' => 'e71ef10def763025e84b16c415cd4286', 
            'codigo_usuario_monitora' => '002165', 
        ),
        array( 
            'digital' => NULL, 
            'codigo_departamento' => 11, 
            'codigo' => 9, 
            'codigo_usuario' => 36280, 
            'codigo_cliente' => 2, 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'fuso_horario' => NULL, 
            'cracha' => NULL, 
            'codigo_seguradora' => NULL, 
            'codigo_corretora' => NULL, 
            'codigo_filial' => NULL, 
            'data_inclusao' => '20140328 16:45:33', 
            'data_senha_expiracao' => NULL, 
            'ativo' => 1, 
            'alerta_portal' => 0, 
            'alerta_email' => 0, 
            'alerta_sms' => 0, 
            'horario_verao' => 0, 
            'admin' => 0, 
            'permite_sm_sem_pgr' => 0, 
            'codigo_documento' => '01648034000150', 
            'nome' => 'Marcos Mouse Kombat', 
            'apelido' => 'mmkt', 
            'senha' => 'ZkMFyipdxtgRit7cMoi+t0GLBIBN6HyzJFk2FC5eU3pawbVUb+zuTfrWUH/BY5Rqm2U08YfxZh/0/pR1lBHPDOaZQ+1ekTNvjKEyCE/zvT/xPxY47JV26ZJmUJ5wDzewrVb4/L/FA44GTL8MI8a3zqA1l4Iw+oWdJ8AXlukQS0Y=', 
            'email' => 'kkk@gmail.com', 
            'celular' => '11976545454', 
            'token' => 'e71ef10def763025e84b16c415cd4286', 
            'codigo_usuario_monitora' => '002165', 
        ),
        array( 
            'digital' => NULL, 
            'codigo_departamento' => 11, 
            'codigo' => 10, 
            'codigo_usuario' => 36280, 
            'codigo_cliente' => 2, 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'fuso_horario' => NULL, 
            'cracha' => NULL, 
            'codigo_seguradora' => NULL, 
            'codigo_corretora' => NULL, 
            'codigo_filial' => NULL, 
            'data_inclusao' => '20140328 16:47:54', 
            'data_senha_expiracao' => NULL, 
            'ativo' => 1, 
            'alerta_portal' => 0, 
            'alerta_email' => 0, 
            'alerta_sms' => 0, 
            'horario_verao' => 0, 
            'admin' => 0, 
            'permite_sm_sem_pgr' => 0, 
            'codigo_documento' => '01648034000150', 
            'nome' => 'Marcos Mouse Kombat', 
            'apelido' => 'mmkt', 
            'senha' => 'ZkMFyipdxtgRit7cMoi+t0GLBIBN6HyzJFk2FC5eU3pawbVUb+zuTfrWUH/BY5Rqm2U08YfxZh/0/pR1lBHPDOaZQ+1ekTNvjKEyCE/zvT/xPxY47JV26ZJmUJ5wDzewrVb4/L/FA44GTL8MI8a3zqA1l4Iw+oWdJ8AXlukQS0Y=', 
            'email' => 'kkk@gmail.com', 
            'celular' => '11976545454', 
            'token' => 'e71ef10def763025e84b16c415cd4286', 
            'codigo_usuario_monitora' => '002165', 
        ),
        array( 
            'digital' => NULL, 
            'codigo_departamento' => 11, 
            'codigo' => 11, 
            'codigo_usuario' => 36280, 
            'codigo_cliente' => 2, 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'fuso_horario' => NULL, 
            'cracha' => NULL, 
            'codigo_seguradora' => NULL, 
            'codigo_corretora' => NULL, 
            'codigo_filial' => NULL, 
            'data_inclusao' => '20140328 16:48:21', 
            'data_senha_expiracao' => NULL, 
            'ativo' => 1, 
            'alerta_portal' => 0, 
            'alerta_email' => 0, 
            'alerta_sms' => 0, 
            'horario_verao' => 0, 
            'admin' => 0, 
            'permite_sm_sem_pgr' => 0, 
            'codigo_documento' => '01648034000150', 
            'nome' => 'Marcos Mouse Kombat', 
            'apelido' => 'mmkt', 
            'senha' => 'ZkMFyipdxtgRit7cMoi+t0GLBIBN6HyzJFk2FC5eU3pawbVUb+zuTfrWUH/BY5Rqm2U08YfxZh/0/pR1lBHPDOaZQ+1ekTNvjKEyCE/zvT/xPxY47JV26ZJmUJ5wDzewrVb4/L/FA44GTL8MI8a3zqA1l4Iw+oWdJ8AXlukQS0Y=', 
            'email' => 'kkk@gmail.com', 
            'celular' => '11976545454', 
            'token' => 'e71ef10def763025e84b16c415cd4286', 
            'codigo_usuario_monitora' => '002165', 
        ),
        array( 
            'digital' => NULL, 
            'codigo_departamento' => 11, 
            'codigo' => 12, 
            'codigo_usuario' => 36280, 
            'codigo_cliente' => 2, 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'fuso_horario' => NULL, 
            'cracha' => NULL, 
            'codigo_seguradora' => NULL, 
            'codigo_corretora' => NULL, 
            'codigo_filial' => NULL, 
            'data_inclusao' => '20140328 16:49:11', 
            'data_senha_expiracao' => NULL, 
            'ativo' => 1, 
            'alerta_portal' => 0, 
            'alerta_email' => 0, 
            'alerta_sms' => 0, 
            'horario_verao' => 0, 
            'admin' => 0, 
            'permite_sm_sem_pgr' => 0, 
            'codigo_documento' => '01648034000150', 
            'nome' => 'Marcos Mouse Kombat', 
            'apelido' => 'mmkt', 
            'senha' => 'ZkMFyipdxtgRit7cMoi+t0GLBIBN6HyzJFk2FC5eU3pawbVUb+zuTfrWUH/BY5Rqm2U08YfxZh/0/pR1lBHPDOaZQ+1ekTNvjKEyCE/zvT/xPxY47JV26ZJmUJ5wDzewrVb4/L/FA44GTL8MI8a3zqA1l4Iw+oWdJ8AXlukQS0Y=', 
            'email' => 'kkk@gmail.com', 
            'celular' => '11976545454', 
            'token' => 'e71ef10def763025e84b16c415cd4286', 
            'codigo_usuario_monitora' => '002165', 
        ),
        array( 
            'digital' => NULL, 
            'codigo_departamento' => 11, 
            'codigo' => 13, 
            'codigo_usuario' => 727, 
            'codigo_cliente' => 2, 
            'codigo_usuario_inclusao' => 2, 
            'codigo_uperfil' => 49, 
            'fuso_horario' => NULL, 
            'cracha' => NULL, 
            'codigo_seguradora' => NULL, 
            'codigo_corretora' => NULL, 
            'codigo_filial' => NULL, 
            'data_inclusao' => '20140328 16:59:04', 
            'data_senha_expiracao' => '02/10/2014 00:00:00', 
            'ativo' => 1, 
            'alerta_portal' => 1, 
            'alerta_email' => 1, 
            'alerta_sms' => 1, 
            'horario_verao' => 0, 
            'admin' => 1, 
            'permite_sm_sem_pgr' => 0, 
            'codigo_documento' => '01648034000150', 
            'nome' => 'BUONNY PROJETOS E SERVICOS DE RISCOS SECURITARIOS LTDA', 
            'apelido' => '2', 
            'senha' => 'ft5owcfbZxaEBYhCQ0Q6X54h9bUd4sQngj+Xo5mvePhGo6WgTdOdCc1rHS1PLftuoeztijpBCfFiyqvlxr9Wb+4laH2L7Xhxpt6N64SjOv00/1i1a6qmTmSXCb0xiWXbFz152jwBbxyiOPa44L+nw6V5ifd/EMHiD8vhRwFWF8M=', 
            'email' => 'rodrigo.sobrinho@buonny.com.br', 
            'celular' => '11985991248', 
            'token' => '5888b73e635e1b1268770397ed9091ee', 
            'codigo_usuario_monitora' => '002165', 
        ),
        array( 
            'digital' => NULL, 
            'codigo_departamento' => 11, 
            'codigo' => 14, 
            'codigo_usuario' => 35989, 
            'codigo_cliente' => 2, 
            'codigo_usuario_inclusao' => 727, 
            'codigo_uperfil' => 21, 
            'fuso_horario' => NULL, 
            'cracha' => NULL, 
            'codigo_seguradora' => NULL, 
            'codigo_corretora' => NULL, 
            'codigo_filial' => NULL, 
            'data_inclusao' => '20140328 16:59:27', 
            'data_senha_expiracao' => NULL, 
            'ativo' => 1, 
            'alerta_portal' => 0, 
            'alerta_email' => 0, 
            'alerta_sms' => 1, 
            'horario_verao' => 0, 
            'admin' => 0, 
            'permite_sm_sem_pgr' => 0, 
            'codigo_documento' => '01648034000150', 
            'nome' => 'Bilbo Bolseiro JrSA', 
            'apelido' => 'bilbo.bolseiro', 
            'senha' => 'SeetBopYlq0dtJaCocKdUUPp++2sBvqfBIMgawOkhYvY+UoNB/9WeYQv3i1lbBbEB4rpLGaX36gN9GlEOFYgs8pZ5vmib/HVxFY2pY6F6v1W8tRudTN1lU61VshNzS4beCp96j+hCBWqyFAimsIGyhnKIgC/wvSI2N3FaRwrpak=', 
            'email' => 'bug@gmail.com', 
            'celular' => '11444444444', 
            'token' => ' ', 
            'codigo_usuario_monitora' => '002165', 
        ),
    );

}