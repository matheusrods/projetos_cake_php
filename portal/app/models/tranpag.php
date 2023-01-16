<?php
class Tranpag extends AppModel {
    var $name = 'Tranpag';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'tranpag';
    var $primaryKey = null;
    var $actsAs = array('Secure');
    
    public function bindLazyClassificacoes() {
        $this->bindModel(array(
           'belongsTo' => array(
               'Tranpcc' => array(
                    'className' => 'Tranpcc'
                   ,'foreignKey' => false
                   ,'type' => 'INNER'
                   ,'conditions' => '
                        Tranpcc.numero = Tranpag.numero AND
                        Tranpcc.serie = Tranpag.serie AND
                        Tranpcc.emitente = Tranpag.emitente AND
                        Tranpcc.ordem = Tranpag.ordem AND
                        Tranpcc.tipodoc = Tranpag.tipodoc'
               ),
               'Planoct' => array(
                      'className' => 'Planoct'
                    ,'foreignKey' => false
                    ,'type' => 'INNER'
                    ,'conditions' => 'Planoct.numred = Tranpcc.numconta'
               ),
               'Grflux' => array(
                     'className' => 'Grflux'
                   ,'foreignKey' => false
                   ,'type' => 'LEFT'
                   ,'conditions' => 'Grflux.codigo = Tranpag.grflux'
               ),
               'Sbflux' => array(
                     'className' => 'Sbflux'
                   ,'foreignKey' => false
                   ,'type' => 'LEFT'
                   ,'conditions' => 'Sbflux.codigo = Tranpag.sbflux AND Sbflux.grflux = Grflux.codigo'
                )
            )
        ));
    }
	
	function prazoMedioRecebimento($filtros) {
		$this->LojaNaveg = ClassRegistry::init('LojaNaveg');
		$this->FornecNaveg = ClassRegistry::init('FornecNaveg');
		
		if ($this->useDbConfig != 'test_suite') {
            if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
                $this->databaseTable = 'dbNavegarqLider';
				$this->FornecNaveg->databaseTable = 'dbNavegarqLider';
				$this->LojaNaveg->databaseTable = 'dbNavegarqLider';
			}
            if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
                $this->databaseTable = 'dbNavegarqNatec';
				$this->FornecNaveg->databaseTable = 'dbNavegarqNatec';
				$this->LojaNaveg->databaseTable = 'dbNavegarqNatec';
			}
        }
		
		$fields = array(
			'SUBSTRING(CONVERT(VARCHAR,Tranpag.dtemiss, 103), 4, 7) AS ano_mes',
			'AVG(datediff(dd, dtemiss, dtvencto)) AS dias_medio',
			'AVG(datediff(dd, dtemiss, dtpagto)) AS pagamento_medio',
			'count(distinct(numero)) AS qtd_titulos'
		);
		
		$group = array('SUBSTRING(CONVERT(VARCHAR,Tranpag.dtemiss, 103), 4, 7)');
		
		$joins = array(
			array(
				'table' => $this->FornecNaveg->databaseTable . '.' . $this->FornecNaveg->tableSchema . '.' . $this->FornecNaveg->useTable,
				'alias' => $this->FornecNaveg->name,
				'type' => 'LEFT',
				'conditions' => 'FornecNaveg.codigo = Tranpag.emitente',
			),
			array(
				'table' => $this->LojaNaveg->databaseTable . '.' . $this->LojaNaveg->tableSchema . '.' . $this->LojaNaveg->useTable,
				'alias' => $this->LojaNaveg->name,
				'type' => 'LEFT',
				'conditions' => 'LojaNaveg.codigo = Tranpag.empresa',
			)
		);
		
		$conditions = array(
			'YEAR(Tranpag.dtemiss)' => $filtros['ano']
		);
		
		if (isset($filtros['empresa']) && !empty($filtros['empresa'])) {
			$conditions['Tranpag.empresa'] = $filtros['empresa'];
		}
		
		$order = array('ano_mes ASC');
		
		return $this->find('all', compact('conditions', 'fields', 'group', 'joins', 'order'));
		
	}
    
}
?>