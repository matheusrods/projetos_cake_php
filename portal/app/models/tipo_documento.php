<?php
class TipoDocumento extends AppModel {

	var $name = 'TipoDocumento';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'tipos_documentos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
	var $validate = array(
        'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Descrição do Documento!'
		),	
        'obrigatorio' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe se é Obrigatorio ou Não!'
		),
        'status' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status!'
		)				
	);     
    
    var $belongsToMany = array(
    	'PropostaCredDocumento' => array(
    		'className' => 'PropostaCredDocumento', 
    		'foreignKey' => 'codigo_tipo_documento'
	    )
    );
    
	/**
	 * Retorna QTD DE DOCUMENTOS OBRIGATORIOS vs QTD DE DOCUMENTOS JA ENVIADOS!!!
	 * 
     * @author: Danilo Borges Pereira
     * <daniloborgespereira@gmail.com>
	 */
	
	function _retornaDocsEnviados($idProposta) {
		
		$model_PropostaCredDocumento = & ClassRegistry::init('PropostaCredDocumento');
		
		// qtd de documentos obrigatorios 
		$qtd_documentos = count($this->find('all', array('conditions' => array('obrigatorio' => '1', 'status' => '1'))));

		// $this->log($qtd_documentos, 'debug');
		
		$options['conditions'] = array('TipoDocumento.obrigatorio' => '1', 'TipoDocumento.status' => '1', 'PropostaCredenciamento.codigo' => $idProposta);

		// $this->log($options, 'debug');
		
		$options['joins'] = array (
			array (
				'table' => 'tipos_documentos',
				'alias' => 'TipoDocumento',
				'type' => 'LEFT',
				'conditions' => array ( 'PropostaCredDocumento.codigo_tipo_documento = TipoDocumento.codigo' )
			),
			array (
				'table' => 'propostas_credenciamento',
				'alias' => 'PropostaCredenciamento',
				'type' => 'LEFT',
				'conditions' => array ( 'PropostaCredenciamento.codigo = PropostaCredDocumento.codigo_proposta_credenciamento' )
			),			
		);
		
		return array('qtd_documentos' => $qtd_documentos, 'qtd_enviados' => count($model_PropostaCredDocumento->find('all', $options)));
	}    
    
    function converteFiltroEmCondition($data) {
        $conditions = array();
        if (!empty($data['descricao']))
            $conditions['TipoDocumento.descricao like'] = $data['descricao'] . '%';
       
        if (!empty($data['obrigatorio']))
        	$conditions['TipoDocumento.obrigatorio = '] = $data['obrigatorio'];
        	
       	$conditions['TipoDocumento.status = '] = 1;        	
        return $conditions;
    }

    function retorna_tipos_documentos(){
      $tipos_documentos = $this->find('list', array('conditions' =>  array('status' => 1), 'fields' => array('codigo', 'descricao')));
      return $tipos_documentos;
    }    
}
