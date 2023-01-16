<?php

class GrupoExpRiscoFonteGera extends AppModel {

	var $name = 'GrupoExpRiscoFonteGera';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupos_exposicao_risco_fontes_geradoras';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_grupos_exposicao_risco_fontes_geradoras'));


	var $validate = array(
		'codigo_fontes_geradoras' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a Fonte Geradora!',
                'required' => true
            ),
            'validaExposicaoRiscoFonteGeradora' => array(
                'rule' => 'validaExposicaoRiscoFonteGeradora',
                'message' => 'Fonte Geradora já cadastrada!',
                'on' => 'create',
                'required' => true
            )
        ),
		'codigo_grupos_exposicao_risco' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Grupo de Exposição',
			'required' => true
		),
	);

	function retorna_fonte_geradora($codigo_grupos_exposicao_risco) {
		$FonteGeradora =& ClassRegistry::Init('FonteGeradora');

		$conditions = array('codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco);
		

	 	$joins  = array(
	        array(
	          'table' => $FonteGeradora->databaseTable.'.'.$FonteGeradora->tableSchema.'.'.$FonteGeradora->useTable,
	          'alias' => 'FonteGeradora',
	          'type' => 'LEFT',
	          'conditions' => 'FonteGeradora.codigo = GrupoExpRiscoFonteGera.codigo_fontes_geradoras',
	        )
	    );

	 	$fields = array(
	 		'FonteGeradora.codigo','FonteGeradora.nome',
	 		'GrupoExpRiscoFonteGera.codigo','GrupoExpRiscoFonteGera.codigo_fontes_geradoras','GrupoExpRiscoFonteGera.codigo_grupos_exposicao_risco'
	 	);

		$fonte_geradora = $this->find("all", array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));

		return $fonte_geradora;
	
	}

	 function validaExposicaoRiscoFonteGeradora() {
        $conditions = array(
            'codigo_grupos_exposicao_risco' => $this->data['GrupoExpRiscoFonteGera']['codigo_grupos_exposicao_risco'],
            'codigo_fontes_geradoras' => $this->data['GrupoExpRiscoFonteGera']['codigo_fontes_geradoras'],
            );

        $fields = array(
            'GrupoExpRiscoFonteGera.codigo','GrupoExpRiscoFonteGera.codigo_grupos_exposicao_risco','GrupoExpRiscoFonteGera.codigo_fontes_geradoras'
        );

        $validar = $this->find('first', array('conditions' => $conditions, 'fields' => $fields));

        if(empty($validar)){
            return true;
        }
        else{   
            return false;
        }
    }

    function grupo_exposicao_risco_fonte_geradora_importacao($dados, $data){
		$this->FonteGeradora =& ClassRegistry::Init('FonteGeradora');

		$retorno['Dados'] = array();
		$retorno['Erro']['GrupoExpRiscoFonteGera'] = array();
		$fonte_geradora = '';
		foreach ($dados as $chave => $nome_fonte_geradora) {
			$localiza_fonte_geradora = $this->FonteGeradora->localiza_fonte_geradora_importacao($nome_fonte_geradora);

			if(!empty($localiza_fonte_geradora['Erro'])){
				$fonte_geradora .= $localiza_fonte_geradora['Erro']['FonteGeradora']['codigo_fonte_geradora']."|";
				$retorno['Erro']['GrupoExpRiscoFonteGera'] = array('codigo_fonte_geradora' => $fonte_geradora);
			}
			else{
				$dados_fontes = array(
					'GrupoExpRiscoFonteGera'=> array(
						'codigo_fontes_geradoras' => $localiza_fonte_geradora['Dados']['FonteGeradora']['codigo'],
						'codigo_grupos_exposicao_risco' => $data['GrupoExposicaoRisco']['codigo']
					)
				);

				if(!parent::incluir($dados_fontes, false)){
  					$retorno['Erro'][$chave] = $this->validationErrors;
		        }
		        else{
		            if(!empty($this->id)){
		                $consulta_dados = $this->find("first", array('conditions' => array('codigo' => $this->id)));

		                if(empty($consulta_dados)){
		                    $retorno['Erro']['GrupoExpRiscoFonteGera'][$chave] = array('codigo_grupo_exposicao_risco' => 'Erro ao gravar o Grupo de Exposicao Risco Fonte Geradora!');
		                }
		                else{
		                    $retorno['Dados']['GrupoExpRiscoFonteGera'][$chave] = $consulta_dados['GrupoExpRiscoFonteGera'];
		                }
		            }
		        }

			}
		}

        return $retorno; 
    }
}

?>