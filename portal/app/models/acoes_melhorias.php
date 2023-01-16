<?php
class AcoesMelhorias extends AppModel
{
    public $name = 'AcoesMelhorias';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'acoes_melhorias';
    public $primaryKey = 'codigo';

    public $actsAs = array( 'Secure' );
    public $validate = array();

    public function getListaAcoesMelhorias($filtros = null)
    {
        $fields = array(
            'AcoesMelhorias.codigo',
            'AcoesMelhorias.prazo',
            'AcoesMelhorias.descricao_desvio',
            'AcoesMelhorias.descricao_acao',
            'AcoesMelhorias.data_inclusao',
            'AcoesMelhorias.codigo_acoes_melhorias_status',
            'Responsavel.nome',
            'UsuarioSolicitacao.nome',
            'IdentificadoPor.nome',
            'OrigemFerramenta.descricao',
            'PosCriticidade.descricao',
            'AcoesMelhoriasTipo.descricao',
            'AcoesMelhoriasStatus.descricao',
            'Cliente.codigo',
            'Cliente.nome_fantasia',
            'Cliente.razao_social',
            "(select top(1) status from acoes_melhorias_solicitacoes AcoesMelhoriasSolicitacoes where AcoesMelhorias.codigo = AcoesMelhoriasSolicitacoes.codigo_acao_melhoria 
            AND AcoesMelhoriasSolicitacoes.status = 1 AND AcoesMelhoriasSolicitacoes.data_remocao is NULL) as solicitacoes_status",
        );

        $joins = array(
            array(
                "table" => "cliente",
                "alias" => "Cliente",
                "type" => "INNER",
                "conditions" => "Cliente.codigo = AcoesMelhorias.codigo_cliente_observacao"
            ),
            array(
                "table" => "origem_ferramentas",
                "alias" => "OrigemFerramenta",
                "type" => "INNER",
                "conditions" => "OrigemFerramenta.codigo = AcoesMelhorias.codigo_origem_ferramenta"
            ),
            array(
                "table" => "pos_criticidade",
                "alias" => "PosCriticidade",
                "type" => "INNER",
                "conditions" => "PosCriticidade.codigo = AcoesMelhorias.codigo_pos_criticidade"
            ),
            array(
                "table" => "usuario",
                "alias" => "Responsavel",
                "type" => "LEFT",
                "conditions" => "Responsavel.codigo = AcoesMelhorias.codigo_usuario_responsavel"
            ),
            array(
                "table" => "usuario",
                "alias" => "IdentificadoPor",
                "type" => "INNER",
                "conditions" => "IdentificadoPor.codigo = AcoesMelhorias.codigo_usuario_identificador"
            ),
            array(
                "table" => "acoes_melhorias_tipo",
                "alias" => "AcoesMelhoriasTipo",
                "type" => "INNER",
                "conditions" => "AcoesMelhoriasTipo.codigo = AcoesMelhorias.codigo_acoes_melhorias_tipo"
            ),
            array(
                "table" => "acoes_melhorias_status",
                "alias" => "AcoesMelhoriasStatus",
                "type" => "INNER",
                "conditions" => "AcoesMelhoriasStatus.codigo = AcoesMelhorias.codigo_acoes_melhorias_status"
            ),
            array(
                "table" => "acoes_melhorias_solicitacoes",
                "alias" => "AcoesMelhoriasSolicitacoes",
                "type" => "LEFT",
                "conditions" => "AcoesMelhorias.codigo = AcoesMelhoriasSolicitacoes.codigo_acao_melhoria AND AcoesMelhoriasSolicitacoes.status = 1 AND AcoesMelhoriasSolicitacoes.data_remocao is NULL"
            ),
            array(
                "table" => "usuario",
                "alias" => "UsuarioSolicitacao",
                "type" => "LEFT",
                "conditions" => " AcoesMelhoriasSolicitacoes.codigo_novo_usuario_responsavel = UsuarioSolicitacao.codigo OR AcoesMelhoriasSolicitacoes.codigo_usuario_solicitado = UsuarioSolicitacao.codigo"
            )
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $conditions[] = array(
            "AcoesMelhorias.data_remocao IS NULL",
        );

        $group = array(
            'AcoesMelhorias.codigo',
            'AcoesMelhorias.prazo',
            'AcoesMelhorias.descricao_desvio',
            'AcoesMelhorias.descricao_acao',
            'AcoesMelhorias.data_inclusao',
            'AcoesMelhorias.codigo_acoes_melhorias_status',
            'Responsavel.nome',
            'UsuarioSolicitacao.nome',
            'IdentificadoPor.nome',
            'OrigemFerramenta.descricao',
            'PosCriticidade.descricao',
            'AcoesMelhoriasTipo.descricao',
            'AcoesMelhoriasStatus.descricao',
            'Cliente.codigo',
            'Cliente.nome_fantasia',
            'Cliente.razao_social',
            'AcoesMelhoriasSolicitacoes.status',
        );

        $acoes_melhorias = array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions,
            'limit' => 20,
//            'group' => $group,
            'order' => 'AcoesMelhorias.codigo desc',
        );

        return $acoes_melhorias;
    }

    public function getListaAcoesMelhoriasObs($codigo_obs)
    {
        $fields = array(
            'AcoesMelhorias.codigo',
            'AcoesMelhorias.prazo',
            'AcoesMelhorias.descricao_desvio',
            'AcoesMelhorias.descricao_acao',
            'AcoesMelhorias.descricao_local_acao',
            'AcoesMelhorias.data_inclusao',
            'AcoesMelhorias.codigo_acoes_melhorias_status',
            'Responsavel.nome',
            'IdentificadoPor.nome',
            'OrigemFerramenta.descricao',
            'PosCriticidade.descricao',
            'AcoesMelhoriasTipo.descricao',
            'AcoesMelhoriasStatus.descricao',
            'Cliente.codigo',
            'Cliente.nome_fantasia',
            'Cliente.razao_social',
            "(select top(1) status from acoes_melhorias_solicitacoes AcoesMelhoriasSolicitacoes where AcoesMelhorias.codigo = AcoesMelhoriasSolicitacoes.codigo_acao_melhoria 
            AND AcoesMelhoriasSolicitacoes.status = 1 AND AcoesMelhoriasSolicitacoes.data_remocao is NULL) as solicitacoes_status",
        );

        $joins = array(
            array(
                "table" => "cliente",
                "alias" => "Cliente",
                "type" => "INNER",
                "conditions" => "Cliente.codigo = AcoesMelhorias.codigo_cliente_observacao"
            ),
            array(
                "table" => "origem_ferramentas",
                "alias" => "OrigemFerramenta",
                "type" => "INNER",
                "conditions" => "OrigemFerramenta.codigo = AcoesMelhorias.codigo_origem_ferramenta"
            ),
            array(
                "table" => "pos_criticidade",
                "alias" => "PosCriticidade",
                "type" => "INNER",
                "conditions" => "PosCriticidade.codigo = AcoesMelhorias.codigo_pos_criticidade"
            ),
            array(
                "table" => "usuario",
                "alias" => "Responsavel",
                "type" => "LEFT",
                "conditions" => "Responsavel.codigo = AcoesMelhorias.codigo_usuario_responsavel"
            ),
            array(
                "table" => "usuario",
                "alias" => "IdentificadoPor",
                "type" => "INNER",
                "conditions" => "IdentificadoPor.codigo = AcoesMelhorias.codigo_usuario_identificador"
            ),
            array(
                "table" => "acoes_melhorias_tipo",
                "alias" => "AcoesMelhoriasTipo",
                "type" => "INNER",
                "conditions" => "AcoesMelhoriasTipo.codigo = AcoesMelhorias.codigo_acoes_melhorias_tipo"
            ),
            array(
                "table" => "acoes_melhorias_status",
                "alias" => "AcoesMelhoriasStatus",
                "type" => "INNER",
                "conditions" => "AcoesMelhoriasStatus.codigo = AcoesMelhorias.codigo_acoes_melhorias_status"
            ),
            array(
                "table" => "pos_obs_observacao_acao_melhoria",
                "alias" => "PosObsObservacaoAcaoMelhoria",
                "type" => "INNER",
                "conditions" => "PosObsObservacaoAcaoMelhoria.acoes_melhoria_id = AcoesMelhorias.codigo AND PosObsObservacaoAcaoMelhoria.ativo = 1"
            )            
        );

        $conditions[] = array(
            "PosObsObservacaoAcaoMelhoria.obs_observacao_id = $codigo_obs",
            "AcoesMelhorias.data_remocao IS NULL",
        );

        $acoes_melhorias = $this->find("all", array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions,
            'order' => 'AcoesMelhorias.codigo desc',
        ));

        return $acoes_melhorias;
    }

    public function getListaAcoesMelhoriasExport($filtros = null)
    {
        $fields = array(
            'AcoesMelhorias.codigo',
            'AcoesMelhorias.prazo',
            'AcoesMelhorias.descricao_desvio',
            'AcoesMelhorias.descricao_acao',
            'AcoesMelhorias.data_inclusao',
            'Responsavel.nome',
            'UsuarioSolicitacao.nome',
            'IdentificadoPor.nome',
            'OrigemFerramenta.descricao',
            'PosCriticidade.descricao',
            'AcoesMelhoriasTipo.descricao',
            'AcoesMelhoriasStatus.descricao',
            'Cliente.codigo',
            'Cliente.nome_fantasia',
            'Cliente.razao_social',
            'AcoesMelhoriasSolicitacoes.status',
        );

        $joins = array(
            array(
                "table" => "cliente",
                "alias" => "Cliente",
                "type" => "INNER",
                "conditions" => "Cliente.codigo = AcoesMelhorias.codigo_cliente_observacao"
            ),
            array(
                "table" => "origem_ferramentas",
                "alias" => "OrigemFerramenta",
                "type" => "INNER",
                "conditions" => "OrigemFerramenta.codigo = AcoesMelhorias.codigo_origem_ferramenta"
            ),
            array(
                "table" => "pos_criticidade",
                "alias" => "PosCriticidade",
                "type" => "INNER",
                "conditions" => "PosCriticidade.codigo = AcoesMelhorias.codigo_pos_criticidade"
            ),
            array(
                "table" => "usuario",
                "alias" => "Responsavel",
                "type" => "LEFT",
                "conditions" => "Responsavel.codigo = AcoesMelhorias.codigo_usuario_responsavel"
            ),
            array(
                "table" => "usuario",
                "alias" => "IdentificadoPor",
                "type" => "INNER",
                "conditions" => "IdentificadoPor.codigo = AcoesMelhorias.codigo_usuario_identificador"
            ),
            array(
                "table" => "acoes_melhorias_tipo",
                "alias" => "AcoesMelhoriasTipo",
                "type" => "INNER",
                "conditions" => "AcoesMelhoriasTipo.codigo = AcoesMelhorias.codigo_acoes_melhorias_tipo"
            ),
            array(
                "table" => "acoes_melhorias_status",
                "alias" => "AcoesMelhoriasStatus",
                "type" => "INNER",
                "conditions" => "AcoesMelhoriasStatus.codigo = AcoesMelhorias.codigo_acoes_melhorias_status"
            ),
            array(
                "table" => "acoes_melhorias_solicitacoes",
                "alias" => "AcoesMelhoriasSolicitacoes",
                "type" => "LEFT",
                "conditions" => "AcoesMelhorias.codigo = AcoesMelhoriasSolicitacoes.codigo_acao_melhoria AND AcoesMelhoriasSolicitacoes.status = 1 AND AcoesMelhoriasSolicitacoes.data_remocao is NULL"
            ),
            array(
                "table" => "usuario",
                "alias" => "UsuarioSolicitacao",
                "type" => "LEFT",
                "conditions" => " AcoesMelhoriasSolicitacoes.codigo_novo_usuario_responsavel = UsuarioSolicitacao.codigo OR AcoesMelhoriasSolicitacoes.codigo_usuario_solicitado = UsuarioSolicitacao.codigo"
            )
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $conditions[] = array(
            "AcoesMelhorias.data_remocao IS NULL",
        );

        $group = array(
            'AcoesMelhorias.codigo',
            'AcoesMelhorias.prazo',
            'AcoesMelhorias.descricao_desvio',
            'AcoesMelhorias.descricao_acao',
            'AcoesMelhorias.data_inclusao',
            'Responsavel.nome',
            'UsuarioSolicitacao.nome',
            'IdentificadoPor.nome',
            'OrigemFerramenta.descricao',
            'PosCriticidade.descricao',
            'AcoesMelhoriasTipo.descricao',
            'AcoesMelhoriasStatus.descricao',
            'Cliente.codigo',
            'Cliente.nome_fantasia',
            'Cliente.razao_social',
            'AcoesMelhoriasSolicitacoes.status',
        );

        $acoes_melhorias = $this->find("sql", array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions,
            'group' => $group,
            'order' => 'AcoesMelhorias.codigo desc',
        ));

        return $acoes_melhorias;
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['AcoesMelhorias.codigo'] = $data['codigo'];
        }

        if (!empty($data['descricao'])) {
            $conditions ['AcoesMelhorias.descricao LIKE'] = '%' . $data['descricao'] . '%';
        }

        if ($data ['admin'] == 1) {

            if (is_array($data['codigo_cliente'])) {

                $unidades_grupos_economicos = $this->unidadesDosGruposEconomicos($data['codigo_cliente']);

                $codigo_cliente = implode(',', $unidades_grupos_economicos);

                $lista_unidades = $codigo_cliente;

                $conditions[] = array(
                    "AcoesMelhorias.codigo_cliente_observacao IN ({$lista_unidades})"
                );
            } else {

                $lista_unidades = $this->retorna_lista_de_unidades_do_grupo_economico($data['codigo_cliente']);
                
                $conditions[] = array(
                    "AcoesMelhorias.codigo_cliente_observacao IN ({$lista_unidades})"
                );
            }

        
        } else {
            $conditions['AcoesMelhorias.codigo_usuario_responsavel'] = $data['codigo_usuario'];
        }

        if (!empty($data['razao_social'])) {
            $conditions['Cliente.razao_social'] = $data['razao_social'];
        }

        if (!empty($data['nome_fantasia'])) {
            $conditions['Cliente.nome_fantasia'] = $data['nome_fantasia'];
        }

        if (!empty($data['codigo_acao_melhoria_status'])) {
            $conditions['AcoesMelhorias.codigo_acoes_melhorias_status'] = $data['codigo_acao_melhoria_status'];
        }

        if (!empty($data['codigo_acao_melhoria_tipo'])) {
            $conditions['AcoesMelhorias.codigo_acoes_melhorias_tipo'] = $data['codigo_acao_melhoria_tipo'];
        }

        if (!empty($data['codigo_pos_criticidade'])) {
            $conditions['AcoesMelhorias.codigo_pos_criticidade'] = $data['codigo_pos_criticidade'];
        }

        if (!empty($data['codigo_origem_ferramenta'])) {
            $conditions['AcoesMelhorias.codigo_origem_ferramenta'] = $data['codigo_origem_ferramenta'];
        }

        if (!empty($data['codigo_usuario_responsavel'])) {
            $conditions['AcoesMelhorias.codigo_usuario_responsavel'] = $data['codigo_usuario_responsavel'];
        }

        if (!empty($data['status_solicitacao'])) {
            $conditions['AcoesMelhoriasSolicitacoes.status'] = $data['status_solicitacao'];
        }

        return $conditions;
    }

    /**
     * Retorna a lista de unidades do grupo economico
     * @param $codigo_cliente
     * @return array
     */
    function retorna_lista_de_unidades_do_grupo_economico($codigo_cliente) {

        $GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
		
		$conditions = array(
            'GrupoEconomico.codigo_cliente' => $codigo_cliente,
            'Cliente.ativo' => 1,
            'Cliente.e_tomador' => 0
        );
		$fields = array( 'Cliente.codigo' );
		$order = array('Cliente.nome_fantasia ASC');
		
		$joins  = array(
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
				),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente',
				)
			);
		
		$unidades = $GrupoEconomicoCliente->find('all', array('conditions' => $conditions, 'fields' => $fields, 'joins' => $joins, 'order' => $order, 'recursive' => -1));
		        
        $arr = array();

        foreach ($unidades as $unidade) {
            $arr[] = $unidade['Cliente']['codigo'];
        };

        $arr = implode(",", $arr);  

		return $arr;    
	}

    public function unidadesDosGruposEconomicos($matrizes) {
        
        $this->GrupoEconomico = ClassRegistry::init('GrupoEconomico');

        $codigo_cliente = implode(',', $matrizes);

        $joins = array(
            array(
                "table" => "grupos_economicos_clientes",
                "alias" => "GrupoEconomicoCliente",
                "type" => "INNER",
                "conditions" => "GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo"
            ),
        );

        $unidades = $this->GrupoEconomico->find('all', array('fields' => array('GrupoEconomicoCliente.codigo_cliente'), 'joins' => $joins, 'conditions' => array('GrupoEconomico.codigo_cliente IN (' . $codigo_cliente . ')')));

        $arr = array();

        foreach ($unidades as $unidade) {
            $arr[] = $unidade['GrupoEconomicoCliente']['codigo_cliente'];
        };

        return $arr;
    }
}
