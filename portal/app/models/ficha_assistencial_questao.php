<?php

class FichaAssistencialQuestao extends AppModel {

    public $name = 'FichaAssistencialQuestao';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'fichas_assistenciais_questoes';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');  

    public $hasMany = array(
        'FichaAssistencialResposta' => array(
            'className' => 'FichaAssistencialResposta',
            'foreignKey' => 'codigo_ficha_assistencial_questao',
            ),
        'FichaAssistencialSubQuest' => array(
            'className' => 'FichaAssistencialQuestao',
            'foreignKey' => 'codigo_ficha_assistencial_questao',
            'fields' => array('codigo', 
                              'tipo', 
                              'campo_livre_label', 
                              'observacao', 
                              'obrigatorio', 
                              'ajuda', 
                              'span', 
                              'label', 
                              'conteudo', 
                              'parentesco_ativo', 
                              'quebra_linha', 
                              'opcao_selecionada', 
                              'opcao_abre_menu_escondido', 
                              'farmaco_ativo', 
                              'opcao_exibe_label', 
                              'multiplas_cids_ativo')
            )
        );

    public $belongsTo = array(
        'FichaAssistencialGQ' => array(
            'className' => 'FichaAssistencialGQ',
            'foreignKey' => 'codigo_ficha_assistencial_grupo_questao',
            )
        );

}//FINAL CLASS FichaAssistencialQuestao