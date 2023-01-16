<?php
class DisparoLink extends AppModel {

    public $name = 'DisparoLink';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'disparos_links';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

    /**
     * [converteFiltroEmCondition description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function converteFiltroEmCondition($data) 
    {

		$conditions = array();
		if (!empty($data['codigo_cliente'])) {
			$conditions['DisparoLink.codigo_cliente'] = $data['codigo_cliente'];
		}

		if (!empty($data['mes_confirmacao'])) {
			$conditions['MONTH(DisparoLink.data_inclusao)'] = $data['mes_confirmacao'];
		}

		if (!empty($data['ano_confirmacao'])) {
			$conditions['YEAR(DisparoLink.data_inclusao)'] = $data['ano_confirmacao'];
		}

		if ($data['status_confirmacao'] != "") {
			$conditions['DisparoLink.status_validacao'] = $data['status_confirmacao'];
		}

		return $conditions;
	} //fim convertFiltroEmConditions

	

} //fim DisparoLink