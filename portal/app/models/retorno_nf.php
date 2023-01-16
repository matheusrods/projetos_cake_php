<?php
class RetornoNf extends AppModel {
	var $name = 'RetornoNf';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth'; 
	var $useTable = 'retornos_nfs';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');	
	var $validates = array(
	    'nota_fiscal' => array(
	        array(
    	        'rule' => 'notEmpty',
    	        'message' => 'Informe a Nota Fiscal'
    	    ),
    	    array(
    	        'rule' => 'isUnique',
    	        'message' => 'Nota Fiscal ja enviada'
    	    )
	    ),
	    'codigo_verificacao' => array(
	        'rule' => 'notEmpty',
	        'message' => 'Informe o Código de Verificação'
	    ),
	);
	var $hasMany = array(
        'RetornoNfLink' => array(
            'className' => 'RetornoNfLink',
            'foreignKey' => 'codigo_retorno_nf'
	    )
	);
	
	function listaParaEnviar() {
	    return $this->find('all', array('conditions' => array('data_envio' => null)));
	}

	function selectJaEnviado() {
		$conditions = array('not' => array('RetornoNf.data_envio' => null));
		$dbo = $this->getDataSource();
	    $base_select = array(
            'fields' => array(
                'nota_fiscal', 
            ),
            'table' => $this->useTable,
            'databaseTable' => $this->databaseTable,
            'tableSchema' => $this->tableSchema,
            'alias' => $this->name,
            'conditions' => $conditions,
            'order' => null,
            'limit' => null,
            'group' => null,
        );
        return $dbo->buildStatement($base_select, $this);
	}
	
	function carregarPeriodo($periodo) {
	    $this->Gernfe = ClassRegistry::init('Gernfe');

        $query_retorno_nf_nao_enviados = "SELECT codigo FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} WHERE data_envio IS NULL";
	    $select_desconsiderar = $this->selectJaEnviado();

	    $notas = $this->Gernfe->notasDoPeriodo($periodo, $select_desconsiderar);
	    if(!$notas)return false;

	    try {
	        $this->query('begin transaction');
	        $this->query("DELETE FROM {$this->RetornoNfLink->databaseTable}.{$this->RetornoNfLink->tableSchema}.{$this->RetornoNfLink->useTable} WHERE codigo_retorno_nf IN ($query_retorno_nf_nao_enviados)");
	        $this->query("DELETE FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} WHERE data_envio IS NULL");
    	    foreach ($notas as $nota) {
    	        $dados = array('RetornoNf' => array(
    	        	'nota_fiscal' => $nota['Gernfe']['numnfe'], 
    	        	'codigo_verificacao' => $nota['Gernfe']['protocolo'],
    	        	'codigo_empresa' => 1 // Multiempresa - Processo funciona somente na RHHealth
    	        ));
    	        if (!$this->incluir($dados)) throw new Exception();
    	    }
    	    $this->commit();
    	    return true;
    	} catch (Exception $ex) {
    	    $this->rollback();
    	    return false;
    	}
	}
	
	function marcaEnvio($dados, $links) {
	    try {
	        $this->query('begin transaction');
	        if (!$this->atualizar($dados)) throw new Exception();
	        if (!$this->RetornoNfLink->atualizar($dados[$this->name]['codigo'], $links, true)) throw new Exception();
	        $this->commit();
	        return true;
	    } catch (Exception $ex) {
	        $this->rollback();
	        return false;
	    }
	}
	
	function atualizar($dados) {
	    if (!isset($dados[$this->name]['codigo']) || empty($dados[$this->name]['codigo']))
	        return false;
	    $dados[$this->name]['data_envio'] = Date('Ymd H:i:s');
	    return $this->save($dados);
	}
	
	function dadosEnvioFaturamento($filtro) {
		App::import('Model', array('Mailer.Outbox'));
	    $this->bindModel(array(
	    	'hasOne' => array(
		    	'Outbox' => array('foreignKey' => 'foreign_key', 'conditions' => array('Outbox.model' => 'RetornoNf')),
		    	'Gernfe' => array('foreignKey' => false, 'conditions' => array("Gernfe.numnfe = RetornoNf.nota_fiscal")),
		    	'Tranrec' => array('foreignKey' => false, 'conditions' => array("Tranrec.empresa = Gernfe.empresa and Tranrec.numero = Gernfe.numero and Tranrec.debcred = 'C'")),
		    	'Banco' => array('foreignKey' => false, 'conditions' => array("Banco.codigo = Tranrec.banco")),
	    	),
	    ));
	    return $this->find('first', array('conditions' => array('RetornoNf.nota_fiscal' => str_pad($filtro['RetornoNf']['nota_fiscal'], 8, '0', STR_PAD_LEFT))));
	}

	function porClienteEPeriodo($codigo_cliente, $data_inicial) {
		$this->bindModel(array('belongsTo' => array(
			'Gernfe' => array('foreignKey' => false, 'conditions' => array("Gernfe.numnfe = RetornoNf.nota_fiscal")),
			'Notafis' => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => array('Notafis.empresa = Gernfe.empresa', 'Notafis.numero = Gernfe.numero', 'Notafis.seq = Gernfe.seq', 'Notafis.serie = Gernfe.serie', "Notafis.cliente = {$codigo_cliente}", 'Notafis.dtemissao >=' => $data_inicial)),
		)));
		$this->unbindModel(array('hasMany' => array('RetornoNfLink')));
		$Outbox = ClassRegistry::init('Mailer.Outbox');
		$fields = array(
			'RetornoNf.nota_fiscal', 
			'RetornoNf.codigo_verificacao',
			'Notafis.dtemissao',
			'Notafis.numero',
			"(SELECT TOP 1 CONVERT(VARCHAR, Outbox.sent, 120) FROM {$Outbox->databaseTable}.{$Outbox->tableSchema}.{$Outbox->useTable} AS Outbox WHERE foreign_key = RetornoNf.codigo AND sent IS NOT NULL AND model = 'RetornoNf' ORDER BY Outbox.id DESC) AS data_ultimo_envio",
		);
		$order = array('RetornoNf.codigo DESC');
		return $this->find('all', compact('fields', 'order'));
	}
}
?>
