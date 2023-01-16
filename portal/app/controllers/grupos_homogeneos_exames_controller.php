<?php
class GruposHomogeneosExamesController extends AppController {
    public $name = 'GruposHomogeneosExames';
    var $uses = array(
        'GrupoHomogeneoExame',
        'GrupoHomogeneoExameDetalhe',
        'GrupoEconomicoCliente',
        'GrupoHomogeneoExterno',
        'Cliente',
        'GrupoEconomico',
        'Cargo',
        'Setor'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        //$this->BAuth->allow('*');
    }
        
    function retorna_dados_cliente($codigo_cliente){
        
        $this->data = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);
        
        $this->set(compact('codigo_cliente'));
    }
    
    function retorna_dados($codigo_cliente){
        $this->retorna_dados_cliente($codigo_cliente);
        
        $cargo = $this->Cargo->lista_por_cliente($codigo_cliente);      
        $setor = $this->Setor->lista_por_cliente($codigo_cliente);

        $this->set(compact('codigo_cliente','cargo','setor'));
    }

    function index($codigo_cliente, $referencia) {
        $this->pageTitle = 'Grupos Homogêneos de Exames';
        $this->retorna_dados($codigo_cliente);
        $this->set(compact('referencia'));
    }

    function listagem($codigo_cliente, $referencia) {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->GrupoHomogeneoExame->name);
        
        $conditions = $this->GrupoHomogeneoExame->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions, array('GrupoHomogeneoExame.codigo_cliente' => $codigo_cliente));

        $fields = array(
            'GrupoHomogeneoExame.codigo', 'GrupoHomogeneoExame.descricao', 'GrupoHomogeneoExame.ativo'
        );

        $order = 'GrupoHomogeneoExame.descricao';

        $this->paginate['GrupoHomogeneoExame'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'order' => $order,
                'limit' => 50
        );
       
        $grupos_homogeneos = $this->paginate('GrupoHomogeneoExame');

        $matriz = $this->GrupoEconomicoCliente->find('first', array('conditions'=> array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));

        $this->set(compact('grupos_homogeneos', 'codigo_cliente','referencia','matriz'));
    }
    
    function incluir($codigo_cliente, $referencia) {
        $this->pageTitle = 'Incluir Grupos Homogêneos de Exames';
        
        if($this->RequestHandler->isPost()) {
            try{
                $this->GrupoHomogeneoExame->query('begin transaction');

                if ($this->GrupoHomogeneoExame->incluir($this->data)) {
                    if(isset($this->data['GrupoHomogeneoExameDetalhe']['x']))
                        unset($this->data['GrupoHomogeneoExameDetalhe']['x']);

                    if(isset($this->data['GrupoHomogeneoExameDetalhe'])){

                        foreach ($this->data['GrupoHomogeneoExameDetalhe'] as $key => $dados) {
                            $dados['codigo_grupo_homogeneo_exame'] = $this->GrupoHomogeneoExame->id;

                            if (!$this->GrupoHomogeneoExameDetalhe->incluir($dados)) {
                                $this->log(join(',', $this->GrupoHomogeneoExameDetalhe->invalidFields()), 'debug');
                                throw new Exception();
                            }
                        }
                    }
                } else{
                    $this->log(join(',', $this->GrupoHomogeneoExame->invalidFields()), 'debug');
                    throw new Exception();
                }

                $this->BSession->setFlash('save_success');
                $this->GrupoHomogeneoExame->commit();
                $this->redirect(array('action' => 'index', 'controller' => 'grupos_homogeneos_exames', $codigo_cliente, $referencia));
            }catch (Exception $ex) {
                $this->BSession->setFlash('save_error');
                $this->GrupoHomogeneoExame->rollback();
            }
        }

        $this->retorna_dados($codigo_cliente);
        $this->set(compact('referencia'));
    }
    
    function editar($codigo_cliente, $codigo, $referencia) {
        $this->pageTitle = 'Editar Grupos Homogêneos de Exames'; 
        
        if($this->RequestHandler->isPost()) {           
            try{  
                $this->GrupoHomogeneoExame->query('begin transaction');
                    if ($this->GrupoHomogeneoExame->atualizar($this->data)) {

                        if(isset($this->data['GrupoHomogeneoExameDetalhe']['x']))
                            unset($this->data['GrupoHomogeneoExameDetalhe']['x']);

                        if(isset($this->data['GrupoHomogeneoExameDetalhe'])){
                            if($this->GrupoHomogeneoExameDetalhe->deleteAll(array('GrupoHomogeneoExameDetalhe.codigo_grupo_homogeneo_exame' => $this->data['GrupoHomogeneoExame']['codigo']), false)){
                                
                                foreach ($this->data['GrupoHomogeneoExameDetalhe'] as $key => $dados) {
                                    unset($dados['codigo']);

                                    $dados['codigo_grupo_homogeneo_exame'] = $this->GrupoHomogeneoExame->id;
                                    if (!$this->GrupoHomogeneoExameDetalhe->incluir($dados)) {
                                        throw new Exception();
                                    }
                                }
                            }else{
                                throw new Exception();
                            }
                        }
                    }else{
                        throw new Exception();
                    }

                $this->BSession->setFlash('save_success');
                $this->GrupoHomogeneoExame->commit();
                $this->redirect(array('action' => 'index', 'controller' => 'grupos_homogeneos_exames', $codigo_cliente, $referencia));
            } catch (Exception $ex) {
                $this->BSession->setFlash('save_error');
                $this->GrupoHomogeneoExame->rollback();
            }
        } 

        if (isset($this->passedArgs[0])) {                 
            $this->retorna_dados($codigo_cliente);

            $dados = $this->GrupoHomogeneoExame->find('first', array('conditions' => array('codigo' => $codigo)));
            $this->data = array_merge($this->data, $dados);

            $query = 'SELECT ghed.codigo as codigo, Setor.codigo as codigo_setor, setor.descricao as descricao_setor, cargo.codigo as codigo_cargo, cargo.descricao as descricao_cargo
                        FROM RHHealth.dbo.grupos_homogeneos_exames_detalhes ghed
                        INNER JOIN RHHealth.dbo.setores setor 
                            ON ghed.codigo_setor = setor.codigo
                        INNER JOIN RHHealth.dbo.cargos cargo 
                            ON ghed.codigo_cargo = cargo.codigo
                        WHERE codigo_grupo_homogeneo_exame = ' . $dados['GrupoHomogeneoExame']['codigo'];
            $dados_detalhes = $this->GrupoHomogeneoExameDetalhe->query($query);

            foreach ($dados_detalhes as $key => $detalhes) {
                $this->data['GrupoHomogeneoExameDetalhe'][$key] = $detalhes[0];
            }
        }
        $this->set(compact('codigo_cliente','dados_detalhes', 'codigo','referencia'));
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['GrupoHomogeneoExame']['codigo'] = $codigo;
        $this->data['GrupoHomogeneoExame']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->GrupoHomogeneoExame->atualizar($this->data, false)) {   
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

        $grupo_homogeneo = $this->GrupoHomogeneoExame->find('first', array('conditions' => array('GrupoHomogeneoExame.codigo' => $codigo)));

        if(!empty($grupo_homogeneo)){

            $conditions = array('GrupoHomogeneoExameDetalhe.codigo_grupo_homogeneo' => $codigo);

            $fields = array(
                'GrupoHomogeneoExameDetalhe.codigo', 
                'GrupoHomogeneoExameDetalhe.codigo_grupo_homogeneo_exame', 
                'GrupoHomogeneoExameDetalhe.codigo_setor', 
                'GrupoHomogeneoExameDetalhe.codigo_cargo'
            );

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
            'GrupoHomogeneoExameDetalhe.codigo_grupo_homogeneo_exame' => $codigo_grupo_homogeneo,
            'GrupoHomogeneoExame.ativo' => 1
        );

        $joins  = array(
            array(
              'table' => $this->GrupoHomogeneoExameDetalhe->databaseTable.'.'.$this->GrupoHomogeneoExameDetalhe->tableSchema.'.'.$this->GrupoHomogeneoExameDetalhe->useTable,
              'alias' => 'GrupoHomogeneoExameDetalhe',
              'type' => 'LEFT OUTER',
              'conditions' => 'GrupoHomogeneoExame.codigo = GrupoHomogeneoExameDetalhe.codigo_grupo_homogeneo_exame',
            ),
            array(
              'table' => $this->Setor->databaseTable.'.'.$this->Setor->tableSchema.'.'.$this->Setor->useTable,
              'alias' => 'Setor',
              'type' => 'LEFT',
              'conditions' => 'GrupoHomogeneoExameDetalhe.codigo_setor = Setor.codigo',
            ),
            array(
              'table' => $this->Cargo->databaseTable.'.'.$this->Cargo->tableSchema.'.'.$this->Cargo->useTable,
              'alias' => 'Cargo',
              'type' => 'LEFT',
              'conditions' => 'GrupoHomogeneoExameDetalhe.codigo_cargo = Cargo.codigo',
            ),
        );

        $fields = array(
            'GrupoHomogeneoExameDetalhe.codigo', 'GrupoHomogeneoExameDetalhe.codigo_grupo_homogeneo_exame', 'GrupoHomogeneoExameDetalhe.codigo_setor', 'GrupoHomogeneoExameDetalhe.codigo_cargo',
            'Setor.codigo','Setor.descricao',
            'Cargo.codigo','Cargo.descricao'
        );

        $dados = $this->GrupoHomogeneoExame->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));
        
        if(empty($dados)){
            echo 0;
        }
        else{
            echo json_encode($dados);
        }
    }

    /*
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
                'GrupoHomogeneoExame.codigo', 
                'GrupoHomogeneoExterno.codigo', 
                'GrupoHomogeneoExame.descricao', 
                'GrupoHomogeneoExame.ativo', 
                'GrupoHomogeneoExterno.codigo_externo',
                'GrupoHomogeneoExterno.codigo_cliente'
            );
            
            $order = 'GrupoHomogeneoExame.descricao';

            $this->GrupoHomogeneo->bindModel(
                array('hasOne' => array(
                        'GrupoHomogeneoExterno' => array(
                            'foreignKey' => 'codigo_ghe', 
                            'conditions' => array('GrupoHomogeneoExterno.codigo_cliente' => $codigo_cliente_matriz)
                        )
                    )
                ), false
            );

            $this->paginate['GrupoHomogeneoExame'] = array(
                    'fields' => $fields,
                    'conditions' => $conditions,
                    'limit' => 50,
                    'order' => $order,
            );
           
            $ghes = $this->paginate('GrupoHomogeneoExame');
            
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

        $dadosGhe = $this->GrupoHomogeneoExame->carregar($codigoGhe);

        if($this->RequestHandler->isPost()) {

            if($this->GrupoHomogeneoExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'grupos_homogeneos_exames'));
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
    */
}