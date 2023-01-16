<?php
class Ace1 extends AppModel {
    var $name = 'Ace1';
    var $tableSchema = 'informacoes1';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'ace1';
    var $primaryKey = 'codigo';

    function carregar() {
    	$Profissional =& ClassRegistry::init('Profissional');
    	$query = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (cpf, data)";
    	$query .= " SELECT {$Profissional->useTable}.codigo_documento, '1990-01-01 00:00:00' FROM {$Profissional->databaseTable}.{$Profissional->tableSchema}.{$Profissional->useTable} WITH(NOLOCK)";
    	$query .= " LEFT JOIN {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} ON {$this->useTable}.cpf = {$Profissional->useTable}.codigo_documento";
    	$query .= " WHERE {$this->useTable}.cpf IS NULL";
    	return ($this->query($query) !== false);
    }

    function separarRegistros($limit, $data_atualizacao) {
    	$ProfissionalContato =& ClassRegistry::init('ProfissionalContato');
        $dbo = $this->getDataSource();
        $query = $dbo->buildStatement(
            array(
                'fields' => array('Ace1.codigo'),
                'table' => $this->databaseTable.'.'.$this->tableSchema.'.'.$this->useTable,
                'alias' => "[{$this->name}] WITH(NOLOCK)",
                'limit' => $limit,
                'offset' => null,
                'joins' => $this->joins(),
                'conditions' => array(
                	'Profissional.data_inclusao >=' => Date('Y-m-d 00:00:00', strtotime('-2 year')),
                	"EXISTS(SELECT TOP 1 descricao 
                		FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} 
                		WHERE codigo_profissional = Profissional.codigo 
                		AND codigo_tipo_contato = 1 
                		AND codigo_tipo_retorno IN (1,5)
                		AND RIGHT(RTRIM(descricao),3) <> '000'
                		AND RIGHT(REPLACE(RTRIM(descricao),'-',''),8) BETWEEN '20000000' AND '99999999'
                	)",
                ),
                'order' => array('data'),
                'group' => null,
                ), $this
        );
        return ($this->query("UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} SET data = '{$data_atualizacao}' WHERE codigo IN ({$query})") !== false);
    }

    function queryListar($data_atualizacao) {
		$ProfissionalContato =& ClassRegistry::init('ProfissionalContato');
		$ProfissionalTipo =& ClassRegistry::init('ProfissionalTipo');
		$Ficha =& ClassRegistry::init('Ficha');
		$ProfissionalLog =& ClassRegistry::init('ProfissionalLog');
		$fields = array(
			'Ace1.codigo',
			'Ace1.cpf',
			'Ace1.data',
			'Profissional.nome',
			'Profissional.codigo_documento',
			'Profissional.rg',
			'CONVERT(VARCHAR, Profissional.data_nascimento, 120) AS data_nascimento',
			'VEndereco.endereco_tipo',
			'VEndereco.endereco_logradouro',
			'VEndereco.endereco_bairro',
			'VEndereco.endereco_cidade',
			'VEndereco.endereco_estado',
			'VEndereco.endereco_cep',
			'ProfissionalEndereco.numero',
			'ProfissionalEndereco.complemento',
			"(SELECT TOP 1 ddd FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 1 AND codigo_tipo_retorno IN (1,5)) AS ddd_residencial",
			"(SELECT TOP 1 descricao FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 1 AND codigo_tipo_retorno IN (1,5) AND RIGHT(RTRIM(descricao),3) <> '000' AND RIGHT(REPLACE(RTRIM(descricao),'-',''),8) BETWEEN '20000000' AND '99999999') AS descricao_residencial",
			"(SELECT TOP 1 nome FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 1 AND codigo_tipo_retorno IN (1,5)) AS nome_residencial",
			"(SELECT TOP 1 ddd FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 2 AND codigo_tipo_retorno IN (1,5)) AS ddd_comercial",
			"(SELECT TOP 1 descricao FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 2 AND codigo_tipo_retorno IN (1,5) AND RIGHT(RTRIM(descricao),3) <> '000' AND RIGHT(REPLACE(RTRIM(descricao),'-',''),8) BETWEEN '20000000' AND '99999999') AS descricao_comercial",
			"(SELECT TOP 1 nome FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 2 AND codigo_tipo_retorno IN (1,5)) AS nome_comercial",
			"(SELECT TOP 1 ddd FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 7 AND codigo_tipo_retorno IN (1,5)) AS ddd_referencia",
			"(SELECT TOP 1 descricao FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 7 AND codigo_tipo_retorno IN (1,5) AND RIGHT(RTRIM(descricao),3) <> '000' AND RIGHT(REPLACE(RTRIM(descricao),'-',''),8) BETWEEN '20000000' AND '99999999') AS descricao_referencia",
			"(SELECT TOP 1 nome FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 7 AND codigo_tipo_retorno IN (1,5)) AS nome_referencia",
			"(SELECT TOP 1 ddd FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 1 AND codigo_tipo_retorno IN (5)) AS ddd_celular",
			"(SELECT TOP 1 descricao FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 1 AND codigo_tipo_retorno IN (5)) AS descricao_celular",
			"(SELECT TOP 1 descricao FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} WITH(NOLOCK) WHERE codigo_profissional = Profissional.codigo AND codigo_tipo_contato = 1 AND codigo_tipo_retorno IN (2)) AS email_residencial",
			// 'ProfissionalTipo.descricao',
			"Profissional.data_inclusao as tempo_relacionamento",
			"(SELECT TOP 1 ProfissionalTipoFicha.descricao FROM {$Ficha->databaseTable}.{$Ficha->tableSchema}.{$Ficha->useTable} AS Ficha INNER JOIN {$ProfissionalLog->databaseTable}.{$ProfissionalLog->tableSchema}.{$ProfissionalLog->useTable} AS ProfissionalLog ON ProfissionalLog.codigo = Ficha.codigo_profissional_log INNER JOIN {$ProfissionalTipo->databaseTable}.{$ProfissionalTipo->tableSchema}.{$ProfissionalTipo->useTable} AS ProfissionalTipoFicha ON ProfissionalTipoFicha.codigo = Ficha.codigo_profissional_tipo WHERE ProfissionalLog.codigo_documento = Ace1.cpf ORDER BY Ficha.codigo DESC) AS descricao",
			"'Ativo' as status"
		);
		$order = array('Ace1.data');
		
		$conditions = array(
			'Ace1.data' => $data_atualizacao,
			"EXISTS(SELECT TOP 1 descricao 
				FROM {$ProfissionalContato->databaseTable}.{$ProfissionalContato->tableSchema}.{$ProfissionalContato->useTable} 
				WHERE codigo_profissional = Profissional.codigo 
				AND codigo_tipo_contato = 1 
				AND codigo_tipo_retorno IN (1,5) 
				AND RIGHT(RTRIM(descricao),3) <> '000'
                AND RIGHT(REPLACE(RTRIM(descricao),'-',''),8) BETWEEN '20000000' AND '99999999'
			)",
		);
		$dbo = $this->getDataSource();
		return $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => $this->databaseTable.'.'.$this->tableSchema.'.'.$this->useTable,
				'alias' => "[{$this->name}] WITH(NOLOCK)",
				'limit' => null,
				'offset' => null,
				'joins' => $this->joins(),
				'conditions' => $conditions,
				'order' => $order,
				'group' => null,
				), $this
		);
    }

    private function joins() {
    	$Profissional =& ClassRegistry::init('Profissional');
		$ProfissionalEndereco =& ClassRegistry::init('ProfissionalEndereco');
		$VEndereco =& ClassRegistry::init('VEndereco');
		$ProfissionalTipo =& ClassRegistry::init('ProfissionalTipo');
    	return array(
			array(
				'table' => "{$Profissional->databaseTable}.{$Profissional->tableSchema}.{$Profissional->useTable}",
				'alias' => '[Profissional] WITH(NOLOCK)',
				'type' => 'INNER',
				'conditions' => array('Ace1.cpf = Profissional.codigo_documento'),
			),
			array(
				'table' => "{$ProfissionalEndereco->databaseTable}.{$ProfissionalEndereco->tableSchema}.{$ProfissionalEndereco->useTable}",
				'alias' => '[ProfissionalEndereco] WITH(NOLOCK)',
				'type' => 'INNER',
				'conditions' => array('ProfissionalEndereco.codigo_profissional = Profissional.codigo'),
			),
			array(
				'table' => "{$VEndereco->databaseTable}.{$VEndereco->tableSchema}.{$VEndereco->useTable}",
				'alias' => '[VEndereco] WITH(NOLOCK)',
				'type' => 'INNER',
				'conditions' => array('VEndereco.endereco_codigo = ProfissionalEndereco.codigo_endereco'),
			),
			// array(
			// 	'table' => "{$ProfissionalTipo->databaseTable}.{$ProfissionalTipo->tableSchema}.{$ProfissionalTipo->useTable}",
			// 	'alias' => '[ProfissionalTipo] WITH(NOLOCK)',
			// 	'type' => 'LEFT',
			// 	'conditions' => array('ProfissionalTipo.codigo = Profissional.codigo_profissional_tipo'),
			// ),
		);
    }
}
?>