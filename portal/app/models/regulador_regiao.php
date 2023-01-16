<?php
class ReguladorRegiao extends AppModel {
    var $name = 'ReguladorRegiao';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'reguladores_regioes';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    var $validate = array(
        'latitude' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Informe a latitude'
            ),
        ),  
        'longitude' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Informe a longitude'
            ),
        ),
        'raio' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Informe o raio'
            ),
        ),
        'cidade' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Informe a cidade'
            ),
        ), 
        'prioridade' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Informe a prioridade'
            ),
        ),    
    );

    function regioes_regulador($codigo_regulador) {       
        $regioes_regulador = $this->find('all', array('conditions' => array('codigo_regulador' => $codigo_regulador)));
        return $regioes_regulador;
    }

}
?>