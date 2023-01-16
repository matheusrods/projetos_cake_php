<?php
class RenovacaoAutomaticaScorecardShell extends Shell {
	var $uses = array( 
		'RenovacaoAutomatica','FichaScorecard','ClienteProduto','Servico', 'FichaScorecardStatus',
		'Produto', 'LogFaturamentoTeleconsult', 'TipoOperacao', 'Cliente', 'ClienteProdutoServico',
		'FichaScorecardLog'
	);
	
	public function main(){
		echo 'Renova as fichas que irão vencer em 7 dias e não foram renovadas pelo cliente.';
	}
	
	public function processar(){
		 $options = array('RenovacaoAutomatica' => array('dias_renovacao' => 15));
		 if( $this->RenovacaoAutomatica->salvarRenovacoesAutomaticas($options) ){
		 	$this->renovar( );
		 	echo 'Fichas de scorecard de profissionais renovadas com sucesso';		 	
		 } else {
		 	echo 'Erro ao renovar fichas de socorecard dos profissionais';		 	
		 }
	}

	public function renovar( ) {
		$listagem = $this->RenovacaoAutomatica->listaProfissionaisRenovar(  );		
		foreach ($listagem as $key => $dados_renovacao ) {
			$this->FichaScorecard->realizaRenovacaoAutomatica( $dados_renovacao['RenovacaoAutomatica'] );
		}
	}

	public function pesquisar(){
		App::import('Usuario');
		App::import('FichaScorecardStatus');
	    $fichas = $this->FichaScorecard->find('all',
	    	array('conditions' => array(	    				
							'FichaScorecard.codigo_status' => 1,
							'FichaScorecard.ativo' => 1	    				
	    				)
	    		)
	    	);
	    foreach($fichas as $ficha){    		
			$codigo_ficha = $ficha['FichaScorecard']['codigo'];			
			$aprovar = $this->FichaScorecard->pesquisador_automatico_scorecard($codigo_ficha); 				
			if(!$aprovar){					
				$ficha['FichaScorecard']['codigo_status'] = FichaScorecardStatus::A_PESQUISAR;
				$this->FichaScorecard->atualizar($ficha);
				
			}else{
				echo 'ok\n';
			}
		}
	}
}
?>