<?php

class GrupoExposicaoRiscoEpc extends AppModel {

	var $name = 'GrupoExposicaoRiscoEpc';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupos_exposicao_risco_epc';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_grupos_exposicao_risco_epc'));

	var $validate = array(
		'codigo_epc' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o EPC!',
            ),
            'validaExposicaoRiscoEpc' => array(
                'rule' => 'validaExposicaoRiscoEpc',
                'message' => 'EPC já cadastrado!',
                'on' => 'create'
            )
        ),
		'codigo_grupos_exposicao_risco' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Grupo de Exposição',
			'required' => true
		),
	);

	function retorna_epc($codigo_grupos_exposicao_risco) {
		$Epc =& ClassRegistry::Init('Epc');

		$conditions = array('codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco);
		

	 	$joins  = array(
	        array(
	          'table' => $Epc->databaseTable.'.'.$Epc->tableSchema.'.'.$Epc->useTable,
	          'alias' => 'Epc',
	          'type' => 'LEFT',
	          'conditions' => 'Epc.codigo = GrupoExposicaoRiscoEpc.codigo_epc',
	        )
	    );

	 	$fields = array(
	 		'Epc.codigo','Epc.nome',
	 		'GrupoExposicaoRiscoEpc.codigo','GrupoExposicaoRiscoEpc.codigo_epc','GrupoExposicaoRiscoEpc.codigo_grupos_exposicao_risco', 'GrupoExposicaoRiscoEpc.controle'
	 	);

		$epc = $this->find("all", array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));
		
		return $epc;
	
	}

	function validaExposicaoRiscoEpc() {
        $conditions = array(
            'codigo_grupos_exposicao_risco' => $this->data['GrupoExposicaoRiscoEpc']['codigo_grupos_exposicao_risco'],
            'codigo_epc' => $this->data['GrupoExposicaoRiscoEpc']['codigo_epc'],
            );

        $fields = array(
            'GrupoExposicaoRiscoEpc.codigo','GrupoExposicaoRiscoEpc.codigo_grupos_exposicao_risco','GrupoExposicaoRiscoEpc.codigo_epc','GrupoExposicaoRiscoEpc.controle'
        );

        $validar = $this->find('first', array('conditions' => $conditions, 'fields' => $fields));

        if(empty($validar)){
            return true;
        }
        else{         
            return false;
        }
    }

    function grupo_exposicao_risco_epc_importacao($data){
		$this->Epc =& ClassRegistry::Init('Epc');
		
		$retorno['Dados'] = array();
		$retorno['Erro'] = array();
		$c = 0;
		$erro_epc = '';
		if(!empty($data['DadoArquivo']['epc'])){
    		$codigo_grupo_exposicao_risco = $data['GrupoExposicaoRisco']['codigo'];
			$item_epc = explode('|', $data['DadoArquivo']['epc']);
			
			foreach ($item_epc as $chave => $dados_epc) {
				$epc = explode(':', $dados_epc);
				$nome_epc = $epc[0];
				$recomendacao = (empty($epc[1]))? '' : $epc[1];

				$localiza_epc = $this->Epc->localiza_epc_importacao($nome_epc);	
				
				$erro_epc .= (empty($localiza_epc['Erro']['codigo_epc']))? '' : $localiza_epc['Erro']['codigo_epc']."|";

				if(!empty($localiza_epc['Erro'])){
					$retorno['Erro']['GrupoExposicaoRiscoEpc'] = array('codigo_epc' => $erro_epc);
					$c++;
				}
				else{
					$dados_epc = array(
						'GrupoExposicaoRiscoEpc'=> array(
							'codigo_epc' => $localiza_epc['Dados']['Epc']['codigo'],
							'codigo_grupos_exposicao_risco' => $data['GrupoExposicaoRisco']['codigo'],
							'controle' => $recomendacao 
						)
					);

					if(!parent::incluir($dados_epc, false)){
  						$retorno['Erro'][$chave] = $this->validationErrors;
			        }
			        else{
			            if(!empty($this->id)){
			                $consulta_dados = $this->find("first", array('conditions' => array('codigo' => $this->id)));

			                if(empty($consulta_dados)){
			                    $retorno['Erro']['GrupoExposicaoRiscoEpc'][$chave] = array('codigo_grupo_exposicao_risco' => 'Erro ao gravar o Grupo de Exposicao Risco Epc!');
			                }
			                else{
			                    $retorno['Dados']['GrupoExposicaoRiscoEpc'][$chave] = $consulta_dados['GrupoExposicaoRiscoEpc'];
			                }
			            }
			        }
				}
			}
    	}
    	
		return $retorno; 
	}
}

?>