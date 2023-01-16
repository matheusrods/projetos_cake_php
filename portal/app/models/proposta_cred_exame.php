<?php
class PropostaCredExame extends AppModel {

    var $name = 'PropostaCredExame';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento_exames';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_propostas_credenciamento_exames'));
    
	var $validate = array(
        'codigo_proposta_credenciamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Proposta!'
		),	
        'codigo_exame' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Exame!'
		)
	);
	
	public function _organizaExamesCoresCampos($exames) {
		
		foreach($exames as $k => $exame) {
			
			// formata valores
			$exames[$k]['PropostaCredExame']['valor'] = !is_null($exame['PropostaCredExame']['valor']) ? number_format($exame['PropostaCredExame']['valor'],2,',','.') : null;
			$exames[$k]['PropostaCredExame']['valor_contra_proposta'] = !is_null($exame['PropostaCredExame']['valor_contra_proposta']) ? number_format($exame['PropostaCredExame']['valor_contra_proposta'],2,',','.') : null;
			$exames[$k]['PropostaCredExame']['valor_minimo'] = !is_null($exame['PropostaCredExame']['valor_minimo']) ? number_format($exame['PropostaCredExame']['valor_minimo'],2,',','.') : null;
			
			$exames[$k]['ListaDePrecoProdutoServico']['valor_base'] = (isset($exame['ListaDePrecoProdutoServico']['valor']) && !is_null($exame['ListaDePrecoProdutoServico']['valor'])) ? number_format($exame['ListaDePrecoProdutoServico']['valor'],2,',','.') : null;
			
			// valida aprovação para mostrar estilo de cor, informando qual é o valor aprovado valido!!!
			if($exame['PropostaCredExame']['valor'] && $exame['PropostaCredExame']['valor_contra_proposta'] && $exame['PropostaCredExame']['aceito'] == '1') {
				if(!$exame['PropostaCredExame']['valor_minimo']) {
					$exames[$k]['Style']['valor_1'] = '';
					$exames[$k]['Style']['valor_2'] = 'border: 2px solid green;';
					$exames[$k]['Style']['valor_3'] = '';
				} else {
					$exames[$k]['Style']['valor_1'] = '';
					$exames[$k]['Style']['valor_2'] = '';
					$exames[$k]['Style']['valor_3'] = 'border: 2px solid green;';
				}
			} else if(($exame['PropostaCredExame']['valor'] && $exame['PropostaCredExame']['valor_contra_proposta']) && $exame['PropostaCredExame']['aceito'] == '0') {
				if(!$exame['PropostaCredExame']['valor_minimo']) {
					$exames[$k]['Style']['valor_1'] = '';
					$exames[$k]['Style']['valor_2'] = 'border: 2px solid #BD362F;';
					$exames[$k]['Style']['valor_3'] = '';					
				} else {
					$exames[$k]['Style']['valor_1'] = '';
					$exames[$k]['Style']['valor_2'] = '';
					$exames[$k]['Style']['valor_3'] = 'border: 2px solid #BD362F;';
				}
			} else if($exame['PropostaCredExame']['valor'] && !$exame['PropostaCredExame']['valor_contra_proposta'] && $exame['PropostaCredExame']['aceito'] == '1') {
				$exames[$k]['Style']['valor_1'] = 'border: 2px solid green;';
				$exames[$k]['Style']['valor_2'] = '';
				$exames[$k]['Style']['valor_3'] = '';				
			} else if($exame['PropostaCredExame']['valor'] && !$exame['PropostaCredExame']['valor_contra_proposta'] && $exame['PropostaCredExame']['aceito'] == '0') {
				$exames[$k]['Style']['valor_1'] = 'border: 2px solid red;';
				$exames[$k]['Style']['valor_2'] = '';
				$exames[$k]['Style']['valor_3'] = '';
			} else if($exame['PropostaCredExame']['aceito'] == null) {
				$exames[$k]['Style']['valor_1'] = '';
				$exames[$k]['Style']['valor_2'] = '';
				$exames[$k]['Style']['valor_3'] = '';				
			} else {
				$exames[$k]['Style']['valor_1'] = '';
				$exames[$k]['Style']['valor_2'] = '';
				$exames[$k]['Style']['valor_3'] = '';				
			}
		}
		
		return $exames;
	} 
}
