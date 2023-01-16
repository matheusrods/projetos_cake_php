<?php
class ObjetivosComerciaisController extends AppController {
    public $name = 'ObjetivosComerciais';
    public $uses = array(
        'ObjetivoComercial',
        'ObjetivoComercialCliente',
        'ObjetivoComercialExcecao',
        'Gestor',
        'EnderecoRegiao',
        'Produto',
        'Diretoria',
    );
    public $helpers = array('BForm', 'Buonny', 'Ajax','Highcharts');

    function index() {
        $this->carregaCombos();
        $this->data['ObjetivoComercial'] = $this->Filtros->controla_sessao($this->data, "ObjetivoComercial");
    } 
    
    function listagem(){
        $this->data['ObjetivoComercial'] = $this->Filtros->controla_sessao($this->data, "ObjetivoComercial");
        $this->ObjetivoComercial->bindObjetivoComercial();
        $fields = array(
            'ObjetivoComercial.codigo AS codigo_id',
            'ObjetivoComercial.mes AS mes',
            'ObjetivoComercial.ano AS ano',
            'ObjetivoComercial.visitas_objetivo AS visitas_objetivo',
            'ObjetivoComercial.faturamento_objetivo AS faturamento_objetivo',
            'ObjetivoComercial.novos_clientes_objetivo AS novos_clientes_objetivo',
            'EnderecoRegiao.descricao AS filial_descricao',
            'Usuario.nome AS nome_gestor',
            'Produto.descricao AS produto_descricao',
        );
        $conditions = $this->ObjetivoComercial->converteFiltrosEmConditions($this->data['ObjetivoComercial']);
     
        $order = 'filial_descricao ASC';   
        $this->paginate['ObjetivoComercial'] = array(
            'limit' => 50,
            'fields' => $fields,
            'conditions' => $conditions,
            'order' => $order
        );
        $listagem = $this->paginate('ObjetivoComercial');
        $this->set(compact('listagem'));
    }

    function carregaCombos(){       
        $meses = Comum::listMeses();       
        $anos = Comum::listAnos(2014);
        array_push($anos, date('Y', strtotime('+1 year')));
        $gestores = $this->Gestor->listarNomesGestoresAtivos();
        $filiais = $this->EnderecoRegiao->listarRegioes();
        $produtos = $this->Produto->listarProdutosNavegarqCodigoBuonny();
        unset($produtos[30]);        
        $listaAgrupamento = $this->ObjetivoComercial->listarAgrupamentos();
        $listaTipoVisualizacao = $this->ObjetivoComercial->listarTipoVisualizacao();
        $diretoria = $this->Diretoria->find('list');
        $this->set(compact('diretoria','meses', 'anos','gestores','filiais','produtos','listaAgrupamento','listaTipoVisualizacao'));
    }


    function incluir(){
        $this->pageTitle = 'Incluir Objetivo Comercial';
        $this->carregaCombos();

        if (!empty($this->data)) {
            $this->data['ObjetivoComercial']['visitas_realizado'] = 0;
            $this->data['ObjetivoComercial']['faturamento_realizado'] = 0;
            $this->data['ObjetivoComercial']['cliente_novo'] = 0;
         
            if ($this->ObjetivoComercial->incluir($this->data)) {              
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
         }
    }

    function editar($codigo){
        $this->pageTitle = 'Atualizar Objetivo Comercial';
        $this->carregaCombos();
        if($this->RequestHandler->isPost()) {
            $this->data['ObjetivoComercial']['codigo'] = $codigo;
            if ($this->ObjetivoComercial->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }    
        $this->data = $this->ObjetivoComercial->carregar($codigo);
        $this->data['ObjetivoComercial']['faturamento_objetivo']     = number_format ( $this->data['ObjetivoComercial']['faturamento_objetivo'] , 2 , ',' , '' );
    }

    function excluir($codigo){
        if($codigo){
            if($this->ObjetivoComercial->excluir($codigo)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }

    function analitico($new_window = FALSE){
        $this->pageTitle = 'Objetivos Comerciais Analítico';
        if($new_window){
            $this->layout = 'new_window';
            $filtrado = TRUE; 
        }
        $this->carregaCombos();
        $this->data['ObjetivoComercial'] = $this->Filtros->controla_sessao($this->data, "ObjetivoComercial");
        $this->set(compact('filtrado'));
    }

    function listagem_analitico(){
        $this->loadModel('ObjetivoComercialCliente');
        $this->data['ObjetivoComercial'] = $this->Filtros->controla_sessao($this->data, "ObjetivoComercial");
        $this->ObjetivoComercialCliente->bindObjetivoComercialCliente();
        $fields = array(
            'ObjetivoComercialCliente.excecao as excecao',
            'ObjetivoComercialCliente.excecao_faturamento_medio as excecao_faturamento_medio',
            'ObjetivoComercialCliente.codigo AS codigo_id',
            'ObjetivoComercialCliente.mes AS mes',
            'ObjetivoComercialCliente.ano AS ano',
            'ObjetivoComercialCliente.cliente_novo AS cliente_novo',
            'ObjetivoComercialCliente.faturamento_realizado AS faturamento',
            'ObjetivoComercialCliente.visitas AS visitas',
            'EnderecoRegiao.descricao AS filial_descricao',
            'Usuario.nome AS nome_gestor',
            'Usuario.codigo AS codigo_gestor',
            'Cliente.codigo AS codigo_cliente',
            'Produto.descricao AS produto_descricao',
            'Produto.codigo AS produto_codigo',
            'Cliente.razao_social AS razao_social',
        );
        $conditions = $this->ObjetivoComercialCliente->converteFiltrosEmConditions($this->data['ObjetivoComercial']);
        $order = 'razao_social ASC'; 
        $this->paginate['ObjetivoComercialCliente'] = array(
            'limit' => 50,
            'conditions' => $conditions,
            'fields' => $fields,
            'order' => $order
        );
        $listagem = $this->paginate('ObjetivoComercialCliente');

        $this->set(compact('listagem'));
    }

    function sintetico(){
        $this->pageTitle = 'Objetivos Comerciais Sintético';
        $this->carregaCombos(); 
        if(empty($this->data['ObjetivoComercial']['mes'])){
            $this->data['ObjetivoComercial']['mes'] = date('m');
        }
        if(empty($this->data['ObjetivoComercial']['ano'])){
            $this->data['ObjetivoComercial']['ano'] = date('Y');
        }      
        $this->data['ObjetivoComercial'] = $this->Filtros->controla_sessao($this->data, "ObjetivoComercial");
    }

    function listagem_sintetico(){
        $this->data['ObjetivoComercial'] = $this->Filtros->controla_sessao($this->data, "ObjetivoComercial");

        $tipoVisualizacao = $this->data['ObjetivoComercial']['tipoVisualizacao'];  
        $agrupamento_atual = $this->data['ObjetivoComercial']['agrupamento'];
             
        if($agrupamento_atual == 2){
            $produto_buonny = 30;
        }else{
            $produto_buonny = FALSE;
        } 

        $conditions = $this->ObjetivoComercial->converteFiltrosEmConditions($this->data['ObjetivoComercial']);
        $listagem = $this->ObjetivoComercial->sintetico($conditions,$agrupamento_atual,$produto_buonny);
        $agrupamento = $this->ObjetivoComercialCliente->tipo_agrupamento($agrupamento_atual);
        $agrupamentoDescricao = $agrupamento['agrupamentoDescricao'];

        if(!empty($listagem)){
            $this->sintetico_grafico($listagem,$agrupamento,$agrupamentoDescricao,$tipoVisualizacao);
        }
        
        $this->set(compact('listagem','agrupamentoDescricao','listaAgrupamento','agrupamento','agrupamento_atual','tipoVisualizacao'));
    }

    function sintetico_grafico($atendimentos,$agrupamento,$agrupamentoDescricao,$tipoVisualizacao){
        $descricaoVisualizacao = $this->ObjetivoComercial->TipoVisualizacaoDescricao($tipoVisualizacao);

        foreach ($atendimentos as $atendimento) {
            if($tipoVisualizacao == ObjetivoComercial::VISITAS){
                $qtd_realizado[] = $atendimento[0]['visitas_realizadas'];
                $qtd_objetivo[] = $atendimento[0]['visitas_objetivo'];
                $descricao[] = "'".$atendimento[0]['descricao']."'";
            }elseif($tipoVisualizacao == ObjetivoComercial::NOVOS_CLIENTES){
                $qtd_realizado[] = $atendimento[0]['cliente_novo'];
                $qtd_objetivo[] = $atendimento[0]['novos_clientes_objetivo'];
                $descricao[] = "'".$atendimento[0]['descricao']."'";
            }elseif($tipoVisualizacao == ObjetivoComercial::FATURAMENTO){
                $qtd_realizado[] = $atendimento[0]['faturamento_realizado'];
                $qtd_objetivo[] = $atendimento[0]['faturamento_objetivo'];
                $descricao[] = "'".$atendimento[0]['descricao']."'";
            }     
        } 

        $descricao_agrupamento = $agrupamentoDescricao;
        $dadosGrafico['eixo_x'] = $descricao;
        
        $dadosGrafico['series'] =  array(
            array(
                'name' => "'Objetivo'",
                'values' => $qtd_objetivo
            ),
            array(
                'name' => "'Realizado'",
                'values' => $qtd_realizado
            ),
        );       
        $this->set(compact('dadosGrafico','descricaoVisualizacao'));

    }

    function excecoes_cliente(){
        $this->pageTitle = 'Exceções de Objetivos Comerciais';
        $this->carregaCombos();
        $this->data['ObjetivoComercialExcecao']['filtrado'] = TRUE;
        $this->data['ObjetivoComercialExcecao'] = $this->Filtros->controla_sessao($this->data, "ObjetivoComercialExcecao");
    }

    function excecoes_cliente_listagem(){
        $this->data['ObjetivoComercialExcecao'] = $this->Filtros->controla_sessao($this->data, "ObjetivoComercialExcecao");
        $this->ObjetivoComercialExcecao->bindModel(array(
            'hasOne' => array(
                'Cliente' => array(
                    'foreignKey' => false,
                    'conditions' => array('codigo_cliente = Cliente.codigo')
                ),
                'Produto' => array(
                    'foreignKey' => false,
                    'conditions' => array('codigo_produto = Produto.codigo')
                ),
            )
        ),FALSE);       

      
        $conditions = $this->ObjetivoComercialExcecao->converteFiltrosEmConditions($this->data['ObjetivoComercialExcecao']);
        $this->paginate['ObjetivoComercialExcecao'] = array(
             'limit' => 50,
             'fields' => array(
                'codigo_cliente',
                'codigo_produto',
                'codigo_pai',
                'Cliente.razao_social',
                'Produto.descricao',
            ),
            'conditions' => $conditions,
            'order' => array('Cliente.razao_social','Produto.descricao'),
            'group' => array('codigo_cliente','codigo_produto','codigo_pai','razao_social','descricao')
        );
        $listagem = $this->paginate('ObjetivoComercialExcecao');
        $this->set(compact('listagem'));
    }

    function incluir_excecao(){
        $this->pageTitle = 'Incluir Exceção de Objetivo Comercial';
        $this->carregaCombos();

        if (!empty($this->data)) { 
             if(!isset($this->data['ObjetivoComercialExcecao']['percentagem_gestor1'])){
                $this->data['ObjetivoComercialExcecao']['percentagem_gestor1'] = 0;
            }
            if(!isset($this->data['ObjetivoComercialExcecao']['percentagem_gestor2'])){
                $this->data['ObjetivoComercialExcecao']['percentagem_gestor2'] = 0;
            }
            for ($i=1; $i <= 2; $i++) {  
                $this->data['ObjetivoComercialExcecao']['codigo_gestor'] = $this->data['ObjetivoComercialExcecao']["codigo_gestor$i"];
                $this->data['ObjetivoComercialExcecao']['percentagem_gestor'] = $this->data['ObjetivoComercialExcecao']["percentagem_gestor$i"];
                $this->data['ObjetivoComercialExcecao']['codigo_pai'] = $this->data['ObjetivoComercialExcecao']['codigo_cliente'].$this->data['ObjetivoComercialExcecao']['codigo_produto'];
                $dados[] = $this->data;
            }
            
            try {
                $this->ObjetivoComercialExcecao->query('BEGIN TRANSACTION');

                    if($dados[0]['ObjetivoComercialExcecao']['gestor_produto'] == 1){
                        $dados[1]['ObjetivoComercialExcecao']['gestor_produto'] = 0;
                    }else{
                        $dados[0]['ObjetivoComercialExcecao']['gestor_produto'] = 0;
                        $dados[1]['ObjetivoComercialExcecao']['gestor_produto'] = 1;
                    }
                    if(empty($dados[0]['ObjetivoComercialExcecao']['codigo_gestor'])){
                        $this->ObjetivoComercialExcecao->invalidate('codigo_gestor1', 'Informe o Gestor');
                        return false;
                    }
                    if(($dados[0]['ObjetivoComercialExcecao']['percentagem_gestor'] >  0) || ($dados[1]['ObjetivoComercialExcecao']['percentagem_gestor'] >  0)){
                        if(empty($dados[1]['ObjetivoComercialExcecao']['codigo_gestor'])){
                            $this->ObjetivoComercialExcecao->invalidate('codigo_gestor2', 'Informe o Gestor');
                            return false;
                        }
                    }
                    foreach ($dados as $dado) {
                        if(!empty($dado['ObjetivoComercialExcecao']['codigo_gestor'])){
                            if (!$this->ObjetivoComercialExcecao->incluir($dado)) { 
                                throw new Exception("Erro ao incluir");
                            } 
                        }     
                    }
                    
                $this->ObjetivoComercialExcecao->commit();    
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'excecoes_cliente'));
            } catch(Exception $e) {             
                $this->ObjetivoComercialExcecao->rollback();
                $this->BSession->setFlash('save_error');
            }
            
        }
         
    }

    function editar_excecao($codigo_pai){
        $this->pageTitle = 'Atualizar Exceções';
        $this->carregaCombos();
              
        if($this->RequestHandler->isPost()) {
            if(!isset($this->data['ObjetivoComercialExcecao']['percentagem_gestor1'])){
                $this->data['ObjetivoComercialExcecao']['percentagem_gestor1'] = 0;
            }
            if(!isset($this->data['ObjetivoComercialExcecao']['percentagem_gestor2'])){
                $this->data['ObjetivoComercialExcecao']['percentagem_gestor2'] = 0;
            }

     
            for ($i=0; $i < 2; $i++) {  
                $key = $i+1;
                $this->data['ObjetivoComercialExcecao']['codigo'] = $codigo_pai;
                $codigos = $this->ObjetivoComercialExcecao->retorna_excecoes_por_codigo_pai($this->data['ObjetivoComercialExcecao']['codigo'],FALSE);
                $this->data['ObjetivoComercialExcecao']['codigo_gestor'] = $this->data['ObjetivoComercialExcecao']["codigo_gestor$key"];
                $this->data['ObjetivoComercialExcecao']['percentagem_gestor'] = $this->data['ObjetivoComercialExcecao']["percentagem_gestor$key"];
                $this->data['ObjetivoComercialExcecao']['codigo_produto'] = $codigos[0]['ObjetivoComercialExcecao']["codigo_produto"];
                $this->data['ObjetivoComercialExcecao']['codigo'] = isset($codigos[$i]['ObjetivoComercialExcecao']['codigo']) ? $codigos[$i]['ObjetivoComercialExcecao']['codigo'] : NULL;
                $this->data['ObjetivoComercialExcecao']['codigo_pai'] = $codigo_pai;
                $dados[] = $this->data;
            }
             

            try {
                $this->ObjetivoComercialExcecao->query('BEGIN TRANSACTION');
                if($dados[0]['ObjetivoComercialExcecao']['gestor_produto'] == 1){
                    $dados[1]['ObjetivoComercialExcecao']['gestor_produto'] = 0;
                }else{
                    $dados[0]['ObjetivoComercialExcecao']['gestor_produto'] = 0;
                    $dados[1]['ObjetivoComercialExcecao']['gestor_produto'] = 1;
                }


                if(empty($dados[0]['ObjetivoComercialExcecao']['codigo_gestor'])){
                    $this->ObjetivoComercialExcecao->invalidate('codigo_gestor1', 'Informe o Gestor');
                    return false;
                }
                if(($dados[0]['ObjetivoComercialExcecao']['percentagem_gestor'] >  0) || ($dados[1]['ObjetivoComercialExcecao']['percentagem_gestor'] >  0)){
                    if(empty($dados[1]['ObjetivoComercialExcecao']['codigo_gestor'])){
                        $this->ObjetivoComercialExcecao->invalidate('codigo_gestor2', 'Informe o Gestor');
                        return false;
                    }
                }
                foreach ($dados as $dado) {
                    if(!empty($dado['ObjetivoComercialExcecao']['codigo_gestor'])){
                        if(!empty($dado['ObjetivoComercialExcecao']['codigo'])){
                            if (!$this->ObjetivoComercialExcecao->atualizar($dado,FALSE)) { 
                                throw new Exception("Erro ao atualizar");
                            }
                        }else{
                            unset($dado['ObjetivoComercialExcecao']['codigo']);
                            if (!$this->ObjetivoComercialExcecao->incluir($dado,FALSE)) { 
                                throw new Exception("Erro ao incluir");
                            } 
                        }    
                    }else{
                        if(!empty($dado['ObjetivoComercialExcecao']['codigo'])){
                            if (!$this->ObjetivoComercialExcecao->excluir($dado['ObjetivoComercialExcecao']['codigo'])) { 
                                throw new Exception("Erro ao Excluir");
                            }
                        }
                    }
                }
                $this->ObjetivoComercialExcecao->commit();    
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'excecoes_cliente'));
            } catch(Exception $e) {
                $this->ObjetivoComercialExcecao->rollback();
                $this->BSession->setFlash('save_error');
            }
        }    
   
        $this->data = $this->ObjetivoComercialExcecao->verifica_gestores_por_codigo_pai($codigo_pai);
       
        if($this->data['ObjetivoComercialExcecao']['gestor_produto1'] == 1){
            $this->data['ObjetivoComercialExcecao']['gestor_produto'] = 1;
        }else{
            $this->data['ObjetivoComercialExcecao']['gestor_produto'] = 2;
        }

  
    }  

    function excluir_excecao($codigo_pai){
        $codigos = $this->ObjetivoComercialExcecao->retorna_excecoes_por_codigo_pai($codigo_pai);
        foreach ($codigos as $codigo) {
            if($codigo){
                if(!$this->ObjetivoComercialExcecao->excluir($codigo['ObjetivoComercialExcecao']['codigo'])){
                    $this->BSession->setFlash('delete_error');
                }                
            }
        }
        $this->BSession->setFlash('save_success');
        $this->redirect(array('action' => 'excecoes_cliente'));
    } 

}
?>    


