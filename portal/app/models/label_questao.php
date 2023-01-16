<?php 
class LabelQuestao extends AppModel {
	
    public $name = 'LabelQuestao';
    public $useTable = 'label_questoes';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure', 'Containable');
    public $displayField = 'label';

    public $hasMany = array(
        'Questao' => array(
            'className' => 'Questao',
            'foreignKey' => 'codigo_label_questao',
            'depentent' => false
            )
        );

    public function converteFiltroEmCondition($data) 
    {
        $conditions = array();
        if (!empty($data['pergunta']))
            $conditions['LabelQuestao.label LIKE'] = '%'.$data['pergunta'].'%';

        return $conditions;
    }

}
