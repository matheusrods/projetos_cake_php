<?php

class GrupoExposicaoRiscoEpi extends AppModel {

	var $name = 'GrupoExposicaoRiscoEpi';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupos_exposicao_risco_epi';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_grupos_exposicao_risco_epi'));

	var $validate = array(
		'codigo_epi' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o EPI!',
            ),
            'validaExposicaoRiscoEpi' => array(
                'rule' => 'validaExposicaoRiscoEpi',
                'message' => 'EPI já cadastrado!',
                'on' => 'create'
            )
        ),
		'codigo_grupos_exposicao_risco' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Grupo de Exposição',
			'required' => true
		),
	);


	function retorna_epi($codigo_grupos_exposicao_risco) {
		
		$this->Behaviors->_attached = array('Secure');
		$Epi =& ClassRegistry::Init('Epi');

		$conditions = array('codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco);
		
	 	$joins  = array(
	        array(
	          'table' => $Epi->databaseTable.'.'.$Epi->tableSchema.'.'.$Epi->useTable,
	          'alias' => 'Epi',
	          'type' => 'LEFT',
	          'conditions' => 'Epi.codigo = GrupoExposicaoRiscoEpi.codigo_epi',
	        )
	    );

	 	$fields = array(
	 		'Epi.codigo',
	 		'Epi.nome', 
	 		// 'Epi.numero_ca', 
	 		// 'Epi.data_validade_ca',
	 		'CONVERT(VARCHAR(10), GrupoExposicaoRiscoEpi.data_validade_ca, 103) AS data_validade_ca',
	 		//'Epi.atenuacao_qtd', 
	 		'GrupoExposicaoRiscoEpi.codigo',
	 		'GrupoExposicaoRiscoEpi.codigo_epi',
	 		'GrupoExposicaoRiscoEpi.codigo_grupos_exposicao_risco', 
	 		'GrupoExposicaoRiscoEpi.controle',
	 		'GrupoExposicaoRiscoEpi.epi_eficaz',
	 		'GrupoExposicaoRiscoEpi.numero_ca',
	 		'GrupoExposicaoRiscoEpi.data_validade_ca',
	 		'GrupoExposicaoRiscoEpi.atenuacao',
	 		'GrupoExposicaoRiscoEpi.med_protecao',
	 		'GrupoExposicaoRiscoEpi.cond_functo',
	 		'GrupoExposicaoRiscoEpi.uso_epi',
	 		'GrupoExposicaoRiscoEpi.prz_valid',
	 		'GrupoExposicaoRiscoEpi.periodic_troca',
	 		'GrupoExposicaoRiscoEpi.higienizacao'
	 	);
		$epis = $this->find("all", array('recursive' => -1, 'conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));

		foreach ($epis as $key => $value) {
			if(!empty($value['GrupoExposicaoRiscoEpi']['data_validade_ca'])) {
				$epis[$key]['GrupoExposicaoRiscoEpi']['data_validade_ca'] = date('d/m/Y', strtotime($value['GrupoExposicaoRiscoEpi']['data_validade_ca']));
			} else {
				$epis[$key]['GrupoExposicaoRiscoEpi']['data_validade_ca'] = '';

			}
			unset($epis[$key][0]);
		}
		return $epis;

	}

	
	function validaExposicaoRiscoEpi() {
        $conditions = array(
            'codigo_grupos_exposicao_risco' => $this->data['GrupoExposicaoRiscoEpi']['codigo_grupos_exposicao_risco'],
            'codigo_epi' => $this->data['GrupoExposicaoRiscoEpi']['codigo_epi'],
            );

        $fields = array(
            'GrupoExposicaoRiscoEpi.codigo','GrupoExposicaoRiscoEpi.codigo_grupos_exposicao_risco','GrupoExposicaoRiscoEpi.codigo_epi','GrupoExposicaoRiscoEpi.controle'
        );

        $validar = $this->find('first', array('conditions' => $conditions, 'fields' => $fields));
        
        if(empty($validar)){
            return true;
        }
        else{         
            return false;
        }
    }
    
    function grupo_exposicao_risco_epi_importacao($data){
		$this->Epi =& ClassRegistry::Init('Epi');
		
		$retorno['Dados'] = array();
		$retorno['Erro'] = array();
		$c = 0;
		$erro_epi = '';
		if(!empty($data['DadoArquivo']['epi'])){
    		$codigo_grupo_exposicao_risco = $data['GrupoExposicaoRisco']['codigo'];
			$itens = explode('|', $data['DadoArquivo']['epi']);

			foreach ($itens as $chave => $linha_epi) {
				if(!empty($linha_epi)){
					$epi = explode(':', $linha_epi);
					$nome_epi = (empty($epi[0])? '':$epi[0]);
					if(!empty($nome_epi)){
						$localiza_epi = $this->Epi->localiza_epi_importacao(utf8_encode($nome_epi));	
					
						if(!empty($epi[1])){
							$epi_recomendacao = explode('?', $epi[1]);
							$recomendacao = (empty($epi_recomendacao[0])? '' : $epi_recomendacao[0]);
							$eficaz = (empty($epi_recomendacao[1])? '' : $epi_recomendacao[1]);
						}
						else{
							$recomendacao = '';
							$eficaz = '';
						}

						
						if(!empty($localiza_epi['Erro'])){
							$erro_epi .= (empty($localiza_epi['Erro']['codigo_epi']))? '' : $localiza_epi['Erro']['codigo_epi']."|";
							$retorno['Erro']['GrupoExposicaoRiscoEpi'] = array('codigo_epi' => $erro_epi);
							$c++;
						}
						else{
							$dados_epi = array(
								'GrupoExposicaoRiscoEpi'=> array(
									'codigo_epi' => $localiza_epi['Dados']['Epi']['codigo'],
									'codigo_grupos_exposicao_risco' => $data['GrupoExposicaoRisco']['codigo'],
									'controle' => $recomendacao, 
									'epi_eficaz' => (empty($eficaz)? '' : ($eficaz == 'S')? 1 : 0)
								)
							);

							if(!parent::incluir($dados_epi, false)){
		  						$retorno['Erro'][$chave] = $this->validationErrors;
					        }
					        else{
					            if(!empty($this->id)){
					                $consulta_dados = $this->find("first", array('conditions' => array('codigo' => $this->id)));

					                if(empty($consulta_dados)){
					                    $retorno['Erro']['GrupoExposicaoRiscoEpi'][$chave] = array('codigo_grupo_exposicao_risco' => 'Erro ao gravar o Grupo de Exposicao Risco Epi!');
					                }
					                else{
					                    $retorno['Dados']['GrupoExposicaoRiscoEpi'][$chave] = $consulta_dados['GrupoExposicaoRiscoEpi'];
					                }
					            }
					        }
						}
					}
				}
			}//for
    	}

	return $retorno; 
	}
}

?>