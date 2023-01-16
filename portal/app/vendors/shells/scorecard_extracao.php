<?php
App::import('Component', 'Extracao');

class ScorecardExtracaoShell extends Shell {
	var $tasks = array('ScorecardPreenchimento');
	
	var $extracaoComponent = null;
	
	public function main() {
		echo "scorecard_extracao processar\n";
	}
	
	public function processar() {
		while (true) {
		    $proximoExtrair = $this->fichaScorecard()->proximoExtrair();
    		if ($proximoExtrair['FichaScorecard']['codigo']) {
    		    echo "extraindo ficha: {$proximoExtrair['FichaScorecard']['codigo']}\n";
    			$resposta = array();
    			//$resposta['denatran_cnh']     = $this->extracaoComponent()->denatranCnh($proximoExtrair['Profissional']['cpf'], $proximoExtrair['Profissional']['cnh'], $proximoExtrair['Profissional']['cnh_seguranca']);
    			//$resposta['denatran_veiculo'] = $this->extracaoComponent()->denatranVeiculo($proximoExtrair['Proprietario']['cpf'], $proximoExtrair['Veiculo']['renavam']);
    			//$resposta['stj']              = $this->extracaoComponent()->stj($proximoExtrair['Profissional']['nome']);
    			$this->ScorecardPreenchimento->processar($proximoExtrair['FichaScorecard']['codigo']);
    			//$this->fichaScorecard()->gravaExtracao($proximoExtrair['FichaScorecard']['codigo'], json_encode($resposta));
    			echo "pronto\n";   
    		} else {
    		    break;
    		}
		}
	}
	
	private function fichaScorecard() {
		return ClassRegistry::init('FichaScorecard');
	}
	
	private function extracaoComponent() {
		if ($this->extracaoComponent == null)
			$this->extracaoComponent = new ExtracaoComponent();
		return $this->extracaoComponent;
	}

}
?>
