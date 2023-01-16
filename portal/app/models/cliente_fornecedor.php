<?php

class ClienteFornecedor extends AppModel {

	public $name = 'ClienteFornecedor';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'clientes_fornecedores';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_cliente_fornecedor'));

	var $validate = array(
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente!',
			'required' => true
		),
		'codigo_fornecedor' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Fornecedor!',
			'required' => true
		),

	);

	function converteFiltroEmCondition($data) {
        $conditions = array();
        
        if (!empty($data['codigo_cliente']))
            $conditions['ClienteFornecedor.codigo_cliente'] = $data['codigo_cliente'];
       
       	if (!empty($data['codigo_fornecedor']))
            $conditions['ClienteFornecedor.codigo_fornecedor'] = $data['codigo_fornecedor'];

        if (!empty($data['fornecedor_codigo']))
            $conditions['Fornecedor.codigo'] = $data['fornecedor_codigo'];
       
       if (!empty($data['razao_social']))
        	$conditions['Fornecedor.razao_social like'] = '%' . $data['razao_social'] . '%';

		if (!empty($data['nome']))
        	$conditions['Fornecedor.nome like'] = '%' . $data['nome'] . '%';

        if (!empty($data['codigo_documento']))
            $conditions['Fornecedor.codigo_documento'] = Comum::soNumero($data['codigo_documento']);
     
        return $conditions;
    }

    function converteFiltroEmConditions($data){
    	$conditions = array();
        
        if (!empty($data['codigo_cliente'])) {
            $conditions['GrupoEconomicos.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (!empty($data['codigo_cliente_alocacao'])) {
            $conditions['GrupoEconomicoCliente.codigo_cliente'] = $data['codigo_cliente_alocacao'];
        }
       
       	if (!empty($data['codigo_fornecedor'])) {
            $conditions['ClienteFornecedor.codigo_fornecedor'] = $data['codigo_fornecedor'];
        }

        if (!empty($data['ativo'])){
            switch($data['ativo']) {
                case 'A': //realizado
                    $conditions['Fornecedor.ativo'] = 1;
                    break;
                case 'I': //não compareceu
                    $conditions['Fornecedor.ativo'] = 0;
                    break;
            }
        }

        return $conditions;
    }

    function converteFiltroClienteFornecedor($data) 
    {
        $conditions = array();
        
        if (!empty($data['codigo']))
            $conditions['Fornecedor.codigo'] = $data['codigo'];
        
        if (!empty($data['razao_social']))
            $conditions['Fornecedor.razao_social like'] = '%' . $data['razao_social'] . '%';

        if (!empty($data['nome']))
            $conditions['Fornecedor.nome like'] = '%' . $data['nome'] . '%';

        if (!empty($data['codigo_documento']))
            $conditions['Fornecedor.codigo_documento like'] = '%' . str_replace(array('.','/','-',''), '', $data['codigo_documento']) . '%';        

        if (!empty($data['ativo'])){
            if($data['ativo'] == '0')
                $conditions[] = '(Fornecedor.ativo = '.$data['ativo'].' OR Fornecedor.ativo IS NULL)';
            else if ($data['ativo'] == '1')
                $conditions ['Fornecedor.ativo'] = $data['ativo'];
        }

        return $conditions;
    }

    //metodo para auxiliar na busca fornecedores por cliente, para usar possiveis consultas no futuro
    function buscar_prestadores_por_cliente($codigo_cliente){
        //condicoes
         $conditions = array(
            'ClienteFornecedor.codigo_cliente' => $codigo_cliente,
            'ClienteFornecedor.ativo' => 1
        );
        //fields
        $fields = array(
            'ClienteFornecedor.codigo',
            'ClienteFornecedor.codigo_cliente',
            'ClienteFornecedor.ativo',
            'Fornecedor.codigo',
            'Fornecedor.nome',
            'Fornecedor.razao_social',
            'Fornecedor.codigo_documento',
            'Fornecedor.ativo',
            'FornecedorEndereco.cidade',
            'FornecedorEndereco.estado_descricao',
            '(SELECT count(*) serv_at FROM (SELECT LPPS.codigo_servico FROM listas_de_preco_produto_servico LPPS INNER JOIN listas_de_preco_produto LPP ON(LPP.codigo = LPPS.codigo_lista_de_preco_produto) INNER JOIN listas_de_preco LP ON(LP.codigo = LPP.codigo_lista_de_preco) INNER JOIN clientes_fornecedores CF ON(CF.codigo_fornecedor = LP.codigo_fornecedor) INNER JOIN cliente_produto_servico2 CPS ON(CPS.codigo_servico = LPPS.codigo_servico) INNER JOIN cliente_produto CP ON(CP.codigo = CPS.codigo_cliente_produto) WHERE LPPS.codigo_servico IN ( SELECT CPS3.codigo_servico FROM cliente_produto_servico2 CPS3 INNER JOIN cliente_produto CP3 ON(CP3.codigo = CPS3.codigo_cliente_produto) WHERE CP3.codigo_cliente = CP.codigo_cliente ) AND CP.codigo_cliente = ClienteFornecedor.codigo_cliente  AND CF.codigo_fornecedor = ClienteFornecedor.codigo_fornecedor  GROUP BY LPPS.codigo_servico) AS serv_at ) as serv_at',
            '(SELECT COUNT(CPS2.codigo_servico) AS total_at FROM cliente_produto_servico2 CPS2 INNER JOIN cliente_produto CP2 ON(CP2.codigo = CPS2.codigo_cliente_produto) WHERE CP2.codigo_cliente = ClienteFornecedor.codigo_cliente) AS total_at'
        );
        //joins
        $joins  = array(
            array(
              'table' => 'fornecedores',
              'alias' => 'Fornecedor',
              'type' => 'LEFT',
              'conditions' => 'ClienteFornecedor.codigo_fornecedor = Fornecedor.codigo',
            ),
            array(
              'table' => 'fornecedores_endereco',
              'alias' => 'FornecedorEndereco',
              'type' => 'LEFT',
              'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
            )
        );  
        //ordem
        $order = array('Fornecedor.codigo DESC','Fornecedor.razao_social ASC');
        //array para a busca
        $dados = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields,
            'order' => $order   
        );
        // pr( $this->find('sql',$dados) );exit;
        return $dados;
    }

    public function getClienteFornecedores(array $conditions = array(), $pagination = false){

        $fields = array(
            'GrupoEconomicoCliente.codigo_grupo_economico',
            'GrupoEconomicoCliente.codigo_cliente',
            'GrupoEconomicoCliente.data_inclusao',
            'GrupoEconomicoCliente.codigo_empresa',
            'ClienteFornecedor.codigo_cliente',
            'Cliente.nome_fantasia',
            'ClienteFornecedor.codigo_fornecedor',
            'Fornecedor.razao_social',
            'ClienteFornecedor.data_inclusao',
            'ClienteFornecedor.ativo',
            'Fornecedor.ativo',
            'Cliente.ativo',
        );

        $joins = array(
            array(
              'table' => 'RHHealth.dbo.grupos_economicos',
              'alias' => 'GrupoEconomicos',
              'type' => 'INNER',
              'conditions' => 'GrupoEconomicos.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
            ), 
            array(
              'table' => 'RHHealth.dbo.clientes_fornecedores',
              'alias' => 'ClienteFornecedor',
              'type' => 'INNER',
              'conditions' => 'ClienteFornecedor.codigo_cliente = GrupoEconomicoCliente.codigo_cliente',
            ),            
            array(
              'table' => 'RHHealth.dbo.fornecedores',
              'alias' => 'Fornecedor',
              'type' => 'INNER',
              'conditions' => 'ClienteFornecedor.codigo_fornecedor = Fornecedor.codigo',
            ),
            array(
              'table' => 'RHHealth.dbo.cliente',
              'alias' => 'Clientes',
              'type' => 'INNER',
              'conditions' => 'Clientes.codigo = ClienteFornecedor.codigo_cliente',
            ),
        );

        //$conditions['Cliente.ativo'] = 1;
        //$conditions['ClienteFornecedor.ativo'] = 1;

        if($pagination){
            $paginate = array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                'limit' => 50,                
                'order' => array('Fornecedor.codigo', 'Cliente.codigo'),                
            );           
            return $paginate;
        } else {            
            return $this->find('sql', array('joins' => $joins, 'fields' => $fields, 'conditions' => $conditions));
        }
    }

    public function getClienteFornecedoresExport(array $conditions = array()){

        $fields = array(
            'Cliente.codigo',
            'Cliente.razao_social',
            'Cliente.nome_fantasia',
            'Cliente.ativo',

            'Fornecedor.codigo',
            'Fornecedor.ativo',
            'Fornecedor.razao_social',
            'Fornecedor.nome',
            'Fornecedor.data_inclusao',
            'Fornecedor.ativo',
          
            'FornecedorEndereco.logradouro',
            'FornecedorEndereco.bairro',
            'FornecedorEndereco.cidade',
            'FornecedorEndereco.estado_abreviacao',
            'FornecedorEndereco.estado_descricao',

            'ClienteFornecedor.codigo_fornecedor',         
            'ClienteFornecedor.data_inclusao',
            'ClienteFornecedor.ativo',                     
        );

        $joins = array(
            array(
                'table' => 'RHHealth.dbo.grupos_economicos_clientes',
                'alias' => 'GrupoEconomicoCliente',
                'type' => 'INNER',
                'conditions' => 'GrupoEconomicoCliente.codigo_cliente = ClienteFornecedor.codigo_cliente',
              ),
            array(
              'table' => 'RHHealth.dbo.grupos_economicos',
              'alias' => 'GrupoEconomicos',
              'type' => 'INNER',
              'conditions' => 'GrupoEconomicos.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
            ),                     
            array(
              'table' => 'RHHealth.dbo.fornecedores',
              'alias' => 'Fornecedor',
              'type' => 'INNER',
              'conditions' => 'ClienteFornecedor.codigo_fornecedor = Fornecedor.codigo',
            ),
            array(
              'table' => 'RHHealth.dbo.cliente',
              'alias' => 'Cliente',
              'type' => 'INNER',
              'conditions' => 'Cliente.codigo = ClienteFornecedor.codigo_cliente',
            ),

            array(
                'table' => 'RHHealth.dbo.fornecedores_endereco',
                'alias' => 'FornecedorEndereco',
                'type' => 'INNER',
                'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
              ),
        );
    
        $conditions[] = array(
            "ClienteFornecedor.codigo_cliente = GrupoEconomicoCliente.codigo_cliente"
        );
  
        $query = $this->find("sql", array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions,            
            'order' => array('Fornecedor.codigo', 'Cliente.codigo'),                
        ));  

        return $query;    
    }
}
?>