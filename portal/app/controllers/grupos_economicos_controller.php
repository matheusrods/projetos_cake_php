<?php
class GruposEconomicosController extends appController
{
    var $name = 'GruposEconomicos';
    var $uses = array(
        'GrupoEconomico',
        'ClienteImplantacao',
        'Pedido',
        'GrupoEconomicoCliente',
        'Cliente',
        'Setor',
        'Cargo',
        'ClienteSetorCargo',
        'FuncionarioSetorCargo',
        'ClienteFuncionario',
        'PedidoExame',
        'GrupoExposicao',
        'ClienteSetor',
        'Gpra',
        'PrevencaoRiscoAmbiental',
        'OrdemServico',
        'ClienteSetorVersoes',
        'GrupoExposicaoVersoes',
        'GpraVersoes',
        'PrevencaoRiscoAmbientalVersoes',
        'AplicacaoExame',
        'CronogramaAcao',
        'Pmps',
        'HospitaisEmergencia',
        'AplicacaoExameVersoes',
        'CronogramaAcaoVersao',
        'SeparacaoGe',
        'ClienteProduto',
        'ClienteProdutoServico2'

    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('novas_unidades_separacao', 'separar_config_grupos');
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');
    } //FINAL FUNCTION beforeFilter

    function index()
    {
        $this->pageTitle = 'Grupos Econômicos';
        $filtros = $this->Filtros->controla_sessao($this->data, 'GrupoEconomico');
        $this->data['GrupoEconomico'] = $filtros;
    } //FINAL FUNCTION index

    function listagem($destino)
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'GrupoEconomico');
        $conditions = $this->GrupoEconomico->converteFiltrosEmConditions($filtros);
        $this->paginate['GrupoEconomico'] = array(
            'recursive' => 1,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'GrupoEconomico.descricao',
        );
        $grupos_economicos = $this->paginate('GrupoEconomico');
        $this->set(compact('grupos_economicos'));
    } //FINAL FUNCTION listagem

    function incluir()
    {
        $this->pageTitle = 'Incluir Grupo Econômico';
        if (!empty($this->data)) {
            if ($this->GrupoEconomico->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $this->data['GrupoEconomico']['editar'] = false;
    } //FINAL FUNCTION incluir

    function editar($codigo)
    {
        $this->pageTitle = 'Editar Grupo Econômico';
        if (!empty($this->data)) {
            if ($this->GrupoEconomico->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->GrupoEconomico->read(null, $codigo);
            $this->data['GrupoEconomico']['editar'] = true;
        }
    } //FINAL FUNCTION editar

    function excluir($codigo)
    {
        if ($this->GrupoEconomico->excluir($codigo)) {
            $this->BSession->setFlash('delete_success');
        } else {
            $this->BSession->setFlash('delete_error');
        }
        $this->redirect(array('action' => 'index'));
    } //FINAL FUNCTION excluir

    function exportar_funcionario($codigo_cliente, $implantacao = 'inplantacao', $params = null)
    { //codigo_cliente -> GrupoEconomico.

        App::import('model', 'PedidoExame');
        // $PedidoExame =& ClassRegistry::init('PedidoExame');

        if (isset($codigo_cliente) && !empty($codigo_cliente)) {
            //para pegar todos os funcionarios até demitidos deve ser passar GrupoEconomico->queryEstrutura($codigo_cliente, false);
            $query = $this->GrupoEconomico->queryEstrutura($codigo_cliente, false, $params);
            //comentado para verificar como podemos proceder e realizar no servidor pois estava dando erro            
            $linhasExportaFuncionarios = $this->GrupoEconomico->query($query);

            $nome_arquivo = date('YmdHis') . 'ex.csv';

            //headers
            ob_clean();
            header('Content-Encoding: ISO-8859-1');
            header('Content-type: text/csv; charset=ISO-8859-1');
            header(sprintf('Content-Disposition: attachment; filename="%s"', $nome_arquivo));
            header('Pragma: no-cache');

            echo utf8_decode('"Código Unidade";"Nome da Unidade";"Nome do Setor";"Nome do Cargo";"Código Matrícula";"Matricula do Funcionario";"Nome do Funcionario";"Data de Nascimento(dd/mm/aaaa)";"Sexo(F:Feminino, M:Masculino)";"Situacao Cadastral(S:Ativo, F:Ferias, A:Afastado, I:Inativo)";"Data de Admissao(dd/mm/aaaa)";"Data de Demissao(dd/mm/aaaa)";"Data Início Cargo(dd/mm/aaaa)";"Estado Civil(1:Solteiro, 2:Casado, 3:Separado, 4:Divorciado, 5:Viuvo, 6:Outros)";"Pis/Pasep";"Rg";"Órgão Expedidor RG";"CPF";"CTPS";"Serie CTPS";"UF CTPS";"Endereco";"Numero";"Complemento";"Bairro";"Cidade";"Estado";"Cep";"Possui Deficiencia(S:Sim, N:Não)";"Codigo CBO";"Codigo GFIP";"Centro Custo";"Turno";"Descricao de atividades do cargo";"Celular do Funcionario((ddd)+numero telefone)";"Autoriza envio de SMS ao funcionario";"E-mail do Funcionario";"Autoriza envio de e-mail ao funcionario";"Contato do responsavel da Unidade";"Telefone do responsavel da Unidade((ddd)+numero telefone)";"E-maildo responsavel da Unidade";"Endereco da Unidade";"Numero da Unidade";"Complemento da Unidade";"Bairro da Unidade";"Cidade da Unidade";"Estado da Unidade";"Cep da Unidade";"CNPJ da Unidade";"Inscricao Estadual";"Inscricao Municipal";"Cnae";"Grau de Risco";"Razao Social Unidade";"Unidade de Negocio";"Regime Tributario(1:Simples Nacional, 2:Simples Nacional, excesso sublimite de receita bruta, 3:Regime Normal)";"Codigo Externo";"Tipo Unidade(F: Fiscal, O: Operacional)";"Conselho Profissional";"Número do Conselho";"Conselho Estado(UF)";"Chave Externa";"Código Cargo Externo";"Inclusão Funcionário";"Data do último ASO"' . "\n");

            foreach ($linhasExportaFuncionarios as $indiceLinha => $dado) {

                // debug($dado[0]);
                // debug($linhasEmArr);

                // if(!empty($dados)) {
                //foreach ($dados as $key => $dado) {

                //verifica se o campo esta nulo caso esteja pula para o proximo
                if (is_null($dado[0]['codigo_alocacao'])) {
                    continue;
                }

                $linha = $dado[0]['codigo_alocacao'] . ';';
                $linha .= '"' . $dado[0]['alocacao_nome_fantasia'] . '";';
                $linha .= '"' . trim($dado[0]['nome_setor']) . '";';
                $linha .= '"' . trim($dado[0]['nome_cargo']) . '";';
                $linha .= '="' . $dado[0]['codigo_matricula'] . '";';
                $linha .= '="' . $dado[0]['matricula'] . '";';
                $linha .= '"' . trim($dado[0]['nome_funcionario']) . '";';
                $linha .= '"' . $dado[0]['data_nascimento'] . '";';
                $linha .= '"' . $dado[0]['sexo'] . '";';

                switch ($dado[0]['status_matricula']) {
                    case 0:
                        $linha .= '"' . 'I' . '";';
                        break;
                    case 1:
                        $linha .= '"' . 'S' . '";';
                        break;
                    case 2:
                        $linha .= '"' . 'F' . '";';
                        break;
                    case 3:
                        $linha .= '"' . 'A' . '";';
                        break;
                    default:
                        $linha .= '"' . '";';
                        break;
                }

                $linha .= '"' . $dado[0]['data_admissao'] . '";';
                $linha .= '"' . $dado[0]['data_demissao'] . '";';
                $linha .= '"' . $dado[0]['data_inicio_cargo'] . '";';
                $linha .= '"' . trim($dado[0]['estado_civil']) . '";';
                $linha .= '"' . $dado[0]['pispasep'] . '";';
                $linha .= '"' . $dado[0]['rg'] . '";';
                $linha .= '"' . trim($dado[0]['rg_orgao']) . '";';
                $linha .= '="' . $dado[0]['cpf'] . '";';
                $linha .= '="' . $dado[0]['ctps'] . '";';
                $linha .= '"' . $dado[0]['ctps_serie'] . '";';
                $linha .= '"' . $dado[0]['ctps_uf'] . '";';
                $linha .= '"' . trim($dado[0]['endereco']) . '";';
                $linha .= '"' . $dado[0]['endereco_numero'] . '";';
                $linha .= '"' . trim(str_replace(";", ", ", $dado[0]['endereco_complemento'])) . '";';
                $linha .= '"' . trim($dado[0]['bairro']) . '";';
                $linha .= '"' . trim($dado[0]['cidade']) . '";';
                $linha .= '"' . $dado[0]['uf'] . '";';
                $linha .= '="' . $dado[0]['cep'] . '";';

                switch ($dado[0]['funcionario_deficiencia']) {
                    case 0:
                        $linha .= '"' . 'N' . '";';
                        break;
                    case 1:
                        $linha .= '"' . 'S' . '";';
                        break;
                    default:
                        $linha .= '"' . '";';
                        break;
                }

                $linha .= '"' . $dado[0]['codigo_cbo'] . '";';
                $linha .= '"' . $dado[0]['gfip'] . '";';
                $linha .= '"' . trim($dado[0]['centro_custo']) . '";';

                $linha .= '"' . $dado[0]['turno'] . '";';
                $linha .= '"' . trim(str_replace("\r\n", " ", str_replace(";", " ", $dado[0]['cargo_descricao_detalhada']))) . '";';
                $linha .= '"' . $dado[0]['celular_funcionario'] . '";';
                $linha .= '"' . $dado[0]['autoriza_envio_sms'] . '";';
                $linha .= '"' . str_replace(";", ", ", $dado[0]['email_funcionario']) . '";';
                $linha .= '"' . $dado[0]['autoriza_envio_email'] . '";';
                $linha .= '"' . $dado[0]['contato_alocacao'] . '";';
                $linha .= '"' . $dado[0]['telefone_alocacao'] . '";';
                $linha .= '"' . str_replace(";", ", ", $dado[0]['email_alocacao']) . '";';
                $linha .= '"' . trim($dado[0]['alocacao_endereco']) . '";';
                $linha .= '"' . $dado[0]['alocacao_endereco_numero'] . '";';
                $linha .= '"' . trim(str_replace(";", ", ", $dado[0]['alocacao_endereco_complemento'])) . '";';
                $linha .= '"' . trim($dado[0]['alocacao_bairro']) . '";';
                $linha .= '"' . trim($dado[0]['alocacao_cidade']) . '";';
                $linha .= '"' . $dado[0]['alocacao_uf'] . '";';
                $linha .= '="' . $dado[0]['alocacao_cep'] . '";';
                $linha .= '="' . $dado[0]['alocacao_cnpj'] . '";';
                $linha .= '="' . $dado[0]['alocacao_inscricao_estadual'] . '";';
                $linha .= '="' . $dado[0]['alocacao_ccm'] . '";';
                $linha .= '"' . $dado[0]['alocacao_cnae'] . '";';
                $linha .= '"' . $dado[0]['cnae_grau_risco'] . '";';
                $linha .= '"' . trim($dado[0]['alocacao_razao_social']) . '";';
                $linha .= '"' . '";';
                $linha .= '"' . $dado[0]['alocacao_cod_regime_tribut'] . '";';
                $linha .= '"' . $dado[0]['alocacao_codigo_externo'] . '";';
                $linha .= '"' . $dado[0]['alocacao_tipo_unidade'] . '";';
                $linha .= '"' . $dado[0]['conselhoprofissional_descricao'] . '";';
                $linha .= '"' . $dado[0]['numero_conselho'] . '";';
                $linha .= '"' . $dado[0]['conselho_uf'] . '";';
                $linha .= '="' . $dado[0]['chave_externa'] . '";';
                $linha .= '"' . $dado[0]['codigo_cargo_externo'] . '";';

                $data = $dado[0]['data_inclusao_funcionario'];

                // $data1 = DateTime::createFromFormat("d-m-Y", $data);
                // echo $data1->format("d-m-Y");


                $data_ultimo_aso = $this->PedidoExame->getDataUltimoAsoByMatricula($dado[0]['matricula']);


                $linha .= '"' . date("d/m/Y", strtotime($data)) . '";';

                !empty($data_ultimo_aso) ?
                    $linha .= '"' . date("d/m/Y", strtotime($data_ultimo_aso)) . '"'  : $linha .= '"' . '"';


                $linha .= PHP_EOL;
                echo utf8_decode($linha);
                //echo ($linha);
            }
            //debug($dado);
        }
        exit;
    } //FINAL FUNCTION exportar_funcionario

    // public function por_cliente($codigo_cliente) {
    // 	echo $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
    // 	die();
    // }//FINAL FUNCTION por_cliente

    public function por_cliente($codigo_cliente = null)
    {

        if (is_null($codigo_cliente)) {
            $this->responseJson();
        }

        $codigo_cliente = $this->normalizaCodigoCliente($codigo_cliente); // normaliza codigo

        $dados = $this->GrupoEconomico->obterCodigoMatrizPeloCodigoFilial($codigo_cliente);

        $this->responseJson($dados);
    } //FINAL FUNCTION por_cliente

    public function monta_arquivo_enviar_funcionarios()
    {

        $this->layout = false;
        $link = $this->params['url']['key'];

        //descriptografa a chave da url
        $link = Comum::descriptografarLink($link);

        //separa os dados
        //all -> para usuarios que não tem cliente relacionado (interno)
        //20 -> codigo do cliente
        $codigo_cliente = null;
        if ($link != 'all') {
            $codigo_cliente = str_replace("'", "", $link);
        } //fim if link

        ob_clean(); //limpa o cache dos dados

        //seta os headers
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="Arquivo_Funcionarios_' . date('YmdHis') . '.csv"');

        //pega o arquivo
        $dadosExame['codigo_cliente'] = $codigo_cliente;
        $dados = $this->Pedido->monta_arquivo_enviar_funcionarios($dadosExame);

        echo $dados;
        die;
    }

    /**
     * Obter clientes associados a um usuario ja autenticado
     *
     * @param [mixed] $param
     * @return void
     */
    public function obter_multiclientes($usuario = null)
    {
        $multiclientes = array();
        $usuario = $this->Session->read('Auth.Usuario');
        if (isset($usuario['multicliente'])) {
            $multiclientes = $usuario['multicliente'];
        }
        $this->responseJson($multiclientes);
    }

    public function index_grupos_economicos($msg_erro = '')
    {


        //titulo
        $this->pageTitle = 'Separação Grupo Econômico';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->GrupoEconomico->name);

        //trazer o codigo_cliente do usuario
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        //setando a model para os filtros
        $this->data['GrupoEconomico'] = $filtros;

        //carrega o combo dos estados para a ctp
        $this->carrega_combos_grupo_economico('GrupoEconomico');

        if ($msg_erro == 1) {
            $msg_erro = $this->Session->read('separacao_ge_erro');
        }

        $this->set(compact('msg_erro'));
    }

    public function lista_unidades_grupo_economico()
    {
        $this->layout = 'ajax';

        //controle da sessao para alimentar os filtros
        $filtros = $this->Filtros->controla_sessao($this->data, $this->GrupoEconomico->name);

        //seta o codigo_codigo cliente para ajudar na buscar pela unidades nas acoes da tela da lista
        $codigo_cliente = $filtros['codigo_cliente'];

        //condicoes vindas do filtro
        $conditions = $this->GrupoEconomico->converteFiltroEmConditionGE($filtros);
        //condicao adicionada para ajudar na busca
        $conditions[] = array('Unidade.ativo = 1');

        //fields
        $fields = array(
            'Unidade.codigo',
            'Unidade.razao_social',
            'Unidade.nome_fantasia',
            'Unidade.tipo_unidade',
            'Unidade.e_tomador'
        );

        //joins
        $joins = array(
            array(
                'table' => 'cliente',
                'alias' => 'Unidade',
                'type' => 'INNER',
                'conditions' => 'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
            )
        );

        //buscando todas as unidades do cliente
        $lista_clientes_grupo = $this->GrupoEconomicoCliente->find('all', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins, 'order' => array('Unidade.codigo', 'Unidade.nome_fantasia')));

        //varre os dados da lista
        $lista_unidades_combo = array();
        $lista_unidades_combo_ge = array();
        //verifica se tem lista de clientes grupo
        if (!empty($lista_clientes_grupo)) {
            //varre a lista de clientes
            foreach ($lista_clientes_grupo as $unidades) {
                //verifica se é o grupo economico que está ativo para tirar
                if ($codigo_cliente == $unidades['Unidade']['codigo']) {
                    continue;
                }

                //verifica se a empresa é fiscal para gerar o grupo economico
                if ($unidades['Unidade']['tipo_unidade'] == 'F' && $unidades['Unidade']['e_tomador'] == 0) {
                    $lista_unidades_combo_ge[$unidades['Unidade']['codigo']] = $unidades['Unidade']['codigo'] . " - " . $unidades['Unidade']['nome_fantasia'];
                }

                //combo das unidades que pode ser todas
                $lista_unidades_combo[$unidades['Unidade']['codigo']] = $unidades['Unidade']['codigo'] . " - " . $unidades['Unidade']['nome_fantasia'];
            }
        }

        $this->set(compact('lista_clientes_grupo', 'codigo_cliente', 'lista_unidades_combo', 'lista_unidades_combo_ge'));
    }

    public function carrega_combos_grupo_economico($model)
    {

        //variavel vazia
        $unidades = array();

        //se existir codigo cliente, ele seta, senao vazio
        $codigo_cliente = (isset($this->data[$model]['codigo_cliente'])) ? $this->data[$model]['codigo_cliente'] : array();

        //se codigo cliente nao for vazio ele carrega o combo e alimenta o filtro das unidades
        if (!empty($codigo_cliente)) {
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
        }

        $this->set(compact('unidades'));
    } //fim

    /**
     * [separar_config_grupos método para realizar a separação do grupo economico com a configuração feita em tela
     *
     * 	cartilha de passos
     *  	Criar novo grupo economico
     *	     Hierarquias
     *		 Matrículas
     *		 Setores
     *   		 deve ser gerado um novo e alterado os códigos da função (funcionarios_setor_cargo)
     *		 Cargo
     *   		 deve ser gerado um novo e alterado os códigos da função (funcionarios_setor_cargo)
     *		 Pedidos de exames
     *		 Histórico de faturamento
     *   		 Quando não foi emitido uma nota para outro
     *		 PGR
     *   		 Colocar o novo código de setor e cargo
     *		 PCMSO
     *   		 Trocar para o novo código de setor e cargo
     *		 Atestados Médicos
     *		 Assinaturas
     *		 Link dos prestadores
     *
     * ]
     * @return [type] [description]
     */
    public function separar_config_grupos($codigo_cliente_matriz_antigo)
    {

        //seta que não vai ter layout ctp
        $this->layout = false;

        $msg_erro = '';

        //verifica se é um post
        if ($this->RequestHandler->isPost()) {

            // unset($this->data['unidade']);
            // $this->data['unidade']['xx'] = array();
            // $this->data['unidade']['0'] = array('codigo' => 10549);
            // $this->log('iniciando o processamento da separacao de grupos economicos','debug');

            // debug($this->data);exit;

            $this->gravaSeparacao($codigo_cliente_matriz_antigo, $this->data);

            //inicia um try/catch
            try {
                //seta que vamos trabalhar com transacao
                $this->GrupoEconomico->query('begin transaction');

                /**
                 * cartilha de passos
                 * Criar novo grupo economico
                 *  criar grupo economico cliente
                 *  • Setores
                 *  	◦ deve ser gerado um novo e alterado os códigos da função (funcionarios_setor_cargo)
                 *  • Cargo
                 *  	◦ deve ser gerado um novo e alterado os códigos da função (funcionarios_setor_cargo)
                 *  • Hierarquias
                 *  • Matrículas
                 *  	• Pedidos de exames
                 *  • PGR
                 *  	◦ Colocar o novo código de setor e cargo
                 *  	prevencao de riscos ambientais
                 *  	versoes
                 *  • PCMSO
                 *  	◦ Trocar para o novo código de setor e cargo
                 *  	cronograma de acoes
                 *  	versoes
                 */
                // debug($codigo_cliente);
                // debug($this->data);

                //pega o id do novo cliente que vai ser um novo grupo economico
                $codigo_cliente_novo_ge = $this->data['GrupoEconomico']['novo_codigo_unidade_grupo_economico'];

                //verificar se tem assinatura
                $verificacao_assinatura = $this->verificaAssinatura($this->data);

                if (!empty($verificacao_assinatura)) {
                    throw new Exception($verificacao_assinatura);
                }

                //para criar um novo grupo economico
                $codigo_grupo_economico = $this->gerarGrupoEconomico($codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo);
                // debug($codigo_grupo_economico);
                if (!$codigo_grupo_economico) {
                    //retorna erro
                    throw new Exception("Houve um erro para incluir o grupo economico");
                } //fim codigo_grupo_economico

                //copia os setores
                if (!$this->gerarNovosSetores($codigo_cliente_matriz_antigo, $codigo_cliente_novo_ge)) {
                    //retorna erro
                    throw new Exception("Houve um erro para copiar os setores");
                }
                // debug("setores");

                //copia os cargos
                if (!$this->gerarNovosCargos($codigo_cliente_matriz_antigo, $codigo_cliente_novo_ge)) {
                    //retorna erro
                    throw new Exception("Houve um erro para copiar os cargos");
                }
                // debug("cargos");

                //gera o grupo economico cliente
                $codigo_grupo_economico_cliente = $this->gerarGrupoEconomicoCliente($codigo_grupo_economico, $codigo_cliente_novo_ge);
                // debug($codigo_grupo_economico_cliente);
                if (!$codigo_grupo_economico_cliente) {
                    //retorna erro
                    throw new Exception("Houve um erro para altualizar o grupo economico cliente");
                } //fim codigo_grupo_economico_cliente

                //separacao do grupo economico matriz
                $this->processar_separacao($codigo_cliente_novo_ge, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo, $codigo_grupo_economico);

                //retira o xx do array
                unset($this->data['unidade']['xx']);

                //metodo para levar as unidades para o grupo economico
                $this->levar_unidades($codigo_grupo_economico, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo, $this->data['unidade']);

                //fecha a transacao com sucesso
                $this->GrupoEconomico->commit();
                // $this->GrupoEconomico->rollback();

                //houve sucesso na regra
                $this->BSession->setFlash('save_success');
            } catch (Exception $e) {

                //seta o log
                $this->log(print_r($e->getMessage(), 1), 'debug');

                $msg_erro = $e->getMessage();

                $this->Session->write('separacao_ge_erro', $msg_erro);

                //houve um erro
                $this->BSession->setFlash(array(MSGT_ERROR, $msg_erro));

                //desfaz o que foi feito no banco de dados
                $this->GrupoEconomico->rollback();

                // debug($e->getMessage());
                // exit;

            } //fim try/catch

        } //fim post

        //redireciona para a tela de view
        $this->redirect(array('controller' => 'grupos_economicos', 'action' => 'index_grupos_economicos', 1));
    } //fim separar_conf_grupos

    public function levar_unidades($codigo_grupo_economico, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo, $unidades)
    {
        //varre os dados de unidade que devem estar abaixo do grupo economico
        foreach ($unidades as $key => $value) {

            //pega o codigo da unidade que vai ser criada
            $codigo_unidade = $value['codigo'];

            // debug("codigo_unidade:" . $codigo_unidade);

            //gera o grupo economico cliente
            $codigo_grupo_economico_cliente = $this->gerarGrupoEconomicoCliente($codigo_grupo_economico, $codigo_unidade);
            if (!$codigo_grupo_economico_cliente) {
                //retorna erro
                throw new Exception("Houve um erro para altualizar o grupo economico cliente");
            } //fim codigo_grupo_economico_cliente

            //leva as unidades
            $this->processar_separacao($codigo_unidade, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo, $codigo_grupo_economico);
        } //fim foreach

    } //fim levar unidades

    /**
     * [processar_separacao description]
     *
     * metodo intermediário para processar a separacao do grupo economico
     *
     * @param  [type] $codigo_unidade 		  [description]
     * @param  [type] $codigo_cliente_novo_ge [description]
     * @param  [type] $codigo_cliente         [description]
     * @return [type]                         [description]
     */
    private function processar_separacao($codigo_unidade, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo, $codigo_grupo_economico)
    {
        //atualiza as ordens de servico
        if (!$this->gerarOrdemServico($codigo_unidade, $codigo_grupo_economico)) {
            //retorna erro
            throw new Exception("Houve um erro para atualizar a ordem de servico");
        }
        // debug("ordem servico");

        //ajusta as hierarquias do cliente cadastrado
        if (!$this->gerarHierarquias($codigo_unidade, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
            //retorna erro
            throw new Exception("Houve um erro para gerar as hierarquias");
        }
        // debug("hierarquias");

        //ajusta as matriculas e funções (cliente_funcionario e funcionario_setores_cargos)
        if (!$this->gerarMatriculas($codigo_unidade, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
            //retorna erro
            throw new Exception("Houve um erro para gerar as matriculas");
        }
        // debug("matriculas");

        //ajustar os pgrs
        if (!$this->gerarPPRA($codigo_unidade, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
            //retorna erro
            throw new Exception("Houve um erro para gerar os ppras");
        }
        // debug("ppra");

        //ajustar os ppras_versoes
        if (!$this->gerarPPRAVersoes($codigo_unidade, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
            //retorna erro
            throw new Exception("Houve um erro para gerar os ppras_versoes");
        }
        // debug("ppras_versoes");

        //ajustar os pcmso
        if (!$this->gerarPCMSO($codigo_unidade, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
            //retorna erro
            throw new Exception("Houve um erro para gerar os PCMSO");
        }
        // debug("pcmso");

        //ajustar os pcmso_versoes
        if (!$this->gerarPCMSOVersoes($codigo_unidade, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
            //retorna erro
            throw new Exception("Houve um erro para gerar os pcmso_versoes");
        }
        // debug("pcmso_versoes");

        // debug("finalizou:" . $codigo_unidade);

    } // fim processar_separacao

    /**
     * [setAssinatura metodo para buscar e verificar se tem assintura ou mesmo se é cliente pagador de alguma outra empresa ]
     * @param [type] $codigo_clliente [codigo do cliente que vai verificar]
     */
    public function verificaAssinatura($data)
    {
        //verifica se tem assinatura para ele
        //variavel auxiliar
        $msg = "";
        $unidades = array();

        //verifica se tem dados no parametro
        if (!empty($data)) {
            //codigo do cliente grupo economico
            $codigo_cliente = $data['GrupoEconomico']['novo_codigo_unidade_grupo_economico'];

            $joinsCP = array(
                array(
                    'table' => 'cliente_produto',
                    'alias' => 'ClienteProduto',
                    'conditions' => 'ClienteProduto.codigo = ClienteProdutoServico2.codigo_cliente_produto'
                )
            );

            //verifica se tem assinatura com o codigo cliente sendo o codigo_cliente_pagador
            // $cliente_pagador = $this->ClienteProdutoServico2->find('first',array('conditions' => array('codigo_cliente_pagador' => $codigo_cliente)));
            $cliente_pagador = $this->ClienteProdutoServico2->find('first', array('fields' => array('ClienteProduto.codigo_cliente AS codigo_cliente', 'ClienteProdutoServico2.codigo_cliente_pagador AS codigo_cliente_pagador'), 'joins' => $joinsCP, 'conditions' => array('ClienteProdutoServico2.codigo_cliente_pagador' => $codigo_cliente)));

            if (!empty($cliente_pagador)) {

                if ($cliente_pagador[0]['codigo_cliente'] != $cliente_pagador[0]['codigo_cliente_pagador']) {
                    $unidades[] = $codigo_cliente;
                }
            } else {
                //verifica se este cliente tem assinatura
                $assinatura = $this->ClienteProdutoServico2->find('first', array('joins' => $joinsCP, 'conditions' => array('ClienteProduto.codigo_cliente' => $codigo_cliente)));
                if (!empty($assinatura)) {
                    //tenta deletar o registro da assinatura
                    $del_cp = $this->deleteAssinatura($codigo_cliente);
                    //verifica se tem msg de erro
                    if (!empty($del_cp)) {
                        $msg .= $del_cp;
                    }
                } // fim assinatura
            } // fim else

            //verifica se tem unidades
            if (count($data['unidade']) > 1) {
                //retira do array este campo
                unset($data['unidade']['xx']);

                //varre as unidades
                foreach ($data['unidade'] as $value) {
                    //pega o codigo_cliente
                    $codigo_cliente = $value['codigo'];

                    if (empty($codigo_cliente)) {
                        continue;
                    }

                    //verifica se tem assinatura com o codigo cliente sendo o codigo_cliente_pagador
                    $cliente_pagador = $this->ClienteProdutoServico2->find('first', array('fields' => array('ClienteProduto.codigo_cliente AS codigo_cliente', 'ClienteProdutoServico2.codigo_cliente_pagador AS codigo_cliente_pagador'), 'joins' => $joinsCP, 'conditions' => array('ClienteProdutoServico2.codigo_cliente_pagador' => $codigo_cliente)));

                    //verifica se tem cliente pagador
                    if (!empty($cliente_pagador)) {

                        if ($cliente_pagador[0]['codigo_cliente'] != $cliente_pagador[0]['codigo_cliente_pagador']) {
                            $unidades[] = $codigo_cliente;
                        }
                    } else {
                        //pega a assinatura do cliente
                        $assinatura = $this->ClienteProdutoServico2->find('first', array('joins' => $joinsCP, 'conditions' => array('ClienteProduto.codigo_cliente' => $codigo_cliente)));
                        if (!empty($assinatura)) {
                            //tenta deletar o registro da assinatura
                            $del_cp = $this->deleteAssinatura($codigo_cliente);

                            //verifica se tem msg de erro
                            if (!empty($del_cp)) {
                                $msg .= $del_cp;
                            }
                        } // fim assinatura
                    } //fim else

                } //fim foreach

            } //fim count

        } //fim verificacao data

        //verificar as unidades
        if (!empty($unidades)) {
            $msg = "As seguintes unidades: " . implode(',', $unidades) . " precisa rever a assinatura, favor verificar!";
        }

        return $msg;
    } //fim verificaAssinatura($codigo_clliente)

    /**
     * [deleteAssinatura metodo para deletar a assinatura do cliente para dar continuidade ao processo]
     *
     * @param  [type] $codigo_cliente [description]
     * @return [type] mensagem do erro ou vazia
     */
    public function deleteAssinatura($codigo_cliente)
    {

        $erro = '';

        //pega a cliente produto pelo codigo do cliente
        $cp = $this->ClienteProduto->find('all', array('ClienteProduto.codigo_cliente' => $codigo_cliente));

        //varre os produtos e servicos
        foreach ($cp as $dados) {
            //codigo cp
            $codigo_cliente_produto = $dados['ClienteProduto']['codigo'];

            //deleta os registros da clienteprodutoservico2
            if (!$this->ClienteProdutoServico2->deleteAll(array('codigo_cliente_produto' => $codigo_cliente_produto))) {
                $erro .= "Erro ao excluir os servicos do produto: " . $dados['ClienteProduto']['codigo_produto'] . " ";
            }

            if (!$this->ClienteProduto->delete($codigo_cliente_produto)) {
                $erro .= "Erro ao excluir o produto: " . $dados['ClienteProduto']['codigo_produto'] . " ";
            }
        }

        return $erro;
    } // fim deleteAssinatura($codigo_cliente)


    /**
     * [gerarGrupoEconomico metodo para gerar um novo grupo economico]
     * @param  [type] $codigo_cliente [codigo do cliente que vai ser o grupo economico]
     * @return [type]                 [description]
     */
    private function gerarGrupoEconomico($codigo_cliente, $cliente_matriz_antigo)
    {

        //verifica se já não foi separado
        $ge = $this->GrupoEconomico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));
        if (!empty($ge)) {
            return $ge['GrupoEconomico']['codigo'];
        }

        //pega os dados do antigo grupo economico que ele estava, para pegar o dado de quantidade de asos que deve ser impresso
        $codigo_ge_antigo = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
        $ge_antigo = $this->GrupoEconomico->find('first', array('conditions' => array('codigo' => $codigo_ge_antigo['GrupoEconomicoCliente']['codigo_grupo_economico'])));

        //pega os dados do cliente, para pegar a razao social e colocar no nome do grupo economico
        $dados_cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));

        //configura os dados de geracao do grupo economico
        $dados_ge = $ge_antigo;
        //arruma os dados para geracao
        unset($dados_ge['GrupoEconomico']['codigo']);
        unset($dados_ge['GrupoEconomico']['codigo_usuario_inclusao']);
        unset($dados_ge['GrupoEconomico']['codigo_empresa']);
        unset($dados_ge['GrupoEconomico']['codigo_usuario_alteracao']);
        unset($dados_ge['GrupoEconomico']['data_inclusao']);
        unset($dados_ge['GrupoEconomico']['data_alteracao']);

        $dados_ge['GrupoEconomico']['codigo_cliente'] = $codigo_cliente;
        $dados_ge['GrupoEconomico']['descricao'] = $dados_cliente['Cliente']['razao_social'];

        //gera o grupo economico
        if ($this->GrupoEconomico->incluir($dados_ge)) {

            //pega os dados da cliente_implantacao do cliente matriz antigo
            $cliente_implantacao_antigo = $this->ClienteImplantacao->find('first', array('conditions' => array('codigo_cliente' => $cliente_matriz_antigo)));

            //verifica se tem algum registro
            if (!empty($cliente_implantacao_antigo)) {
                //pega os novos dados criados
                $cliente_implantacao = $this->ClienteImplantacao->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));

                //atualiza a tabela de cliente implantacao para o codigo do cliente, copiando a estrutura de implantacao da matriz anterior
                $dados_cliente_implantacao = array(
                    'ClienteImplantacao' => array(
                        'codigo' => $cliente_implantacao['ClienteImplantacao']['codigo'],
                        'estrutura' => $cliente_implantacao_antigo['ClienteImplantacao']['estrutura'],
                        'ppra' => $cliente_implantacao_antigo['ClienteImplantacao']['ppra'],
                        'pcmso' => $cliente_implantacao_antigo['ClienteImplantacao']['pcmso'],
                        'liberado' => $cliente_implantacao_antigo['ClienteImplantacao']['liberado']
                    )
                );

                //atualiza o novo grupo economico
                if (!$this->ClienteImplantacao->atualizar($dados_cliente_implantacao)) {
                    return false;
                }
            } //fim atualizacao cliente_implantacao para a nova estrutura

            //retorna o codigo do grupo economico
            return $this->GrupoEconomico->id;
        } //fim verificacao incluir grupo economico

        //retorna com erro
        return false;
    } //fim gerarGrupoEconomico

    /**
     * [gerarGrupoEconomicoCliente metodo para gerar um novo grupo economico cliente]
     * @param  [type] $codigo_cliente [codigo do cliente que vai ter o novo grupo economico cliente]
     * @return [type]                 [description]
     */
    private function gerarGrupoEconomicoCliente($codigo_grupo_economico, $codigo_cliente)
    {

        //verifica se já esta relacionado
        $gec = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_grupo_economico' => $codigo_grupo_economico, 'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
        if (!empty($gec)) {
            return $gec['GrupoEconomicoCliente']['codigo'];
        }

        //pega os dados do antigo grupo economico cliente que ele estava para relacionar o cliente ao novo grupo_economico cliente
        $dados_gec = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
        $dados_gec['GrupoEconomicoCliente']['codigo_grupo_economico'] = $codigo_grupo_economico;

        //gera o grupo economico
        if ($this->GrupoEconomicoCliente->atualizar($dados_gec)) {

            //retorna o codigo do grupo economico cliente
            return $dados_gec['GrupoEconomicoCliente']['codigo'];
        } //fim verificacao incluir grupo economico

        //retorna com erro
        return false;
    } //fim gerarGrupoEconomico

    /**
     * [gerarOrdemServico description]
     *
     * metodo para atualizar as ordens de servico
     *
     * @param  [type] $codigo_cliente         [description]
     * @param  [type] $codigo_cliente_novo_ge [description]
     * @param  [type] $codigo_grupo_economico [description]
     * @return [type]                         [description]
     */
    private function gerarOrdemServico($codigo_unidade, $codigo_grupo_economico)
    {

        //pega as ordens de servico
        $os = $this->OrdemServico->find('first', array('conditions' => array('OrdemServico.codigo_cliente' => $codigo_unidade)));
        //verifica se existe ordem de servico para este cliente
        if (!empty($os)) {

            //monta a query
            $query_atualizar_os = "
    			UPDATE RHHealth.dbo.ordem_servico SET codigo_grupo_economico = " . $codigo_grupo_economico . " WHERE codigo_cliente = " . $codigo_unidade . ";
    			UPDATE RHHealth.dbo.ordem_servico_versoes SET codigo_grupo_economico = " . $codigo_grupo_economico . " WHERE codigo_cliente = " . $codigo_unidade . ";";

            // debug($query_atualizar_os);

            //executa a query
            if (!$this->OrdemServico->query($query_atualizar_os)) {
                return false;
            } //fim if
        } //fim verificacoa se existe os

        return true;
    } //fim gerarOrdemServico($codigo_cliente,$codigo_cliente_novo_ge,$codigo_grupo_economico)

    /**
     * [gerarNovosSetores description]
     *
     * metodo para copiar os setores do antigo grupo economico para o novo
     *
     * @param  [type] $codigo_cliente         [codigo do cliente do grupo economico antigo]
     * @param  [type] $codigo_cliente_novo_ge [codigo do novo grupo economico]
     * @return [type]                         [description]
     */
    private function gerarNovosSetores($codigo_cliente_matriz_antigo, $codigo_cliente_novo_ge, $descricao = null)
    {

        //verifica se existe setores para o novo cliente
        $conditions['codigo_cliente'] = $codigo_cliente_novo_ge;
        if (!is_null($descricao)) {
            $conditions['descricao'] = $descricao;
        }
        $setor_novo = $this->Setor->find('first', array('conditions' => $conditions));
        if (!empty($setor_novo)) {
            return true;
        }

        //pega os setores do grupo economico antigo
        $setores = $this->Setor->find('all', array('conditions' => array('codigo_cliente' => $codigo_cliente_matriz_antigo)));

        //variavel axiliar
        $var_aux_erro = false;

        if (!empty($setor)) {
            //varre os setores criando novos para a nova estrutura
            foreach ($setores as $setor) {

                //copia o setor para o novo cliente
                //limpa os dados desnecessários
                unset($setor['Setor']['codigo']);
                unset($setor['Setor']['codigo_usuario_inclusao']);
                unset($setor['Setor']['codigo_usuario_alteracao']);
                unset($setor['Setor']['codigo_empresa']);
                unset($setor['Setor']['data_inclusao']);
                unset($setor['Setor']['data_alteracao']);

                $setor['Setor']['codigo_cliente'] = $codigo_cliente_novo_ge;

                //insere na tabela um novo setor
                if (!$this->Setor->incluir($setor)) {
                    $var_aux_erro = true;
                }
            } //fim setores
        } else {
            //verfica se e null
            if (!is_null($descricao)) {
                //seta os dados
                $setor = array(
                    'Setor' => array(
                        'descricao' => $descricao,
                        'codigo_cliente' => $codigo_cliente_novo_ge,
                        'ativo' => 1
                    )
                );

                //insere na tabela um novo setor
                if (!$this->Setor->incluir($setor)) {
                    $var_aux_erro = true;
                }
            } //fim verificacao is null descricao
        }

        //verfica se teve algum erro
        if ($var_aux_erro) {
            return false;
        }

        return true;
    } //fim gerarNovosSetores($codigo_cliente_matriz_antigo,$codigo_cliente_novo_ge)

    /**
     * [gerarNovosCargos description]
     *
     * metodo para copiar os cargos do antigo grupo economico para o novo
     *
     * @param  [type] $codigo_cliente_matriz_antigo         [description]
     * @param  [type] $codigo_cliente_novo_ge [description]
     * @return [type]                         [description]
     */
    private function gerarNovosCargos($codigo_cliente_matriz_antigo, $codigo_cliente_novo_ge, $descricao = null)
    {

        //verifica se existe cargos para o novo cliente
        $conditions['codigo_cliente'] = $codigo_cliente_novo_ge;
        if (!is_null($descricao)) {
            $conditions['descricao'] = $descricao;
        }
        $cargo_novo = $this->Cargo->find('first', array('conditions' => $conditions));
        if (!empty($cargo_novo)) {
            return true;
        }

        //pega os cargos do grupo economico antigo
        $cargos = $this->Cargo->find('all', array('conditions' => array('codigo_cliente' => $codigo_cliente_matriz_antigo)));
        //variavel axiliar
        $var_aux_erro = false;
        //verifica se existe os cargos
        if (!empty($cargos)) {
            //varre os setores criando novos para a nova estrutura
            foreach ($cargos as $cargo) {

                //copia o cargo para o novo cliente, limpa os dados desnecessários
                unset($cargo['Cargo']['codigo']);
                unset($cargo['Cargo']['codigo_usuario_inclusao']);
                unset($cargo['Cargo']['codigo_usuario_alteracao']);
                unset($cargo['Cargo']['codigo_empresa']);
                unset($cargo['Cargo']['data_inclusao']);
                unset($cargo['Cargo']['data_alteracao']);

                $cargo['Cargo']['codigo_cliente'] = $codigo_cliente_novo_ge;

                //desabilita a validação de descricao
                unset($this->Cargo->validate['descricao']);

                //insere na tabela um novo setor
                if (!$this->Cargo->incluir($cargo)) {
                    $var_aux_erro = true;
                }
            } //fim setores

        } else {
            //verfica se e null
            if (!is_null($descricao)) {
                //seta os dados
                $cargo = array(
                    'Cargo' => array(
                        'descricao' => $descricao,
                        'codigo_cliente' => $codigo_cliente_novo_ge,
                        'ativo' => 1
                    )
                );

                //insere na tabela um novo setor
                if (!$this->Cargo->incluir($cargo)) {
                    $var_aux_erro = true;
                }
            } //fim verificacao is null descricao
        } //fim cargos

        //verfica se teve algum erro
        if ($var_aux_erro) {
            return false;
        }

        return true;
    } //fim gerarNovosCargos($codigo_cliente_matriz_antigo,$codigo_cliente_novo_ge)

    /**
     * [gerarNovosCargos description]
     *
     * metodo para atualizar as hierarquias
     *
     * @param  [type] $codigo_cliente         [description]
     * @param  [type] $codigo_cliente_novo_ge [description]
     * @return [type]                         [description]
     */
    private function gerarHierarquias($codigo_cliente_unidade, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)
    {

        //verifica se existe hierarquias para o cliente
        $joins = array(
            array(
                'table' => 'setores',
                'alias' => 'Setor',
                'conditions' => 'Setor.codigo = ClienteSetorCargo.codigo_setor AND Setor.ativo = 1'
            ),
            array(
                'table' => 'cargos',
                'alias' => 'Cargo',
                'conditions' => 'Cargo.codigo = ClienteSetorCargo.codigo_cargo AND Cargo.ativo = 1'
            ),
        );
        //set os campos
        $fields = array('ClienteSetorCargo.*', 'Setor.codigo', 'Setor.descricao', 'Cargo.codigo', 'Cargo.descricao');

        $hierarquia_validacao = $this->ClienteSetorCargo->find(
            'all',
            array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => array(
                    'ClienteSetorCargo.codigo_cliente' => $codigo_cliente_unidade,
                    'Setor.codigo_cliente' => $codigo_cliente_novo_ge,
                    'Cargo.codigo_cliente' => $codigo_cliente_novo_ge,
                )
            )
        );

        //verifica se existe registro de hierarquias com os setores do cliente ja cadastrado
        if (!empty($hierarquia_validacao)) {
            return true;
        }

        //pega os dado de hierarquia para corrigir a configuração
        $hierarquia = $this->ClienteSetorCargo->find(
            'all',
            array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => array(
                    'ClienteSetorCargo.codigo_cliente' => $codigo_cliente_unidade,
                )
            )
        );
        // debug($hierarquia);exit;

        //variavel axiliar
        $var_aux_erro = false;
        //varre as hierarquias
        foreach ($hierarquia as $dados) {

            //busca o setor pelo nome e seta os valores
            if (!$novo_setor = $this->getNovoSetor($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                // $this->log('novo_setor','debug');
                // $this->log(print_r($novo_setor,1),'debug');
                $var_aux_erro = true;
            } //fim novo setor
            // debug($novo_setor);

            //busca o cargo pelo nome
            if (!$novo_cargo = $this->getNovoCargo($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                // $this->log('novo_cargo','debug');
                // $this->log(print_r($novo_cargo,1),'debug');
                $var_aux_erro = true;
            } //fim novo_cargo
            // debug($novo_cargo);

            //seta os dados
            $dados_hierarquia['ClienteSetorCargo'] = $dados['ClienteSetorCargo'];
            $dados_hierarquia['ClienteSetorCargo']['codigo_setor'] = $novo_setor['Setor']['codigo'];
            $dados_hierarquia['ClienteSetorCargo']['codigo_cargo'] = $novo_cargo['Cargo']['codigo'];

            //atualiza a hierarquia para os novos campos
            if (!$this->ClienteSetorCargo->atualizar($dados_hierarquia)) {
                $var_aux_erro = true;
            }
        } //fim setores

        //verfica se teve algum erro
        if ($var_aux_erro) {
            return false;
        }

        return true;
    } //fim gerarHierarquias($codigo_cliente)

    /**
     * [getNovoSetor
     * metodo para buscar ou cadastrar um novo setor]
     *
     * @param  [type] $dados                  [array com os dados que contenha os dados do setor, principalmente a descricao array('Setor' => array('descricao' => 'descricao do setor') ]
     * @param  [type] $codigo_cliente_novo_ge [codigo do cliente do novo grupo economico ou seja a codigo do cliente matriz]
     * @param  [type] $codigo_cliente         [codigo do cliente antigo matriz]
     * @return [type]                         [false caso haja erro ou os dados do setor encontrado/cadastrado]
     */
    public function getNovoSetor($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)
    {

        //busca o setor pelo nome
        $novo_setor = $this->Setor->find('first', array('conditions' => array("descricao" => $dados['Setor']['descricao'], 'codigo_cliente' => $codigo_cliente_novo_ge, 'ativo' => 1)));
        //verifica se achou o setor
        if (empty($novo_setor)) {
            //copia os setores
            if (!$this->gerarNovosSetores($codigo_cliente_matriz_antigo, $codigo_cliente_novo_ge, $dados['Setor']['descricao'])) {
                $this->log("entro erro setor", 'debug');
                $this->log(print_r(array($codigo_cliente, $codigo_cliente_novo_ge, $dados['Setor']['descricao']), 1), 'debug');
                return false;
            }

            //busca o setor pelo nome
            $novo_setor = $this->Setor->find('first', array('conditions' => array("descricao" => $dados['Setor']['descricao'], 'codigo_cliente' => $codigo_cliente_novo_ge, 'ativo' => 1)));
        }

        return $novo_setor;
    } // fim getNovoSetor

    /**
     * [getNovoCargo
     * metodo para buscar ou cadastrar um novo cargo]
     *
     * @param  [type] $dados                  [array com os dados que contenha os dados do cargo, principalmente a descricao array('cargo' => array('descricao' => 'descricao do cargo') ]
     * @param  [type] $codigo_cliente_novo_ge [codigo do cliente do novo grupo economico ou seja a codigo do cliente matriz]
     * @param  [type] $codigo_cliente         [codigo do cliente antigo matriz]
     * @return [type]                         [false caso haja erro ou os dados do cargo encontrado/cadastrado]
     */
    public function getNovoCargo($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)
    {
        $novo_cargo = $this->Cargo->find('first', array('conditions' => array("descricao" => $dados['Cargo']['descricao'], 'codigo_cliente' => $codigo_cliente_novo_ge, 'ativo' => 1)));
        //verifica se achou o cargo
        if (empty($novo_cargo)) {
            //copia os cargos
            if (!$this->gerarNovosCargos($codigo_cliente_matriz_antigo, $codigo_cliente_novo_ge, $dados['Cargo']['descricao'])) {
                $this->log("entro erro cargo", 'debug');
                $this->log(print_r(array($codigo_cliente, $codigo_cliente_novo_ge, $dados['Cargo']['descricao']), 1), 'debug');
                return false;
            }

            //busca o cargo pelo nome
            $novo_cargo = $this->Cargo->find('first', array('conditions' => array("descricao" => $dados['Cargo']['descricao'], 'codigo_cliente' => $codigo_cliente_novo_ge, 'ativo' => 1)));
        }

        return $novo_cargo;
    } //fim getNovoCargo


    /**
     * [gerarMatriculas description]
     *
     * metodo para atualizar as matriculas
     *
     * @param  [type] $codigo_cliente         [description]
     * @param  [type] $codigo_cliente_novo_ge [description]
     * @return [type]                         [description]
     */
    private function gerarMatriculas($codigo_cliente, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)
    {

        //verifica se existe matriculas para o cliente
        $funcao_validacao = $this->FuncionarioSetorCargo->find('all', array('conditions' => array('FuncionarioSetorCargo.codigo_cliente_alocacao' => $codigo_cliente, 'ClienteFuncionario.codigo_cliente_matricula' => $codigo_cliente_novo_ge)));

        //verifica se tem dados caso exista ja foi feita a separacao
        if (!empty($funcao_validacao)) {
            return true;
        }

        //pega os dados que devem ser transformado
        $funcao = $this->FuncionarioSetorCargo->find('all', array('conditions' => array('FuncionarioSetorCargo.codigo_cliente_alocacao' => $codigo_cliente, 'Setor.ativo' => 1, 'Cargo.ativo' => 1)));
        // debug($funcao);exit;

        //variavel axiliar
        $var_aux_erro = false;
        //varre as hierarquias
        foreach ($funcao as $dados) {

            //gera uma nova matricula, verificando se já não está no grupo economico correto
            $matricula_ge_novo = $this->ClienteFuncionario->find('first', array('conditions' => array('ClienteFuncionario.codigo' => $dados['ClienteFuncionario']['codigo'], 'ClienteFuncionario.codigo_cliente_matricula' => $codigo_cliente_novo_ge)));

            //seta a variavel de controle
            $codigo_cliente_funcionario = '';
            //verifica se existe o registro
            if (empty($matricula_ge_novo)) {

                //seta os dados da matricula
                $dados_Matricula['ClienteFuncionario'] = $dados['ClienteFuncionario'];
                $dados_Matricula['ClienteFuncionario']['codigo_cliente'] = $codigo_cliente_novo_ge;
                $dados_Matricula['ClienteFuncionario']['codigo_cliente_matricula'] = $codigo_cliente_novo_ge;

                //retira o codigo para inseririr um novo codigo matricula
                unset($dados_Matricula['ClienteFuncionario']['codigo']);

                //desabilita a validação da cliente funcionario
                unset($this->ClienteFuncionario->validate);

                //atualiza a matricula
                if (!$this->ClienteFuncionario->incluir($dados_Matricula)) {
                    // $this->log('erro matricula 1','debug');
                    // $this->log(print_r($this->ClienteFuncionario->validationErrors,1),'debug');
                    $var_aux_erro = true;
                } else {
                    //seta o codigo_cliente_funcionario / codigo da matricula
                    $codigo_cliente_funcionario = $this->ClienteFuncionario->id;

                    ////////////////////////////////////////////
                    //inativa a matricula de onde foi copiada //
                    ////////////////////////////////////////////
                    $dados_matricula_antiga['ClienteFuncionario'] = $dados['ClienteFuncionario'];
                    $dados_matricula_antiga['ClienteFuncionario']['data_demissao'] = (!empty($dados_matricula_antiga['ClienteFuncionario']['data_demissao'])) ? $dados_matricula_antiga['ClienteFuncionario']['data_demissao'] : date('Y-m-d');
                    $dados_matricula_antiga['ClienteFuncionario']['ativo'] = 0;

                    //atualiza a matricula como inativa
                    if (!$this->ClienteFuncionario->atualizar($dados_matricula_antiga)) {
                        // $this->log('erro matricula 2','debug');
                        // $this->log(print_r($this->ClienteFuncionario->validationErrors,1),'debug');
                        $var_aux_erro = true;
                    }
                    ////////////////////////////////
                    //fim inativavao da matricula //
                    ////////////////////////////////

                } //fim inclusao matricula
            } else {
                //seta o codigo_cliente_funcionario / codigo da matricula
                $codigo_cliente_funcionario = $dados['ClienteFuncionario']['codigo'];
            } //fim matricula

            //busca o setor pelo nome e seta os valores
            if (!$novo_setor = $this->getNovoSetor($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                // $this->log('matricula - Nao encontrou setor','debug');
                // $this->log($this->Setor->find('sql', array('conditions' => array('descricao' => $dados['Setor']['descricao'], 'codigo_cliente' => $codigo_cliente_novo_ge, 'ativo' => 1))),'debug');
                $var_aux_erro = true;
            } //fim novo setor
            // debug($novo_setor);

            //busca o cargo pelo nome
            if (!$novo_cargo = $this->getNovoCargo($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                // $this->log('matricula - Nao encontrou cargo','debug');
                // $this->log($this->Cargo->find('sql', array('conditions' => array('descricao' => $dados['Cargo']['descricao'], 'codigo_cliente' => $codigo_cliente_novo_ge, 'ativo' => 1))),'debug');
                $var_aux_erro = true;
            } //fim novo_cargo
            // debug($novo_cargo);

            if (!empty($novo_setor) && !empty($novo_cargo)) {
                //seta os dados funcionario setores cargos
                $dados_FSC['FuncionarioSetorCargo'] = $dados['FuncionarioSetorCargo'];
                $dados_FSC['FuncionarioSetorCargo']['codigo_setor'] = $novo_setor['Setor']['codigo'];
                $dados_FSC['FuncionarioSetorCargo']['codigo_cargo'] = $novo_cargo['Cargo']['codigo'];
                $dados_FSC['FuncionarioSetorCargo']['codigo_cliente_funcionario'] = $codigo_cliente_funcionario;


                //desabilita a validação da funcionario setores cargos
                unset($this->FuncionarioSetorCargo->validate);

                //atualiza a hierarquia para os novos campos
                if (!$this->FuncionarioSetorCargo->atualizar($dados_FSC)) {
                    // $this->log('matricula funcionario setores cargos','debug');
                    // $this->log(print_r($this->FuncionarioSetorCargo->validationErrors,1),'debug');
                    $var_aux_erro = true;
                } else {

                    // atualiza o pedido de exame
                    $this->PedidoExame->atualizarDadosPedido($codigo_cliente, $codigo_cliente_funcionario, $dados['FuncionarioSetorCargo']['codigo']);
                } //fim atualizacao fsc

            } // fim verificacao setor e cargo


        } //fim setores

        //verfica se teve algum erro
        if ($var_aux_erro) {
            return false;
        }

        return true;
    } //fim gerarMatriculas($codigo_cliente)

    /**
     * [gerarPPRA description]
     *
     * metodo para ajustar os pgrs da separação do grupo economico
     *
     * @param  [type] $codigo_cliente         [description]
     * @param  [type] $codigo_cliente_novo_ge [description]
     * @return [type]                         [description]
     */
    private function gerarPPRA($codigo_cliente, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)
    {

        //pega os dados que devem ser transformado
        $this->ClienteSetor->bindModel(
            array(
                'hasMany' => array(
                    'GrupoExposicao' => array(
                        'foreignKey' => 'codigo_cliente_setor',
                    ),
                ),
                'belongsTo' => array(
                    'Setor' => array(
                        'className' => 'Setor',
                        'foreignKey' => false,
                        'type' => 'INNER',
                        'conditions' => array('ClienteSetor.codigo_setor = Setor.codigo AND Setor.ativo = 1'),
                    ),
                ),
            )
        );
        $ppras = $this->ClienteSetor->find('all', array('conditions' => array('ClienteSetor.codigo_cliente_alocacao' => $codigo_cliente)));
        // debug($ppras);	exit;

        //variavel axiliar
        $var_aux_erro = false;
        if (!empty($ppras)) {
            //varre as hierarquias
            foreach ($ppras as $dados) {

                /////////////////////
                //CLIENTES SETORES //
                /////////////////////
                //busca o setor pelo nome e seta os valores
                if (!$novo_setor = $this->getNovoSetor($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                    // $this->log('novo_setor ppra','debug');
                    $var_aux_erro = true;
                } //fim novo setor

                if (!empty($novo_setor)) {
                    //seta os dados do cliente setores
                    $dados_cs['ClienteSetor'] = $dados['ClienteSetor'];
                    $dados_cs['ClienteSetor']['codigo_setor'] = $novo_setor['Setor']['codigo'];

                    //desabilita a validação da funcionario setores cargos
                    unset($this->ClienteSetor->validate);

                    //atualiza o setor do cliente setor
                    if (!$this->ClienteSetor->atualizar($dados_cs)) {
                        // $this->log('cliente setor ppra','debug');
                        $var_aux_erro = true;
                    }

                    ////////////////////
                    //GRUPO EXPOSICAO //
                    ////////////////////
                    foreach ($dados['GrupoExposicao'] as $gr) {

                        //pega a descricao do cargo
                        $cargo = $this->Cargo->find('first', array('conditions' => array('Cargo.codigo' => $gr['codigo_cargo'], 'Cargo.ativo' => 1)));
                        //pois pode haver ppra com cargo inativo
                        if (empty($cargo)) {
                            continue;
                        }

                        //busca o cargo pelo nome
                        if (!$novo_cargo = $this->getNovoCargo($cargo, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                            // $this->log('novo_cargo ppra','debug');
                            // $this->log(print_r(array($cargo, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo),1),'debug');
                            $var_aux_erro = true;
                        } //fim novo cargo

                        if (!empty($novo_cargo)) {
                            //seta os dados do grupo de exposicao
                            $dados_ge = 'UPDATE RHHealth.dbo.grupo_exposicao SET codigo_cargo = ' . $novo_cargo['Cargo']['codigo'] . ' WHERE codigo = ' . $gr['codigo'];

                            //atualiza a hierarquia para os novos campos
                            if (!$this->GrupoExposicao->query($dados_ge)) {
                                $this->log('grupo exposicao ppra', 'debug');
                                $var_aux_erro = true;
                            }
                        }
                    } //fim foreach grupo exposicao


                } //fim verificacao setor

            } //fim ppras
        } //fim ppras


        //retira o belongs da class
        // unset($this->PrevencaoRiscoAmbiental->belongsTo);
        //relacionamento com o setor
        $this->PrevencaoRiscoAmbiental->bindModel(
            array(
                'belongsTo' => array(
                    'Setor' => array(
                        'className' => 'Setor',
                        'foreignKey' => false,
                        'type' => 'INNER',
                        'conditions' => array('PrevencaoRiscoAmbiental.codigo_setor = Setor.codigo AND Setor.ativo = 1'),
                    ),
                )
            )
        );
        //pega os grupos de prevencao de riscos ambientais (plano de acao)
        // $gpra = $this->PrevencaoRiscoAmbiental->find('all', array('conditions' => array('Gpra.codigo_cliente' => 10011)));
        $gpra = $this->PrevencaoRiscoAmbiental->find('all', array('conditions' => array('Gpra.codigo_cliente' => $codigo_cliente_novo_ge)));
        // debug($gpra);exit;

        //verifica se existe valor para atualziar
        if (!empty($gpra)) {

            //varre os planos de acao
            foreach ($gpra as $dados) {
                //busca o setor pelo nome
                if (!$novo_setor_pra = $this->getNovoSetor($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                    // $this->log("novo setor pra ppra ", 'debug');
                    $var_aux_erro = true;
                } //fim novo setor

                if (!empty($novo_setor_pra)) {

                    //seta os dados do cliente setores
                    $dados_pra['PrevencaoRiscoAmbiental'] = $dados['PrevencaoRiscoAmbiental'];
                    $dados_pra['PrevencaoRiscoAmbiental']['codigo_setor'] = $novo_setor_pra['Setor']['codigo'];

                    //desabilita a validação da funcionario setores cargos
                    unset($this->PrevencaoRiscoAmbiental->validate);

                    //atualiza o setor do cliente setor
                    if (!$this->PrevencaoRiscoAmbiental->atualizar($dados_pra)) {
                        // $this->log("prevencao de risco ambiental ppra ", 'debug');
                        $var_aux_erro = true;
                    }
                }
            } //fim foreach


        } //fim empty gpra


        //verfica se teve algum erro
        if ($var_aux_erro) {
            return false;
        }

        return true;
    } //fim gerarPPRA

    /**
     * [gerarPPRAVersoes description]
     *
     * metodo para ajustar as versões do ppra para a separação do grupo economico
     *
     * @param  [type] $codigo_cliente         [description]
     * @param  [type] $codigo_cliente_novo_ge [description]
     * @return [type]                         [description]
     */
    private function gerarPPRAVersoes($codigo_cliente, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)
    {

        //pega os dados que devem ser transformado
        $this->ClienteSetorVersoes->bindModel(
            array(
                'belongsTo' => array(
                    'Setor' => array(
                        'className' => 'Setor',
                        'foreignKey' => false,
                        'type' => 'INNER',
                        'conditions' => array('ClienteSetorVersoes.codigo_setor = Setor.codigo AND Setor.ativo = 1'),
                    ),
                    'GrupoExposicaoVersoes' => array(
                        'className' => 'GrupoExposicaoVersoes',
                        'foreignKey' => false,
                        'type' => 'INNER',
                        'conditions' => array('ClienteSetorVersoes.codigo_clientes_setores = GrupoExposicaoVersoes.codigo_cliente_setor'),
                    ),
                    'Cargo' => array(
                        'className' => 'Cargo',
                        'foreignKey' => false,
                        'type' => 'INNER',
                        'conditions' => array('GrupoExposicaoVersoes.codigo_cargo = Cargo.codigo AND Cargo.ativo = 1'),
                    ),
                ),
            )
        );
        $ppras_versoes = $this->ClienteSetorVersoes->find('all', array('conditions' => array('ClienteSetorVersoes.codigo_cliente_alocacao' => $codigo_cliente)));
        // debug($ppras_versoes);	exit;

        //variavel axiliar
        $var_aux_erro = false;
        if (!empty($ppras_versoes)) {
            //varre os ppras versoes
            foreach ($ppras_versoes as $dados) {

                /////////////////////
                //CLIENTES SETORES //
                /////////////////////
                //busca o setor pelo nome
                if (!$novo_setor = $this->getNovoSetor($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                    $var_aux_erro = true;
                } //fim novo setor

                if (!empty($novo_setor)) {

                    //seta os dados do cliente setores
                    $dados_cs['ClienteSetorVersoes'] = $dados['ClienteSetorVersoes'];
                    $dados_cs['ClienteSetorVersoes']['codigo_setor'] = $novo_setor['Setor']['codigo'];

                    //desabilita a validação da funcionario setores cargos
                    unset($this->ClienteSetorVersoes->validate);

                    //atualiza o setor do cliente setor
                    if (!$this->ClienteSetorVersoes->atualizar($dados_cs)) {
                        $var_aux_erro = true;
                    }

                    ////////////////////
                    //GRUPO EXPOSICAO //
                    ////////////////////

                    //busca o cargo pelo nome
                    if (!$novo_cargo = $this->getNovoCargo($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                        $var_aux_erro = true;
                    } //fim novo cargo

                    if (!empty($novo_cargo)) {

                        //seta os dados do grupo de exposicao
                        $dados_ge = 'UPDATE RHHealth.dbo.grupo_exposicao_versoes SET codigo_cargo = ' . $novo_cargo['Cargo']['codigo'] . ' WHERE codigo = ' . $dados['GrupoExposicaoVersoes']['codigo'];

                        //atualiza a hierarquia para os novos campos
                        if (!$this->GrupoExposicao->query($dados_ge)) {
                            $var_aux_erro = true;
                        }
                    }
                }
            } //fim ppra versoes
        } //fim empty

        //verfica se teve algum erro
        if ($var_aux_erro) {
            return false;
        }

        return true;
    } //fim gerarPPRAVersoes


    /**
     * [gerarPCMSO description]
     *
     * metodo para ajustar os ppras da separação do grupo economico
     *
     * @param  [type] $codigo_cliente         [description]
     * @param  [type] $codigo_cliente_novo_ge [description]
     * @return [type]                         [description]
     */
    private function gerarPCMSO($codigo_cliente, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)
    {

        //pega os dados que devem ser transformado
        $this->AplicacaoExame->bindModel(
            array(
                'belongsTo' => array(
                    'Cargo' => array(
                        'className' => 'Cargo',
                        'foreignKey' => false,
                        'type' => 'INNER',
                        'conditions' => array('AplicacaoExame.codigo_cargo = Cargo.codigo AND Cargo.ativo = 1'),
                    ),
                    'Setor' => array(
                        'className' => 'Setor',
                        'foreignKey' => false,
                        'type' => 'INNER',
                        'conditions' => array('AplicacaoExame.codigo_setor = Setor.codigo AND Setor.ativo = 1'),
                    ),
                ),
            )
        );

        //pega os pcmsos
        $pcmsos = $this->AplicacaoExame->find('all', array('conditions' => array('AplicacaoExame.codigo_cliente_alocacao' => $codigo_cliente)));
        // debug($pcmsos);exit;

        //variavel axiliar
        $var_aux_erro = false;
        $reprocessamento = array();
        if (!empty($pcmsos)) {

            //varre as hierarquias
            foreach ($pcmsos as $dados) {

                ////////////////////////
                //aplicacao de exames //
                ////////////////////////
                //busca o setor pelo nome
                if (!$novo_setor = $this->getNovoSetor($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                    $var_aux_erro = true;
                } //fim novo setor

                //busca o cargo pelo nome
                if (!$novo_cargo = $this->getNovoCargo($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                    $var_aux_erro = true;
                } //fim novo cargo

                if (!empty($novo_setor) && !empty($novo_cargo)) {

                    //query
                    $query_atualizar_ae = "UPDATE RHHealth.dbo.aplicacao_exames SET codigo_setor = " . $novo_setor['Setor']['codigo'] . ', codigo_cargo = ' . $novo_cargo['Cargo']['codigo'] . ' WHERE codigo = ' . $dados['AplicacaoExame']['codigo'] . ';';

                    //atualiza o setor do cliente setor
                    if (!$this->AplicacaoExame->query($query_atualizar_ae)) {
                        $this->log($query_atualizar_ae, 'debug');
                        $var_aux_erro = true;
                    }
                }
            } //fim pcmsos foreach

        } //fim pcmso

        //pega os cronogramas de acoes
        $cronograma = $this->CronogramaAcao->find('all', array('conditions' => array('codigo_cliente_unidade' => $codigo_cliente)));
        // debug($cronograma); exit;

        //verifica se existe valor para atualziar
        if (!empty($cronograma)) {

            //varre os planos de acao
            foreach ($cronograma as $dados) {

                //busca o setor pelo nome
                if (!$novo_setor = $this->getNovoSetor($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                    $var_aux_erro = true;
                } //fim novo setor

                if (!empty($novo_setor)) {
                    //seta os dados do cronograma de acao
                    $dados_ca['CronogramaAcao'] = $dados['CronogramaAcao'];
                    $dados_ca['CronogramaAcao']['codigo_setor'] = $novo_setor['Setor']['codigo'];
                    $dados_ca['CronogramaAcao']['codigo_cliente_matriz'] = $codigo_cliente_novo_ge;

                    //desabilita a validação da funcionario setores cargos
                    unset($this->CronogramaAcao->validate);

                    //atualiza o setor do cliente setor
                    if (!$this->CronogramaAcao->atualizar($dados_ca)) {
                        $var_aux_erro = true;
                    }
                }
            } //fim foreach
        } //fim empty cronograma

        //pega os materiais de pronto socorro
        $materiais = $this->Pmps->find('all', array('conditions' => array('codigo_cliente_unidade' => $codigo_cliente)));

        //verifica se existe valor para atualziar
        if (!empty($materiais)) {

            //varre os planos de acao
            foreach ($materiais as $dados) {

                //seta os dados do cronograma de acao
                $dados_pmsps['Pmps'] = $dados['Pmps'];
                $dados_pmsps['Pmps']['codigo_cliente_matriz'] = $codigo_cliente_novo_ge;

                //desabilita a validação da funcionario setores cargos
                unset($this->Pmps->validate);

                //atualiza o setor do cliente setor
                if (!$this->Pmps->atualizar($dados_pmsps)) {
                    $var_aux_erro = true;
                }
            } //fim foreach
        } //fim empty Pmps

        //pega os hospitais de emergencia
        $hospitais = $this->HospitaisEmergencia->find('all', array('conditions' => array('codigo_cliente_unidade' => $codigo_cliente)));

        //verifica se existe valor para atualziar
        if (!empty($hospitais)) {

            //varre os planos de acao
            foreach ($hospitais as $dados) {

                //seta os dados do cronograma de acao
                $dados_hospitais['HospitaisEmergencia'] = $dados['HospitaisEmergencia'];
                $dados_hospitais['HospitaisEmergencia']['codigo_cliente_matriz'] = $codigo_cliente_novo_ge;

                //desabilita a validação da funcionario setores cargos
                unset($this->HospitaisEmergencia->validate);

                //atualiza o setor do cliente setor
                if (!$this->HospitaisEmergencia->atualizar($dados_hospitais)) {
                    $var_aux_erro = true;
                }
            } //fim foreach
        } //fim empty hospitais emergencia

        //verfica se teve algum erro
        if ($var_aux_erro) {
            return false;
        }

        return true;
    } //fim gerarPCMSO

    /**
     * [gerarPCMSOVersoes description]
     *
     * metodo para ajustar os pcmso verdoes da separação do grupo economico
     *
     * @param  [type] $codigo_cliente         [description]
     * @param  [type] $codigo_cliente_novo_ge [description]
     * @return [type]                         [description]
     */
    private function gerarPCMSOVersoes($codigo_cliente, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)
    {

        //pega os dados que devem ser transformado
        $this->AplicacaoExameVersoes->bindModel(
            array(
                'belongsTo' => array(
                    'Cargo' => array(
                        'className' => 'Cargo',
                        'foreignKey' => false,
                        'type' => 'INNER',
                        'conditions' => array('AplicacaoExameVersoes.codigo_cargo = Cargo.codigo AND Cargo.ativo = 1'),
                    ),
                    'Setor' => array(
                        'className' => 'Setor',
                        'foreignKey' => false,
                        'type' => 'INNER',
                        'conditions' => array('AplicacaoExameVersoes.codigo_setor = Setor.codigo AND Setor.ativo = 1'),
                    ),
                ),
            )
        );

        //pega os pcmsos
        $pcmsos = $this->AplicacaoExameVersoes->find('all', array('conditions' => array('AplicacaoExameVersoes.codigo_cliente_alocacao' => $codigo_cliente)));
        // debug($pcmsos);exit;

        //variavel axiliar
        $var_aux_erro = false;
        if (!empty($pcmsos)) {

            //varre as hierarquias
            foreach ($pcmsos as $dados) {

                ////////////////////////
                //aplicacao de exames //
                ////////////////////////
                //busca o setor pelo nome
                if (!$novo_setor = $this->getNovoSetor($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                    $var_aux_erro = true;
                } //fim novo setor

                //busca o cargo pelo nome
                if (!$novo_cargo = $this->getNovoCargo($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                    $var_aux_erro = true;
                } //fim novo cargo

                if (!empty($novo_cargo) && !empty($novo_setor)) {

                    //seta os dados do cliente setores
                    $dados_ae['AplicacaoExameVersoes'] = $dados['AplicacaoExameVersoes'];
                    $dados_ae['AplicacaoExameVersoes']['codigo_setor'] = $novo_setor['Setor']['codigo'];
                    $dados_ae['AplicacaoExameVersoes']['codigo_cargo'] = $novo_cargo['Cargo']['codigo'];

                    //desabilita a validação da funcionario setores cargos
                    unset($this->AplicacaoExameVersoes->validate);

                    //atualiza o setor do cliente setor
                    if (!$this->AplicacaoExameVersoes->atualizar($dados_ae)) {
                        $var_aux_erro = true;
                    }
                }
            } //fim pcmsos
        } //fim pcmso

        $this->CronogramaAcaoVersao->bindModel(
            array(
                'belongsTo' => array(
                    'Setor' => array(
                        'className' => 'Setor',
                        'foreignKey' => false,
                        'type' => 'INNER',
                        'conditions' => array('CronogramaAcaoVersao.codigo_setor = Setor.codigo AND Setor.ativo = 1'),
                    ),
                ),
            )
        );
        //pega os cronogramas de acoes
        $cronograma = $this->CronogramaAcaoVersao->find('all', array('conditions' => array('codigo_cliente_unidade' => $codigo_cliente)));
        // debug($cronograma); exit;

        //verifica se existe valor para atualziar
        if (!empty($cronograma)) {

            //varre os planos de acao
            foreach ($cronograma as $dados) {

                //busca o setor pelo nome
                if (!$novo_setor = $this->getNovoSetor($dados, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo)) {
                    $var_aux_erro = true;
                } //fim novo setor

                if (!empty($novo_setor)) {
                    //seta os dados do cronograma de acao
                    $dados_ca['CronogramaAcaoVersao'] = $dados['CronogramaAcaoVersao'];
                    $dados_ca['CronogramaAcaoVersao']['codigo_setor'] = $novo_setor['Setor']['codigo'];
                    $dados_ca['CronogramaAcaoVersao']['codigo_cliente_matriz'] = $codigo_cliente_novo_ge;

                    //desabilita a validação da funcionario setores cargos
                    unset($this->CronogramaAcaoVersao->validate);

                    //atualiza o setor do cliente setor
                    if (!$this->CronogramaAcaoVersao->atualizar($dados_ca)) {
                        $var_aux_erro = true;
                    }
                }
            } //fim foreach
        } //fim empty cronograma

        //verfica se teve algum erro
        if ($var_aux_erro) {
            return false;
        }

        return true;
    } //fim gerarPCMSOVersoes

    /**
     * [gravaSeparacao metodo para gravar os dados da separacao]
     * @param  [type] $dados [description]
     * @return [type]        [description]
     */
    public function gravaSeparacao($codigo_cliente_anterior, $dados)
    {

        //pega o grupo economico anterior
        $ge_anterior = $this->GrupoEconomico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente_anterior)));

        //seta o codigo _cliente novo
        $codigo_cliente_matriz_novo = $dados['GrupoEconomico']['novo_codigo_unidade_grupo_economico'];

        //retira o xx
        unset($dados['unidade']['xx']);

        //unidades
        $unidades = $dados['unidade'];

        //varre as unidades
        foreach ($unidades as $uni) {

            $dados_busca_insercao = array(
                'codigo_cliente_ge_anterior' => $codigo_cliente_anterior,
                'codigo_ge_anterior' => $ge_anterior['GrupoEconomico']['codigo'],
                'codigo_cliente_ge_novo' => $codigo_cliente_matriz_novo,
                'codigo_cliente_unidade' => $uni['codigo']
            );

            $dados_separacao = $this->SeparacaoGe->find('first', array('conditions' => $dados_busca_insercao));

            if (empty($dados_separacao)) {
                //separacao
                $separacao_ge = array(
                    'SeparacaoGe' => $dados_busca_insercao
                );

                $this->SeparacaoGe->incluir($separacao_ge);
            }
        } //fim foreach

    } //fim gravaSeparacao($this->data)

    /**
     * [novas_unidades_separacao metodo para levar unidades quando precisar ajustar]
     *
     * @return [type] [description]
     */
    public function novas_unidades_separacao()
    {

        $codigo_cliente_novo_ge = 230;

        $unidades = array(
            // array('codigo' => 10120),
            //  		array('codigo' => 59999),
            //  		array('codigo' => 60001),
            // array('codigo' => 2396),
            // array('codigo' => 70273),
            // array('codigo' => 81161),
            // array('codigo' => 82816),
        );

        $grupo_economico = $this->GrupoEconomico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente_novo_ge)));
        $codigo_grupo_economico = $grupo_economico['GrupoEconomico']['codigo'];

        $separacao_ge = $this->SeparacaoGe->find('first', array('conditions' => array('codigo_cliente_ge_novo' => $codigo_cliente_novo_ge)));
        $codigo_cliente_matriz_antigo = $separacao_ge['SeparacaoGe']['codigo_cliente_ge_anterior'];

        // debug(array($codigo_grupo_economico,$codigo_cliente_novo_ge,$codigo_cliente_matriz_antigo,$unidades));
        // exit;

        //inicia um try/catch
        try {
            //seta que vamos trabalhar com transacao
            $this->GrupoEconomico->query('begin transaction');

            //metodo para trocar a unidade
            $this->levar_unidades($codigo_grupo_economico, $codigo_cliente_novo_ge, $codigo_cliente_matriz_antigo, $unidades);

            $this->GrupoEconomico->commit();
        } catch (Exception $e) {

            //seta o log
            $this->log(print_r($e->getMessage(), 1), 'debug');

            $msg_erro = $e->getMessage();

            //houve um erro
            $this->BSession->setFlash(array(MSGT_ERROR, $msg_erro));

            //desfaz o que foi feito no banco de dados
            $this->GrupoEconomico->rollback();

            debug($e->getMessage());
            exit;
        }

        debug($unidades);
        exit;
    } //fim novas undiades separacao

}//FINAL CLASS GruposEconomicosController
