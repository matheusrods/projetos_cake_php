<?php

class FichaScorecardPesquisaArtCriminal extends AppModel {

    var $name = 'FichaScorecardPesquisaArtCriminal';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'fichascorecard_pesquisa_artigo_criminal';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $validate = array(
        'codigo' => 'notEmpty'
    );

    function duplicar($codigo_ficha_pesquisa_antiga, $codigo_ficha_pesquisa_nova) {
        try {
            if (empty($codigo_ficha_pesquisa_antiga)) {
                throw new Exception();
            }
            $artigos = $this->find('all', array(
                'conditions' => array(
                    'codigo_ficha_pesquisa' => $codigo_ficha_pesquisa_antiga
                )
                    ));
            if (count($artigos) == 0) {
                return true;
            }
            foreach ($artigos as $artigo) {
                $artigo['FichaPesquisaArtCriminal']['codigo_ficha_pesquisa'] = $codigo_ficha_pesquisa_nova;
                $result = $this->incluir($artigo);
                if (!$result) {
                    throw new Exception('Não foi possível inserir o artigo');
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function salvarDaFicha($codigo_ultima_ficha_pesquisa, $codigo_ficha_pesquisa){
    	$artigos = $this->find('all', array('conditions'=>array('codigo_ficha_pesquisa'=>$codigo_ultima_ficha_pesquisa)));
    	
    	if (!empty($artigos)) {
    		foreach ($artigos as $artigo) {
    			unset($artigo['FichaPesquisaArtCriminal']['codigo']);
    			$artigo['FichaPesquisaArtCriminal']['codigo_ficha_pesquisa'] = $codigo_ficha_pesquisa;
    			$this->create();
    			if (!$this->save($artigo)) {
    				return false;
    			}
    		}
    	}
    }
}