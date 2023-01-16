<?php

class FichaClinicaQuestao extends AppModel {

    public $name = 'FichaClinicaQuestao';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'fichas_clinicas_questoes';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');  

    public $hasMany = array(
        'FichaClinicaResposta' => array(
            'className' => 'FichaClinicaResposta',
            'foreignKey' => 'codigo_ficha_clinica_questao',
            ),
        'FichaClinicaSubQuestao' => array(
            'className' => 'FichaClinicaQuestao',
            'foreignKey' => 'codigo_ficha_clinica_questao',
            'fields' => array('codigo', 'tipo', 'campo_livre_label', 'observacao', 'obrigatorio', 'ajuda', 'span', 'label', 'conteudo', 'parentesco_ativo', 'quebra_linha', 'opcao_selecionada', 'opcao_abre_menu_escondido', 'farmaco_ativo', 'opcao_exibe_label', 'multiplas_cids_ativo', 'ativo', 'farmaco_campo_exibir', 'descricao_ativo')
            )
        );

    public $belongsTo = array(
        'FichaClinicaGrupoQuestao' => array(
            'className' => 'FichaClinicaGrupoQuestao',
            'foreignKey' => 'codigo_ficha_clinica_grupo_questao',
            )
        );

}