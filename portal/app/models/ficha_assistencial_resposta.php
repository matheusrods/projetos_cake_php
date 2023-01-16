<?php

class FichaAssistencialResposta extends AppModel {

    public $name          = 'FichaAssistencialResposta';
    public $tableSchema   = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable      = 'fichas_assistenciais_respostas';
    public $primaryKey    = 'codigo';
    public $actsAs		  = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_ficha_assistencial_resposta'));
    //public $recursive = -1; 
    public $virtualFields =  array('cl' =>  'CONVERT(TEXT,  campo_livre)');
    
    public $belongsTo = array(
        'FichaAssistencial' => array(
            'className' => 'FichaAssistencial',
            'foreignKey' => 'codigo_ficha_assistencial',
        ),
        'FichaAssistencialQuestao' => array(
            'className' => 'FichaAssistencialQuestao',
            'foreignKey' => 'codigo_ficha_assistencial_questao',
        )
    );

    public function afterFind($data, $options = array()){
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                if(!empty($value[$this->name]['cl'])) {
                    $data[$key][$this->name]['campo_livre'] = $value[$this->name]['cl'];
                }
                unset($data[$key][$this->name]['cl']);
            }
        }
        return $data;
    }//FINAL FUNCTION afterFind
    
}//FINAL CLASS FichaAssistencialResposta