<?php
class LogFaturamentoExcluido extends AppModel {
    var $name = 'LogFaturamentoExcluido';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'log_faturamento_excluido';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $validate = array(
        'codigo_log_faturamento' => array(
            'notEmpty' => array(
                'rule'      => 'notEmpty',
                'required'  => true,
                'message'   => 'Informe o numero do log',
             )
        ),
        'motivo_exclusao' => array(
            'notEmpty' => array(
                'rule'      => 'notEmpty',
                'required'  => true,
                'message'   => 'Informe o Motivo da exclusão',
             )
        ),
        'codigo_usuario_exclusao' => array(
            'notEmpty' => array(
                'rule'      => 'notEmpty',
                'required'  => true,
                'message'   => 'Erro ao identiicar o responsavel pela exclusão',
            )
        )
    );
}