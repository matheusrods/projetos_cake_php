<?php
class OcorrenciasTiposController extends AppController {
    var $name = 'OcorrenciasTipos';
    var $helpers = array('Highcharts');
    var $uses = array('OcorrenciaTipo', 'TipoOcorrencia');

    function sla_tipos_ocorrencia() {
    	$this->pageTitle = 'SLA por Tipo de Ocorrência';
        $dados = $this->carrega_series_sla();
        if (empty($dados))
            $this->BSession->setFlash('no_data');
        $this->set(compact('dados'));
    }
    
    function carrega_series_sla() {
        $periodo = array(
        	'20120501 00:00:00',
        	'20151231 23:59:59'
        );
        $dados = $this->OcorrenciaTipo->listaSLA($periodo, 30);
        if ($dados) {
	        $pre_series = array();
	        $eixo_x = array();
	        foreach ($dados as $dado) {
	            $eixo_x[] = "'".iconv('ISO-8859-1', 'UTF-8', $dado['TipoOcorrencia']['descricao'])."'";
	            $pre_series['dentro'][] = $dado[0]['dentro'];
	            $pre_series['fora'][] = $dado[0]['fora'];
	        }
	        $series = array(
	            array('name' => "'Dentro do SLA'",
	                  'values' => $pre_series['dentro'],
	            ),
	            array('name' => "'Fora do SLA'",
	                  'values' => $pre_series['fora'],
	            ),
	        );
	        return array('eixo_x' => $eixo_x, 'series' => $series);
	      }
    }
}