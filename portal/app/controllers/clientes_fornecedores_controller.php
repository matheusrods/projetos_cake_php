<?php
class ClientesFornecedoresController extends AppController {
    public $name = 'ClientesFornecedores';
    var $uses = array(
        'ClienteFornecedor',
        'Fornecedor',
        'FornecedorEndereco',
        'Endereco',
        'EnderecoCidade',
        'EnderecoEstado',
        'GrupoEconomicoCliente',
        'GrupoEconomico',
        'Setor',
        );
      
    function listagem($codigo_cliente) {
       $this->layout = 'ajax';

        $conditions = array(
            'ClienteFornecedor.codigo_cliente' => $codigo_cliente,
            'ClienteFornecedor.ativo' => 1
            );

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
            '(SELECT count(*) serv_at FROM (
            SELECT LPPS.codigo_servico 
                         FROM listas_de_preco_produto_servico LPPS
                            INNER JOIN listas_de_preco_produto LPP 
                                ON(LPP.codigo = LPPS.codigo_lista_de_preco_produto)
                            INNER JOIN listas_de_preco LP
                                ON(LP.codigo = LPP.codigo_lista_de_preco)
                            INNER JOIN clientes_fornecedores CF
                                ON(CF.codigo_fornecedor = LP.codigo_fornecedor)
                            INNER JOIN cliente_produto_servico2 CPS
                                ON(CPS.codigo_servico = LPPS.codigo_servico)
                            INNER JOIN cliente_produto CP
                                ON(CP.codigo = CPS.codigo_cliente_produto)
                        WHERE LPPS.codigo_servico IN (
                        SELECT CPS3.codigo_servico FROM cliente_produto_servico2 CPS3
                            INNER JOIN cliente_produto CP3
                                ON(CP3.codigo = CPS3.codigo_cliente_produto)
                        WHERE CP3.codigo_cliente = CP.codigo_cliente
                        ) AND CP.codigo_cliente = ClienteFornecedor.codigo_cliente  AND CF.codigo_fornecedor = ClienteFornecedor.codigo_fornecedor  GROUP BY LPPS.codigo_servico) AS serv_at ) as serv_at',
            '(SELECT COUNT(CPS2.codigo_servico) AS total_at FROM cliente_produto_servico2 CPS2
                INNER JOIN cliente_produto CP2
                    ON(CP2.codigo = CPS2.codigo_cliente_produto)
            WHERE CP2.codigo_cliente = ClienteFornecedor.codigo_cliente) AS total_at'                
          );

        $joins  = array(
            array(
              'table' => $this->Fornecedor->databaseTable.'.'.$this->Fornecedor->tableSchema.'.'.$this->Fornecedor->useTable,
              'alias' => 'Fornecedor',
              'type' => 'LEFT',
              'conditions' => 'ClienteFornecedor.codigo_fornecedor = Fornecedor.codigo',
            ),
            array(
              'table' => $this->FornecedorEndereco->databaseTable.'.'.$this->FornecedorEndereco->tableSchema.'.'.$this->FornecedorEndereco->useTable,
              'alias' => 'FornecedorEndereco',
              'type' => 'LEFT',
              'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
            )
        );  
        
        $order = array('Fornecedor.codigo DESC','Fornecedor.razao_social ASC');

        $this->paginate['ClienteFornecedor'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => $order,
        );
        // $query_teste = $this->ClienteFornecedor->find('sql', $this->paginate['ClienteFornecedor']);
        // debug($query_teste);
        $fornecedores = $this->paginate('ClienteFornecedor');
        
        $this->set(compact('fornecedores', 'codigo_cliente'));
    }

    function buscar_cliente_fornecedor($codigo_cliente){
        $this->layout = 'ajax_placeholder';

        $this->data['ClienteFornecedor'] = $this->Filtros->controla_sessao($this->data, $this->ClienteFornecedor->name);
                       
        $this->set(compact('codigo_cliente'));
    }

     function buscar_listagem_cliente_fornecedor($codigo_cliente){
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteFornecedor->name);
        $conditions = $this->ClienteFornecedor->converteFiltroEmCondition($filtros);
        $param = array(
            'Fornecedor.ativo' => 1, //ATIVO
            'FornecedorEndereco.codigo_tipo_contato' => 2, //ENDERECO COMERCIAL
            'Fornecedor.codigo NOT IN ( SELECT codigo_fornecedor 
                                        FROM '.$this->ClienteFornecedor->databaseTable.'.'.$this->ClienteFornecedor->tableSchema.'.'.$this->ClienteFornecedor->useTable.'
                                        WHERE codigo_cliente = '.$codigo_cliente.' and ativo = 1)'
            );
        $conditions = array_merge($conditions, $param);
        $joins  = array(
            array(
              'table' => $this->FornecedorEndereco->databaseTable.'.'.$this->FornecedorEndereco->tableSchema.'.'.$this->FornecedorEndereco->useTable,
              'alias' => 'FornecedorEndereco',
              'type' => 'LEFT',
              'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
            )
        );

        $fields = array(
            'Fornecedor.codigo', 'Fornecedor.razao_social', 'Fornecedor.nome', 'Fornecedor.codigo_documento', 'Fornecedor.ativo',
            'FornecedorEndereco.codigo', 'FornecedorEndereco.codigo_fornecedor', 'FornecedorEndereco.codigo_tipo_contato',
            'FornecedorEndereco.estado_descricao', 'FornecedorEndereco.cidade',
            '(SELECT count(*) serv_at FROM (
            SELECT LPPS.codigo_servico 
                         FROM listas_de_preco_produto_servico LPPS
                            INNER JOIN listas_de_preco_produto LPP 
                                ON(LPP.codigo = LPPS.codigo_lista_de_preco_produto)
                            INNER JOIN listas_de_preco LP
                                ON(LP.codigo = LPP.codigo_lista_de_preco)
                            INNER JOIN clientes_fornecedores CF
                                ON(CF.codigo_fornecedor = LP.codigo_fornecedor)
                            INNER JOIN cliente_produto_servico2 CPS
                                ON(CPS.codigo_servico = LPPS.codigo_servico)
                            INNER JOIN cliente_produto CP
                                ON(CP.codigo = CPS.codigo_cliente_produto)
                        WHERE LPPS.codigo_servico IN (
                        SELECT CPS3.codigo_servico FROM cliente_produto_servico2 CPS3
                            INNER JOIN cliente_produto CP3
                                ON(CP3.codigo = CPS3.codigo_cliente_produto)
                        WHERE CP3.codigo_cliente = '.$codigo_cliente.'
                        ) AND CP.codigo_cliente = '.$codigo_cliente.'  AND CF.codigo_fornecedor = Fornecedor.codigo GROUP BY LPPS.codigo_servico) AS serv_at ) as serv_at',
            '(SELECT COUNT(CPS2.codigo_servico) AS total_at FROM cliente_produto_servico2 CPS2
                INNER JOIN cliente_produto CP2
                    ON(CP2.codigo = CPS2.codigo_cliente_produto)
            WHERE CP2.codigo_cliente = '.$codigo_cliente.') AS total_at' 
            );
          
          $order = array('Fornecedor.razao_social');

        $this->paginate['Fornecedor'] = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields,
            'order' => $order,
            'limit' => 10,
            'recursive' => -1
        );


        $dados_fornecedores = $this->paginate('Fornecedor');     
        $this->set(compact('dados_fornecedores', 'codigo_cliente'));
    }

    function incluir() {
        
        if($this->RequestHandler->isPost()) {

            $codigo_cliente = $_POST['codigo_cliente'];
            $codigo_fornecedor = $_POST['codigo_fornecedor'];

            $consulta = $this->ClienteFornecedor->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_fornecedor' => $codigo_fornecedor, 'ativo' => 1)));
            if(empty($consulta)){
                $dados = array(
                    'ClienteFornecedor' => array(
                        'codigo_cliente' => $codigo_cliente,
                        'codigo_fornecedor' => $codigo_fornecedor,
                        'ativo' => 1
                        )
                    );

                if ($this->ClienteFornecedor->incluir($dados)) {
                    $this->BSession->setFlash('save_success');
                    echo 1;
                } 
                else {
                    $this->BSession->setFlash('save_error');
                    echo 0;
                }
            }
            else{
                echo 2;
            }
        }
        exit;
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['ClienteFornecedor']['codigo'] = $codigo;
        $this->data['ClienteFornecedor']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->ClienteFornecedor->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }

    function index(){
        $this->pageTitle = 'Consultar Mapeamento de Rede';
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteFornecedor->name);
        
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if(empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];

        $this->data['ClienteFornecedor'] = $filtros;

        $this->loadCombo('ClienteFornecedor');
    }

    function listagem_cliente_por_fornecedor($export = null){

        $filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteFornecedor->name);

        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if(empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $dados = array();
        if(!empty($filtros['codigo_fornecedor']) || !empty($filtros['codigo_cliente'])){
            $conditions = $this->ClienteFornecedor->converteFiltroEmConditions($filtros);
            $this->paginate['GrupoEconomicoCliente'] = $this->ClienteFornecedor->getClienteFornecedores($conditions, true);
            // pr($this->GrupoEconomicoCliente->find('sql', $this->paginate['GrupoEconomicoCliente']));        
            $dados = $this->paginate('GrupoEconomicoCliente');            
        }
        
        if($export == 'export'){
        
            $query = $this->ClienteFornecedor->getClienteFornecedoresExport($conditions);
           // debug($query);exit;
            $this->export($query);
        } 

        $this->set(compact('dados'));
    }

    private function loadCombo($model) {
        $unidades = array();
        $setores = array();
        $cargos = array();

        $codigo_cliente = (isset($this->data[$model]['codigo_cliente'])) ? $this->data[$model]['codigo_cliente'] : array();

        if(!empty($codigo_cliente)){
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
            $setores = $this->Setor->lista($codigo_cliente);            
        }
        
        $this->set(compact('unidades', 'setores'));
    }

    public function export($query)
    {
        //para aumentar o tempo para nao estourar a memoria, solucao feita para solucionar o problema apresentado no chamado CDCT-165
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300); // 5min
        
        //instancia o dbo
        $dbo = $this->ClienteFornecedor->getDataSource();
        
        //pega todos os resultados
        $dbo->results = $dbo->rawQuery($query);
        // debug($query);exit;
        //headers
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="mapeamento_de_redes.csv"');
        header('Pragma: no-cache');
        //cabecalho do arquivo
        echo utf8_decode('"Código Unidade";"Razão Social Unidade";"Nome Fantasia Unidade";"Código Prestador";"Razão Social Prestador";"Nome Fantasia Prestador";"UF Prestador";"Cidade Prestador";"Bairro Prestador";"Data Inclusão";"Status (Ativo/Inativo)";')."\n";
        
        // varre todos os registros da consulta no banco de dados
        while($dados = $dbo->fetchRow()) 
        {              
           
            $linha  = $dados['Cliente']['codigo'].';';
            $linha .= $dados['Cliente']['razao_social'].';';
            $linha .= $dados['Cliente']['nome_fantasia'].';';

            $linha .= $dados['Fornecedor']['codigo'].';';
            $linha .= $dados['Fornecedor']['razao_social'].';';
            $linha .= $dados['Fornecedor']['nome'].';';
            
            $linha .= empty($dados['FornecedorEndereco']['estado_abreviacao']) ? $dados['FornecedorEndereco']['estado_descricao'] .';' : $dados['FornecedorEndereco']['estado_abreviacao'] .';';
            $linha .= $dados['FornecedorEndereco']['cidade'].';';
            $linha .= $dados['FornecedorEndereco']['bairro'].';';           

            $DataAtual = new DateTime();
            $DataEspecifica = new DateTime($dados['ClienteFornecedor']['data_inclusao']);
            $data = $DataEspecifica->format('d-m-Y');

            $linha .= $data.';';

            $status = "";
            if ($dados['Fornecedor']['ativo'] == 1) {
                $status = 'Ativo';
            } else {
                $status = 'Inativo';
            }

            $linha .= $status.';';    

            //joga no arquivo os dados
            echo utf8_decode($linha)."\n";
        }//fim while
        
        //mata o metodo
        die();
    }//fim export_listagem2
}