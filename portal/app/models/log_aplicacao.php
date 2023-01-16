<?php
class LogAplicacao extends AppModel {
    var $name = 'LogAplicacao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'logs_aplicacoes';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    var $codigo_cliente = null;
    var $sistema = null;

    const INFO 	= 1;
    const WARN 	= 2;
    const ERROR = 3;

    function incluirLog($conteudo, $tipo_log = self::INFO,$sistema = null, $enviar_para_arquivo = null){
        if($sistema) $this->sistema = $sistema;
        
        $logAplicacao = array(
            'LogAplicacao' => array(
                'conteudo'          => $conteudo,
                'codigo_cliente'    => $this->codigo_cliente,
                'sistema'           => substr($this->sistema,0,20),
                'tipo'              => $tipo_log,
                'tratado'           => ($tipo_log == self::ERROR ? 0 : 1),
            ),
        );
        if (false && $enviar_para_arquivo) {
            $this->log(print_r($logAplicacao, true), $enviar_para_arquivo);
        }

    	return $this->incluir($logAplicacao);
    }

    public function converteFiltrosEmConditions($filtros){
    	$conditions = array();

    	if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) 
    		$conditions['codigo_cliente'] = $filtros['codigo_cliente'];

        if (isset($filtros['hora_inicial']) && !empty($filtros['hora_inicial']) && $filtros['hora_inicial'] <> '__:__')
            $filtros['data_inicial'] .= ' '.$filtros['hora_inicial'];
        else
            $filtros['data_inicial'] .= ' 00:00:00';

        if (isset($filtros['hora_final']) && !empty($filtros['hora_final']) && $filtros['hora_final'] <> '__:__')
            $filtros['data_final'] .= ' '.$filtros['hora_final'].':59';
        else
            $filtros['data_final'] .= ' 23:59:59';

    	if ((isset($filtros['data_inicial']) && !empty($filtros['data_inicial'])) && (isset($filtros['data_final']) && !empty($filtros['data_final'])))
    		$conditions['data_inclusao BETWEEN ? AND ?'] = array(AppModel::dateToDbDate2($filtros['data_inicial']),AppModel::dateToDbDate2($filtros['data_final']));
    	
    	if (isset($filtros['descricao']) && !empty($filtros['descricao'])) 
            $conditions['conteudo LIKE'] = '%'.$filtros['descricao'].'%';

        if (isset($filtros['sistema']) && !empty($filtros['sistema'])) 
            $conditions['sistema'] = $filtros['sistema'];

        return $conditions;
    }

    public function listarSistemas(){
        $sistemas = $this->find('all',array('fields'=>'DISTINCT sistema', 'order' => 'sistema'));
        foreach($sistemas as $key => $value){
        	$sistemas[$value['LogAplicacao']['sistema']] = $value['LogAplicacao']['sistema'];
        	unset($sistemas[$key]);
        }
        return $sistemas;
    }

    public function listarSistemasResumido(){
        return array(
                    'Finalizador'  => 'Finalizador', 
                    'Reprogramador' => 'Reprogramador', 
                    'Inicializador' => 'Inicializador'
                );
    }

}
?>