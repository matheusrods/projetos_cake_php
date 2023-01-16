<?php
class InformacaoCliente extends AppModel {
    var $name = 'InformacaoCliente';
    var $tableSchema = 'vendas';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'cliente';
    var $primaryKey = 'codigo';
    var $displayField = 'razao_social';
    var $actsAs = array('Secure');

		var $belongsTo = array(
			'AreaAtuacao' => array('foreignKey' => 'codigo_area_atuacao')
		);

    function converteFiltroEmCondition($data) {
        $conditions = array();
        if (!empty($data['codigo']))
            $conditions['InformacaoCliente.codigo'] = $data['codigo'];
        if (!empty($data['razao_social']))
            $conditions['InformacaoCliente.razao_social like'] = '%' . $data['razao_social'] . '%';
        if (!empty($data['codigo_area_atuacao']))
            $conditions['InformacaoCliente.codigo_area_atuacao'] = $data['codigo_area_atuacao'];
        if (!empty($data['codigo_sistema_monitoramento']))
            $conditions['InformacaoCliente.codigo_sistema_monitoramento'] = $data['codigo_sistema_monitoramento'];

        return $conditions;
    }

}
?>