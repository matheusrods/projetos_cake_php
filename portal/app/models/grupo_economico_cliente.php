<?php
class GrupoEconomicoCliente extends AppModel
{
    var $name = 'GrupoEconomicoCliente';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'grupos_economicos_clientes';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_grupos_economicos_clientes'));

    var $belongsTo = array(
        'GrupoEconomico' => array('foreignKey' => 'codigo_grupo_economico', 'fields' => array('descricao', 'codigo_cliente')),
        'Cliente' => array('foreignKey' => 'codigo_cliente', 'fields' => array('razao_social', 'nome_fantasia', 'codigo_medico_pcmso', 'codigo_documento'))
    );

    var $validate = array(
        'codigo_grupo_economico' => array(
            'rule' => array('notEmpty'),
            'message' => 'Grupo Econômico não informado',
            'required' => true,
            'allowEmpty' => false,
        ),
        'codigo_cliente' => array(
            array(
                'rule' => array('notEmpty'),
                'message' => 'Cliente não informado',
                'required' => true,
                'allowEmpty' => false,
            ),
            array(
                'rule' => array('isUnique'),
                'message' => 'Cliente já tem Grupo Econômico',
            ),
        ),
    );

    public $virtualFields = array('unidade' => 'GrupoEconomicoCliente.codigo_cliente', 'matriz' => 'GrupoEconomico.codigo_cliente');

    function retorna_dados_cliente($codigo_cliente)
    {
        $GrupoEconomico = &ClassRegistry::Init('GrupoEconomico');
        $Cliente = &ClassRegistry::Init('Cliente');

        $conditions = array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente);

        $fields = array(
            'GrupoEconomico.codigo_cliente',
            'GrupoEconomicoCliente.codigo', 'GrupoEconomicoCliente.codigo_grupo_economico', 'GrupoEconomicoCliente.codigo_cliente',
            'Matriz.codigo', 'Matriz.razao_social', 'Matriz.nome_fantasia',
            'Unidade.codigo', 'Unidade.razao_social', 'Unidade.nome_fantasia'
        );

        $joins  = array(
            array(
                'table' => $this->GrupoEconomico->databaseTable . '.' . $this->GrupoEconomico->tableSchema . '.' . $this->GrupoEconomico->useTable,
                'alias' => 'GrupoEconomico',
                'type' => 'LEFT',
                'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
            ),
            array(
                'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
                'alias' => 'Matriz',
                'type' => 'LEFT',
                'conditions' => 'Matriz.codigo = GrupoEconomico.codigo_cliente',
            ),
            array(
                'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
                'alias' => 'Unidade',
                'type' => 'LEFT',
                'conditions' => 'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente',
            )
        );

        $recursive = -1;
        $dados = $this->find('first', array('conditions' => $conditions, 'fields' => $fields, 'joins' => $joins, 'recursive' => $recursive));

        return $dados;
    } //FINAL FUNCTION retorna_dados_cliente

    function retorna_lista_de_unidades_de_um_grupo_economico($codigo_grupo_economico)
    {

        $conditions = array('GrupoEconomicoCliente.codigo_grupo_economico' => $codigo_grupo_economico);
        $fields = array('Cliente.codigo', 'Cliente.nome_fantasia');
        $order = array('Cliente.nome_fantasia ASC');

        $joins  = array(
            array(
                'table' => $this->GrupoEconomico->databaseTable . '.' . $this->GrupoEconomico->tableSchema . '.' . $this->GrupoEconomico->useTable,
                'alias' => 'GrupoEconomico',
                'type' => 'INNER',
                'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
            ),
            array(
                'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente',
            )
        );

        return $this->find('list', array('conditions' => $conditions, 'fields' => $fields, 'joins' => $joins, 'order' => $order));
    } //FINAL FUNCTION retorna_lista_de_unidades_de_um_grupo_economico  

    public function listaCargos($codigo_grupo_economico)
    {
        //$conditions = array('GrupoEconomicoCliente.codigo_grupo_economico' => $codigo_grupo_economico);
        $fields = array('Cargo.codigo', 'Cargo.descricao');
        $order = array('Cargo.descricao ASC');

        $codigos_cliente = $this->find('list', array('conditions' => array('codigo_grupo_economico' => $codigo_grupo_economico), 'fields' => array('codigo_cliente')));
        if ($codigos_cliente) {
            $this->Cargo = ClassRegistry::init('Cargo');
            return $this->Cargo->find('list', array('conditions' => array('codigo_cliente' => $codigos_cliente), 'fields' => $fields, 'order' => $order));
        }
        return array();


        // $joins 	= array(
        //  array(
        //   'table'	=> 'cliente_funcionario',
        //   'alias'	=> 'ClienteFuncionario',
        //   'type' => 'INNER',
        //   'conditions' => 'GrupoEconomicoCliente.codigo_cliente = ClienteFuncionario.codigo_cliente',
        //   ),    			
        //  array(
        //   'table'	=> 'cargos',
        //   'alias'	=> 'Cargo',
        //   'type' => 'INNER',
        //   'conditions' => 'Cargo.codigo = ClienteFuncionario.codigo_cargo',
        //   ),
        //  );

        // return $this->find('list', array('conditions' => $conditions, 'fields' => $fields, 'order' => $order, 'joins' => $joins));
    } //FINAL FUNCTION listaCargos

    public function listaSetores($codigo_grupo_economico)
    {
        //$conditions = array('GrupoEconomicoCliente.codigo_grupo_economico' => $codigo_grupo_economico);
        $fields = array('Setor.codigo', 'Setor.descricao');
        $order = array('Setor.descricao ASC');

        $codigo_cliente = $this->find('list', array('conditions' => array('codigo_grupo_economico' => $codigo_grupo_economico), 'fields' => array('codigo_cliente')));
        if ($codigo_cliente) {
            $this->Setor = ClassRegistry::init('Setor');
            return $this->Setor->find('list', array('conditions' => array('codigo_cliente' => $codigo_cliente), 'fields' => $fields, 'order' => $order));
        }
        return array();

        // $joins 	= array(
        // 	array(
        // 		'table'	=> 'cliente_funcionario',
        // 		'alias'	=> 'ClienteFuncionario',
        // 		'type' => 'INNER',
        // 		'conditions' => 'GrupoEconomicoCliente.codigo_cliente = ClienteFuncionario.codigo_cliente',
        // 	),
        // 	array(
        // 		'table'	=> 'setores',
        // 		'alias'	=> 'Setor',
        // 		'type' => 'INNER',
        // 		'conditions' => 'Setor.codigo = ClienteFuncionario.codigo_setor',
        // 	),
        // );

        //return $this->find('list', array('conditions' => $conditions, 'fields' => $fields, 'order' => $order, 'joins' => $joins));
    } //FINAL FUNCTION listaSetores

    public function listaFuncionarios($codigo_grupo_economico)
    {
        $conditions = array('GrupoEconomicoCliente.codigo_grupo_economico' => $codigo_grupo_economico);
        $fields = array('Funcionario.codigo', 'Funcionario.nome');
        $order = array('Funcionario.nome ASC');

        $joins     = array(
            array(
                'table'    => 'cliente_funcionario',
                'alias'    => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => 'GrupoEconomicoCliente.codigo_cliente = ClienteFuncionario.codigo_cliente',
            ),
            array(
                'table'    => 'funcionarios',
                'alias'    => 'Funcionario',
                'type' => 'INNER',
                'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
            ),
        );

        return $this->find('list', array('conditions' => $conditions, 'fields' => $fields, 'order' => $order, 'joins' => $joins));
    } //FINAL FUNCTION listaFuncionarios

    function retorna_unidades_grupo_economico($codigo_grupo_economico)
    {
        $GrupoEconomico = &ClassRegistry::Init('GrupoEconomico');
        $Cliente = &ClassRegistry::Init('Cliente');

        $conditions = array('GrupoEconomicoCliente.codigo_grupo_economico' => $codigo_grupo_economico);
        //$conditions = array('Unidade.codigo' => 30);

        $fields = array(
            'GrupoEconomico.codigo_cliente',
            'GrupoEconomicoCliente.codigo', 'GrupoEconomicoCliente.codigo_grupo_economico', 'GrupoEconomicoCliente.codigo_cliente',
            'Matriz.codigo', 'Matriz.razao_social', 'Matriz.codigo_documento', 'Matriz.codigo_corporacao', 'Matriz.razao_social', 'Matriz.nome_fantasia',
            'Matriz.inscricao_estadual', 'Matriz.ccm', 'Matriz.codigo_endereco_regiao', 'Matriz.ativo', 'Matriz.cnae', 'Matriz.codigo_gestor', 'Matriz.data_alteracao',
            'Matriz.codigo_usuario_alteracao', 'Matriz.codigo_regime_tributario', 'Matriz.codigo_gestor_operacao', 'Matriz.codigo_gestor_contrato',
            'Matriz.codigo_seguradora', 'Matriz.codigo_plano_saude', 'Matriz.codigo_empresa', 'Matriz.codigo_corretora', 'Matriz.codigo_medico_pcmso',
            'Unidade.codigo', 'Unidade.razao_social', 'Unidade.codigo_documento', 'Unidade.codigo_documento_real', 'Unidade.codigo_corporacao', 'Unidade.razao_social', 'Unidade.nome_fantasia',
            'Unidade.inscricao_estadual', 'Unidade.ccm', 'Unidade.codigo_endereco_regiao', 'Unidade.ativo', 'Unidade.cnae', 'Unidade.codigo_gestor', 'Unidade.data_alteracao',
            'Unidade.codigo_usuario_alteracao', 'Unidade.codigo_regime_tributario', 'Unidade.codigo_gestor_operacao', 'Unidade.codigo_gestor_contrato',
            'Unidade.codigo_seguradora', 'Unidade.codigo_plano_saude', 'Unidade.codigo_empresa', 'Unidade.codigo_corretora', 'Unidade.codigo_medico_pcmso',
            'Unidade.codigo_externo', 'Unidade.tipo_unidade'


        );

        $joins  = array(
            array(
                'table' => $this->GrupoEconomico->databaseTable . '.' . $this->GrupoEconomico->tableSchema . '.' . $this->GrupoEconomico->useTable,
                'alias' => 'GrupoEconomico',
                'type' => 'LEFT',
                'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
            ),
            array(
                'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
                'alias' => 'Matriz',
                'type' => 'LEFT',
                'conditions' => 'Matriz.codigo = GrupoEconomico.codigo_cliente',
            ),
            array(
                'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
                'alias' => 'Unidade',
                'type' => 'LEFT',
                'conditions' => 'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente',
            )
        );

        $order = array('Unidade.nome_fantasia ASC, Unidade.razao_social ASC');

        $recursive = -1;
        $dados = $this->find('all', array('conditions' => $conditions, 'fields' => $fields, 'joins' => $joins, 'recursive' => $recursive));
        return $dados;
    } //FINAL FUNCTION retorna_unidades_grupo_economico

    public function atualizaBloqueio($data)
    {
        $return = -1;
        $query = 'UPDATE grupos_economicos_clientes SET bloqueado = ' . $data[$this->name]['bloqueado'] . ' WHERE codigo = ' . $data[$this->name]['codigo'];
        if ($this->query($query)) {
            $return = $data[$this->name]['bloqueado'];
        }
        return $return;
    } //FINAL FUNCTION atualizaBloqueio

    public function lista($codigo_cliente, $nao_e_tomador = false)
    {
        $conditions = array('GrupoEconomico.codigo_cliente' => $codigo_cliente, 'Cliente.ativo' => 1);

        if ($nao_e_tomador) {
            $conditions[] = 'Cliente.e_tomador <> 1';
            $conditions[] = "Cliente.tipo_unidade = 'F'";
        }

        //carrega as unidades caso ele tenha
        $this->UsuarioUnidade = &ClassRegistry::Init('UsuarioUnidade');
        $codigo_usuario = $_SESSION['Auth']['Usuario']['codigo'];
        // $codigo_usuario = $this->Bauth
        $usuario_unidade = $this->UsuarioUnidade->find('list', array('fields' => array('UsuarioUnidade.codigo_cliente'), 'conditions' => array('UsuarioUnidade.codigo_usuario' => $codigo_usuario)));

        //verifica se existe registros
        if (!empty($usuario_unidade)) {
            //retira a matriz
            unset($conditions['GrupoEconomicoCliente']);

            if (is_array($codigo_cliente)) {
                $codigo_cliente = implode(',', $codigo_cliente);
            }
            //trata os dados retornados
            $usuario_unidade[] = $codigo_cliente;

            $filtros_codigos_unidades = implode(',', $usuario_unidade);
            //seta as empresas que ele pode ver
            $conditions[] = array('GrupoEconomicoCliente.codigo_cliente IN (' . $filtros_codigos_unidades . ')');
        } //fim empty

        $fields = array('Cliente.codigo', 'Cliente.nome_fantasia');
        $recursive = 1;
        $order = array('Cliente.nome_fantasia');
        return $this->find('list', compact('conditions', 'fields', 'recursive', 'order'));
    } //FINAL FUNCTION lista

    public function lista2($codigo_cliente)
    {
        $conditions = array('GrupoEconomico.codigo_cliente' => $codigo_cliente, 'Cliente.ativo' => 1);

        //carrega as unidades caso ele tenha
        $this->UsuarioUnidade = &ClassRegistry::Init('UsuarioUnidade');
        $codigo_usuario = $_SESSION['Auth']['Usuario']['codigo'];
        // $codigo_usuario = $this->Bauth
        $usuario_unidade = $this->UsuarioUnidade->find('list', array('fields' => array('UsuarioUnidade.codigo_cliente'), 'conditions' => array('UsuarioUnidade.codigo_usuario' => $codigo_usuario)));

        //verifica se existe registros
        if (!empty($usuario_unidade)) {
            //retira a matriz
            unset($conditions['GrupoEconomicoCliente']);

            if (is_array($codigo_cliente)) {
                $codigo_cliente = implode(',', $codigo_cliente);
            }
            //trata os dados retornados
            $usuario_unidade[] = $codigo_cliente;

            $filtros_codigos_unidades = implode(',', $usuario_unidade);
            //seta as empresas que ele pode ver
            $conditions[] = array('GrupoEconomicoCliente.codigo_cliente IN (' . $filtros_codigos_unidades . ')');
        } //fim empty

        $fields = array('Cliente.codigo', 'Cliente.nome_fantasia');
        $recursive = 1;
        $order = array('Cliente.nome_fantasia');
        $unidades = $this->find('list', compact('conditions', 'fields', 'recursive', 'order'));

        $unidade2 = '';
        foreach ($unidades as $key => $unidade) {
            $unidade2 .= $key . ",";
        }

        return substr($unidade2, 0, -1);
    } //FINAL FUNCTION lista

    public function listaAjax($codigo_cliente)
    {
        $conditions = array('GrupoEconomico.codigo_cliente' => $codigo_cliente, 'Cliente.ativo' => 1);

        //carrega as unidades caso ele tenha
        $this->UsuarioUnidade = &ClassRegistry::Init('UsuarioUnidade');
        $codigo_usuario = $_SESSION['Auth']['Usuario']['codigo'];
        // $codigo_usuario = $this->Bauth
        $usuario_unidade = $this->UsuarioUnidade->find('list', array('fields' => array('UsuarioUnidade.codigo_cliente'), 'conditions' => array('UsuarioUnidade.codigo_usuario' => $codigo_usuario)));

        //verifica se existe registros
        if (!empty($usuario_unidade)) {
            //retira a matriz
            unset($conditions['GrupoEconomicoCliente']);

            if (is_array($codigo_cliente)) {
                $codigo_cliente = implode(',', $codigo_cliente);
            }
            //trata os dados retornados
            $usuario_unidade[] = $codigo_cliente;

            $filtros_codigos_unidades = implode(',', $usuario_unidade);
            //seta as empresas que ele pode ver
            $conditions[] = array('GrupoEconomicoCliente.codigo_cliente IN (' . $filtros_codigos_unidades . ')');
        } //fim empty

        $fields = array('Cliente.codigo', 'Cliente.nome_fantasia');
        $recursive = 1;
        $order = array('Cliente.nome_fantasia');
        return $this->find('all', compact('conditions', 'fields', 'recursive', 'order'));
    } //FINAL FUNCTION lista

    public function hierarquia_bloqueada($codigo_cliente)
    {
        $bloqueado = false;

        $cliente_bloqueado = $this->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente), 'fields' => 'bloqueado', 'recursive' => -1));

        if (!empty($cliente_bloqueado)) {
            if (!empty($cliente_bloqueado['GrupoEconomicoCliente']['bloqueado'])) {
                $bloqueado = true;
            }
        }

        return $bloqueado;
    } //FINAL FUNCTION hierarquia_bloqueada

    public function getCodigoGrupoEconomico($codigo_cliente)
    {
        $grupos_economicos_cliente = $this->find('all', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));

        return $grupos_economicos_cliente[0]['GrupoEconomicoCliente']['matriz'];
    }

    //Função para validar a exclusão da matriz do grupo econômico
    public function valida_exclusao_matriz($codigo_grupo_economico)
    {
        $ClienteFuncionario = &ClassRegistry::Init('ClienteFuncionario');

        $grupo_economico = $this->GrupoEconomico->carregar($codigo_grupo_economico);

        //Verifica a quantidade de unidades do grupo econômico exceto a matriz
        $qtd_unidades_grupo = $this->find('count', array('conditions' => array('codigo_cliente <>' => $grupo_economico['GrupoEconomico']['codigo_cliente'], 'codigo_grupo_economico' => $codigo_grupo_economico), 'recursive' => -1));

        //Valida se a matriz é o único registro de unidade no grupo econômico
        if ($qtd_unidades_grupo == 0) {

            $setores = $this->listaSetores($codigo_grupo_economico);
            $cargos = $this->listaCargos($codigo_grupo_economico);
            $matriculas = $ClienteFuncionario->find('count', array('conditions' => array('ClienteFuncionario.codigo_cliente_matricula' => $grupo_economico['GrupoEconomico']['codigo_cliente'])));

            //Verifica se a matriz não possui setores, cargos e matrículas associadas a ela
            if (empty($setores) && empty($cargos) && ($matriculas == 0)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } //FINAL FUNCTION valida_exclusao_matriz

    public function getUnidadesFromVersoesPCMSO()
    {

        $fields = "{$this->name}.codigo_cliente, GruposEconomicos.descricao";

        $joins = array(
            array(
                'table'         => 'grupos_economicos',
                'alias'         => 'GruposEconomicos',
                'type'        => 'INNER',
                'conditions'     => "GruposEconomicos.codigo = {$this->name}.codigo_grupo_economico"
            ),
            array(
                'table'    => 'pcmso_versoes',
                'alias'     => 'PCMSOVersoes',
                'type'     => 'INNER',
                'conditions' => "PCMSOVersoes.codigo_cliente_alocacao = {$this->name}.codigo_cliente"
            )
        );

        $group = array("{$this->name}.codigo_cliente", 'GruposEconomicos.descricao');

        return $this->find(
            'list',
            array(
                'fields'    => $fields,
                'joins'    => $joins,
                'group'    => $group
            )
        );
    } //FINAL FUNCTION getUnidadesFromVersoesPCMSO

    public function getCodigoClientesByCodigoMatriz($codigo)
    {

        $fields_subquery         = array('GrupoEconomicoCliente.codigo_grupo_economico');
        $conditions_subquery     = array('GrupoEconomicoCliente.codigo_cliente' => $codigo);
        $codigo_grupo_economico = $this->find('first', array('fields' => $fields_subquery, 'conditions' => $conditions_subquery));

        $fields     = array('GrupoEconomicoCliente.codigo_cliente');
        $conditions = array('GrupoEconomicoCliente.codigo_grupo_economico' => $codigo_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico']);
        $query = $this->find('all', array('fields' => $fields, 'conditions' => $conditions));

        $codigos_cliente = Set::extract(Set::extract($query, '{n}.GrupoEconomicoCliente'), '{n}.codigo_cliente');

        return implode(',', $codigos_cliente);
    } //FINAL FUNCTION getCodigoClientesByCodigoMatriz

    public function paginateCount($conditions = array(), $recursive = -1, $extra = array())
    {
        $return = $this->find(
            'all',
            array(
                'conditions' => $conditions,
                'joins' => !empty($extra['joins']) ? $extra['joins'] : array(),
                'group' => !empty($extra['group']) ? $extra['group'] : array(),
                'fields' => !empty($extra['group']) ? $extra['group'] : array(),
                'recursive' => $recursive,
            )
        );

        return count($return);
    } //fim paginateCounte

    public function monta_lista_matriz_pendente()
    {
        $this->GrupoEconomicoCliente = ClassRegistry::Init("GrupoEconomicoCliente");

        $this->GrupoEconomicoCliente->virtualFields = array(
            "matriz" => "CONCAT(GrupoEconomico.codigo_cliente,' - ',ClienteMatriz.nome_fantasia)"
        );

        $fields = array("GrupoEconomico.codigo_cliente", "matriz");

        $conditions = array(
            "Cliente.ativo = 1",
            "(PPRA.STATUS_PPRA = 1 OR PCMSO.STATUS_PCMSO = 1)"
        );

        $joins = array(
            array(
                "table" => "RHHealth.dbo.grupos_economicos",
                "alias" => "GrupoEconomico",
                'type'    => 'LEFT',
                "conditions" => "(GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo)"
            ),
            array(
                "table" => "RHHealth.dbo.cliente",
                "alias" => "Cliente",
                'type'    => 'LEFT',
                "conditions" => "(Cliente.codigo = GrupoEconomicoCliente.codigo_cliente)"
            ),
            array(
                "table" => "( SELECT COUNT(GrupoExposicao.codigo) AS TOTAL_GrupoExposicao, COUNT(ClientesSetoresCargos.codigo) AS TOTAL, ClientesSetoresCargos.codigo_cliente_alocacao, (CASE WHEN COUNT(GrupoExposicao.codigo) < COUNT(ClientesSetoresCargos.codigo) THEN 1 ELSE 2 END) AS STATUS_PPRA FROM clientes_setores_cargos AS ClientesSetoresCargos INNER JOIN cargos AS Cargo ON Cargo.codigo = ClientesSetoresCargos.codigo_cargo AND Cargo.ativo = 1 INNER JOIN setores Setor ON Setor.codigo = ClientesSetoresCargos.codigo_setor AND Setor.ativo = 1 LEFT JOIN clientes_setores AS ClientesSetores ON (ClientesSetoresCargos.codigo_setor = ClientesSetores.codigo_setor and ClientesSetores.codigo_cliente = ClientesSetoresCargos.codigo_cliente_alocacao) LEFT JOIN grupo_exposicao AS GrupoExposicao ON (ClientesSetores.codigo = GrupoExposicao.codigo_cliente_setor AND ClientesSetoresCargos.codigo_cargo = GrupoExposicao.codigo_cargo AND (SELECT COUNT(*) FROM grupos_exposicao_risco GrupoExposicaoRisco WHERE GrupoExposicaoRisco.codigo_grupo_exposicao = GrupoExposicao.codigo ) > 0) WHERE ([ClientesSetoresCargos].[ativo] = 1
          OR [ClientesSetoresCargos].[ativo] IS NULL) GROUP BY ClientesSetoresCargos.codigo_cliente_alocacao )",
                "alias" => "PPRA",
                "conditions" => "PPRA.codigo_cliente_alocacao = Cliente.codigo"
            ),
            array(
                "table" => "( SELECT COUNT(AplicacaoExame.codigo) AS TOTAL_AplicacaoExame, COUNT(ClientesSetoresCargos.codigo) AS TOTAL, ClientesSetoresCargos.codigo_cliente_alocacao, (CASE WHEN COUNT(AplicacaoExame.codigo) < COUNT(ClientesSetoresCargos.codigo) THEN 1 ELSE 2 END) AS STATUS_PCMSO FROM clientes_setores_cargos AS ClientesSetoresCargos INNER JOIN cargos AS Cargo ON Cargo.codigo = ClientesSetoresCargos.codigo_cargo AND Cargo.ativo = 1 INNER JOIN setores Setor ON Setor.codigo = ClientesSetoresCargos.codigo_setor AND Setor.ativo = 1 LEFT JOIN clientes_setores AS ClientesSetores ON (ClientesSetoresCargos.codigo_setor = ClientesSetores.codigo_setor and ClientesSetores.codigo_cliente = ClientesSetoresCargos.codigo_cliente) LEFT JOIN aplicacao_exames AS AplicacaoExame ON (ClientesSetoresCargos.codigo_cargo = AplicacaoExame.codigo_cargo and ClientesSetoresCargos.codigo_setor = AplicacaoExame.codigo_setor and ClientesSetoresCargos.codigo_cliente_alocacao = AplicacaoExame.codigo_cliente_alocacao) WHERE ([ClientesSetoresCargos].[ativo] = 1
          OR [ClientesSetoresCargos].[ativo] IS NULL) GROUP BY ClientesSetoresCargos.codigo_cliente_alocacao )",
                "alias" => "PCMSO",
                "conditions" => "PCMSO.codigo_cliente_alocacao = Cliente.codigo"
            ),
            array(
                "table" => "RHHealth.dbo.cliente",
                "alias" => "ClienteMatriz",
                "type" => "LEFT",
                "conditions" => "GrupoEconomico.codigo_cliente = ClienteMatriz.codigo"
            )
        );

        $group = "GrupoEconomico.codigo_cliente, ClienteMatriz.nome_fantasia";

        $order = "GrupoEconomico.codigo_cliente";


        $options = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'group' => $group,
            'order' => $order,
            'recursive' => -1
        );

        // pr($this->GrupoEconomicoCliente->find('sql', $options));exit;

        return $this->GrupoEconomicoCliente->find('list', $options);
    }

    /**
     * Retorno de lista de empresa Matriz e suas filiais a partir do codigo_cliente fornecido(s)
     * 
     * @param array $codigo_cliente
     * @return array
     * 
     *	ex. resposta
     *
     *	"79929": {
     *		"clientes": [{
     *			"codigo_cliente": 79929,
     *			"descricao": "IRIEL IND E COM DE SISTEMAS ELETRICOS LTDA  - Canoas - 06005455000186"
     *		}],
     *		"matriz": {
     *			"codigo": 79929,
     * 			"descricao": "IRIEL IND E COM"
     *		}
     *	},
     *	"79933": {
     *		"clientes": [{
     *			"codigo_cliente": 79933,
     *			"descricao": "Jaguari Energ\u00e9tica S\/A  - Jaguari - 04324226000107"
     *		}],
     *		"matriz": {
     *			"codigo": 79933,
     *			"descricao": "JAGUARI ENERGETICA S\/A"
     *		}
     *	},
     *
     */
    public function obterLista($codigo_cliente = array())
    {
        $conditions = array('GrupoEconomico.codigo_cliente' => $codigo_cliente, 'Cliente.ativo' => 1);

        //carrega as unidades caso ele tenha
        $this->UsuarioUnidade = &ClassRegistry::Init('UsuarioUnidade');
        $usuario_unidade = null;
        if (isset($_SESSION['Auth']) && isset($_SESSION['Auth']['Usuario'])) {
            $codigo_usuario = $_SESSION['Auth']['Usuario']['codigo'];
            // $codigo_usuario = $this->Bauth
            $usuario_unidade = $this->UsuarioUnidade->find('all', array('fields' => array('UsuarioUnidade.codigo_cliente'), 'conditions' => array('UsuarioUnidade.codigo_usuario' => $codigo_usuario)));
        }
        //verifica se existe registros
        if (!empty($usuario_unidade)) {
            //retira a matriz
            unset($conditions['GrupoEconomicoCliente']);

            //trata os dados retornados
            $filtros_codigos_unidades = implode(',', $usuario_unidade);
            //seta as empresas que ele pode ver
            $conditions[] = array('GrupoEconomicoCliente.codigo_cliente IN (' . $filtros_codigos_unidades . ')');
        } //fim empty

        $fields = array('Cliente.codigo AS codigo_cliente ', 'Cliente.nome_fantasia AS descricao', 'GrupoEconomico.codigo_cliente AS codigo_matriz', 'GrupoEconomico.descricao AS descricao_matriz');
        $recursive = 1;
        $order = array('Cliente.nome_fantasia');

        $list = $this->find('all', compact('conditions', 'fields', 'recursive', 'order'));

        $dados = array();
        if (is_array($list)) {
            foreach ($list as $key => $value) {
                $linha = $list[$key][0];
                $cliente = $linha;
                $matriz = array(
                    'codigo' => $linha['codigo_matriz'],
                    'descricao' => $linha['descricao_matriz']
                );

                if (isset($cliente['codigo_matriz']))
                    unset($cliente['codigo_matriz']);
                if (isset($cliente['descricao_matriz']))
                    unset($cliente['descricao_matriz']);

                $dados[$linha['codigo_matriz']]['clientes'][] = $cliente;
                $dados[$linha['codigo_matriz']]['matriz'] = $matriz;
            }
        }

        return $dados;
    } //FINAL FUNCTION obterLista

    public function lista_unidades_embarcados($codigo_cliente)
    {
        $conditions = array('GrupoEconomico.codigo_cliente' => $codigo_cliente, 'Cliente.ativo' => 1);

        //carrega as unidades caso ele tenha
        $this->UsuarioUnidade = &ClassRegistry::Init('UsuarioUnidade');
        $codigo_usuario = $_SESSION['Auth']['Usuario']['codigo'];
        // $codigo_usuario = $this->Bauth
        $usuario_unidade = $this->UsuarioUnidade->find('list', array('fields' => array('UsuarioUnidade.codigo_cliente'), 'conditions' => array('UsuarioUnidade.codigo_usuario' => $codigo_usuario)));

        //verifica se existe registros
        if (!empty($usuario_unidade)) {
            //retira a matriz
            unset($conditions['GrupoEconomicoCliente']);

            if (is_array($codigo_cliente)) {
                $codigo_cliente = implode(',', $codigo_cliente);
            }
            //trata os dados retornados
            $usuario_unidade[] = $codigo_cliente;

            $filtros_codigos_unidades = implode(',', $usuario_unidade);
            //seta as empresas que ele pode ver
            $conditions[] = array('GrupoEconomicoCliente.codigo_cliente IN (' . $filtros_codigos_unidades . ')');
        } //fim empty

        $fields = array('Cliente.codigo');
        $recursive = 1;
        $order = array('Cliente.nome_fantasia');
        return $this->find('list', compact('conditions', 'fields', 'recursive', 'order'));
    } //FINAL FUNCTION lista

    public function getUnidades($filtros)
    {
        $fields = array(
            'Matriz.codigo',
            'Matriz.nome_fantasia',
            'Matriz.razao_social',
            'Matriz.codigo_documento',
            'GrupoEconomicoCliente.codigo',
            'ClienteOpco.codigo',
            'ClienteOpco.descricao',
            'ClienteBu.codigo',
            'ClienteBu.descricao',
            'GrupoEconomicoCliente.codigo_grupo_economico',
            'GrupoEconomicoCliente.codigo_cliente'
        );

        $joins = array(
            array(
                "table" => "cliente",
                "alias" => "Matriz",
                "type" => "INNER",
                "conditions" => array("GrupoEconomicoCliente.codigo_cliente = Matriz.codigo AND Matriz.ativo = 1 AND Matriz.e_tomador <> 1")
            ),
            array(
                "table" => "cliente_opco",
                "alias" => "ClienteOpco",
                "type" => "LEFT",
                "conditions" => array("ClienteOpco.codigo_cliente = Matriz.codigo")
            ),
            array(
                "table" => "cliente_bu",
                "alias" => "ClienteBu",
                "type" => "LEFT",
                "conditions" => array("ClienteBu.codigo_cliente = Matriz.codigo")
            ),
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $dados = $this->find('all', array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions,
            'limit' => 50
        ));

        return $dados;
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['razao_social'])) {
            $conditions['Cliente.razao_social LIKE'] = '%' . $data['razao_social'] . '%';
        }

        if (!empty($data['nome_fantasia'])) {
            $conditions['Cliente.nome_fantasia LIKE'] = '%' . $data['nome_fantasia'] . '%';
        }

        if (!empty($data['codigo_grupo_economico'])) {
            $conditions['GrupoEconomicoCliente.codigo_grupo_economico'] = $data['codigo_grupo_economico'];
        }

        if (!empty($data['codigo_cliente'])) {
            $conditions['GrupoEconomicoCliente.codigo_cliente'] = $data['codigo_cliente'];
        }

        return $conditions;
    }

    public function comboGetUnidades($codigo_cliente)
    {

        $fields = array(
            'Matriz.codigo',
            'Matriz.nome_fantasia',
        );

        $joins = array(
            array(
                "table" => "cliente",
                "alias" => "Matriz",
                "type" => "INNER",
                "conditions" => array("GrupoEconomicoCliente.codigo_cliente = Matriz.codigo AND Matriz.ativo = 1 AND Matriz.e_tomador <> 1")
            )
        );

        $conditions['GrupoEconomicoCliente.codigo_grupo_economico'] = $codigo_cliente;

        $dados = $this->find('list', array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions,
        ));

        return $dados;
    }

    public function comboGetUnidadesAjax($codigo_cliente)
    {

        $fields = array(
            'Matriz.codigo',
            'Matriz.nome_fantasia',
        );

        $joins = array(
            array(
                "table" => "cliente",
                "alias" => "Matriz",
                "type" => "INNER",
                "conditions" => array("GrupoEconomicoCliente.codigo_cliente = Matriz.codigo AND Matriz.ativo = 1 AND Matriz.e_tomador <> 1")
            )
        );

        $conditions['GrupoEconomicoCliente.codigo_grupo_economico'] = $codigo_cliente;

        $dados = $this->find('all', array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions,
        ));

        return $dados;
    }

    public function getMatriz($codigo_cliente)
    {

        $matriz = $this->find(
            'first',
            array(
                'recursive' => -1,
                'joins' => array(
                    array(
                        'table' => 'grupos_economicos',
                        'alias' => 'GrupoEconomico',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
                            )
                    ),
                    array(
                        'table' => 'cliente',
                        'alias' => 'ClienteMatriz',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ClienteMatriz.codigo = GrupoEconomico.codigo_cliente'
                            )
                    ),   
                    array(
                        'table' => 'cliente',
                        'alias' => 'Cliente',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente'
                            )
                    ),                                                          
                ),
                'conditions' => array(
                    'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente
                ),
                'fields' => array(
                    'GrupoEconomico.codigo',
                    'GrupoEconomico.codigo_cliente',
                    'GrupoEconomico.descricao',
                    'ClienteMatriz.codigo',
                    'ClienteMatriz.codigo_documento',
                    'ClienteMatriz.nome_fantasia',
                    'ClienteMatriz.razao_social',
                    'Cliente.codigo',
                    'Cliente.codigo_documento',
                    'Cliente.nome_fantasia',
                    'Cliente.razao_social',                    
                )
            )
        );

        return $matriz;
    }

    public function getClientesUnidadesMatriz() {

        $queryOptions = array(
            'fields' => array(
                'GrupoEconomicoCliente.codigo_grupo_economico',
                'GrupoEconomicoCliente.codigo_cliente',
                'ClienteMatriz.codigo',
                'Cliente.codigo',
                'Cliente.nome_fantasia',
                'Cliente.razao_social',
                'GrupoEconomico.codigo',
                'GrupoEconomico.codigo_cliente',
                'GrupoEconomico.descricao',
            ),
            'joins' => array(
                array(
                    'table' => 'grupos_economicos',
                    'alias' => 'GrupoEconomico',
                    'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
                    'type' => 'INNER',						
                ),
                array(
                    'table' => 'cliente',
                    'alias' => 'Cliente',
                    'conditions' => 'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente',
                    'type' => 'INNER',						
                ),		
                array(
                    'table' => 'cliente',
                    'alias' => 'ClienteMatriz',
                    'conditions' => 'ClienteMatriz.codigo = GrupoEconomico.codigo_cliente',
                    'type' => 'INNER',						
                ),									
            ),
            'conditions' => array(
                'Cliente.ativo' => 1,
                'Cliente.codigo_empresa' => 1,
                'Cliente.codigo = ClienteMatriz.codigo' 
            ),
            'recursive' => -1
        );

        $query = $this->find(
			'all',
            $queryOptions
		);

        return $query;
    }
}//FINAL CLASS GrupoEconomicoCliente
