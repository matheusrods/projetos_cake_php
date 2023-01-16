<?php
class ProfNegativacaoClienteLog extends AppModel {
	var $name          = 'ProfNegativacaoClienteLog';
  var $databaseTable = 'dbTeleconsult';
	var $tableSchema   = 'informacoes';
	var $useTable      = 'profissional_negativacao_cliente_log';
	var $primaryKey    = 'codigo';
	var $actsAs        = array('Secure');
  var $belongsTo = array(
      'ProfNegativacaoCliente' => array(
          'class' => 'ProfNegativacaoCliente',
          'foreignKey' => 'codigo'
      )
  );
  
  public function converteFiltroEmCondition( $filtros ) {
    $condition = array();
    if (isset($filtros['codigo_documento']) && !empty($filtros["codigo_documento"])) {
      $condition["Profissional.codigo_documento LIKE"] = "%".COMUM::soNumero($filtros["codigo_documento"])."%";
    }
    if (isset($filtros['codigo_cliente']) && !empty($filtros["codigo_cliente"])) {
      $condition["ProfNegativacaoClienteLog.codigo_cliente"] = $filtros["codigo_cliente"];
    }
    if (isset($filtros['codigo_negativacao']) && !empty($filtros["codigo_negativacao"])) {
      $condition["ProfNegativacaoClienteLog.codigo_negativacao"] = $filtros["codigo_negativacao"];
    }        
    if (isset($filtros['data_inicial']) && !empty($filtros["data_inicial"]) &&  isset($filtros['data_final']) && !empty($filtros["data_final"]) ) {
        array_push($condition, array( 'ProfNegativacaoClienteLog.data_inclusao BETWEEN ? AND ? '=> array(
            AppModel::dateToDbDate($filtros['data_inicial'].' 00:00'), AppModel::dateToDbDate($filtros['data_final'].' 23:59') 
          )
        )
      );
    } 
    return $condition; 
  }
}
?>