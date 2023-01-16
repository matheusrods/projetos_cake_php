<?php
class PesquisaVeiculoLog extends AppModel {

    var $name = 'PesquisaVeiculoLog';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'pesquisa_veiculo_log';
    var $primaryKey = 'codigo';
    var $displayField = '';
    var $actsAs = array('Secure');
    var $foreignKeyLog = 'codigo_pesquisa_veiculo';
    var $belongsTo = array(
        'PesquisaVeiculo' => array(
            'class' => 'PesquisaVeiculo',
            'foreignKey' => 'codigo_pesquisa_veiculo'
        )
    );    
  
}
