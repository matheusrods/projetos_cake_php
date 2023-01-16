<?php
class PushKeys extends AppModel {
    var $name           = 'PushKeys';
    var $tableSchema    = 'dbo';
    var $databaseTable  = 'RHHealth';
    var $useTable       = 'push_keys';
    var $primaryKey     = 'codigo';
    var $actsAs         = array('Secure');

    var $validate       = array(
        'tipo' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o tipo de chave',
                'required' => true
            ), 
            'isAllowedTipo' => array(
                'rule' => 'isAllowedTipo',
                'message' => 'Tipo de chave não permitido'
            )
        ),

        'projeto' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o projeto',
                'required' => true
            ), 
            'isAllowedProjeto' => array(
                'rule' => 'isAllowedProjeto',
                'message' => 'Projeto não permitido'
            )
        ),

        'titulo' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o título da chave',
                'required' => true
            )
        )
    );

    var $arrTipos    = array('GoogleMaps', 'PushNotification');
    var $arrProjetos = array('BuonnyDriver', 'BuonnyDelivery', 'BuonnyTruck', 'LiderRegulator','BuonnyPronta', 'GestaoEntrega', 'SompoDriver', 'sompoDriver', 'SompoLog');

    const TIPO_GOOGLE_MAPS = 'GoogleMaps';
    const TIPO_GOOGLE_GMC  = 'PushNotification';

    const PROJETO_APP_DRIVER                = 'BuonnyDriver';
    const PROJETO_APP_DELIVERY              = 'BuonnyDelivery';
    const PROJETO_APP_TRUCK                 = 'BuonnyTruck';
    const PROJETO_APP_REGULATOR             = 'LiderRegulator';
    const PROJETO_APP_MUNDOFRETE            = 'MundoFrete';
    const PROJETO_APP_PRONTA                = 'BuonnyPronta';
    const PROJETO_APP_GESTAOENTREGA         = 'GestaoEntrega';
    const PROJETO_APP_CONTROLERETENCAO      = 'ControleRetencao';
    const PROJETO_APP_SOMPODRIVER           = 'SompoDriver';
    const PROJETO_APP_SOMPODRIVER_SEM_SM    = 'sompoDriver';
    const PROJETO_APP_SOMPOLOG              = 'SompoLog';

    public function isAllowedTipo(){
        if(!empty($this->data[$this->name]['tipo'])){
            if(!in_array($this->data[$this->name]['tipo'], $this->arrTipos)) return false;
        }
        
        return true;
    }    

    public function isAllowedProjeto(){
        if(!empty($this->data[$this->name]['projeto'])){
            if(!in_array($this->data[$this->name]['projeto'], $this->arrProjetos)) return false;
        }
        
        return true;
    }    


}
?>