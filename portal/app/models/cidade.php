<?php
class Cidade extends AppModel {

    var $name = 'Cidade';
    var $tableSchema = 'dbo';
    var $databaseTable = 'Monitora';
    var $useTable = 'cidades';
    var $primaryKey = 'Codigo';
    var $actsAs = array('Secure');

    const CIDADE_DESCONHECIDA 	= '023021';

    function novo_codigo(){
		$fields 	= array('MAX(CAST(Codigo AS INT))+1 AS novo_codigo');
		$novo_sm 	= $this->find('first',compact('fields'));

		return str_pad($novo_sm[0]['novo_codigo'],6,0,STR_PAD_LEFT);
	}

    function pegaCodigo($cidade, $estado){
	   $cidade = $this->find('first', array('fields' => array('Codigo'), 'conditions' => array('[Descricao] collate SQL_Latin1_General_Cp1_CI_AI' => trim($cidade), 'estado' => trim($estado), 'status' => 'S')));
	   return $cidade['Cidade']['Codigo'];
    }

    function pegaCidadePorDescricao($cidade_descricao,$estado){
        $cidade = $this->find('first', array(
                            'fields' => array('Codigo'), 
                            'conditions' => array(
                                '[Descricao] collate SQL_Latin1_General_Cp1_CI_AI' => $cidade_descricao,
                                'estado' => trim($estado), 
                                'status' => 'S'
                        )));
        return $cidade;
    }
    
    function listaCidades(){
        $cidade = $this->find('list', array('fields' => array('Descricao'), 'conditions' => array('status' => 'S')));
        return $cidade;
    }

    function buscaCodigoCidadeTrafegus($codigo){
    	$TCidaCidade	=& ClassRegistry::Init('TCidaCidade');
    	$conditions 	= array('codigo' => $codigo);
        $cidade 		= $this->find('first', compact('conditions'));

        if($cidade){
        	
        	$conditions = array('TCidaCidade.cida_descricao' => $cidade['Cidade']['Descricao'], 'TEstaEstado.esta_sigla' =>$cidade['Cidade']['Estado']);
        	
        	return $TCidaCidade->find('first',compact('conditions'));

        } else {
        	return false;
    	}
    }

    public function enderecoMonitoraPorReferencia($codigo_referencia){
    	$TCidaCidade 		=& ClassRegistry::Init('TCidaCidade');
    	$TEstaEstado 		=& ClassRegistry::Init('TEstaEstado');
    	$TRefeReferencia 	=& ClassRegistry::Init('TRefeReferencia');
    	
        
    	$TRefeReferencia->bindModel(array(
    			'belongsTo'	=> array(
    					'TCidaCidade' => array(
    						'className'		=> 'TCidaCidade',
    						'foreignKey' 	=> 'refe_cida_codigo',
    					),
    					'TEstaEstado' => array(
    						'className'		=> 'TEstaEstado',
    						'foreignKey' 	=> false,
    						'conditions' 	=> 'TCidaCidade.cida_esta_codigo = TEstaEstado.esta_codigo',
    					),
    			),
    				
    		));
        
		$conditions = array('refe_codigo' => $codigo_referencia);
		$fields 	= array('TCidaCidade.cida_descricao','TEstaEstado.esta_sigla','TRefeReferencia.refe_cida_codigo','TRefeReferencia.refe_codigo','TCidaCidade.cida_codigo');
		$city 		= $TRefeReferencia->find('first',compact('conditions','fields'));
		
		$conditions = array('Descricao' => $city['TCidaCidade']['cida_descricao'], 'Estado' => $city['TEstaEstado']['esta_sigla'], 'Status' => 'S');
		return $this->find('first',compact('conditions'));

    }

    public function incluirDoGuardian($refe_codigo){
    	$TRefeReferencia =& ClassRegistry::init('TRefeReferencia');
    	
    	try{
    		$referencia = $TRefeReferencia->carregarCompleto($refe_codigo,'INNER');
    		if(!$referencia)
    			throw new Exception('Alvo não localizado');

    		$cidade = array(
    				'Cidade'	=> array(
    					'Codigo' 		=> $this->novo_codigo(),
    					'Descricao'		=> $referencia['TCidaCidade']['cida_descricao'],
    					'Estado'		=> $referencia['TEstaEstado']['esta_sigla'],
    					'Status'		=> 'S',
    				)
    			);

    		return $this->save($cidade);

    	} catch( Exception $e ) {
    		return false;
    	}

    }

   
}
?>