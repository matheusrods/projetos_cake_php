<?php
class FichasScorecardArtCriminaisController extends AppController {
	public $name = 'FichasScorecardArtCriminais';
	public $uses = array('FichaScorecardArtCriminal');

	function listar_por_profissional($codigo_documento, $codigo_ficha_scorecard) {
		$conditions = array('ProfissionalLog.codigo_documento' => $codigo_documento);
		$ficha_scorecard_artigo_criminal = $this->FichaScorecardArtCriminal->listar($conditions);
		$this->set(compact('ficha_scorecard_artigo_criminal','codigo_documento'));
	}
    
    function carrega_combos(){
        $this->loadModel('Instituicao');
		$this->loadModel('IPrestador');
		$this->loadModel('SituacaoProcesso');
		$instituicoes = $this->Instituicao->find('list');
		$prestadores = $this->IPrestador->find('list');
		$situcoes_processos = $this->SituacaoProcesso->find('list');
		$this->set(compact('instituicoes', 'prestadores', 'situcoes_processos'));
    }

	function editar($codigo_documento, $codigo_ficha_scorecard,$codigo_ficha_scorecard_art_criminal) {
		
		if (!empty($this->data)) {
			$this->data['FichaScorecardArtCriminal']['codigo_ficha_scorecard'] = $codigo_ficha_scorecard;
			$this->data['FichaScorecardArtCriminal']['codigo'] =$codigo_ficha_scorecard_art_criminal;
			if ($this->FichaScorecardArtCriminal->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->trataValidacoes();
			}
		}else{
        	$this->data = $this->FichaScorecardArtCriminal->carregar($codigo_ficha_scorecard_art_criminal);
        	$this->data['FichaScorecardArtCriminal']['codigo_endereco_cidade_visual'] = $this->data['EnderecoCidade']['descricao'].' - '.$this->data['EnderecoEstado']['abreviacao'];
        	$this->data['FichaScorecardArtCriminal']['codigo_artigo_criminal_visual'] = $this->data['ArtigoCriminal']['nome'].' - '.$this->data['ArtigoCriminal']['descricao'];
		}
		$this->carrega_combos();
		
	}

    function excluir($codigo_ficha_scorecard_art_criminal,$codigo_ficha){
        
         
        if($this->FichaScorecardArtCriminal->excluir($codigo_ficha_scorecard_art_criminal)){
            $this->BSession->setFlash('delete_success');
            $this->redirect(array('controller'=>'fichas_status_criterios','action'=>'editar',$codigo_ficha,'1'));
            
        } else {
            $this->BSession->setFlash('delete_error'); 
        }
    }
	function incluir($codigo_documento, $codigo_ficha_scorecard) {
		if (!empty($this->data)) {
			$this->data['FichaScorecardArtCriminal']['codigo_ficha_scorecard'] = $codigo_ficha_scorecard;
			if ($this->FichaScorecardArtCriminal->incluir($this->data)) {
				$this->BSession->setFlash('save_success');

			} else {
				$this->trataValidacoes();
			}
		}
		$this->carrega_combos();
	}

	function trataValidacoes() {
		$validationErrors = $this->FichaScorecardArtCriminal->validationErrors;
		if (isset($validationErrors['codigo_artigo_criminal'])) {
			$this->FichaScorecardArtCriminal->invalidate('codigo_artigo_criminal_visual', $validationErrors['codigo_artigo_criminal']);
		}
		if (isset($validationErrors['codigo_endereco_cidade'])) {
			$this->FichaScorecardArtCriminal->invalidate('codigo_endereco_cidade_visual', $validationErrors['codigo_endereco_cidade']);
		}
	}
}