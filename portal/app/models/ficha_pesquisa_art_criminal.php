<?php

class FichaPesquisaArtCriminal extends AppModel {

    var $name = 'FichaPesquisaArtCriminal';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_pesquisa_artigo_criminal';
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

    
    function listagem_apontamentos($codigo_documento){
         
         $fields = array('FichaPesquisaArtCriminal.codigo',
                         'ArtigoCriminal.nome',
                         'ArtigoCriminal.descricao',
                         'FichaPesquisaArtCriminal.data_ocorrencia',
                         'FichaPesquisaArtCriminal.local_ocorrencia',
                         'FichaPesquisaArtCriminal.inquerito',
                         'FichaPesquisaArtCriminal.processo',
                         'FichaPesquisaArtCriminal.observacao',
                         'Prestador.nome',
                         'SituacaoProcesso.descricao',
                         'FichaPesquisaArtCriminal.data_inclusao',
                         'Usuario.apelido');
         $joins = array( 
                            
                            array(
                                "table"     => "dbBuonny.publico.artigo_criminal",
                                "alias"     => "ArtigoCriminal",
                                "type"      => "INNER",
                                "conditions"=> array("FichaPesquisaArtCriminal.codigo_artigo_criminal = ArtigoCriminal.codigo")
                            ),
                            array(
                                "table"     => "dbBuonny.publico.prestador",
                                "alias"     => "Prestador",
                                "type"      => "INNER",
                                "conditions"=> array("FichaPesquisaArtCriminal.codigo_prestador = Prestador.codigo")
                            ),
                            array(
                                "table"     => "dbTeleconsult.informacoes.situacao_processo",
                                "alias"     => "SituacaoProcesso",
                                "type"      => "INNER",
                                "conditions"=> array("FichaPesquisaArtCriminal.codigo_situacao_processo = SituacaoProcesso.codigo")
                            ),
                            array(
                                "table"     => "dbTeleconsult.informacoes.ficha",
                                "alias"     => "Ficha",
                                "type"      => "INNER",
                                "conditions"=> array("FichaPesquisaArtCriminal.codigo_ficha_pesquisa  = Ficha.codigo")
                            ),
                            array(
                                "table"     => "dbBuonny.publico.profissional_log",
                                "alias"     => "ProfissionalLog",
                                "type"      => "INNER",
                                "conditions"=> array("Ficha.codigo_profissional_log = ProfissionalLog.codigo")
                            ),
                            array(
                                "table"     => "dbBuonny.portal.usuario",
                                "alias"     => "Usuario", 
                                "type"      => "INNER",
                                "conditions"=> array("FichaPesquisaArtCriminal.codigo_usuario_inclusao = Usuario.codigo")
                            )
                        );
        
        $conditions['ProfissionalLog.codigo_documento'] = $codigo_documento;

        $return = $this->find('all', array('fields' => $fields,'joins'=>$joins, 'conditions' =>$conditions));

        return $return;
         
    }



}