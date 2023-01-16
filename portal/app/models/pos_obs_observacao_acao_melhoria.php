<?php

class PosObsObservacaoAcaoMelhoria extends AppModel
{

    public $name = 'PosObsObservacaoAcaoMelhoria';

    var $tableSchema   = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable      = 'pos_obs_observacao_acao_melhoria';
    var $primaryKey    = 'codigo';
	var $recursive     = 2;

    public $belongsTo = array(
         'AcoesMelhorias' => array(
            'className'  => 'AcoesMelhorias',
            'joinTable'  => 'acoes_melhorias',
            'foreignKey' => 'acoes_melhoria_id',
         )
    );
}