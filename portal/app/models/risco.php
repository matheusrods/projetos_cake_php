<?php
class Risco extends AppModel
{
    var $name = 'Risco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'riscos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_riscos'));

    var $utf8DecodeHeaders = array(
        'Nome_Agente',
        'Grupo',
        'Atributo',
        'Classificacao_Efeito',
        'Obs_ASO_Inapto',
        'Obs_ASO_Inapto',
        'Agentes_Nocivos_Atividades_ESocial',
        'Caracterizado_Por_Altura',
        'Caracterizado_Por_Trabalho_Confinado',
        'Caracterizado_Por_Ruido',
        'Caracterizado_Por_Calor',
        'Ausencia_De_Risco',
        'Usa_Limite_Tolerancia_Do_PGR',
        'Considera_Medicao_Inferiro_Limite_Tolerancia',
        'Valor_Teto',
        'Classificacao_Efeito',
        'Copia_Para_Empresa_Cliente',
        'PCA'
    );

    var $validate = array(
        'nome_agente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Nome.',
            'required' => true
        ),
        'codigo_grupo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Grupo',
            'required' => true
        ),
    );

    //const GRUPO_QUIMICO = array('1' => 'Físico', '2' => '', '3' => '', '4' => '', '5' => '', '6' => '' );
    const FISICO = "Físico";
    const QUIMICO = "Químico";
    const BIOLOGICO = "Biológico";
    const AUSENCIADERISCO = "Ausência de Risco";
    const ERGONOMICOS = "Ergonômico";
    const ACIDENTES = "Acidentes";
    const MECANICO = "Mecânico";
    const OUTROS = "Outros";
    const MECANICOACIDENTES = "Mecânico/Acidentes";
    const PERICULOSOS = "Periculosos";
    const PENOSOS = "Penosos";
    const ASSOCIACAODEFATORESDERISCO = "Associação de Fatores de Risco";
    const AUSENCIADEFATORESDERISCO  = "Ausência de Fatores de Risco";

    public function incluir($dados)
    {
        $model_RiscoPeriodicidade = &ClassRegistry::init('Periodicidade');

        try {
            $this->query('begin transaction');

            if (!parent::incluir($dados['Risco'])) {
                throw new Exception('Não incluiu o risco!');
            }

            if (isset($dados['Periodicidade'])) {
                foreach ($dados['Periodicidade'] as $key => $campo) {

                    if (($campo['de'] != "") && ($campo['de'] != "") && ($campo['de'] != "") && $this->id) {
                        if (!$model_RiscoPeriodicidade->incluir(array(
                            'de' => $campo['de'],
                            'ate' => $campo['ate'],
                            'meses' => $campo['meses'],
                            'codigo_risco' => $this->id
                        ))) {
                            throw new Exception('Não incluiu o periodo!');
                        }
                    }
                }
            }
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            return false;
        }
    }

    public function atualizar($dados)
    {
        $model_RiscoPeriodicidade = &ClassRegistry::init('Periodicidade');
        $lista_periodos = $model_RiscoPeriodicidade->find('all', array('conditions' => array('codigo_risco' => $dados['Risco']['codigo'])));

        try {
            $this->query('begin transaction');

            // verifica se ta igual, se é igual nao atualiza!!!
            foreach ($lista_periodos as $key => $compara) {

                $achou = false;
                foreach ($dados['Periodicidade'] as $campo) {
                    if (($campo['de'] == $compara['Periodicidade']['de']) && ($campo['ate'] == $compara['Periodicidade']['ate']) && ($campo['meses'] == $compara['Periodicidade']['meses'])) {
                        $achou = true;
                        unset($dados['Periodicidade'][$key]);
                    }
                }

                if ($achou == false) {
                    $model_RiscoPeriodicidade->excluir($compara['Periodicidade']['codigo']);
                }
            }

            if (!parent::atualizar($dados)) {
                throw new Exception('Não atualizou o risco!');
            }

            if (isset($dados['Periodicidade']) && !empty($dados['Periodicidade'])) {
                foreach ($dados['Periodicidade'] as $key => $campo) {
                    if (($campo['de'] != "") && ($campo['de'] != "") && ($campo['de'] != "") && $this->id) {
                        if (!$model_RiscoPeriodicidade->incluir(array(
                            'de' => $campo['de'],
                            'ate' => $campo['ate'],
                            'meses' => $campo['meses'],
                            'codigo_risco' => $this->id
                        ))) {
                            throw new Exception('Não incluiu o periodo!');
                        }
                    }
                }
            }

            $this->commit();
            return true;
        } catch (Exception $e) {

            $this->rollback();
            return false;
        }
    }

    function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['Risco.codigo'] = $data['codigo'];

        if (!empty($data['nome_agente']))
            $conditions['Risco.nome_agente LIKE'] = '%' . $data['nome_agente'] . '%';

        if (!empty($data['codigo_agente_nocivo_esocial']))
            $conditions['Risco.codigo_agente_nocivo_esocial LIKE'] = '%' . $data['codigo_agente_nocivo_esocial'] . '%';

        if (!empty($data['codigo_grupo']))
            $conditions['GruposRiscos.codigo'] = $data['codigo_grupo'];

        if ($data['ativo'] != '') {
            $conditions['Risco.ativo'] = $data['ativo'];
        }

        return $conditions;
    }

    function carregar($codigo)
    {
        $dados = $this->find('first', array(
            'conditions' => array(
                $this->name . '.codigo' => $codigo
            )
        ));
        return $dados;
    }

    function retorna_grupo($codigo_grupo)
    {
        switch ($codigo_grupo) {
            case 1:
                $descricao_grupo = self::FISICO;
                break;
            case 2:
                $descricao_grupo = self::QUIMICO;
                break;
            case 3:
                $descricao_grupo = self::BIOLOGICO;
                break;
            case 4:
                $descricao_grupo = self::AUSENCIADERISCO;
                break;
            case 5:
                $descricao_grupo = self::ERGONOMICOS;
                break;
            case 6:
                $descricao_grupo = self::ACIDENTES;
                break;
            case 7:
                $descricao_grupo = self::MECANICO;
                break;
            case 8:
                $descricao_grupo = self::OUTROS;
                break;
            case 10:
                $descricao_grupo = self::MECANICOACIDENTES;
                break;
            case 11:
                $descricao_grupo = self::PERICULOSOS;
                break;
            case 12:
                $descricao_grupo = self::PENOSOS;
                break;
            case 13:
                $descricao_grupo = self::ASSOCIACAODEFATORESDERISCO;
                break;
            case 14:
                $descricao_grupo = self::AUSENCIADEFATORESDERISCO;
                break;
            default:
                $descricao_grupo = 'Não Encontrado';
                break;
        }
        return $descricao_grupo;
    }

    function carrega_grupo()
    {
        $grupo = array(
            1      => self::FISICO,
            2      => self::QUIMICO,
            3      => self::BIOLOGICO,
            4      => self::AUSENCIADERISCO,
            5      => self::ERGONOMICOS,
            6      => self::ACIDENTES,
            7      => self::MECANICO,
            8      => self::OUTROS,
            10  => self::MECANICOACIDENTES,
            11  => self::PERICULOSOS,
            12  => self::PENOSOS,
            13  => self::ASSOCIACAODEFATORESDERISCO,
            14  => self::AUSENCIADEFATORESDERISCO,
        );
        return $grupo;
    }

    function lista_por_cliente($codigo_cliente)
    {
        $GrupoEconomico = &ClassRegistry::Init('GrupoEconomico');
        $GrupoEconomicoCliente = &ClassRegistry::Init('GrupoEconomicoCliente');
        $GrupoExposicao = &ClassRegistry::Init('GrupoExposicao');
        $GrupoExposicaoRisco = &ClassRegistry::Init('GrupoExposicaoRisco');
        $ClienteSetor = &ClassRegistry::Init('ClienteSetor');

        $conditions = array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente);
        $fields = array('Risco.codigo', 'Risco.nome_agente');
        $order = array('Risco.nome_agente ASC');
        $joins     = array(
            array(
                'table' => $GrupoExposicaoRisco->databaseTable . '.' . $GrupoExposicaoRisco->tableSchema . '.' . $GrupoExposicaoRisco->useTable,
                'alias' => 'GrupoExposicaoRisco',
                'conditions' => 'GrupoExposicaoRisco.codigo_risco = Risco.codigo'
            ),
            array(
                'table'    => $GrupoExposicao->databaseTable . '.' . $GrupoExposicao->tableSchema . '.' . $GrupoExposicao->useTable,
                'alias'    => 'GrupoExposicao',
                'conditions' => 'GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao',
            ),
            array(
                'table'    => $ClienteSetor->databaseTable . '.' . $ClienteSetor->tableSchema . '.' . $ClienteSetor->useTable,
                'alias'    => 'ClienteSetor',
                'conditions' => 'GrupoExposicao.codigo_cliente_setor = ClienteSetor.codigo',
            ),
            array(
                'table'    => $GrupoEconomicoCliente->databaseTable . '.' . $GrupoEconomicoCliente->tableSchema . '.' . $GrupoEconomicoCliente->useTable,
                'alias'    => 'GrupoEconomicoCliente',
                'conditions' => 'ClienteSetor.codigo_cliente = GrupoEconomicoCliente.codigo_cliente',
            ),
            array(
                'table'    => $GrupoEconomico->databaseTable . '.' . $GrupoEconomico->tableSchema . '.' . $GrupoEconomico->useTable,
                'alias'    => 'GrupoEconomico',
                'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
            ),
        );

        $dados = $this->find('list', compact('conditions', 'fields', 'order', 'joins'));

        return $dados;
    }
    function lista_por_grupo_risco($codigo_grupo)
    {
        $GrupoRisco = &ClassRegistry::Init('GrupoRisco');
        $conditions = array('Risco.codigo_grupo' => $codigo_grupo);
        $fields = array('Risco.codigo', 'Risco.nome_agente');
        $order = array('Risco.nome_agente ASC');

        $joins     = array(
            array(
                'table'    => $GrupoRisco->databaseTable . '.' . $GrupoRisco->tableSchema . '.' . $GrupoRisco->useTable,
                'alias'    => 'GrupoRisco',
                'conditions' => 'Risco.codigo_grupo = GrupoRisco.codigo',
            )
        );

        $dados = $this->find('list', compact('conditions', 'fields', 'order', 'joins'));

        return $dados;
    }

    function localiza_risco_importacao($data)
    {
        $retorno = '';

        $codigo_cliente_grupo_economico = $data['codigo_cliente_grupo_economico'];
        $nome_agente = $data['risco'];
        $conditions = array(
            'Risco.nome_agente' => $nome_agente
        );

        $fields = array(
            'Risco.codigo', 'Risco.nome_agente'
        );

        $dados = $this->find('first', compact('conditions', 'fields'));
        if (empty($dados)) {
            $retorno['Erro']['Risco'] = array('codigo_risco' => 'Setor não encontrado!');
        } else {
            $retorno['Dados'] = $dados;
        }

        return $retorno;
    }

    public function determina_ausencia_risco($codigo)
    {

        $verifica_ausencia = $this->read('ausencia_de_risco', $codigo);
        //Atualiza somente se o risco passado é uma ausência de risco
        if ($verifica_ausencia['Risco']['ausencia_de_risco'] == 1) {
            $this->query('UPDATE riscos SET ausencia_de_risco = 0 WHERE codigo != ' . $codigo . ' AND codigo_empresa = (SELECT codigo_empresa FROM riscos WHERE codigo = ' . $codigo . ')');
        }

        return true;
    }

    public function carregaragente($agente)
    {
        $agente = $agente;
        $conditions = array(
            'Risco.nome_agente' => $agente,
            'Risco.ativo' => 1
        );
        $dados = $this->find('first', array('conditions' => $conditions));
        return $dados;
    }

    public function getListaRiscos($filtros = null)
    {
        $fields = array(
            'Risco.codigo',
            'Risco.nome_agente',
            'Risco.codigo_agente_nocivo_esocial',
            'Risco.codigo_grupo',
            'Risco.ativo',
            'GruposRiscos.codigo',
            'GruposRiscos.descricao'
        );

        $joins = array(
            array(
                'table' => 'grupos_riscos',
                'alias' => 'GruposRiscos',
                'type' => 'INNER',
                'conditions' => "Risco.codigo_grupo = GruposRiscos.codigo and Risco.codigo_agente_nocivo_esocial <> ''  "
            )
        );

        $conditions = $this->converteFiltroEmCondition($filtros);
        $conditions["Risco.ativo"] = 1;

        $risco = array(
            'fields'     => $fields,
            'joins'      => $joins,
            'conditions' => $conditions,
            'limit'      => 50,
            'order'      => 'Risco.nome_agente ASC',
        );

        return $risco;
    }

    //Retorna o risco_esocial vinculado ao riscos_impacto
    function getByCodigoRiscosImpactos($codigo)
    {
        //		pr($codigo);exit;
        $fields = array(
            'Risco.codigo as codigo',
            'Risco.nome_agente as nome_agente',
            'Risco.codigo_agente_nocivo_esocial as codigo_agente_nocivo_esocial',
            'Risco.codigo_grupo as codigo_grupo',
            'GruposRiscos.descricao as descricao'
        );

        $joins = array(
            array(
                'table' => 'grupos_riscos',
                'alias' => 'GruposRiscos',
                'type' => 'INNER',
                'conditions' => "Risco.codigo_grupo = GruposRiscos.codigo and Risco.codigo_agente_nocivo_esocial <> ''  "
            ),
            array(
                'table' => 'riscos_impactos',
                'alias' => 'RiscosImpactos',
                'type' => 'INNER',
                'conditions' => "RiscosImpactos.codigo_risco = Risco.codigo "
            ),
        );

        $conditions = array('RiscosImpactos.codigo' => $codigo);

        $riscos = $this->find(
            'first',
            array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions
            )
        );

        if (empty($riscos)) {
            return array();
        }

        return $riscos;
    }

    public function relatorio()
    {

        $riscosRsArr = $this->query("SELECT
            Risco.codigo AS Codigo,
            Risco.codigo_rh AS Codigo_RH,
            Risco.nome_agente AS Nome_Agente,
            Risco.descricao_ingles AS Nome_Agente_IN,
            GrupoRisco.descricao AS Grupo,
            RiscoAtributo.descricao AS Atributo,
            Unidade_Medida,
            CASE
                WHEN Risco.unidade_medida IS NOT NULL AND Risco.unidade_medida != ''
                    THEN Risco.unidade_medida
                ELSE ''
            END AS Unidade_Medida,
            CASE
                WHEN Risco.risco_caracterizado_por_altura = 1 THEN 'Sim'
                ELSE 'Não'
            END AS Caracterizado_Por_Altura,
            CASE
                WHEN Risco.risco_caracterizado_por_trabalho_confinado = 1 THEN 'Sim'
                ELSE 'Não'
            END AS Caracterizado_Por_Trabalho_Confinado,
            CASE
                WHEN Risco.risco_caracterizado_por_ruido = 1 THEN 'Sim'
                ELSE 'Não'
            END AS Caracterizado_Por_Ruido,
            CASE
                WHEN Risco.risco_caracterizado_por_calor = 1 THEN 'Sim'
                ELSE 'Não'
            END AS Caracterizado_Por_Calor,
            CASE
                WHEN Risco.ausencia_de_risco = 1 THEN 'Sim'
                ELSE 'Não'
            END AS Ausencia_De_Risco,
            CASE
                WHEN Risco.usa_limite_tolerancia_no_ppra = 1 THEN 'Sim'
                ELSE 'Não'
            END AS Usa_Limite_Tolerancia_Do_PGR,
            CASE
                WHEN Risco.considera_medicao_inferior_limite_tolerancia = 1 THEN 'Sim'
                ELSE 'Não'
            END AS Considera_Medicao_Inferiro_Limite_Tolerancia,
            Risco.limite_tolerancia AS Limite_Tolerancia,
            Risco.nivel_acao AS Nivel_Acao,
            CASE
                WHEN Risco.valor_teto = 1 THEN 'Sim'
                ELSE 'Não'
            END AS Valor_Teto,
            Risco.faixa_conforto_de AS Faixa_Conforto_De,
            Risco.faixa_conforto_ate AS Faixa_Conforto_Ate,
            Risco.quantidade_casas_decimais AS Qtde_Casas_Decimais,
            Risco.periodicidade_medicao AS Periodicidade_Medicao,
            CASE
                WHEN ClassificacaoEfeito.codigo IS NOT NULL
                    THEN ClassificacaoEfeito.descricao
                ELSE ''
            END AS Classificacao_Efeito,
            CASE
                WHEN Risco.copia_para_empresa_cliente = 1 THEN 'Sim'
                ELSE 'Não'
            END AS Copia_Para_Empresa_Cliente,
            CASE
                WHEN Risco.pca = 1 THEN 'Sim'
                ELSE 'Não'
            END AS PCA,
            CASE 
                WHEN Risco.obs_aso_apto IS NOT NULL AND Risco.obs_aso_apto != ''
                    THEN Risco.obs_aso_apto
                ELSE ''
            END AS Obs_ASO_Apto,
            CASE
                WHEN Risco.obs_aso_inapto IS NOT NULL AND Risco.obs_aso_inapto != ''
                    THEN Risco.obs_aso_inapto
                ELSE ''
            END Obs_ASO_Inapto,
            Risco.codigo_agente_nocivo_esocial AS Codigo_Agente_Nocivo_ESocial,
            Risco.fator_risco_esocial AS Fator_Risco_ESocial,
            Risco.aponsentadoria_especial_inss_esocial AS Aposentadoria_Especial_INSS_ESocial,
            CASE
                WHEN ESocial.codigo IS NOT NULL
                THEN  CONCAT(ESocial.codigo_descricao, ' - ', ESocial.descricao)	
                ELSE ''
            END AS Agentes_Nocivos_Atividades_ESocial  
        FROM
            riscos Risco
            JOIN grupos_riscos GrupoRisco ON GrupoRisco.codigo = Risco.codigo_grupo
            LEFT JOIN riscos_atributos RiscoAtributo ON RiscoAtributo.codigo = Risco.codigo_risco_atributo
            LEFT JOIN riscos_atributos_detalhes ClassificacaoEfeito ON ClassificacaoEfeito.codigo = Risco.classificacao_efeito
            LEFT JOIN esocial ESocial ON ESocial.codigo = Risco.codigo_esocial_24 
        WHERE
            Risco.ativo = 1
        ORDER BY
            Risco.codigo ASC");

        require_once APP . 'vendors' . DS . 'encoding.php';

        $returnArr = array();
        $inReturnArr = array();
        foreach ($riscosRsArr as $indiceLinhaArr => $linhaArr) {

            foreach ($linhaArr as $indiceLinha => $linha) {

                foreach ($linha as $coluna => $valor) {


                    if (in_array($coluna, $this->utf8DecodeHeaders)) {

                        $linha[$coluna] = Encoding::fixUTF8($valor);
                    }
                }

                $returnArr[] = $linha;
                $inReturnArr[] = $linha['Codigo'];
            }
        }

        return $returnArr;
    }
}
