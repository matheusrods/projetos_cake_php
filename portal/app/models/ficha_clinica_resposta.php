<?php

class FichaClinicaResposta extends AppModel {

    public $name = 'FichaClinicaResposta';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
   	public $useTable = 'fichas_clinicas_respostas';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_ficha_clinica_resposta'));  
    //public $recursive = -1; 
    public $virtualFields =  array('cl' =>  'CONVERT(TEXT,  campo_livre)', 'resposta' =>  'CONVERT(TEXT,  resposta)');

    public $belongsTo = array(
        'FichaClinica' => array(
            'className' => 'FichaClinica',
            'foreignKey' => 'codigo_ficha_clinica',
        ),
         'FichaClinicaQuestao' => array(
            'className' => 'FichaClinicaQuestao',
            'foreignKey' => 'codigo_ficha_clinica_questao',
        )
    );

    public function afterFind($data, $options = array())
    {
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                if(!empty($value[$this->name]['cl'])) {
                    $data[$key][$this->name]['campo_livre'] = $value[$this->name]['cl'];
                }
                unset($data[$key][$this->name]['cl']);
            }
        }
       return $data;
    }
    
}