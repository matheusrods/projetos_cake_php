<?php
class WsConfiguracao extends AppModel {
    var $name = 'WsConfiguracao';
    var $tableSchema = 'portal';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'ws_configuracao';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    var $validate = array(
    	'codigo_documento' => array(
    		'rule' => 'notEmpty',
    		'message' => 'Informe o cliente',
    		'required' => true
    	),
    	'tipo_mensagem' => array(
    		'rule' => 'notEmpty',
    		'message' => 'Informe um tipo de mensagem',
    		'required' => true
    	),
    	'soap_url' => array(
    		'notEmpty' => array(
	    		'rule' => 'notEmpty',
	    		'message' => 'Informe uma URL SOAP',
	    		'required' => true
    		),
    		'website' => array(
	    		'rule' => 'url',
	    		'message' => 'Informe uma URL SOAP válida',
    		)
    	),
    	'soap_funcao' => array(
    		'rule' => 'notEmpty',
    		'message' => 'Informe uma função SOAP',
    		'required' => true
    	),
    );

	public function localizaConfiguracao($codigo_documento, $tipo_mensagem) {
		$config =  $this->find('first', array('conditions'=>array(
			'codigo_documento' => $codigo_documento,
			'tipo_mensagem' => $tipo_mensagem,
		)));
        
        if (!empty($config)) $config['WsConfiguracao']['soap_param'] = null;
        if (strpos($config['WsConfiguracao']['soap_url'],'?')) {
            $arrURL = explode('?',$config['WsConfiguracao']['soap_url']);
            $url = $arrURL[0];
            $param = $arrURL[1];
            $config['WsConfiguracao']['soap_url'] = $url;
            $config['WsConfiguracao']['soap_param'] = $param;
        }

        return $config;
	}


    public function localizaClientePorTipoMensagem( $tipo_mensagem ){
            $this->bindModel(
                array('hasOne' => array(
                'Cliente' => array( 'foreignKey' => FALSE, 'conditions' =>'Cliente.codigo_documento=WsConfiguracao.codigo_documento'),
            )));
            $conditions = array('WsConfiguracao.tipo_mensagem'=>$tipo_mensagem );
            $fields     = array('Cliente.codigo', 'Cliente.codigo_documento');            
            return  $this->find('all', compact('conditions', 'fields'));
    }

}
?>