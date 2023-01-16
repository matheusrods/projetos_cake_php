<?php
class OcorrenciaHistorico extends AppModel {
    var $name = 'OcorrenciaHistorico';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'ocorrencias_historicos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
     var $belongsTo = array(
        'Funcionario' => array(
            'className' => 'Funcionario',
            'foreignKey' => 'usuario_monitora_inclusao',
            'fields' => 'Apelido'
        ),
        'Usuario' => array(
            'className' => 'Usuario',
            'foreignKey' => 'codigo_usuario_inclusao',
            'fields' => 'Apelido'
        ),
    );
    
    function incluir($dados) {
        $this->create();
        return $this->save($dados);
    }
}