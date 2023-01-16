<?php
class MedicosController extends AppController {
    public $name = 'Medicos';
    var $uses = array(  'Medico',
        'ConselhoProfissional',
        'EnderecoEstado',
    	'EnderecoCidade',
    	'MedicoEndereco'
    );
        
    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(
            array(
                'buscar_listagem_readonly',
                'carrega_medicos_para_ajax',
            )
        );
    }//FINAL FUNCTION beforeFilter   

    function index() {
        $this->pageTitle = 'Profissionais';

        $this->retorna_combos();
    }

    function retorna_combos(){
        $conselho_profissional = $this->ConselhoProfissional->find('list', array('fields' => array('codigo', 'descricao'),'order' => 'codigo'));
        $estado = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1),'fields' => array('abreviacao', 'descricao'),'order' => 'descricao'));
        
        $this->set(compact('conselho_profissional', 'estado'));
    }
   
    function listagem() {
        $this->layout = 'ajax'; 

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Medico->name);
        $conditions = $this->Medico->converteFiltroEmCondition($filtros);

        $fields = array('Medico.codigo', 'Medico.nome', 'Medico.numero_conselho', 'Medico.conselho_uf', 'Medico.codigo_conselho_profissional', 'ConselhoProfissional.descricao', 'Medico.ativo');
        $order = 'Medico.nome';
        
        $this->Medico->bindModel( 
            array(
                'belongsTo' => array(
                    'ConselhoProfissional' => array(
                        'foreignKey' => false, 
                        'conditions' => array('ConselhoProfissional.codigo = Medico.codigo_conselho_profissional')
                    ),
                )
            ), false
        );


        $this->paginate['Medico'] = array(
                'recursive' => 0,
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $medicos = $this->paginate('Medico');

        $this->set(compact('medicos'));
    }
    
    function incluir() {
        $this->pageTitle = 'Incluir Novo Profissional';

        if($this->RequestHandler->isPost()) {
        	
            $this->data['Medico']['nome'] = strtoupper ($this->data['Medico']['nome']);
            $this->data['Medico']['numero_conselho'] = Comum::soNumero($this->data['Medico']['numero_conselho']);
            $this->data['Medico']['cpf'] = Comum::soNumero($this->data['Medico']['cpf']);
            $this->data['Medico']['nis'] = Comum::soNumero($this->data['Medico']['nis']);

            // debug($this->data);exit;

            if ($this->Medico->incluir($this->data)) {
                
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'medicos', 'action' => 'index'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }

			if(isset($this->data['MedicoEndereco']['codigo_estado_endereco']) && $this->data['MedicoEndereco']['codigo_estado_endereco']) {
				$this->set('cidades', $this->EnderecoCidade->combo($this->data['MedicoEndereco']['codigo_estado_endereco']));
			} else {
				$this->set('cidades', array('' => 'Cidade (Selecione Primeiro o Estado)'));
			}   
        } else {
        	$this->set('cidades', array('' => 'Cidade (Selecione Primeiro o Estado)'));
        }

        $this->retorna_combos();
        
        $lista_estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1), 'fields' => array('codigo', 'descricao')));
        $lista_estados[''] = 'UF';
        ksort($lista_estados);
        
        $this->set('estados', $lista_estados);
    }
    
    
    function editar() {
        $this->pageTitle = 'Editar Profissional'; 
        
        if($this->RequestHandler->isPost()) {

            $this->data['Medico']['nome'] = strtoupper ($this->data['Medico']['nome']);
            $this->data['Medico']['numero_conselho'] = Comum::soNumero($this->data['Medico']['numero_conselho']);
            $this->data['Medico']['cpf'] = Comum::soNumero($this->data['Medico']['cpf']);
            $this->data['Medico']['nis'] = Comum::soNumero($this->data['Medico']['nis']);

            if ($this->Medico->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'medicos', 'action' => 'index'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
            
        } else {
        	
        	if (isset($this->passedArgs[0])) {
        		
        		$options['fields'] = array('*', 'MedicoEndereco.codigo', 'MedicoEndereco.cep', 'MedicoEndereco.logradouro', 'MedicoEndereco.numero', 'MedicoEndereco.bairro', 'MedicoEndereco.complemento', 'MedicoEndereco.codigo_estado_endereco', 'MedicoEndereco.codigo_cidade_endereco');
        		$options['joins'] = array(
       				array(
 						'table' => 'medicos_endereco',
   						'alias' => 'MedicoEndereco',
   						'type' => 'LEFT',
   						'conditions' => array(
							'MedicoEndereco.codigo_medico = Medico.codigo'
    					)
        			)
        		);
        		$options['conditions'] = array('Medico.codigo' => $this->passedArgs[0]);
        		
        		$this->data = $this->Medico->find('first', $options);
        	}
        }
        
        if(isset($this->data['MedicoEndereco']['codigo_estado_endereco']) && $this->data['MedicoEndereco']['codigo_estado_endereco']) {
        	$this->set('cidades', $this->EnderecoCidade->combo($this->data['MedicoEndereco']['codigo_estado_endereco']));
        } else {
        	$this->set('cidades', array('' => 'Cidade (Selecione Primeiro o Estado)'));
        }        

        $this->retorna_combos();
        
        $lista_estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1), 'fields' => array('codigo', 'descricao')));
        $lista_estados[''] = 'UF';
        ksort($lista_estados);
        
        $this->set('estados', $lista_estados);        
    }

    function buscar_medico_lista($codigo_fornecedor){
        $this->layout = 'ajax_placeholder';

        $this->Medico->bindModel( 
            array(
                'belongsTo' => array(
                    'ConselhoProfissional' => array(
                        'foreignKey' => false, 
                        'conditions' => array('ConselhoProfissional.codigo = Medico.codigo_conselho_profissional')
                    ),
                )
            ), false
        );

        $this->data['Medico'] = $this->Filtros->controla_sessao($this->data, $this->Medico->name);
        $this->retorna_combos();
               
        $this->set(compact('codigo_fornecedor'));
    }    

    function buscar_medico_readonly() {
    	
        $this->layout = 'ajax_placeholder';

        $this->data['Medico'] = $this->Filtros->controla_sessao($this->data, $this->Medico->name);
        $this->retorna_combos();
        
        if (isset($this->passedArgs['input_id']))
            $this->set('input_id', str_replace('-search', '', $this->passedArgs['input_id']));
        
        if (isset($this->passedArgs['input_crm_display']))
            $this->set('input_crm_display', str_replace('-search', '', $this->passedArgs['input_crm_display']));
        
		if (isset($this->passedArgs['input_uf_display']))
        	$this->set('input_uf_display', str_replace('-search', '', $this->passedArgs['input_uf_display']));
            
		if (isset($this->passedArgs['input_nome_display']))
        	$this->set('input_nome_display', str_replace('-search', '', $this->passedArgs['input_nome_display']));

        if (isset($this->passedArgs['input_cpf_display']))
            $this->set('input_cpf_display', str_replace('-search', '', $this->passedArgs['input_cpf_display']));
    }

    function buscar_listagem_readonly($destino) {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Medico->name);
        $filtros['ativo'] = 1;
        $conditions = $this->Medico->converteFiltroEmCondition($filtros);
        
        $this->Medico->bindModel( 
            array(
                'belongsTo' => array(
                    'ConselhoProfissional' => array(
                        'foreignKey' => false, 
                        'conditions' => array('ConselhoProfissional.codigo = Medico.codigo_conselho_profissional'
                           )
                    ),
                )
            ), false
        );        

        $this->paginate['Medico'] = array(
            'conditions' => $conditions,
            'joins' => null,
            'order' => 'Medico.nome',
            'limit' => 10,
        );

        if (isset($this->passedArgs['input_id'])){
            $this->set('input_id', str_replace('-search', '', $this->passedArgs['input_id']));
		}
        
        if (isset($this->passedArgs['input_crm_display']))
            $this->set('input_crm_display', str_replace('-search', '', $this->passedArgs['input_crm_display']));
        
		if (isset($this->passedArgs['input_uf_display']))
			$this->set('input_uf_display', str_replace('-search', '', $this->passedArgs['input_uf_display']));
            
		if (isset($this->passedArgs['input_nome_display']))
			$this->set('input_nome_display', str_replace('-search', '', $this->passedArgs['input_nome_display']));     

        if (isset($this->passedArgs['input_cpf_display']))
            $this->set('input_cpf_display', str_replace('-search', '', $this->passedArgs['input_cpf_display']));            	

        $medicos = $this->paginate('Medico');        
        $this->set(compact('medicos', 'destino','codigo_fornecedor'));
    }
    
    function buscar_medico($codigo_fornecedor){
        $this->layout = 'ajax_placeholder';

        $this->Medico->bindModel( 
            array(
                'belongsTo' => array(
                    'ConselhoProfissional' => array(
                        'foreignKey' => false, 
                        'conditions' => array('ConselhoProfissional.codigo = Medico.codigo_conselho_profissional')
                    ),
                )
            ), false
        );

        $this->data['Medico'] = $this->Filtros->controla_sessao($this->data, $this->Medico->name);
        $this->retorna_combos();
               
        $this->set(compact('codigo_fornecedor'));
    }

    function buscar_listagem($destino, $codigo_fornecedor){
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Medico->name);
        $filtros['ativo'] = 1;
        $conditions = $this->Medico->converteFiltroEmCondition($filtros);

        $this->Medico->bindModel( 
            array(
                'belongsTo' => array(
                    'ConselhoProfissional' => array(
                        'foreignKey' => false, 
                        'conditions' => array('ConselhoProfissional.codigo = Medico.codigo_conselho_profissional'
                            )
                    ),
                )
            ), false
        );
        $this->paginate['Medico'] = array(
            'conditions' => $conditions,
            'joins' => null,
            'order' => 'Medico.nome',
            'limit' => 10,
        );


        $medicos = $this->paginate('Medico');        
        $this->set(compact('medicos', 'destino','codigo_fornecedor'));
    }

     function listagem_visualizar($destino) {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Fornecedor->name);
        $conditions = $this->Fornecedor->converteFiltroEmCondition($filtros,null);
        $this->paginate['Fornecedor'] = array(
            'recursive' => 1,
            'joins' => null,
            'conditions' => $conditions,
            'limit' => 10,
            'order' => 'Fornecedor.razao_social',
        );

        $fornecedores = $this->paginate('Fornecedor');
        $this->set(compact('fornecedores', 'destino'));
        if (isset($this->passedArgs['searcher']))
            $this->set('input_id', str_replace('-search', '', $this->passedArgs['searcher']));
        if (isset($this->passedArgs['display']))
            $this->set('input_display', str_replace('-search', '', $this->passedArgs['display']));
    }

    function cadastro_fornecedor_incluir_medico($codigo_fornecedor){
        $this->pageTitle = 'Novo Profissional';
        $this->layout = 'ajax';

        if($this->RequestHandler->isPost()) {
            try{
                $this->Medico->query('begin transaction');
                debug($this->data);
                $conditions = array(
                    'numero_conselho' => $this->data['Medico']['numero_conselho'],
                    'codigo_conselho_profissional' => $this->data['Medico']['codigo_conselho_profissional'],
                    'conselho_uf' => $this->data['Medico']['conselho_uf']
                );
                $consulta = $this->Medico->find('all', array('conditions' => $conditions));

                if(empty($consulta)){
                    $this->data['Medico']['nome'] = strtoupper ($this->data['Medico']['nome']);
                    $this->data['Medico']['numero_conselho'] = Comum::soNumero($this->data['Medico']['numero_conselho']);

                    if(!$this->Medico->save($this->data)){
                         throw new Exception();

                            }
                    else{
                        $codigo_medico = $this->Medico->id;
                    

                        $dados = array(
                            'FornecedorMedico' => array(
                                'codigo_fornecedor' => $codigo_fornecedor,
                                'codigo_medico' => $codigo_medico
                                )
                            );

                        if ($this->FornecedorMedico->save($dados)) {
                            $this->BSession->setFlash('save_success');
                        } 
                        else {
                            throw new Exception();
                        }
                    }   
                }
                $this->Medico->commit();
            }
            catch(Exception $e) {
                $this->Medico->rollback();
                $this->BSession->setFlash('save_error');
            }   
        }
        $this->retorna_combos();

        $this->set(compact('codigo_fornecedor'));
    }

    public function atualiza_status($codigo, $status){
        $this->layout = 'ajax';

        $this->data['Medico']['codigo'] = $codigo;
        $this->data['Medico']['ativo'] = ($status == "0") ? 1 : 0;

        if ($this->Medico->save($this->data, false)) { // 0 -> ERRO | 1 -> SUCESSO     
            echo 1;
        } else {
            echo 0;
        }
        $this->render(false,false);
    }

    /**
     * [carregaMedicosParaAjax description]
     * 
     * metodo para carregar medicos conforme for digitando o nome ou inscricao do medico
     * 
     * @return [type] [description]
     */
    public function carrega_medicos_para_ajax()
    {
        //nao redenriza para um html
        $this->autoRender = false;
        $this->layout = 'ajax';
        //variavel auxiliar
        $html = false;

        //verifica se esta enviando o metodo post para buscas os dados
        if($this->RequestHandler->isPost()) {

            $joins = array(
                array(
                    'table' => 'RHHealth.dbo.conselho_profissional',
                    'alias' => 'ConselhoProfissional',
                    'type' => 'LEFT',
                    'conditions' => array('Medico.codigo_conselho_profissional = ConselhoProfissional.codigo')
                )
            );

            //busca os medicos 
            $medicos = $this->Medico->find('all', array(
                'recursive' => -1,
                'fields' => array('Medico.codigo','CONCAT(Medico.nome, \' - \', ConselhoProfissional.descricao, \': \', Medico.numero_conselho) as nome'),
                'joins' => $joins,
                'conditions' => array("Medico.nome LIKE '%".$_POST['string']."%' OR Medico.numero_conselho LIKE '%".$_POST['string']."%'"),
                'limit' => 6,
                'order' => 'Medico.nome ASC'
                )
            );

            // pr($medicos);exit;

            if(!empty($medicos)) {
                $html = '<table class="table">';
                foreach ($medicos as $key => $medico) {
                    $html .= '<tr class="js-medico-click" data-codigo="'.$medico['Medico']['codigo'].'">';
                    $html .= '<td>';
                    $html .= $medico[0]['nome'];
                    $html .= '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
        }//fim post
        
        //retorna json a resposta da variavel auxiliar
        return json_encode($html);

    }//fim carregaMedicosParaAjax

    function conselho_classe() {
        $this->pageTitle = 'Conselho Classe';

        $this->retorna_combos();
    }

    function listagem_conselho_classe() {
        $this->layout = 'ajax';
        $this->loadModel('ConselhoProfissional');
          
        $filtros    = $this->Filtros->controla_sessao($this->data, $this->ConselhoProfissional->name); 
        $conditions = $this->ConselhoProfissional->converteFiltroEmCondition($filtros);

        $this->paginate['ConselhoProfissional'] = array(
            'conditions' => $conditions,
            'recursive' => 0,
            'limit' => 50,
        );

        $conselhos = $this->paginate('ConselhoProfissional');

        $this->set(compact('conselhos'));
    }

    function incluir_conselho_classe() {
        $this->pageTitle = 'Incluir Conselho Classe';

        if($this->RequestHandler->isPost()) {
            
            $this->data['ConselhoProfissional']['descricao'] = strtoupper ($this->data['ConselhoProfissional']['descricao']);
                
            if ($this->ConselhoProfissional->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                } else { 
                    $this->BSession->setFlash('save_error');
                }
                $this->redirect(array('controller' => 'medicos', 'action' => 'conselho_classe'));
            }
    }
     
    function editar_conselho_classe($codigo = null) {
        $this->pageTitle = 'Editar Conselho Classe'; 

        if($this->RequestHandler->isPost()) {

            $this->data['ConselhoProfissional']['descricao'] = strtoupper ($this->data['ConselhoProfissional']['descricao']);

            if ($this->ConselhoProfissional->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'medicos', 'action' => 'conselho_classe'));
            } else {
                $this->BSession->setFlash('save_error');
            }        
        } else {
            $this->data = $this->ConselhoProfissional->find('first', array(
                'recursive'  => -1, 
                'conditions' => array(
                    'codigo' => $codigo
                )
            ));
        }
    }

    /**
     * Funcoes do Corpo Clinico
    */
    public function corpo_clinico(){
        $this->pageTitle = 'Corpo Clínico';
        $this->Filtros->limpa_sessao($this->Medico->name);
        $this->data['Medico'] = $this->Filtros->controla_sessao($this->data, $this->Medico->name);

        if(!is_null($this->BAuth->user('codigo_cliente')) && !isset($this->data['Medico']['codigo_cliente'])) {
            $this->data['Medico']['codigo_cliente'] = $this->BAuth->user('codigo_cliente');
        }

        $data_lista_unidades = $data_lista_fornecedores = array();
        $this->set(compact('data_lista_unidades', 'data_lista_fornecedores'));
    }

    public function corpo_clinico_listagem(){
        $this->layout = 'ajax';
        $filters = $this->Filtros->controla_sessao($this->data, $this->Medico->name);
        $filters = (is_array($filters) ? $filters : array());

        if(!is_null($this->BAuth->user('codigo_cliente')) && !isset($this->data['Medico']['codigo_cliente'])){
            $filters['codigo_cliente'] = $this->BAuth->user('codigo_cliente');
        }

        $parameters = $this->Medico->get_parametros_para_consulta_corpo_clinico($filters);
        
        // debug($parameters);exit;
        // $this->paginate['Medico'] = $parameters;

        $this->paginate['FornecedorMedico'] = array(
            'conditions' => $parameters['conditions'],
            'fields' => $parameters['fields'],
            'limit' => $parameters['limit'],
            'joins' => $parameters['joins'],
            'order' => $parameters['order'],
            'recursive' => $parameters['recursive'],
            'groupBy' => $parameters['group']
        );

        // debug($this->Medico->find('sql',$this->paginate['Medico']));exit;

        $data = $this->paginate('FornecedorMedico');

        $this->set(compact('data'));
    }

    public function corpo_clinico_imprimir($codigo_cliente, $codigo_fornecedor){
        self::__jasperCorpoClinicoImprimir($codigo_cliente, $codigo_fornecedor);
    }

    private function __jasperCorpoClinicoImprimir($codigo_cliente, $codigo_fornecedor){

        // opcoes de relatorio
        $opcoes = array(
            'REPORT_NAME'=>'/reports/RHHealth/relatorio_corpo_clinico', // especificar qual relatório
            'FILE_NAME'=> basename( 'relatorio_corpo_clinico.pdf' ) // nome do relatório para saida
        );

        // parametros do relatorio
        $parametros = array(
            'CODIGO_CLIENTE' => $codigo_cliente,
            'CODIGO_FORNECEDOR' => $codigo_fornecedor,
        );

        $this->loadModel('Cliente');
        $parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
        $this->loadModel('MultiEmpresa');
        //codigo empresa emulada
        $codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
        //url logo da multiempresa
        $parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);

        try {

            // envia dados ao componente para gerar
            $url = $this->Jasper->generate( $parametros, $opcoes );

            if($url){
                // se obter retorno apresenta usando cabeçalho apropriado
                header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
                header('Pragma: no-cache');
                header('Content-type: application/pdf');
                echo $url; exit;
            }

        } catch (Exception $e) {
            // se ocorreu erro
            debug($e); exit;
        }

        exit;

    } //fim __jasperconsulta
    /**
     * FIM Funcoes do Corpo Clinico
     */
}