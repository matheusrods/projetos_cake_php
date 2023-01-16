<?php
class GruposHomogeneosController extends AppController {
    public $name = 'GruposHomogeneos';
    var $uses = array('GrupoHomogeneo',
        'GrupoHomDetalhe',
        'GrupoEconomicoCliente',
        'GrupoHomogeneoExterno',
        'Cliente',
        'GrupoEconomico',
        'Cargo',
        'Setor'
        );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('index', 'listagem','index_externo', 'listagem_externo', 'editar_externo', 'incluir', '
        editar', 'atualiza_status', 'retornaGrupoHomogeneo', 'retornaDetalhesGrupoHomogeneo', 'editar');
    }
        
    function retorna_dados_cliente($codigo_cliente){
        
        $this->data = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);
        
        $this->set(compact('codigo_cliente'));
    }
    
    function retorna_dados($codigo_cliente){
        $this->retorna_dados_cliente($codigo_cliente);
        
        $cargo = $this->Cargo->lista_por_cliente($codigo_cliente);      
        $setor = $this->Setor->lista_por_cliente($codigo_cliente);
        

        // $query_cargo = "SELECT c.codigo as codigo, c.descricao as descricao 
        //                 FROM RHHealth.dbo.cargos c 
        //                     INNER JOIN RHHealth.dbo.grupos_economicos ge ON c.codigo_cliente = gec.codigo_cliente
        //                     INNER JOIN RHHealth.dbo.grupos_economicos_clientes gec ON ge.codigo = gec.codigo_grupo_economico
        //                 WHERE gec.codigo_cliente = " . $codigo_cliente ."
        //                 ORDER BY c.descrica ASC";


        $this->set(compact('codigo_cliente','cargo','setor'));
    }

    function index($codigo_cliente, $referencia) {
        $this->pageTitle = 'Grupos Homogêneos de Exposição';
        $this->retorna_dados($codigo_cliente);
        $this->set(compact('referencia'));
    }

    function listagem($codigo_cliente, $referencia) {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->GrupoHomogeneo->name);
        
        $conditions = $this->GrupoHomogeneo->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions, array('GrupoHomogeneo.codigo_cliente' => $codigo_cliente));

        $fields = array(
            'GrupoHomogeneo.codigo', 'GrupoHomogeneo.descricao', 'GrupoHomogeneo.ativo'
            );

        $order = 'GrupoHomogeneo.descricao';

        $this->paginate['GrupoHomogeneo'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'order' => $order,
                'limit' => 50
        );
       
        $grupos_homogeneos = $this->paginate('GrupoHomogeneo');

        $matriz = $this->GrupoEconomicoCliente->find('first', array('conditions'=> array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));

        $this->set(compact('grupos_homogeneos', 'codigo_cliente','referencia','matriz'));
    }
    
    function incluir($codigo_cliente, $referencia) {
        $this->pageTitle = 'Incluir Grupos Homogêneos de Exposição';

        if($this->RequestHandler->isPost()) {
            try{  
                $this->GrupoHomogeneo->query('begin transaction');

                if ($this->GrupoHomogeneo->incluir($this->data)) {
                    if(isset($this->data['GrupoHomDetalhe']['x']))
                        unset($this->data['GrupoHomDetalhe']['x']);

                    if(isset($this->data['GrupoHomDetalhe'])){

                        foreach ($this->data['GrupoHomDetalhe'] as $key => $dados) {
                            $dados['codigo_grupo_homogeneo'] = $this->GrupoHomogeneo->id;

                            if (!$this->GrupoHomDetalhe->incluir($dados)) {
                                throw new Exception();
                            }
                        }
                    }
                    

                } else{
                    throw new Exception();
                }


                $this->BSession->setFlash('save_success');
                $this->GrupoHomogeneo->commit();
                $this->redirect(array('action' => 'index', 'controller' => 'grupos_homogeneos', $codigo_cliente, $referencia));
            } 
            catch (Exception $ex) {
                $this->BSession->setFlash('save_error');
                $this->GrupoHomogeneo->rollback();
            }

        }

        $this->retorna_dados($codigo_cliente);
        $this->set(compact('referencia'));
    }
    
    function editar($codigo_cliente, $codigo, $referencia) {
        $this->pageTitle = 'Editar Grupos Homogêneos de Exposição'; 
        
         if($this->RequestHandler->isPost()) {           
            try{  
                $this->GrupoHomogeneo->query('begin transaction');
                    if ($this->GrupoHomogeneo->atualizar($this->data)) {

                        if(isset($this->data['GrupoHomDetalhe']['x']))
                            unset($this->data['GrupoHomDetalhe']['x']);

                        if(isset($this->data['GrupoHomDetalhe'])){
                            if($this->GrupoHomDetalhe->deleteAll(array('GrupoHomDetalhe.codigo_grupo_homogeneo' => $this->data['GrupoHomogeneo']['codigo']), false)){
                                
                                foreach ($this->data['GrupoHomDetalhe'] as $key => $dados) {
                                    unset($dados['codigo']);

                                    $dados['codigo_grupo_homogeneo'] = $this->GrupoHomogeneo->id;

                                    if (!$this->GrupoHomDetalhe->incluir($dados)) {
                                        throw new Exception();
                                    }
                                }
                            } 
                            else{
                                throw new Exception();
                            }
                        }
                    }
                    else{
                    throw new Exception();
                    }

                $this->BSession->setFlash('save_success');
                $this->GrupoHomogeneo->commit();

                $this->redirect(array('action' => 'index', 'controller' => 'grupos_homogeneos', $codigo_cliente, $referencia));
            } 
            catch (Exception $ex) {
                $this->BSession->setFlash('save_error');
                $this->GrupoHomogeneo->rollback();
            }
        } 

        if (isset($this->passedArgs[0])) {                 
            
            $this->retorna_dados($codigo_cliente);
            
            $dados = $this->GrupoHomogeneo->find('first', array('conditions' => array('codigo' => $codigo)));
            $this->data = array_merge($this->data, $dados);

            // $dados_detalhes = $this->GrupoHomDetalhe->find('all', array('conditions' => array('codigo_grupo_homogeneo' => $dados['GrupoHomogeneo']['codigo']), 'limit' => 10));

            // pr($dados_detalhes);exit;
            $query = '  SELECT ghed.codigo as codigo, Setor.codigo as codigo_setor, setor.descricao as descricao_setor, cargo.codigo as codigo_cargo, cargo.descricao as descricao_cargo
                        FROM RHHealth.dbo.grupos_homogeneos_exposicao_detalhes ghed
                            INNER JOIN RHHealth.dbo.setores setor ON ghed.codigo_setor = setor.codigo
                            INNER JOIN RHHealth.dbo.cargos cargo ON ghed.codigo_cargo = cargo.codigo
                        WHERE codigo_grupo_homogeneo = ' . $dados['GrupoHomogeneo']['codigo'];
            $dados_detalhes = $this->GrupoHomDetalhe->query($query);

            // pr($dados_detalhes);exit;

            foreach ($dados_detalhes as $key => $detalhes) {
                $this->data['GrupoHomDetalhe'][$key] = $detalhes[0];
            }
        }
        $this->set(compact('codigo_cliente','dados_detalhes', 'codigo','referencia'));
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['GrupoHomogeneo']['codigo'] = $codigo;
        $this->data['GrupoHomogeneo']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->GrupoHomogeneo->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }

    function retornaGrupoHomogeneo(){
        $this->layout = 'ajax';
        $this->render(false, false);

        $codigo = $_POST['codigo'];

        $grupo_homogeneo = $this->GrupoHomogeneo->find('first', array('conditions' => array('GrupoHomogeneo.codigo' => $codigo)));

        if(!empty($grupo_homogeneo)){

            $conditions = array('GrupoHomDetalhe.codigo_grupo_homogeneo' => $codigo);

            $fields = array('GrupoHomDetalhe.codigo', 'GrupoHomDetalhe.codigo_grupo_homogeneo', 'GrupoHomDetalhe.codigo_setor', 'GrupoHomDetalhe.codigo_cargo');

            $dados = $this->GrupoHomDetalhe->find('first', array('conditions' => $conditions, 'fields' => $fields));

            if(!empty($dados)){
                echo json_encode($dados);
            }
            else{
                echo 2;
            }
        }
        else{
            echo 0;
        }
        exit;
    }

     function retornaDetalhesGrupoHomogeneo($codigo_grupo_homogeneo){
        $this->layout = 'ajax';
        $this->render(false, false);

        $conditions = array(
            'GrupoHomDetalhe.codigo_grupo_homogeneo' => $codigo_grupo_homogeneo,
            'GrupoHomogeneo.ativo' => 1
        );

        $joins  = array(
            array(
              'table' => $this->GrupoHomDetalhe->databaseTable.'.'.$this->GrupoHomDetalhe->tableSchema.'.'.$this->GrupoHomDetalhe->useTable,
              'alias' => 'GrupoHomDetalhe',
              'type' => 'LEFT OUTER',
              'conditions' => 'GrupoHomogeneo.codigo = GrupoHomDetalhe.codigo_grupo_homogeneo',
            ),
            array(
              'table' => $this->Setor->databaseTable.'.'.$this->Setor->tableSchema.'.'.$this->Setor->useTable,
              'alias' => 'Setor',
              'type' => 'LEFT',
              'conditions' => 'GrupoHomDetalhe.codigo_setor = Setor.codigo',
            ),
            array(
              'table' => $this->Cargo->databaseTable.'.'.$this->Cargo->tableSchema.'.'.$this->Cargo->useTable,
              'alias' => 'Cargo',
              'type' => 'LEFT',
              'conditions' => 'GrupoHomDetalhe.codigo_cargo = Cargo.codigo',
            ),
        );

        $fields = array(
            'GrupoHomDetalhe.codigo', 'GrupoHomDetalhe.codigo_grupo_homogeneo', 'GrupoHomDetalhe.codigo_setor', 'GrupoHomDetalhe.codigo_cargo',
            'Setor.codigo','Setor.descricao',
            'Cargo.codigo','Cargo.descricao'
        );

        $dados = $this->GrupoHomogeneo->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));
        
        if(empty($dados)){
            echo 0;
        }
        else{
            echo json_encode($dados);
        }
    }


    function index_externo() {
        $this->pageTitle = "GHE Externos";
        $this->data[$this->GrupoHomogeneoExterno->name] = $this->Filtros->controla_sessao($this->data, $this->GrupoHomogeneoExterno->name);
    }

    function listagem_externo() {
        $this->layout = 'ajax';
        $ghes = array();
        $listagem = false;
        $filtros = $this->Filtros->controla_sessao($this->data, $this->GrupoHomogeneoExterno->name);

        $this->loadModel('GrupoEconomico');        
        $codigo_cliente_filial = isset($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : null;
        $codigo_cliente_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente_filial);

        if(!empty($filtros['codigo_cliente'])){

            $filtros['codigo_cliente'] = $codigo_cliente_matriz;
            $conditions = $this->GrupoHomogeneoExterno->converteFiltroEmCondition($filtros);

            $fields = array(
                'GrupoHomogeneo.codigo', 
                'GrupoHomogeneoExterno.codigo', 
                'GrupoHomogeneo.descricao', 
                'GrupoHomogeneo.ativo', 
                'GrupoHomogeneoExterno.codigo_externo',
                'GrupoHomogeneoExterno.codigo_cliente'
            );
            
            $order = 'GrupoHomogeneo.descricao';

            $this->GrupoHomogeneo->bindModel(
                array('hasOne' => array(
                        'GrupoHomogeneoExterno' => array(
                            'foreignKey' => 'codigo_ghe', 
                            'conditions' => array('GrupoHomogeneoExterno.codigo_cliente' => $codigo_cliente_matriz)
                        )
                    )
                ), false
            );

            $this->paginate['GrupoHomogeneo'] = array(
                    'fields' => $fields,
                    'conditions' => $conditions,
                    'limit' => 50,
                    'order' => $order,
            );
           
            $ghes = $this->paginate('GrupoHomogeneo');
            
            $listagem = true;
        }

        $this->set(compact('ghes','listagem'));
        $this->set('codigo_cliente', $codigo_cliente_matriz);
    }

    function editar_externo() {
        $this->pageTitle = 'GHEs Externos'; 

        $codigoGhe = $this->RequestHandler->params['pass'][1];
        $codigo_cliente = $this->RequestHandler->params['pass'][0];
        if (isset($this->RequestHandler->params['pass'][2])) {
            $codigoGheExterno = $this->RequestHandler->params['pass'][2];
        }

        $dadosGhe = $this->GrupoHomogeneo->carregar($codigoGhe);

        if($this->RequestHandler->isPost()) {

            if($this->GrupoHomogeneoExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'grupos_homogeneos'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[2])) {
            $this->data = $this->GrupoHomogeneoExterno->find('first',array('conditions' => array('GrupoHomogeneoExterno.codigo' => $this->passedArgs[2])));
        } else {
            $this->data = $dadosGhe;
        }
        $this->set('codigo_cliente', $codigo_cliente);
    }
}