<?php
class PontuacaoSCProfissional extends AppModel {    
    var $name          = 'PontuacaoSCProfissional';
    var $databaseTable = 'dbTeleconsult';
    var $tableSchema   = 'informacoes';
    var $useTable      = 'pontuacoes_status_criterios_profissional';
    var $primaryKey    = 'codigo';

    function insereStatusCriterioProfissional( $codigo_pontuacao_status_criterio, $tipos_profissional ){
    	$deleteAll = $this->deleteAll(array('codigo_pontuacao_status_criterio'=>$codigo_pontuacao_status_criterio));    	
		if( is_array($tipos_profissional) && count($tipos_profissional) > 0 ){
			foreach( $tipos_profissional as $tipos => $codigo ){
				$data[] =
					array(
						'codigo_pontuacao_status_criterio' => $codigo_pontuacao_status_criterio,
						'codigo_tipo_profissional' => $codigo,
						'data_inclusao' => date('Y-m-d h:i:s')
					);
			}
			$saveAll = $this->saveAll($data, array('validate' => false));
			return $saveAll;
		}
    }
}   
