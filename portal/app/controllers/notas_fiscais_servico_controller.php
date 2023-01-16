<?php 

App::import('Core', 'Sanitize');

class NotasFiscaisServicoController extends AppController {
    public $name = 'NotasFiscaisServico';
    public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts', 'Buonny', 'Ithealth');
    public $components = array('Upload');

    	// Constantes para uso nesta classe
	const UPLOAD_ARQUIVO_TAMANHO_MAX = 5242880; // 1024*1024*5 = 5 MB

    const UPLOAD_ARQUIVO_MIMETYPES_ACEITOS = 'jpeg|png|bmp|pdf';
    
    var $uses = array(
        'NotaFiscalServico',
        'NotaFiscalStatus',
        'Fornecedor',
        'ConsolidadoNfsExame',
        'PedidoExame',
        'ItemPedidoExame',
        'AuditoriaExame',
        'Glosas',
        'GlosasStatus',
        'MotivosAcrescimo',
        'MotivosDesconto',
        'FormaPagto',
        'TipoRecebimento',
        'AnexoNotaFiscalServico',
        'DiaPagamento',
        'Usuario',
        'TipoServicosNfs',
        'Configuracao',
        'NotaFiscalServicoLog'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(); // TODO
    }

    public function index() {        
        
        $this->layout = 'ithealth';

        $this->pageTitle = 'Notas Fiscais Serviço (NFS)';
        //pega os filtro do controla sessao da exames
        $filtros = $this->Filtros->controla_sessao($this->data, $this->NotaFiscalServico->name);

        $this->data['NotaFiscalServico'] = $this->NotaFiscalServico->converteFiltroEmConditiON($filtros);

        //pega os status das notas fiscais
        $status_nfs = $this->NotaFiscalStatus->find('list', array('fields' => array('codigo','descricao')));
        
        //Carregando os tipos de data para filtras por inclusão ou vencimento
        $tipo_data = array(
            'I' => 'Inclusão',
            'V' => 'Vencimento'
        );
        
        $tiposServicosList = $this->TipoServicosNfs->find('list', array('fields' => array('codigo','descricao')));
        $this->data['NotaFiscalServico'] = $filtros;
        $this->set(compact('status_nfs','tiposServicosList','tipo_data'));
    }

    public function listagem($destino, $export = false) {
        $this->layout = 'ajax'; 
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->NotaFiscalServico->name);

        $fields = array(
            'Fornecedor.codigo',                       // Código Credenciado
            'Fornecedor.razao_social',                 // Razão Social Credenciado
            'Fornecedor.nome',                         
            'Fornecedor.codigo_documento',              // Cnpj Credenciado
            'Fornecedor.prestador_qualificado',
            'Fornecedor.faturamento_dias',

            'NotaFiscalServico.numero_nota_fiscal',    // Número NF
            'NotaFiscalServico.data_emissao',          // Data de Emissão
            'NotaFiscalServico.data_vencimento',        // Data do Vencimento
            'NotaFiscalServico.data_recebimento',       // Data do Recebimento
            'NotaFiscalServico.data_pagamento',         // Data Pagamento
            'NotaFiscalServico.codigo_nota_fiscal_status',
            'NotaFiscalStatus.descricao',
            'RHHealth.publico.ufn_formata_moeda(NotaFiscalServico.valor,2) AS valor', // Valor NFS
            'NotaFiscalServico.valor',                  // Valor NFS
            'NotaFiscalServico.codigo',
            'NotaFiscalServico.ativo',                  // Status
            'NotaFiscalServico.codigo_fornecedor',
            'NotaFiscalServico.chave_rastreamento',     // Código Rastreamento
            
            'TipoRecebimento.descricao',                // Tipo de Recebimento
            'MotivosAcrescimo.descricao',               // Acréscimo SIM/NÃO
            'TipoServicosNfs.descricao',
            'FormaPagto.descricao',                     // Forma de Pagamento
            'NotaFiscalServico.flag_acrescimo', 
            'NotaFiscalServico.descricao_acrescimo', 
            'NotaFiscalServico.flag_desconto', 
            'NotaFiscalServico.descricao_desconto', 
            'NotaFiscalServico.observacao', 
            'NotaFiscalServico.auditoria_codigo_usuario_responsavel', 
            'NotaFiscalServico.codigo_usuario_inclusao', 
            'Usuarios.nome', 
            'NotaFiscalServico.data_conclusao', 
            'UsuarioAuditoria.nome',
            'NotaFiscalServico.data_inclusao', 
            'NotaFiscalServico.liberacao_data', 
            'UsuarioAuditoria2.nome',
            'NotaFiscalServico.baixa_boleto_data', 
            'NotaFiscalServico.baixa_boleto_descricao', 
            'NotaFiscalServico.codigo_tipo_servicos_nfs', 
        );

        $joins = array(
            array(
                'table' => 'fornecedores',
                'alias' => 'Fornecedor',
                'type' => 'INNER',
                'conditions' => 'NotaFiscalServico.codigo_fornecedor = Fornecedor.codigo',
            ),
            array(
                'table' => 'nota_fiscal_status',
                'alias' => 'NotaFiscalStatus',
                'type' => 'LEFT',
                'conditions' => 'NotaFiscalStatus.codigo = NotaFiscalServico.codigo_nota_fiscal_status',
            ),
            array(
                'table' => 'formas_pagto',
                'alias' => 'FormaPagto',
                'type' => 'LEFT',
                'conditions' => 'FormaPagto.codigo = NotaFiscalServico.codigo_formas_pagto',
            ),
            array(
                'table' => 'motivos_acrescimo',
                'alias' => 'MotivosAcrescimo',
                'type' => 'LEFT',
                'conditions' => 'MotivosAcrescimo.codigo = NotaFiscalServico.codigo_motivo_acrescimo',
            ),
            array(
                'table' => 'motivos_desconto',
                'alias' => 'MotivosDesconto',
                'type' => 'LEFT',
                'conditions' => 'MotivosDesconto.codigo = NotaFiscalServico.codigo_motivo_desconto',
            ),
            array(
                'table' => 'tipo_recebimento',
                'alias' => 'TipoRecebimento',
                'type' => 'LEFT',
                'conditions' => 'TipoRecebimento.codigo = NotaFiscalServico.codigo_tipo_recebimento',
            ),
            array(
                'table' => 'tipo_servicos_nfs',
                'alias' => 'TipoServicosNfs',
                'type' => 'LEFT',
                'conditions' => 'TipoServicosNfs.codigo = NotaFiscalServico.codigo_tipo_servicos_nfs',
            ),
            array(
                'table' => 'usuario',
                'alias' => 'Usuarios',
                'type' => 'LEFT',
                'conditions' => 'Usuarios.codigo = NotaFiscalServico.codigo_usuario_inclusao',
            ),
            array(
                'table' => 'usuario',
                'alias' => 'UsuarioAuditoria',
                'type' => 'LEFT',
                'conditions' => 'UsuarioAuditoria.codigo = NotaFiscalServico.codigo_usuario_conclusao',
            ),
            array(
                'table' => 'usuario',
                'alias' => 'UsuarioAuditoria2',
                'type' => 'LEFT',
                'conditions' => 'UsuarioAuditoria2.codigo = NotaFiscalServico.auditoria_codigo_usuario_responsavel',
            )
        );

        $conditions = $this->NotaFiscalServico->converteFiltroEmConditiON($filtros);

        $order = 'Fornecedor.codigo';

        $this->paginate['NotaFiscalServico'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'joins' => $joins,
            'order' => $order,
        );

         // pr($this->NotaFiscalServico->find('sql', $this->paginate['NotaFiscalServico']));

        // debug($dados_nfs);exit;


        if($export){                
            $query = $this->NotaFiscalServico->find('sql',array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins));            
            $this->export_lista_notas_fiscais($query);
        } else {
            $dados_nfs = $this->paginate('NotaFiscalServico');
        }

        $this->set(compact('dados_nfs'));
    }

    public function incluir() 
    {
        $this->pageTitle = 'Incluir Nota Fiscal Serviço';

        $entity = $this->NotaFiscalServico->newEmptyEntity();
        if($this->RequestHandler->isPost()) {
            
            $valor = $this->RequestHandler->params['data']['NotaFiscalServico']['valor'];
            $valor = str_replace(".","",$valor);
            $this->RequestHandler->params['data']['NotaFiscalServico']['valor'] = $valor;

            // debug($this->RequestHandler->params['data']);exit;


            $entity = $this->NotaFiscalServico->patchEntity($entity, $this->RequestHandler->params['data']);

            $salvarData = $this->salvar(null, $entity);
            
            // se for um numero é porque gravou e esta retornando qual o codigo
            if(Validation::numeric($salvarData)){
                $this->BSession->setFlash('save_success');
                return $this->redirect(array('action' => 'editar', $salvarData));
            }
            
            // se possui erros 
            if(isset($salvarData['error'])){
                $this->BSession->setFlash(array(MSGT_ERROR, $salvarData['error']));
            }
        }
        
        $usuarioAutenticado = $_SESSION['Auth']['Usuario'];
        $flagGestorOperacoes = isset($usuarioAutenticado['flag_notas_fiscais_servicos_acrescimo_desconto']) ? (int)$usuarioAutenticado['flag_notas_fiscais_servicos_acrescimo_desconto'] : 0;
        
        // marcação das datas e quem criou e modificou registros
        $carimbo = array(
            'criacao'=> array(
                'nome'=>$usuarioAutenticado['nome'], 
                'data'=>date('d/m/Y') 
            )
        );

        $this->data = $entity;
        $codigo = null;
        $liberar_edicao_vencimento = false;
        $this->set(compact('entity', 'codigo', 'flagGestorOperacoes', 'carimbo','liberar_edicao_vencimento'));

        $this->render('incluir', 'ithealth');
    }

    public function editar($codigo = null)
    {
        $this->pageTitle = 'Editar Nota Fiscal Serviço';

        // Se não existir um código válido redireciona para página anterior, ex. geralmente a lista
        if(!isset($codigo) || empty($codigo) || !Validation::numeric($codigo))
        {
            $this->BSession->setFlash(array(MSGT_ERROR, 'Registro não encontrado, por favor tente novamente.'));
            $this->redirect(array('controller' => 'notas_fiscais_servico', 'action' => 'index'));
        }
        
        $entity = $this->NotaFiscalServico->obterEntityPorCodigo($codigo);
        if(!$entity){
            $this->BSession->setFlash(array(MSGT_ERROR, 'Registro não encontrado, por favor tente novamente.'));
            $this->redirect(array('controller' => 'notas_fiscais_servico', 'action' => 'index'));
        }


        if($this->RequestHandler->isPost()) {

            $valor = $this->RequestHandler->params['data']['NotaFiscalServico']['valor'];
            $valor = str_replace(".","",$valor);
            $this->RequestHandler->params['data']['NotaFiscalServico']['valor'] = $valor;

            // atualiza entity com dados recebidos do formulario
            $entity = $this->NotaFiscalServico->patchEntity($entity, $this->RequestHandler->params['data']);

            $salvarData = $this->salvar($codigo, $entity);
            
            // se for um numero é porque gravou e esta retornando qual o codigo
            if(Validation::numeric($salvarData)){
                $this->BSession->setFlash('save_success');
                return $this->redirect(array('action' => 'editar', $salvarData));
            }
            
            // se possui erros 
            if(isset($salvarData['error'])){

                $this->BSession->setFlash(array(MSGT_ERROR, $salvarData['error']));
                //incluido aqui porque está dando erro no field $this->data['AnexoNotaFiscalServico']['caminho_arquivo'] que no post
                //é um array com o arquivo a ser anexado, mas quando por algum motivo da erro na alteração ele precisa recarregar como string
                $entity = $this->NotaFiscalServico->obterEntityPorCodigo($codigo);
            }
        }

        //buscar pra constar se na nota se o campo auditoria_codigo_usuario_responsavel esta preenchido
        $empty_aud_responsavel = $this->NotaFiscalServico->obterPorCodigo($codigo);
        //se tiver vazio o campo no registro da nota o campo auditoria_codigo_usuario_responsavel, ele poem vazio pro usuario poder colocar na tela. Por que o metodo entity verifica pra preencher o this->data e tb pro post
        if(empty($empty_aud_responsavel['NotaFiscalServico']['auditoria_codigo_usuario_responsavel'])){
            $entity['NotaFiscalServico']['auditoria_codigo_usuario_responsavel'] = null;
        }

        //esse trecho serve pra atender a CDCT-389, onde o campo liberacao_data e a data_conclusao vão ter o mesmo fim, respeitando uma data de corte
        if (!is_null($entity['NotaFiscalServico']['liberacao_data'])) {
            $data_liberacao = strtotime(str_replace('/', '-',$entity['NotaFiscalServico']['liberacao_data']));
            $data_corte = strtotime('04-09-2021');
            
            if((!is_null($data_liberacao)) && ($data_liberacao < $data_corte) && (is_null($entity['NotaFiscalServico']['data_conclusao']))){
                
                $entity['UsuarioConclusao']['apelido']         = $entity['UsuarioAudResponsavel']['apelido'];
                $entity['NotaFiscalServico']['data_conclusao'] = $entity['NotaFiscalServico']['liberacao_data'];
            }
        }
            
        $this->data = $entity;

        $upload = $this->AnexoNotaFiscalServico->find('first', array('conditions' => array('codigo' => $codigo)));
        
        $codigo = $entity['NotaFiscalServico']['codigo']; // codigo registro

        $usuarioAutenticado = $_SESSION['Auth']['Usuario'];
        $flagGestorOperacoes = isset($usuarioAutenticado['flag_notas_fiscais_servicos_acrescimo_desconto']) ? (int)$usuarioAutenticado['flag_notas_fiscais_servicos_acrescimo_desconto'] : 0;
        
        $usuarioInclusao = $this->NotaFiscalServico->obterNomeUsuario($entity['NotaFiscalServico']['codigo_usuario_inclusao']);
        
        //tratamento cdct-537
        $dataInclusao = null;
        if(!empty($entity['NotaFiscalServico']['data_inclusao'])) {
            $dataInclusao = $entity['NotaFiscalServico']['data_inclusao'];
            $valida_data = explode("/",$entity['NotaFiscalServico']['data_inclusao']);
            if(strlen($valida_data[0]) == 4) {
                $dataInclusao = AppModel::formataData($entity['NotaFiscalServico']['data_inclusao']);
            }
        }//fim id data inclusao
        
        $usuarioAlteracao = $this->NotaFiscalServico->obterNomeUsuario($entity['NotaFiscalServico']['codigo_usuario_alteracao']);
        $dataAlteracao = !empty($entity['NotaFiscalServico']['data_alteracao']) ? AppModel::formataData($entity['NotaFiscalServico']['data_alteracao']) : null;  
            
        // marcação das datas e quem criou e modificou registros
        $carimbo = array(
            'criacao'=> array(
                'nome'=>$usuarioInclusao, 
                'data'=>$dataInclusao
            ),
            'alteracao'=> array(
                'nome'=>$usuarioAlteracao, 
                'data'=>$dataAlteracao
            )
        );

        //liberar o campo vencimento para edição
        $liberar_edicao_vencimento = false;
        $usuario_permissao_editar_vencimento = $this->Configuracao->getChave('COD_USU_CM_DATA_VENC');
        $codigos_users = explode(',',$usuario_permissao_editar_vencimento);
        //valida se o usuario que esta logado está dentro da lista de usuario com permissão apra alterar o valor do campo
        if(in_array($this->BAuth->user('codigo'),$codigos_users)) {
            $liberar_edicao_vencimento = true;
        }

        // debug($this->BAuth->user('codigo'));
        // debug($codigos_users);
        // debug($usuario_permissao_editar_vencimento);exit;
        $this->set(compact('codigo', 'upload', 'flagGestorOperacoes', 'carimbo','liberar_edicao_vencimento'));
        
        $this->render('editar', 'ithealth');
    }

    public function visualizar($codigo = null)
    {
        $this->pageTitle = 'Visualizar Nota Fiscal Serviço';

        // Se não existir um código válido redireciona para página anterior, ex. geralmente a lista
        if(!isset($codigo) || empty($codigo) || !Validation::numeric($codigo))
        {
            $this->BSession->setFlash(array(MSGT_ERROR, 'Registro não encontrado, por favor tente novamente.'));
            $this->redirect(array('controller' => 'notas_fiscais_servico', 'action' => 'index'));
        }
        
        $entity = $this->NotaFiscalServico->obterEntityPorCodigo($codigo);
        
        if(!$entity){
            $this->BSession->setFlash(array(MSGT_ERROR, 'Registro não encontrado, por favor tente novamente.'));
            $this->redirect(array('controller' => 'notas_fiscais_servico', 'action' => 'index'));
        }

        
        $this->data = $entity;

        $upload = $this->AnexoNotaFiscalServico->find('first', array('conditions' => array('codigo' => $codigo)));
        
        $codigo = $entity['NotaFiscalServico']['codigo']; // codigo registro

        $usuarioAutenticado = $_SESSION['Auth']['Usuario'];
        $flagGestorOperacoes = isset($usuarioAutenticado['flag_notas_fiscais_servicos_acrescimo_desconto']) ? (int)$usuarioAutenticado['flag_notas_fiscais_servicos_acrescimo_desconto'] : 0;
        
        $usuarioInclusao = $this->NotaFiscalServico->obterNomeUsuario($entity['NotaFiscalServico']['codigo_usuario_inclusao']);
        $dataInclusao = !empty($entity['NotaFiscalServico']['data_inclusao']) 
            ? Comum::formataData($entity['NotaFiscalServico']['data_inclusao'], $formato_in = "dmy", $formato_out = "dmy")
            : null;  
        
        $usuarioAlteracao = $this->NotaFiscalServico->obterNomeUsuario($entity['NotaFiscalServico']['codigo_usuario_alteracao']);
        $dataAlteracao = !empty($entity['NotaFiscalServico']['data_alteracao']) 
            ? Comum::formataData($entity['NotaFiscalServico']['data_alteracao'], $formato_in = "ymd", $formato_out = "dmy")
            : null;  
            
        // marcação das datas e quem criou e modificou registros
        $carimbo = array(
            'criacao'=> array(
                'nome'=>$usuarioInclusao, 
                'data'=>$dataInclusao
            ),
            'alteracao'=> array(
                'nome'=>$usuarioAlteracao, 
                'data'=>$dataAlteracao
            )
        );

        //liberar o campo vencimento para edição
        $liberar_edicao_vencimento = false;
        $usuario_permissao_editar_vencimento = $this->Configuracao->getChave('COD_USU_CM_DATA_VENC');
        $codigos_users = explode(',',$usuario_permissao_editar_vencimento);
        //valida se o usuario que esta logado está dentro da lista de usuario com permissão apra alterar o valor do campo
        if(in_array($this->BAuth->user('codigo'),$codigos_users)) {
            $liberar_edicao_vencimento = true;
        }

        $this->set(compact('codigo', 'upload', 'flagGestorOperacoes', 'carimbo','liberar_edicao_vencimento'));
        
        $this->render('visualizar', 'ithealth');
    }
    
    public function salvar($codigo = null, $postData = array() ) 
    {
        $edit_mode = !empty($codigo);
        
        $this->log('salvar >> '.print_r($postData, true), 'debug');
        // validação na model
        $this->NotaFiscalServico->set( $postData['NotaFiscalServico'] );

        // se payload para nota fiscal não for válido termina processo
        if(!$this->NotaFiscalServico->validates())
        {
            $errors = $this->NotaFiscalServico->obterErros('HTML');
            // $this->log('ERROR NOTA >> '.print_r($errors, true), 'debug');
            return array('error' => $errors);
        }

        // verifica anexos
        $postDataAnexo = false;

        // se tem algo aqui não devia
        if(isset($postData['NotaFiscalServico']['anexo_nota_fiscal_servico'])){
            unset($postData['NotaFiscalServico']['anexo_nota_fiscal_servico']);
        }

        // verifica se esta passando algum arquivo válido
        if(isset($postData['AnexoNotaFiscalServico']['caminho_arquivo_binario']['name']) 
            && !empty($postData['AnexoNotaFiscalServico']['caminho_arquivo_binario']['name'])
            && $postData['AnexoNotaFiscalServico']['caminho_arquivo_binario']['error'] == 0)
        {
            
            $postDataAnexo['Nota_fiscal'] = $postData['AnexoNotaFiscalServico']['caminho_arquivo_binario']; // mantem binario em outra var
            $postDataAnexo['Nota_fiscal']['codigo_tipo_anexo_nota_fiscal_servico'] = $postData['AnexoNotaFiscalServico']['codigo_tipo_anexo_nota_fiscal_servico'];
            //$this->AnexoNotaFiscalServico->set( $postData['AnexoNotaFiscalServico'] );
            
            // valida arquivo binario
            if(!$this->validaBinario($postDataAnexo['Nota_fiscal']))
            {
                $errors = 'Ocorreu algum erro com a leitura deste arquivo';
                // $this->log('ERROR BINARIO >> '.print_r($errors, true), 'debug');
                return array('error' => $errors);
            }
            
            unset($postData['AnexoNotaFiscalServico']['caminho_arquivo_binario']); // limpa memoria do postData
        }

        if(isset($postData['AnexoNFsBoleto']['caminho_arquivo_binario']['name']) 
            && !empty($postData['AnexoNFsBoleto']['caminho_arquivo_binario']['name'])
            && $postData['AnexoNFsBoleto']['caminho_arquivo_binario']['error'] == 0)
        {
            
            $postDataAnexo['Boleto'] = $postData['AnexoNFsBoleto']['caminho_arquivo_binario']; // mantem binario em outra var
            $postDataAnexo['Boleto']['codigo_tipo_anexo_nota_fiscal_servico'] = $postData['AnexoNFsBoleto']['codigo_tipo_anexo_nota_fiscal_servico'];
            
            //$this->AnexoNFsBoleto->set( $postData['AnexoNFsBoleto'] );
            
            // valida arquivo binario
            if(!$this->validaBinario($postDataAnexo['Boleto']))
            {
                $errors = 'Ocorreu algum erro com a leitura deste arquivo';
                // $this->log('ERROR BINARIO >> '.print_r($errors, true), 'debug');
                return array('error' => $errors);
            }
            
            unset($postData['AnexoNFsBoleto']['caminho_arquivo_binario']); // limpa memoria do postData
        }

        if(isset($postData['AnexoNFSEspelhoFaturamento']['caminho_arquivo_binario']['name']) 
            && !empty($postData['AnexoNFSEspelhoFaturamento']['caminho_arquivo_binario']['name'])
            && $postData['AnexoNFSEspelhoFaturamento']['caminho_arquivo_binario']['error'] == 0)
        {
            
            $postDataAnexo['EspelhoFaturamento'] = $postData['AnexoNFSEspelhoFaturamento']['caminho_arquivo_binario']; // mantem binario em outra var
            $postDataAnexo['EspelhoFaturamento']['codigo_tipo_anexo_nota_fiscal_servico'] = $postData['AnexoNFSEspelhoFaturamento']['codigo_tipo_anexo_nota_fiscal_servico'];
            
            //$this->AnexoNFSEspelhoFaturamento->set( $postData['AnexoNFSEspelhoFaturamento'] );
            
            // valida arquivo binario
            if(!$this->validaBinario($postDataAnexo['EspelhoFaturamento']))
            {
                $errors = 'Ocorreu algum erro com a leitura deste arquivo';
                // $this->log('ERROR BINARIO >> '.print_r($errors, true), 'debug');
                return array('error' => $errors);
            }
            
            unset($postData['AnexoNFSEspelhoFaturamento']['caminho_arquivo_binario']); // limpa memoria do postData
        }



        // se não foi possível gravar termina processo
        if($edit_mode){          
            
            unset($postData['NotaFiscalServico']['codigo_usuario_inclusao']);
            unset($postData['NotaFiscalServico']['data_inclusao']);

            if(!$this->NotaFiscalServico->atualizar(array('NotaFiscalServico'=> $postData['NotaFiscalServico']))){
                $errors = $this->NotaFiscalServico->obterErros('HTML');
                // $this->log('ERRO SALVAR NOTA >> '.print_r($errors, true), 'debug');
                return array('error' => $errors);
            }
            
            $codigo_nota_fiscal_servico = $postData['NotaFiscalServico']['codigo'];

        } else {
            
            if(!$this->NotaFiscalServico->incluir($postData['NotaFiscalServico'])){
                $errors = $this->NotaFiscalServico->obterErros('HTML');
                // $this->log('ERRO INCLUIR NOTA >> '.print_r($errors, true), 'debug');
                return array('error' => $errors);
            }

            // obter id da nota fiscal para relacionar ao arquivo
            $codigo_nota_fiscal_servico = $this->NotaFiscalServico->getLastInsertID();
        }

        // se existe binario anexo no payload
        if($codigo_nota_fiscal_servico && $postDataAnexo)
        {            
            foreach($postDataAnexo as $anexo){

                $retornoSalvarArquivo = $this->salvarArquivo(null, $anexo, $codigo_nota_fiscal_servico);

                if (isset($retornoSalvarArquivo['error']) && !empty($retornoSalvarArquivo['error']) ){
                    //$this->BSession->setFlash(array(MSGT_ERROR, 'Nota fiscal foi salva, mas não foi possível salvar o anexo, verifique o tipo de arquivo e tente novamente.'));
                    //return $this->responseJson(array('error'=>'Nota fiscal foi salva, mas não foi possível salvar o anexo, verifique o tipo de arquivo e tente novamente.'));
                    // $this->log('Linha['.__LINE__.']'.__CLASS__ .' > '.__METHOD__.' >> '.print_r('Nota fiscal foi salva, mas não foi possível salvar o anexo, verifique o tipo de arquivo e tente novamente.', true), 'debug');
                }
            }           
        }

        return $codigo_nota_fiscal_servico;
    }


    function obter_faturamento_credenciado($codigo)
    {
        $this->layout = 'ajax';
        $data = array();
        $fornecedorData = $this->Fornecedor->find('first', array('recursive'=>-1,'fields'=>array('faturamento_dias'),'conditions'=> array('codigo'=>$codigo)));
        
        if(isset($fornecedorData['Fornecedor']['faturamento_dias'])){
            $data['faturamento_dias'] = $fornecedorData['Fornecedor']['faturamento_dias'];
        }
        return $this->responseJson($data);
    }


    function atualiza_status($codigo, $status)
    {
        $this->layout = 'ajax';
        
        $this->data['NotaFiscalServico']['codigo'] = $codigo;
        $this->data['NotaFiscalServico']['ativo'] = ($status == 1) ? 0 : 1;

        if ($this->NotaFiscalServico->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO

            print 1;
        } else {

            print 0;
        }

        $this->render(false,false);              
    }

    /**
	 * Visualizar anexo de nota fiscal
	 *
	 * @return responseJson
	 */
	// public function visualizar_anexo_nota_fiscal( $codigo_nota_fiscal_servico = null)
    // {
    //     $this->layout = 'ajax';
    //     $this->autoLayout = false;
    //     $this->autoRender = false;

    //     $anexoNfsData = $this->AnexoNotaFiscalServico->find('first', array('conditions' => array('codigo_nota_fiscal_servico' => $codigo_nota_fiscal_servico)));
    //     $this->log('Linha['.__LINE__.']'.__CLASS__ .' > '.__METHOD__.' >> '.print_r($anexoNfsData, true), 'debug');
       

    //     header(sprintf('Content-Disposition: attachment; filename="%s"', basename($arquivo)));
    //     header("Content-type: ".mime_content_type($arquivo));
    //     header('Content-Transfer-Encoding: binary');
    //     header('Pragma: no-cache');

    //     ob_clean();
    //     flush();
    //     echo file_get_contents($arquivo);
    //     exit;
    
        
    // }

    public function modal_glosas($codigo_fornecedor, $codigo_nota)
    {
         //pega os dados para o exame que vai glosar
         $fields = array(
            'ItemPedidoExame.codigo as codigo',
            "CONCAT(ItemPedidoExame.codigo_pedidos_exames, ' - ' , Exame.descricao) as descricao",
            "Funcionario.nome as nome"  
        );

        //relacioanamento para os exames que foram consolidados e podem ser glosados
        $joins = array(
            array(
                "table"      => "RHHealth.dbo.exames",
                "alias"      => "Exame",
                "type"       => "INNER",
                "conditions" => "Exame.codigo = ItemPedidoExame.codigo_exame"
            ),
            array(
                "table"      => "RHHealth.dbo.auditoria_exames",
                "alias"      => "AuditoriaExame",
                "type"       => "INNER",
                "conditions" => "AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo"
            ),
            array(
				"table"      => "RHHealth.dbo.pedidos_exames",
				"alias"      => "PedidoExame",
				"type"       => "LEFT",
				"conditions" => "ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo",
			),
            array(
				"table"      => "RHHealth.dbo.funcionarios",
				"alias"      => "Funcionario",
				"type"       => "LEFT",
				"conditions" => "PedidoExame.codigo_funcionario = Funcionario.codigo",
			),
        );


        //monta as conditions para pegar os pedidos e exames passados
        $conditions = array(
            'ItemPedidoExame.codigo_fornecedor' => $codigo_fornecedor
        );

        //achar os exames do fornecedor relacionados a nota fiscal
        $dados = $this->ItemPedidoExame->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));
        //remonta os dados para apresentar no combo
        $dados_glosa = array();
        if(!empty($dados)) {
            foreach ($dados as $value) {
                $dados_glosa[$value[0]['codigo']]['descricao'] = $value[0]['descricao'];
                $dados_glosa[$value[0]['codigo']]['nome']      = $value[0]['nome'];
            }
        }

        $this->loadModel('GlosasStatus');
        $status = $this->GlosasStatus->find('all', array('conditions'=> array('ativo' => 1)));
        $glosas_status = array();
        if(!empty($status)) {
            foreach ($status as $value) {
                $glosas_status[$value['GlosasStatus']['codigo']] = $value['GlosasStatus']['descricao'];
            }
        }    

        $this->loadModel('TipoGlosas');
        $tipos = $this->TipoGlosas->find('all', array('conditions'=> array('ativo'=>1,'codigo <>'=> 8)));
        $tipos_glosas = array();
        if(!empty($tipos)) {
            foreach ($tipos as $value) {
                $tipos_glosas[$value['TipoGlosas']['codigo']] = $value['TipoGlosas']['descricao'];
            }
        }
        //pega os dados da nfs para pegar o numero
        $nfs = $this->NotaFiscalServico->find('first', array('conditions' => array('codigo' => $codigo_nota)));
        
        $numero_nfs = $nfs['NotaFiscalServico']['numero_nota_fiscal'];

        $this->set(compact('codigo_fornecedor', 'codigo_nota','numero_nfs', 'nfs','tipos_glosas','dados_glosa','glosas_status'));
    }

    public function salvar_dados_glosas() 
    {
        //para nao solicitar um ctp
        $this->autoRender = false;

        $retorno = true;

        //verifica se existe o codigo do item pedido exame
        $codigo_item_pedido_exame = null;
        $codigo_pedido_exame = null;

        if(!empty($this->params['form']['codigo_itens_pedidos_exames'])) {
            
            //pega o codigo do pedido de exames
            $item = $this->ItemPedidoExame->find('first',array('conditions' => array('codigo' => $this->params['form']['codigo_itens_pedidos_exames'])));
            
            $codigo_pedido_exame = $item['ItemPedidoExame']['codigo_pedidos_exames'];
            
            $codigo_item_pedido_exame = $item['ItemPedidoExame']['codigo'];
        }

        $dados_glosa['Glosas'] = array(
            'codigo_pedidos_exames'         => $codigo_pedido_exame,
            'codigo_itens_pedidos_exames'   => $codigo_item_pedido_exame,
            'valor'                         => $this->params['form']['valor'],
            'data_glosa'                    => $this->params['form']['data_glosa'],
            'data_vencimento'               => $this->params['form']['data_vencimento'],
            'data_pagamento'                => $this->params['form']['data_pagamento'],
            'codigo_status_glosa'           => $this->params['form']['codigo_status_glosa'],
            'codigo_tipo_glosa'             => $this->params['form']['motivo_glosa'],
            'codigo_fornecedor'             => $this->params['form']['codigo_fornecedor'],
            'codigo_nota_fiscal_servico'    => $this->params['form']['codigo_nota_fiscal_servico'],
            'ativo'                         => 1,
            'codigo_classificacao_glosa'    => 1
        );

        //verifica se existe o codigo da glosa, caso exista é edicao
        if(!empty($this->params['form']['codigo_glosa'])) {
            
            $dados_glosa['Glosas']['codigo'] = $this->params['form']['codigo_glosa'];

            if(!$this->Glosas->atualizar($dados_glosa)) {
                //retornar erro
                $retorno = false;
            }

        }
        else {

            if(!$this->Glosas->incluir($dados_glosa)) {
                
                //retornar erro
                $retorno = false;
            }

        }//para saber se atualiza ou insere

        //troca o status da nota fiscal de processado para processamento parcial
        $dados_nfs['NotaFiscalServico']['codigo'] = $this->params['form']['codigo_nota_fiscal_servico'];
        
        $dados_nfs['NotaFiscalServico']['codigo_nota_fiscal_status'] = 2;//processamento parcial
        
        if(!$this->NotaFiscalServico->atualizar($dados_nfs)){
            $retorno = false;
        }

        return $retorno;     
    }

    public function editar_glosas($codigo_fornecedor, $codigo_nota, $codigo_glosa){
        
        //pega os dados para o exame que vai glosar
        $fields = array(
            'ItemPedidoExame.codigo as codigo',
            "CONCAT(ItemPedidoExame.codigo_pedidos_exames, ' - ' , Exame.descricao) as descricao",
            "Funcionario.nome as nome"
        );

        //relacioanamento para os exames que foram consolidados e podem ser glosados
        $joins = array(
            array(
                "table"      => "RHHealth.dbo.exames",
                "alias"      => "Exame",
                "type"       => "INNER",
                "conditions" => "Exame.codigo = ItemPedidoExame.codigo_exame"
            ),
            array(
                "table"      => "RHHealth.dbo.auditoria_exames",
                "alias"      => "AuditoriaExame",
                "type"       => "INNER",
                "conditions" => "AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo"
            ),
            array(
				"table"      => "RHHealth.dbo.pedidos_exames",
				"alias"      => "PedidoExame",
				"type"       => "LEFT",
				"conditions" => "ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo",
			),
            array(
				"table"      => "RHHealth.dbo.funcionarios",
				"alias"      => "Funcionario",
				"type"       => "LEFT",
				"conditions" => "PedidoExame.codigo_funcionario = Funcionario.codigo",
			),
        );


        //monta as conditions para pegar os pedidos e exames passados
        $conditions = array(
            'ItemPedidoExame.codigo_fornecedor' => $codigo_fornecedor
        );

        //achar os exames do fornecedor relacionados a nota fiscal
        $dados = $this->ItemPedidoExame->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));
        $dados_glosa = array();
        //remonta os dados para apresentar no combo
        if(!empty($dados)) {
            foreach ($dados as $value) {
                $dados_glosa[$value[0]['codigo']]['descricao'] = $value[0]['descricao'];
                $dados_glosa[$value[0]['codigo']]['nome']      = $value[0]['nome'];
                $dados_glosa[$value[0]['codigo']]['codigo']     = $value[0]['codigo'];
            }
        }

        $this->loadModel('GlosasStatus');
        $status = $this->GlosasStatus->find('all', array('conditions'=> array('ativo' => 1)));
        $glosas_status = array();
        if(!empty($status)) {
            foreach ($status as $value) {
                $glosas_status[$value['GlosasStatus']['codigo']] = $value['GlosasStatus']['descricao'];
            }
        }    

        $this->loadModel('TipoGlosas');
        //Tipo de glosa reservada para valor(8) não será carregada
        $tipos = $this->TipoGlosas->find('all', array('conditions'=> array('ativo'=>1,'codigo <>'=> 8)));
        $tipos_glosas = array();
        if(!empty($tipos)) {
            foreach ($tipos as $value) {
                $tipos_glosas[$value['TipoGlosas']['codigo']] = $value['TipoGlosas']['descricao'];
            }
        }

        //pega os dados da nfs para pegar o numero
        $nfs = $this->NotaFiscalServico->find('first', array('conditions' => array('codigo' => $codigo_nota)));
        
        $numero_nfs = $nfs['NotaFiscalServico']['numero_nota_fiscal'];

        $glosas = $this->Glosas->find('first',array('conditions' => array('codigo' => $codigo_glosa)));

        $this->set(compact('dados_glosa', 'codigo_fornecedor', 'codigo_nota','numero_nfs', 'nfs','glosas', 'codigo_glosa','tipos_glosas','glosas_status'));
    }

    public function save_edicao_glosas() 
    {
        //para nao solicitar um ctp
        $this->autoRender = false;

        $retorno = true;

        //verifica se existe o codigo do item pedido exame
        $codigo_item_pedido_exame = null;
        
        $codigo_pedido_exame = null;
        
        if(!empty($this->params['form']['codigo_itens_pedidos_exames'])) {
            
            //pega o codigo do pedido de exames            
            $item = $this->ItemPedidoExame->find('first',array('conditions' => array('codigo' => $this->params['form']['codigo_itens_pedidos_exames'])));
            
            $codigo_pedido_exame = $item['ItemPedidoExame']['codigo_pedidos_exames'];
            
            $codigo_item_pedido_exame = $item['ItemPedidoExame']['codigo'];
        }

        $dados_glosa['Glosas'] = array(
            'codigo'                        => $this->params['form']['codigo_glosa'],
            'codigo_pedidos_exames'         => $codigo_pedido_exame,
            'codigo_itens_pedidos_exames'   => $codigo_item_pedido_exame,
            'valor'                         => $this->params['form']['valor'],
            'data_glosa'                    => $this->params['form']['data_glosa'],
            'data_vencimento'               => $this->params['form']['data_vencimento'],
            'data_pagamento'                => $this->params['form']['data_pagamento'],
            'codigo_status_glosa'           => $this->params['form']['codigo_status_glosa'],
            'motivo_glosa'                  => $this->params['form']['motivo_glosa'],
            'codigo_fornecedor'             => $this->params['form']['codigo_fornecedor'],
            'codigo_nota_fiscal_servico'    => $this->params['form']['codigo_nota_fiscal_servico']
        );

        if(!$this->Glosas->atualizar($dados_glosa)) {
            
            //retornar erro
            $retorno = false;
        }

        //troca o status da nota fiscal de processado para processamento parcial
        $dados_nfs['NotaFiscalServico']['codigo'] = $this->params['form']['codigo_nota_fiscal_servico'];
        
        $dados_nfs['NotaFiscalServico']['codigo_nota_fiscal_status'] = 2;//processamento parcial
        
        if(!$this->NotaFiscalServico->atualizar($dados_nfs)){
            $retorno = false;
        }

        return $retorno;     
    }

    function lista_glosas($codigo_fornecedor, $codigo_nota){
        
        $this->pageTitle = 'Lista Glosas'; 

        $fields = array('Glosas.codigo',
            'Glosas.codigo_pedidos_exames',
            'Exame.descricao',
            'Glosas.valor',
            'Glosas.data_glosa',
            'Glosas.data_pagamento',
            'Glosas.codigo_status_glosa',
            'GlosasStatus.descricao',
            'Glosas.data_vencimento',
            'Glosas.motivo_glosa',
            'Glosas.ativo',
            'ClassificacaoGlosa.descricao',
            'Funcionario.nome',
			'Funcionario.cpf',
            'TipoGlosas.descricao'
        );

        $joins = array(
            array(
                "table"      => "RHHealth.dbo.glosas_status",
                "alias"      => "GlosasStatus",
                "type"       => "LEFT",
                "conditions" => "Glosas.codigo_status_glosa = GlosasStatus.codigo"
            ),
            array(
                "table"      => "RHHealth.dbo.tipo_glosas",
                "alias"      => "TipoGlosas",
                "type"       => "LEFT",
                "conditions" => "Glosas.codigo_tipo_glosa = TipoGlosas.codigo"
            ),
            array(
                "table"      => "RHHealth.dbo.classificacao_glosa",
                "alias"      => "ClassificacaoGlosa",
                "type"       => "INNER",
                "conditions" => "Glosas.codigo_classificacao_glosa = ClassificacaoGlosa.codigo"
            ),
            array(
                "table"      => "RHHealth.dbo.itens_pedidos_exames",
                "alias"      => "ItemPedidoExame",
                "type"       => "LEFT",
                "conditions" => "ItemPedidoExame.codigo = Glosas.codigo_itens_pedidos_exames"
            ),
			array(
				'table' => 'RHHealth.dbo.pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
			),
            array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'LEFT',
				'conditions' => 'PedidoExame.codigo_funcionario = Funcionario.codigo',
			),
            array(
                "table"      => "RHHealth.dbo.exames",
                "alias"      => "Exame",
                "type"       => "LEFT",
                "conditions" => "Exame.codigo = ItemPedidoExame.codigo_exame"
            ),
        );

        $conditions = array(
            'Glosas.codigo_nota_fiscal_servico' => $codigo_nota
        );

        $order = 'Glosas.codigo';

        $this->paginate['Glosas'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 10,
            'joins' => $joins,
            'order' => $order
        );

        $dados_glosas = $this->paginate('Glosas');
        $this->set(compact('dados_glosas','codigo_fornecedor', 'codigo_nota'));
    }

     function atualiza_status_glosas($codigo, $status,$codigo_nota){
        
        $this->layout = 'ajax';
        
        $nota_finalizada = $this->NotaFiscalServico->find('first', array('conditions' => array('codigo_nota_fiscal_status' => 5,'codigo' => $codigo_nota)));
        if(!$nota_finalizada){
            $this->data['Glosas']['codigo'] = $codigo;
        
            $this->data['Glosas']['ativo'] = ($status == 1) ? 0 : 1;
    
            if ($this->Glosas->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
                print 1;
            } else {
                print 0;
            }
        }else{
            print 'finalizada';
        }
        

        $this->render(false,false);              
    }

    /**
     * Está tela é para relacionar por Credenciado os Exames emitidos com as Notas Fiscais.
     *
     * @return void
     */
    public function consolida_exames() {
        
        $this->pageTitle = 'Consolidação Nota Fiscal x Exames';

        $this->data[$this->NotaFiscal->name] = $this->Filtros->controla_sessao($this->data, $this->NotaFiscal->name);
    }

    /**
     * listagem de exames consolidados de acordo com Filtros selecionados na consolida_exames
     * 
     */
    public function consolida_exames_listagem() {

    }


    /**
     * No final da listagem deve existir o botão para Consolidar os dados fechando para Edição, 
     * quando acionado deve gravar os dados na tabela consolidando o relacionamento da 
     * Nota Fiscal com o Exame, e atualizando o Status da Nota Fiscal para “Processado”.
     *
     */
    public function consolidar($codigo_nota)
    {
        
    }



    /**
     * Relatorio Exames
     * Este relatório tem o objetivo de apresentar os dados de Exames 
     * que não está consolidado com a Nota Fiscal.
     *
     * @return void
     */
    public function relatorio_exames_sem_nfs() {
        
        $this->pageTitle = 'Relatório Exames Sem Nota Fiscal';

        //filtros setados
        $this->data['NotaFiscalServico'] = $this->Filtros->controla_sessao($this->data, 'NotaFiscalServico');

        //pega todos os meses
        $meses = Comum::anoMes(null, true);

        //seta o mes passado selecionado
        $this->data['NotaFiscalServico']['mes'] = isset($this->data['NotaFiscalServico']['mes']) ? $this->data['NotaFiscalServico']['mes'] : date('m', strtotime('-1 months', strtotime(date('Y-m-d'))));

        //pega o ano corrent
        $this->data['NotaFiscalServico']['ano'] = isset($this->data['NotaFiscalServico']['ano']) ? $this->data['NotaFiscalServico']['ano'] : date('Y');

        $this->set(compact('meses'));

    }


    /**
     * listagem de exames de acordo com Filtros selecionados na relatorio_exames
     * 
     */
    public function relatorio_exames_sem_nfs_listagem($export=null)
    {

        //executado por ajax este metodo
        $this->layout = 'ajax';

        //filtra o resultado
        $filtros = $this->Filtros->controla_sessao($this->data, 'NotaFiscalServico');

        //verifica se existe o codigo do fornecendor para pesquisar
        $dados = array();        
        if(!empty($filtros['codigo_fornecedor'])) {

            //seta para pegar os exames que não estao consolidados
            $filtros['consolidado'] = 2; //nao consolidado
            
            //gera a query para pegar os dados dos exames do fornecedor
            $dados_query = $this->ConsolidadoNfsExame->getDadosNfsExame($filtros);

            //para quando acionar o botao de exportar os dados
            if($export) {
                
                $query = $this->PedidoExame->find('sql',  array(
                        'fields' => $dados_query['fields'],
                        'conditions' => $dados_query['conditions'],
                        'joins' => $dados_query['joins'],
                        'recursive' => -1
                    )
                );
                $this->export_exames_sem_nfs($query);
            }
            
            //monta a query com paginacao
            $this->paginate['PedidoExame'] = array(
                'fields' => $dados_query['fields'],
                'conditions' => $dados_query['conditions'],
                'joins' => $dados_query['joins'],                
                'recursive' => -1
                );

            //executa com paginacao
            $dados = $this->paginate('PedidoExame');
            
        }//fim verificacao

        //seta os dados para a listagem
        $this->set(compact('dados'));

    }//fim relatorio_exames_sem_nfs_listagem

     /**
     * [export_consolidado description]
     * 
     * metodo para exportar os dados do relatorio exames sem nfs
     * 
     * @return [type] [description]
     */
    public function export_exames_sem_nfs($query)
    {

        $dbo = $this->PedidoExame->getDataSource();
        $dbo->results = $dbo->rawQuery($query);

        ob_clean();
        
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="exames_sem_nfs_'.date('YmdHis').'.csv"');

        echo utf8_decode('"Pedido Exame";"Exame";"Cód. Credenciado";"Cód. Cliente";"Cliente";"Valor Custo"'."\n");

        while ($value = $dbo->fetchRow()) {
            
            $linha = $value[0]['codigo_pedido_exame'].';';
            $linha .= $value[0]['exame'].';';
            $linha .= $value[0]['codigo_credenciado'].';';
            $linha .= $value[0]['codigo_cliente'].';';
            $linha .= $value[0]['nome_cliente'].';';
            $linha .= $value[0]['valor_custo'].';';

            echo utf8_decode($linha)."\n";
        }
        die();

    }//fim export_consolidado


    /**
     * Demonstrativo de contas médicas
     * 
     * Este relatório tem o objetivo de apresentar os dados de Exames que foram consolidados e notas fiscais pagas
     *
     * @return void
     */
    public function demonstrativo_contas_medicas() 
    {
        
        $this->pageTitle = 'Demonstrativo Contas Médicas';

        //filtros setados
        $this->data['NotaFiscalServico'] = $this->Filtros->controla_sessao($this->data, 'NotaFiscalServico');

        //seta os status do pagamento
        $status_pagamento = array('' => 'Todos', '1' => 'Pago', '2' => 'Não Pago');

        //pega todos os meses
        $meses = Comum::anoMes(null, true);

        //seta o mes passado selecionado
        $this->data['NotaFiscalServico']['mes'] = isset($this->data['NotaFiscalServico']['mes']) ? $this->data['NotaFiscalServico']['mes'] : date('m');

        //pega o ano corrent
        $this->data['NotaFiscalServico']['ano'] = isset($this->data['NotaFiscalServico']['ano']) ? $this->data['NotaFiscalServico']['ano'] : date('Y');

        $this->set(compact('meses','status_pagamento'));

    }// fim demonstrativo_contas_medicas


    /**
     * listagem das notas fiscais de acordo com Filtros
     * 
     */
    public function demonstrativo_contas_medicas_listagem($export=null)
    {

        //executado por ajax este metodo
        $this->layout = 'ajax';

        //filtra o resultado
        $filtros = $this->Filtros->controla_sessao($this->data, 'NotaFiscalServico');

        //verifica se existe o codigo do fornecendor para pesquisar
        $dados = array();        
        if(!empty($filtros['codigo_fornecedor'])) {


            //gera a query para pegar os dados dos exames do fornecedor
            $dados_query = $this->NotaFiscalServico->get_demonstrativo_nfs($filtros);
            
            //monta a query com paginacao
            $this->paginate['NotaFiscalServico'] = array(
                'fields' => $dados_query['fields'],
                'conditions' => $dados_query['conditions'],
                'joins' => $dados_query['joins'],                
                'recursive' => -1
                );

            //executa com paginacao
            $dados = $this->paginate('NotaFiscalServico');
            
        }//fim verificacao

        //seta os dados para a listagem
        $this->set(compact('dados'));

    }//fim demonstrativo_contas_medicas_listagem


    public function listagem_demonstrativo_contas_medicas($codigo_nfs)
    {

        //titulo da pagina
        $this->pageTitle = 'Detalhes Nota Fiscal';
        $this->layout = 'new_window';
        
        //detalhes da nfs
        $dados = $this->NotaFiscalServico->get_detalhes_nfs($codigo_nfs);

        $this->set(compact('dados'));
    }//fim listagem_demonstrativo_contas_medicas


     /**
     * relatorio de glosas
     * 
     * Este relatório tem o objetivo de apresentar as glosas das notas fiscais
     *
     * @return void
     */
    public function relatorio_glosas() 
    {
        
        $this->pageTitle = 'Relatório Glosas';

        //filtros setados
        $this->data['NotaFiscalServico'] = $this->Filtros->controla_sessao($this->data, 'NotaFiscalServico');

        //seta os status do pagamento        
        $status_glosas = $this->GlosasStatus->find('list',array('fields' => array('codigo','descricao')));

        $this->set(compact('status_glosas'));

    }// fim relatorio_glosas


    /**
     * listagem das notas fiscais de acordo com Filtros
     * 
     */
    public function relatorio_glosas_listagem($export=null)
    {

        //executado por ajax este metodo
        $this->layout = 'ajax';

        //filtra o resultado
        $filtros = $this->Filtros->controla_sessao($this->data, 'NotaFiscalServico');

        // debug($filtros);

        //verifica se existe o codigo do fornecendor para pesquisar
        $dados = array();        
        if(!empty($filtros['codigo_fornecedor'])) {


            //gera a query para pegar os dados dos exames do fornecedor
            $dados_query = $this->Glosas->get_glosas($filtros);
            
            //monta a query com paginacao
            $this->paginate['Glosas'] = array(
                'fields' => $dados_query['fields'],
                'conditions' => $dados_query['conditions'],
                'joins' => $dados_query['joins'],                
                'recursive' => -1
                );

            //executa com paginacao
            $dados = $this->paginate('Glosas');
            
        }//fim verificacao

        //seta os dados para a listagem
        $this->set(compact('dados'));
    }//fim demonstrativo_contas_medicas_listagem


    /**
     * lista de status para a nota fiscal de serviço
     *
     * @return json
     */
    public function obterStatusDisponiveis() {

        $dados = $this->NotaFiscalServico->listaStatusDeNFS();

        return json_encode($dados);
    }

    /**
     * Ação Glosas
     * apresentar as Glosas que foram cadastradas para a 
     * nota fiscal em especifica, em formato de listagem
     *
     * @return json
     */
    public function obterGlosasPorNota( $codigo_nota ) {

        $dados = $this->GlosaMedica->obterListaPorCodigoNota( $codigo_nota );

        return json_encode($dados);
    }


    /**
     * consolida_nfs_exame
     * 
     * filtros iniciais da consolidação da nota fiscal com os exames
     * 
     * @return [type] [description]
     */
    public function consolida_nfs_exame()
    {
        $this->pageTitle = 'Consolidação NFS x Exames'; //titulo da pagia

        $this->data['NotaFiscalServico'] = $this->Filtros->controla_sessao($this->data, 'NotaFiscalServico'); //filtros setados

        //seta os status consolidado
        $consolidado = array(
            '' => 'Todos', 
            ConsolidadoNfsExame::CONCLUIDO => 'Sim', 
            ConsolidadoNfsExame::PENDENTE => 'Não'
        );

        // seta o periodo
        if(empty($this->data['NotaFiscalServico']['data_inicio']) && empty($this->data['NotaFiscalServico']['data_fim'])){
            $this->data['NotaFiscalServico']['data_fim'] = date('d/m/Y');
            $this->data['NotaFiscalServico']['data_inicio'] = '01/'.date('m/Y');
        }
        $this->set(compact('consolidado','meses'));
    }// fim consolida_nfs_exame

    /**
     * reverte_consolidado
     * 
     * metodo para reverter a nota consolidada
     * 
     * @return json responseJson
     */
    public function reverte_consolidado()
    {

        $retorno = array(); // inicializa variavel de retorno
        $params = $this->params['form'];
        
        //pega os dados de paramentros        
        $codigo_item_pedido_exame = Comum::soNumero($params['codigo_item_pedido_exame']);
        $codigo_consolidado_nfs_exame =  Comum::soNumero($params['codigo']);

        if(empty($codigo_item_pedido_exame) || empty($codigo_consolidado_nfs_exame)) {
            $retorno['erro'] = 'Faltam parâmetros para efetuar a reversão de consolidação do exame';
            return $this->responseJson($retorno);
        }

        // obter dados de consolidação
        $dados = $this->ConsolidadoNfsExame->find('first',  array(
            'fields' => array('codigo'),
            'conditions' => array('codigo_item_pedido_exame' => $codigo_item_pedido_exame)
        ));

        // prepara atualização dos dados consolidado revertido
        $dados_atualizar = array(
            'ConsolidadoNfsExame' => array(
                'codigo' => $codigo_consolidado_nfs_exame,
                'status' => ConsolidadoNfsExame::PENDENTE, 
                'codigo_nota_fiscal_servico' => NULL,
                'data_pagamento' => NULL,
                'data_vencimento' => NULL
            )
        );

        // verifica se atualizou os dados
        if(!$this->ConsolidadoNfsExame->atualizar($dados_atualizar)){
            $retorno['erro'] = 'Erro ao atualizar dados consolidado';
            return $this->responseJson($retorno);
        }
            
        $retorno['success'] = "Dados atualizados com sucesso";

        $this->responseJson($retorno);

    }//fim reverte_consolidado

    /**
     * grava_nfs_exame
     * 
     * metodo para gravar o relacionamento nfs_exames
     * 
     * @return json responseJson
     */
    public function grava_nfs_exame()
    {
        $retorno = array();         // variavel de retorno
        $codigo_consolidado = null; // inicializa var para identicar se esta incluindo ou alterando
        
        // valida a requisição deste método
        if(!Comum::postValidator($this->RequestHandler, $validaUsuarioAutenticado = false)){ // TODO: mudar para true
            return $this->responseJsonError('Request inválido');
        }

        $params = $this->params['form']; //pega os dados de paramentros

        $codigo_nota_fiscal_servico = Comum::codigoParamValidator($params, 'codigo_nota_fiscal');
        $codigo_item_pedido_exame = Comum::codigoParamValidator($params, 'codigo_item_pedido_exame');
        $codigo_credenciado = Comum::codigoParamValidator($params, 'codigo_credenciado');  
        $flag_aplicar_todos_exames = isset($params['aplicar_todos_exames']) ? Comum::boolStatusParamValidator($params['aplicar_todos_exames']) : null;
        
        // verifica se todos os paramentros necessários existem
        if(empty($codigo_item_pedido_exame) || empty($codigo_credenciado) || is_null($flag_aplicar_todos_exames)){
            return $this->responseJsonError('Faltam parâmetros para efetuar a consolidação do exame');
        }

        // obter registro de consolidação anterior deste item de pedido
        $consolidadoItemDados = $this->ConsolidadoNfsExame->obterItemConsolidado(
            array('data_inclusao'), // fields para agregar ao resultado
            array('codigo_item_pedido_exame' => $codigo_item_pedido_exame) // conditions
        );
        
        // se existir registro então já ocorreu tentativa de relacionar item de exame com a nota
        if(!empty($consolidadoItemDados)) {
            
            $codigo_consolidado = $consolidadoItemDados['ConsolidadoNfsExame']['codigo'];

            // se o código da Nota foi enviada vazia é provável da caixa de seleção estar em branco("Num Nfs") no onchange
            // pode ter selecionado uma nota por engano e voltar na condição de vazia
            if(empty($codigo_nota_fiscal_servico)){
                
                $codigo_nota_fiscal_servico = $consolidadoItemDados['ConsolidadoNfsExame']['codigo_nota_fiscal_servico'];

                $consolidacaoDados = array(
                    'codigo' => $codigo_consolidado,
                    'codigo_nota_fiscal_servico' => $codigo_nota_fiscal_servico
                );

                // excluo a consolidação
                try {
                    $this->ConsolidadoNfsExame->excluirConsolidacao($consolidacaoDados);
    
                } catch (\Exception $e) {
                    return $this->responseJsonError($e->getMessage());
                }

                $retorno['data'] = array(
                    'codigo_item_pedido_exame' => $codigo_item_pedido_exame,
                    'data_pagamento' => NULL, 
                    'data_vencimento' => NULL
                );

                return $this->responseJson($retorno);
            }
        }
        
        // pega os dados da nfs, a nota é sempre necessária em qualquer situação a partir daqui
        $nfs = $this->NotaFiscalServico->find('first', array(
            'conditions' => array('codigo' => $codigo_nota_fiscal_servico))
        );       

        if(empty($nfs) || !isset($nfs['NotaFiscalServico'])){
            return $this->responseJsonError('Dados da nota fiscal não encontrado');
        }

        $data_pagamento = $nfs['NotaFiscalServico']['data_pagamento'];   // data pagamento da nota fiscal
        $data_vencimento = $nfs['NotaFiscalServico']['data_vencimento']; // data vencimento da nota fiscal
        $numero_nota_fiscal = $nfs['NotaFiscalServico']['numero_nota_fiscal'];

        /**
         * Se existe codigo_consolidado então Item pedido exame "JÁ" foi relacionado com alguma nota
         * se já ocorreu um relacionamento de item de exame com a nota fiscal então deve apenas atualizar
         */
        if(!empty($codigo_consolidado)){
            
            // prepara os dados para atualizar consolidado
            $consolidacaoDados = array(
                'codigo' => $codigo_consolidado, // para atualizar
                'codigo_nota_fiscal_servico' => $codigo_nota_fiscal_servico,
                'data_pagamento' => $data_pagamento,
                'data_vencimento' => $data_vencimento
            );

            try {
                
                $this->ConsolidadoNfsExame->atualizarConsolidacao($consolidacaoDados);

            } catch (\Exception $e) {
                return $this->responseJsonError($e->getMessage());
            }

            $retorno['data'] = array(
                'codigo_item_pedido_exame' => $codigo_item_pedido_exame,
                'data_pagamento' => $data_pagamento, 
                'data_vencimento' => $data_vencimento
            );

            // se foi enviado boolean verdadeiro é porque houve um aceite no modal "Aplicar o número de Nota Fiscal aos exames deste pedido"
            if($flag_aplicar_todos_exames){
                
                try {

                    $examesDados = $this->ConsolidadoNfsExame->atualizarExamesParaConsolidacaoPorCredenciado($codigo_credenciado, $codigo_nota_fiscal_servico);

                    $retorno['data']['exames_atualizados'] = $examesDados;

                } catch (\Exception $e) {
                    return $this->responseJsonError($e->getMessage());
                }
            }

            return $this->responseJson($retorno);
        }

        /**
         * Se não existe codigo_consolidado, Item pedido exame "NUNCA" foi relacionado com alguma nota
         * devo incluir este relacionamento
         */
        $consolidacaoDados = array(
            'codigo_nota_fiscal_servico' => $codigo_nota_fiscal_servico,
            'codigo_fornecedor' => $codigo_credenciado,
            'codigo_item_pedido_exame' => $codigo_item_pedido_exame,
            'data_vencimento' => $data_vencimento,
            'data_pagamento' => $data_pagamento,
        );

        try {
                
            $this->ConsolidadoNfsExame->incluirConsolidacao($consolidacaoDados);

        } catch (\Exception $e) {
            return $this->responseJsonError($e->getMessage());
        }

        $retorno['data'] = array(
            'codigo_item_pedido_exame' => $codigo_item_pedido_exame,
            'data_pagamento' => $data_pagamento, 
            'data_vencimento' => $data_vencimento,
            'numero_nota_fiscal' => $numero_nota_fiscal
        );
        
        // avalio se existem exames para apresentar no modal de escolher "Aplicar o número de Nota Fiscal aos exames deste pedido"
        $examesDados = $this->ConsolidadoNfsExame->obterExamesParaConsolidacaoPorCredenciado($codigo_credenciado);
       
        if(is_array($examesDados) && count($examesDados)>0){
            $retorno['data']['exames'] = $examesDados;
        }

        $this->responseJson($retorno);
    }


    

    /**
     * consolida_dados
     *    
     * metodo para consolidar os dados mudando o status da nota fiscal para processada
     * 
     * @return json responseJson
     */
    public function consolida_dados()
    {

        $retorno = array(); // inicializa variavel de retorno
        
        $params = $this->params['form']; //pega os dados de paramentros
        
        $codigos = $params['codigos']; //pega os dados de paramentros
        
        if(empty($codigos)) {
            return $this->responseJsonError('Faltam parâmetros para efetuar a consolidação do exame. Por favor verifique se foi selecionado corretamente uma ou mais Notas Fiscais.');
        }

        $array_codigos = explode(";",$codigos); //separa os codigos
        
        array_pop($array_codigos); //retirar o ultimo indice do array
        
        $codigos = array(); //seta a variavel auxiliar
        $valor_exame = array();
        $valor_nfs = array();

        //retornos com erros
        $return_consolidado = true;
        $return_nfs = true;
        $nfs_atualizada = false;

        //varre o array gerador
        foreach ($array_codigos as $val) {
        
            //separa os dados indice 0 codigo da nota fiscal, indice 1 codigo do item pedido de exame
            $codigos = explode("|",$val);

            //codigos para trocar o status
            $codigo_nfs = $codigos[0];
        
            $codigo_ipe = $codigos[1];

            //pega o valor_custo dos exames para exibir no alerta
            $dados = $this->ConsolidadoNfsExame->getDadosConsolidadoNfs($codigo_ipe, $codigo_nfs);
           
            //verifica se existem dados
            if(!empty($dados)) {

                if(!isset($valor_exame[$codigo_nfs])) {
        
                    $valor_exame[$codigo_nfs] = '0';
                }

                //pega os valores
                $valor_exame[$codigo_nfs] = $valor_exame[$codigo_nfs] + $dados['ConsolidadoNfsExame']['valor'];
        
                $valor_nfs[$codigo_nfs] = array( 
                    'numero_nota_fiscal' => $dados['NotaFiscalServico']['numero_nota_fiscal'], 
                    'valor' => $dados['NotaFiscalServico']['valor']
                );

                // atualiza o status para consolidado 
                $consolidado = array(
                    'ConsolidadoNfsExame' => array(
                        'codigo' => $dados['ConsolidadoNfsExame']['codigo'],
                        'status' => ConsolidadoNfsExame::CONCLUIDO,
                    )
                );

                //atualizado
                if(!$this->ConsolidadoNfsExame->atualizar($consolidado)) {
        
                    $return_consolidado = false;
                }//fim consolidado

                // verifica se ja atualizou a nfs e altera para status 4 - processamento parcial
                if(!$nfs_atualizada) {
        
                    $nfs = array(
                        'NotaFiscalServico' => array(
                            'codigo' => $codigo_nfs,
                            'codigo_nota_fiscal_status' => NotaFiscalStatus::EM_ANALISE
                        )
                    );
        
                    if(!$this->NotaFiscalServico->atualizar($nfs)) {
        
                        $return_nfs = false;
                    }
                    else {
        
                        $nfs_atualizada = true;
                        
                        $this->Glosas->enviarNotificacaoGlosaAoPrestador($codigo_nfs);
                    }
                }//fim nfs_atualizada
            }//fim dados        
        }//fim foreach
        
        if(!empty($valor_nfs) && $return_consolidado) {

            //varre os dados para gerar a mensagem de retorno
            $msg = "";
            
            foreach ($valor_nfs as $num_nfs => $valor) {
                $this->log('$valor >> '.print_r($valor, true), 'debug');
                
                $msg .= "Nº NFS: " . $valor['numero_nota_fiscal'] . " Valor R$" . $valor['valor'] . ", Exames Consolidados Valor R$" . $valor_exame[$num_nfs];
            }
            $retorno = array('success' => $msg);
        }
        else {
             $retorno['erro'] = 'Erro ao atualizar Consolidar dados';
        }

        $this->responseJson($retorno);
    }//fim consolida_dados

    /**
     * export_consolidado
     * 
     * metodo para exportar os dados do relatorio
     * 
     * @return [type] [description]
     */
    public function export_consolidado($query)
    {

        $dbo = $this->PedidoExame->getDataSource();
        
        $dbo->results = $dbo->rawQuery($query);

        ob_clean();
        
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="consolidado_'.date('YmdHis').'.csv"');


        echo utf8_decode('"Número NFS";"Consolidado";"Pedido Exame";"Exame";"Data Realização";"Valor Custo";"Funcionário";"CPF Funcionário";"Cód. Credenciado";"Razão Social Credenciado";"CNPJ Credenciado";"Nome Fantasia Credenciado";"Cód. Cliente";"Cliente";"Data Vencimento";"Data Pagamento";"Data Baixa"'."\n");

        while ($value = $dbo->fetchRow()) {
            
            $linha = $value[0]['numero_nfs'].';';
            $linha .= (($value[0]['status_consolidado'] == ConsolidadoNfsExame::CONCLUIDO) ? 'Sim':'Não').';';
            $linha .= $value[0]['codigo_pedido_exame'].';';
            $linha .= $value[0]['exame'].';';
            $linha .= AppModel::dbDateToDate($value[0]['data_realizacao']).';';
            $linha .= $value[0]['valor_custo'].';';
            $linha .= $value[0]['funcionario_nome'].';';
            $linha .= Comum::formatarDocumento($value[0]['funcionario_cpf']).';';
            $linha .= $value[0]['codigo_credenciado'].';';
            $linha .= $value[0]['credenciado_razao_social'].';'; 
            $linha .= Comum::formatarDocumento($value[0]['credenciado_cnpj']).';'; 
            $linha .= $value[0]['credenciado_nome_fantasia'].';';
            $linha .= $value[0]['codigo_cliente'].';';
            $linha .= $value[0]['nome_cliente'].';';
            $linha .= AppModel::dbDateToDate($value[0]['data_vencimento_nfs']).';';
            $linha .= AppModel::dbDateToDate($value[0]['data_pagamento_nfs']).';';
            $linha .= AppModel::dbDateToDate($value[0]['data_baixa']).';';
            
            echo utf8_decode($linha)."\n";
        }
        die();
    }//fim export_consolidado

    public function export_lista_notas_fiscais($query){

        //instancia o dbo
        $dbo = $this->NotaFiscalServico->getDataSource();
        
        //pega todos os resultados
        $dbo->results = $dbo->rawQuery($query);

        //headers
        ob_clean();

        //$relatorio_padrao_encoding =  'UTF-8';   // UTF funciona, mas exigiu conversão UTF pelo programa usado LibreOffice
        $relatorio_padrao_encoding =  'ISO-8859-1'; // conforme importação padrão sugerida no LibreOffice ISO-8859-1 funcionou bem para 
                                                    // Windows 1252/WinLatin 1 
                                                    // Windows 1250/WinLatin 2
                                                    // ISO-8859-15/EURO
                                                    // ISO-8859-14
                                                    // ASCII/Inglês Norte Americano
                                                    // Europa oriental ISO 8859-2
                                                    // Turco (ISO 8859-9)
                                                    // Turco (Windows-1254)
                                                    // Vietnamita (Windows-1258)
                                                    // Sistema, Caso o sistema operacional seja Português Brasil


        header('Content-Encoding: '.$relatorio_padrao_encoding);
        header("Content-Type: application/force-download;charset=".$relatorio_padrao_encoding);
        header('Content-Disposition: attachment; filename="notas_fiscais_servico.csv"');
        header('Pragma: no-cache');

        //cabecalho do arquivo
        echo Comum::converterEncodingPara('"Código Credenciado";"Razão Social";"Nome Fantasia";"CNPJ";"Numero Nota Fiscal";"Tipo de Recebimento";"Data Recebimento";"Data Emissão";"Data Vencimento";"Data Pagamento";"Código Rastreamento";"Forma de Pagamento";"Data Baixa Boleto";"Descrição Baixa Boleto";"Acréscimo";"Descrição Acréscimo";"Desconto";"Descrição Desconto";"Status";"Valor NFS";"Tipo Serviço NFS";"Usuário Inserção";"Data Inserção";"Data Liberação";"Responsável";"Observação";"Prestador Qualificado";"Faturamento Dias";"Anexo";', $relatorio_padrao_encoding)."\n";
        
        // varre todos os registros da consulta no banco de dados
        while($lista_nfs = $dbo->fetchRow()){

            $linha  = '';

            $linha .= $lista_nfs['NotaFiscalServico']['codigo_fornecedor'].';';
            $linha .= Comum::converterEncodingPara(trim($lista_nfs['Fornecedor']['razao_social']), $relatorio_padrao_encoding).';';
            $linha .= Comum::converterEncodingPara(trim($lista_nfs['Fornecedor']['nome']), $relatorio_padrao_encoding).';';
            $linha .= Comum::formatarDocumento($lista_nfs['Fornecedor']['codigo_documento']).';';
            $linha .= $lista_nfs['NotaFiscalServico']['numero_nota_fiscal'].';';
            $linha .= Comum::converterEncodingPara(trim($lista_nfs['TipoRecebimento']['descricao']), $relatorio_padrao_encoding).';';
            $linha .= $lista_nfs['NotaFiscalServico']['data_recebimento'].';';
            $linha .= $lista_nfs['NotaFiscalServico']['data_emissao'].';';
            $linha .= $lista_nfs['NotaFiscalServico']['data_vencimento'].';';
            $linha .= $lista_nfs['NotaFiscalServico']['data_pagamento'].';';
            $linha .= trim($lista_nfs['NotaFiscalServico']['chave_rastreamento']).';';
            $linha .= Comum::converterEncodingPara(trim($lista_nfs['FormaPagto']['descricao']), $relatorio_padrao_encoding).';';
            $linha .= $lista_nfs['NotaFiscalServico']['baixa_boleto_data'].';';
            $linha .= Comum::converterEncodingPara(trim($lista_nfs['NotaFiscalServico']['baixa_boleto_descricao']), $relatorio_padrao_encoding).';';

            $flagAcrescimo = (isset($lista_nfs['NotaFiscalServico']['flag_acrescimo']) && !empty($lista_nfs['NotaFiscalServico']['flag_acrescimo'])) ? 'Sim' : 'Não';
            $flagDesconto = (isset($lista_nfs['NotaFiscalServico']['flag_desconto']) && !empty($lista_nfs['NotaFiscalServico']['flag_desconto'])) ? 'Sim' : 'Não';

            $linha .= Comum::converterEncodingPara(trim($flagAcrescimo), $relatorio_padrao_encoding).';';
            $linha .= Comum::converterEncodingPara(trim($lista_nfs['NotaFiscalServico']['descricao_acrescimo']), $relatorio_padrao_encoding).';';
            $linha .= Comum::converterEncodingPara(trim($flagDesconto), $relatorio_padrao_encoding).';';
            $linha .= Comum::converterEncodingPara(trim($lista_nfs['NotaFiscalServico']['descricao_desconto']), $relatorio_padrao_encoding).';';
            $linha .= Comum::converterEncodingPara(trim($lista_nfs['NotaFiscalStatus']['descricao']), $relatorio_padrao_encoding).';';
            $linha .= $lista_nfs[0]['valor'].';';

            $linha .= Comum::converterEncodingPara(trim($lista_nfs['TipoServicosNfs']['descricao']), $relatorio_padrao_encoding).';';
            $linha .= $lista_nfs['Usuarios']['nome'].';';
            
            $data_inclusao = DateTime::createFromFormat('Y-m-d H:i:s',$lista_nfs['NotaFiscalServico']['data_inclusao']);  
            
            $linha .= $data_inclusao->format('d/m/Y').';';





            $data_liberacao = strtotime(str_replace('/', '-',$lista_nfs['NotaFiscalServico']['liberacao_data']));
            $data_corte = strtotime('2021-09-04');



            if(($data_liberacao < $data_corte) && (is_null($lista_nfs['NotaFiscalServico']['data_conclusao']))){
                $linha .= $lista_nfs['NotaFiscalServico']['liberacao_data'].';';
                $linha .= $lista_nfs['UsuarioAuditoria2']['nome'].';';
            }else{
                $linha .= $lista_nfs['NotaFiscalServico']['data_conclusao'].';';
                $linha .= $lista_nfs['UsuarioAuditoria']['nome'].';';
            }
            
            $linha .= Comum::converterEncodingPara(trim(preg_replace("/\r|\n/", "", $lista_nfs['NotaFiscalServico']['observacao'])), $relatorio_padrao_encoding).';';

            $temAnexo = (isset($lista_nfs['AnexoNotaFiscalServico']['caminho_arquivo']) && !empty($lista_nfs['AnexoNotaFiscalServico']['caminho_arquivo'])) ? 'Sim' : 'Não';
            $prestadorQualificado = (isset($lista_nfs['Fornecedor']['prestador_qualificado']) && !empty($lista_nfs['Fornecedor']['prestador_qualificado'])) ? 'Sim' : 'Não';
            $faturamentoDias = (isset($lista_nfs['Fornecedor']['faturamento_dias']) && !empty($lista_nfs['Fornecedor']['faturamento_dias'])) ? 'Sim' : 'Não';

            $linha .= Comum::converterEncodingPara(trim($prestadorQualificado), $relatorio_padrao_encoding).';';
            $linha .= Comum::converterEncodingPara(trim($faturamentoDias), $relatorio_padrao_encoding).';';
            $linha .= Comum::converterEncodingPara(trim($temAnexo), $relatorio_padrao_encoding).';';
            
            $linha .= "\n";
            
            echo $linha;
            

        }//fim while
        
        //mata o metodo
        die();
    }//fim export_lista_notas_fiscais


	/**
	 * Função para auxilio na pré-validação do arquivo binario de upload
	 * 
	 * - valida nome, se não existir invalida o processo
	 * - valida tamanho
	 * - valida extensão de arquivo
	 *
	 * payload esperado em $binaryFile é o de um campo FILE em modo binario, form-data
	 * 
	 * $binaryFile = array
	 * (
	 *   [name] => cakephp-2.x.zip
	 *   [type] => application/x-zip-compressed
	 *   [tmp_name] => /tmp/php6Uaad5
	 *   [error] => 0
	 *   [size] => 2254548
	 * )
	 * 
	 * @param binary $binaryFile
	 * @return bool
	 */
	public function validaBinario( $binaryFile = null)
	{
		// ver se nome é válido
		if (!isset($binaryFile['name']) || empty($binaryFile['name'])){
			$this->AnexoNotaFiscalServico->invalidate('anexo_nota_fiscal_servico', 'Arquivo inválido');	
			return false;
		}

		// valida tamanho do arquivo
		if(!isset($binaryFile['size']) || $binaryFile['size'] > self::UPLOAD_ARQUIVO_TAMANHO_MAX){
			$this->AnexoNotaFiscalServico->invalidate('anexo_nota_fiscal_servico', 'Tamanho de arquivo inválido');	
			return false;
		}

		// valida tipo do arquivo
		$arquivoExtensao = strtolower(end(explode('.', $binaryFile['name'])));
		
		$tiposAceitos = self::UPLOAD_ARQUIVO_MIMETYPES_ACEITOS;

		if(!empty($tiposAceitos) && strstr($tiposAceitos,"|") )
		{
			$tiposAceitos = explode('|', $tiposAceitos);
			
			if(!in_array($arquivoExtensao, $tiposAceitos)){
				$this->AnexoNotaFiscalServico->invalidate('anexo_nota_fiscal_servico', 'Tipo de arquivo inválido');	
				return false;
			}
		} else {
			if($arquivoExtensao != $tiposAceitos){
				$this->AnexoNotaFiscalServico->invalidate('anexo_nota_fiscal_servico', 'Tipo de arquivo inválido');	
				return false;
			}
		}
		return true;

	}


	/**
	 * Faz o processo de salvar o upload do arquivo no file server e registra na tabela desta model retornando o codigo
	 *
	 * @param integer $codigo  | se código for nulo então o método entende que irá criar novo registro
	 * 
     * @param binary $binaryFile
	 * (
	 *    [name] => cakephp-2.x.zip
	 *    [type] => application/x-zip-compressed
	 *    [tmp_name] => /tmp/php6Uaad5
	 *    [error] => 0
	 *    [size] => 2254548
	 * )
	 * 
     * @param integer $codigo_nota_fiscal_servico
     * 
	 * @return array
	 */
	public function salvarArquivo( $codigo = null, $binaryFile = null, $codigo_nota_fiscal_servico = null)
	{        
        $data = array();
        
		if( is_null($binaryFile) || empty($binaryFile))
		{
			$this->log('['.__LINE__.'] :: '.__CLASS__ .' -> '.__METHOD__.' :: '.'Arquivo não encontrado ou inválido', 'debug');
			return array('error' => 'Arquivo não encontrado ou inválido');
        }
        
		if( is_null($codigo_nota_fiscal_servico) || empty($codigo_nota_fiscal_servico))
		{
			$this->log('['.__LINE__.'] :: '.__CLASS__ .' -> '.__METHOD__.' :: '.'Código da nota fiscal não informada', 'debug');
			return array('error' => 'Código da nota fiscal não informada');
        }


		$this->Upload->setOption('size_max', self::UPLOAD_ARQUIVO_TAMANHO_MAX);
		$this->Upload->setOption('size_max_message', sprintf('Tamanho máximo de 5 Megabytes foi excedido!'));
		$this->Upload->setOption('accept_extensions', array('jpg','png','jpeg','pdf')); 
		$this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! É aceito extensões pdf, jpg, jpeg ou png. Por favor tente novamente.');
        
        $retorno = $this->Upload->fileServer( array('file'=>$binaryFile) );

        $this->log('Linha['.__LINE__.']'.__CLASS__ .' > '.__METHOD__.' >> '.print_r($retorno, true), 'debug');
        /**
         * Array
                (
                    [data] => Array
                        (
                            [notas_fiscais_servico.pdf] => Array
                                (
                                    [path] => /ithealth/2020/12/28/ED3AC71A-2951-FB92-65B7-886D7AF70233.pdf
                                    [path_url] => https://api.rhhealth.com.br/ithealth/2020/12/28/ED3AC71A-2951-FB92-65B7-886D7AF70233.pdf
                                    [message] => Upload do arquivo [notas_fiscais_servico.pdf] feito com sucesso.
                                )

                        )

                )
         */
        if (isset($retorno['error']) && !empty($retorno['error']) )
		{
			return array('error' => $retorno['error']);
		}

        // arquivo
        $retornoArquivo = $retorno['data'][$binaryFile['name']];
        

        if(!empty($codigo)){
            $data['AnexoNotaFiscalServico']['codigo'] = $codigo;
            $data['AnexoNotaFiscalServico']['data_alteracao'] = Comum::now();
            $data['AnexoNotaFiscalServico']['codigo_usuario_alteracao'] = Comum::codigoUsuarioAutenticado();

        } else {
            $data['AnexoNotaFiscalServico']['ativo'] = 1;
            $data['AnexoNotaFiscalServico']['data_inclusao'] = Comum::now();
            $data['AnexoNotaFiscalServico']['codigo_usuario_inclusao'] = Comum::codigoUsuarioAutenticado();
    
        }

        $data['AnexoNotaFiscalServico']['caminho_arquivo'] = $retornoArquivo['path'];
        $data['AnexoNotaFiscalServico']['codigo_nota_fiscal_servico'] = $codigo_nota_fiscal_servico;
        $data['AnexoNotaFiscalServico']['descricao'] = $binaryFile['name'];
        $data['AnexoNotaFiscalServico']['codigo_tipo_anexo_nota_fiscal_servico'] = $binaryFile['codigo_tipo_anexo_nota_fiscal_servico'];

        $this->AnexoNotaFiscalServico->set( $data );
        
        if (!$this->AnexoNotaFiscalServico->validates()) {
            
            $errors = $this->AnexoNotaFiscalServico->invalidFields();
            $this->log('Linha['.__LINE__.']'.__CLASS__ .' > '.__METHOD__.' >> '.print_r($errors, true), 'debug');
            return array('error'=>$errors);
        }
        if($binaryFile['codigo_tipo_anexo_nota_fiscal_servico'] == 1){
            $buscar_anexo = $this->AnexoNotaFiscalServico->find('first', array('conditions' => array('codigo_nota_fiscal_servico' => $data['AnexoNotaFiscalServico']['codigo_nota_fiscal_servico'],'codigo_tipo_anexo_nota_fiscal_servico' => array($binaryFile['codigo_tipo_anexo_nota_fiscal_servico'], null))));
        }else{
            $buscar_anexo = $this->AnexoNotaFiscalServico->find('first', array('conditions' => array('codigo_nota_fiscal_servico' => $data['AnexoNotaFiscalServico']['codigo_nota_fiscal_servico'],'codigo_tipo_anexo_nota_fiscal_servico' => $binaryFile['codigo_tipo_anexo_nota_fiscal_servico'])));
        }
        $this->log($buscar_anexo,'testeatualizaranexo');

        // ce ja tiver anexo, atualizar
		if($buscar_anexo)
		{
            $data['AnexoNotaFiscalServico']['codigo'] = $buscar_anexo['AnexoNotaFiscalServico']['codigo'];//seta o codigo
			if(!$this->AnexoNotaFiscalServico->atualizar($data)){
                $errors = $this->AnexoNotaFiscalServico->invalidFields();
                $this->log('Linha['.__LINE__.']'.__CLASS__ .' > '.__METHOD__.' >> '.print_r($errors, true), 'debug');
				return array('error' => 'Não foi possível salvar arquivo no banco de dados');
			};

		} else {

			if(!$this->AnexoNotaFiscalServico->incluir($data)){
                $errors = $this->AnexoNotaFiscalServico->invalidFields();
                $this->log('Linha['.__LINE__.']'.__CLASS__ .' > '.__METHOD__.' >> '.print_r($errors, true), 'debug');
				return array('error' => 'Não foi possível salvar arquivo no banco de dados');
			};

			$codigo = $this->AnexoNotaFiscalServico->getLastInsertID();
		}
        $this->log($codigo,'testeatualizaranexo');
		
		return array(
			'data' => array(
			'path' => $retornoArquivo['path'],
			'url' => $retornoArquivo['path_url'],
			'message'=>'Arquivo atualizado com sucesso',
			'codigo_anexo'=>$codigo // codigo do anexo

		));
	
	}

    /**
     * [obter_nova_data metodo para calcular a nova data de vencimento]
     * @param  [type] $data_vencimento [description]
     * @return [type]                  [description]
     */
    public function obter_nova_data($data_vencimento) 
    {
        $this->layout = 'ajax';
        $data = array();
        $codigo_empresa = $this->BAuth->user('codigo_empresa');

        $retorna_dias = $this->DiaPagamento->find('all', array(
                'recursive'=>-1,
                'fields'=>array('dia'),
                'conditions'=> array('codigo_empresa'=>$codigo_empresa,'ativo' => '1'),
                'order' => 'DiaPagamento.dia ASC'
            ));

        //verifica se tem valor de dias
        if(!empty($retorna_dias)) {
            //separa a data de vencimento
            $arr_data = explode("-",$data_vencimento);

            //descobrir qual é o ultimo dia do mes da data que está passando
            $ultimo_dia_mes = date("t", mktime(0,0,0,$arr_data[1],$arr_data[2],$arr_data[0]));
            if($arr_data[2] == $ultimo_dia_mes) {

                //verifico se a nova data é do mes de fevereiro e ultimo dia do mes
                if($arr_data[1] == '02') {
                    //seta o ultimo dia do mes
                    $arr_data[2] = $ultimo_dia_mes;
                }
                else {
                    //ao descobrir calcular para o primeiro dia configurado para pagamento do fornecedor
                    $dias = $retorna_dias[0]['DiaPagamento']['dia'];

                    $nova_data = date('Y-m-d', strtotime('+'.$dias.' days', strtotime($data_vencimento)));
                    $arr_data = explode("-",$nova_data);
                }//fim else
                

            }//fim verificacao se é o ultimo dia do mes
            else {
                //varre os dias para saber se o dia que está passando do mes está proximo de qual data que deve agendar o vencimento do pagamento da nota
                foreach($retorna_dias AS $dias) {

                    $dia = $dias['DiaPagamento']['dia'];
                    if($arr_data['2'] <= $dia) {
                        $arr_data['2'] = $dia;
                        break;
                    }

                }//fim foreach
            }//fim verificacao else se é o ultimo dia do mes

            // debug($arr_data);

            if($arr_data[1] == '02' && $arr_data[2] == '30') {
                $ultimo_dia_mes = date("t", mktime(0,0,0,$arr_data[1],'01',$arr_data[0]));
                $arr_data[2] = $ultimo_dia_mes;
            }

            // debug($arr_data);exit;

            $data['data_vencimento'] = $arr_data['2']."/".$arr_data['1']."/".$arr_data['0'];

        }//fim retorna_dias

        return $this->responseJson($data);

    }//fim data_vencimento
    
	/**
	 * Obter código da NFe por chave de rastreamento ou número da Nfe
	 *
	 * @return responseJson
	 */
	public function obter_codigo_nfe()
    {
        $this->layout = 'ajax';
        $this->autoLayout = false;
		$this->autoRender = false;
		
		if(!isset($this->RequestHandler->params['url']) ){
			throw new Exception("Request inválido", 1); exit;
		}
        
        $conditions = array();

		$params = $this->RequestHandler->params['url'];

        // por chave Nfe
        if(isset($params['chave']))
        {
            if(empty($params['chave']) || !Validation::alphaNumeric($params['chave'])){
				$data['error'] = 'Chave inválida';
				return $this->responseJson($data);
			}
		
			// if(strlen($params['chave']) <= 40){
			// 	$data['error'] = 'Quantidade de caracteres insuficientes';
			// 	return $this->responseJson($data);
            // }
            
            $chave_rastreamento = trim($params['chave']);
            $conditions['chave_rastreamento'] = $chave_rastreamento;
        }
        
        // por número da NFe
        if(isset($params['numero']))
        {
            if(empty($params['numero']) || !Validation::numeric($params['numero'])){
				$data['error'] = 'Número NFe inválida';
				return $this->responseJson($data);
			}

            $numero = $params['numero'];
            $conditions['numero_nota_fiscal'] = $numero;
        }

        $data = array('data' => null);
    
        $NotaFiscalServicoData = $this->NotaFiscalServico->find('first', array('conditions' => $conditions));

		if(!empty($NotaFiscalServicoData))
		{
            if(isset($NotaFiscalServicoData['NotaFiscalServico']['codigo'])){
                $codigo_nfe = $NotaFiscalServicoData['NotaFiscalServico']['codigo'];
                $codigo_nota_fiscal_status = $NotaFiscalServicoData['NotaFiscalServico']['codigo_nota_fiscal_status'];
                $ativo = $NotaFiscalServicoData['NotaFiscalServico']['ativo'];
                $data_emissao = $NotaFiscalServicoData['NotaFiscalServico']['data_emissao'];
                $numero_nota_fiscal = $NotaFiscalServicoData['NotaFiscalServico']['numero_nota_fiscal'];
                $data['data'] = array(
                    'codigo' => $codigo_nfe,
                    'codigo_nota_fiscal_status' => $codigo_nota_fiscal_status, 
                    'data_emissao' => $data_emissao,
                    'numero_nota_fiscal' => $numero_nota_fiscal,
                    'ativo' => $ativo
                );
            }
		}

		return $this->responseJson($data);
    }

    public function log_nfs($codigo_nfs){

        $this->pageTitle = 'Log Nota Fiscal Servico';
        $this->layout    = 'new_window';
        //tipo
        $tipo = "log_nfs";

        //busca o log
        $dados = $this->NotaFiscalServicoLog->log_nfs($codigo_nfs);
        
        //setar para a view
        $this->set(compact('codigo_nfs','dados'));
    }

    private function usuarioGestorOperacao(){
        $this->load->model('Usuario');
        return $this->Usuario->obterFlagGestorOperacoes();
    }
    
    public function consolida_nfs_exame_listagem(){
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, 'NotaFiscalServico');

        if(empty($filtros['data_inicio']) && empty($filtros['data_fim'])){
            $filtros['data_fim'] = date('d/m/Y');
            $filtros['data_inicio'] = '01/'.date('m/Y');
        }

        if(!empty($filtros['codigo_fornecedor']) || !empty($filtros['codigo_pedido_exame']) || !empty($filtros['cpf_funcionario']) || !empty($filtros['numero_nota_fiscal'])){
            //monta a query que busca as notas fiscais na tabela de consolidação
            
            $query_notas_fiscais = $this->ConsolidadoNfsExame->getNotasFiscaisConsolidadas($filtros);
            

            $this->paginate['ConsolidadoNfsExame'] = array(
                'fields' => $query_notas_fiscais['fields'],
                'conditions' => $query_notas_fiscais['conditions'],
                'joins' => $query_notas_fiscais['joins'],
            );


            
            $dados = $this->ConsolidadoNfsExame->find('all',$this->paginate['ConsolidadoNfsExame']);
            //fim da primeira query

            //Trazendo a query
            $filtros['consolidado'] = 2;
            $query_notas_fiscais = $this->ConsolidadoNfsExame->getExamesNaoConsolidados($filtros);
            $this->paginate['ItemPedidoExame'] = array(
                'fields' => $query_notas_fiscais['fields'],
                'conditions' => $query_notas_fiscais['conditions'],
                'limit' => 50,
                'joins' => $query_notas_fiscais['joins'],
            );

            //Executando
            $nao_consolidadas1 = $this->ItemPedidoExame->find('all',$this->paginate['ItemPedidoExame']);
            //monta os arrays de acordo com o fornecedor
            $array_fornecedores = array();
            $nao_consolidadas = array();
            if(!empty($nao_consolidadas1)) {
                foreach($nao_consolidadas1 as $key1 => $nao_consolidada){
                    $array_fornecedores[$nao_consolidada[0]['codigo_credenciado']]['razao_social_forn'] = $nao_consolidada[0]['credenciado_razao_social'];
                    $nao_consolidadas[$nao_consolidada[0]['codigo_credenciado']][] = $nao_consolidada[0];
                }
            }
        }else if(empty($filtros)){
            $dados = 'erro2';
        }else{
            $dados = 'erro';
        }

        $this->set(compact('dados','nao_consolidadas','array_fornecedores'));
    }

    public function exibir_exames_consolidados($codigo_nf, $retorno = null)
    {
        $this->layout = 'ajax'; 

        //condição para trazer apenas notas fiscais consolidadas
        $conditions = array();
        $conditions['ConsolidadoNfsExame.status'] = 1;
        $conditions['ConsolidadoNfsExame.codigo_nota_fiscal_servico'] = $codigo_nf;
        
        //Trazendo a query
        $query_notas_fiscais = $this->ConsolidadoNfsExame->getDadosNfsExame();

        $this->paginate['PedidoExame'] = array(
            'fields' => $query_notas_fiscais['fields'],
            'conditions' => $conditions,
            'joins' => $query_notas_fiscais['joins'],
        );
        //Executando
        $retornos = $this->PedidoExame->find('all',$this->paginate['PedidoExame']);

        $retornos_formatado = $this->formataExames($retornos);

        if($retornos_formatado){
            $dados['consolidadas'] = $retornos_formatado; 
        }else{
            $dados = 'erro';
        }

        if($retorno){
            return $dados;
        }else{
            echo json_encode($dados);
        }

    }    
    
    public function modal_consolidar()
    {
        $this->layout = 'ajax'; 

        //pegando os codigos válidos
        $codigos = array();
        foreach($_POST['codigo'] as $codigo){
            if($codigo){
                array_push($codigos,$codigo);
            }            
        }

        //pegando o codigo do fornecedor
        $codigo_fornecedor = $_POST['codigo_fornecedor'];

        //pega os dados do item
   		$fornecedor = $this->Fornecedor->find('first',array('fields' => array('tipo_unidade','codigo','codigo_fornecedor_fiscal'),'conditions'=> array('codigo' => $codigo_fornecedor)));

        //verificando se o fornecedor é operacional, se sim ele irá listar notas fiscais da filial e da matriz(PC-2647 - Matheus Brum)
		$array_codigo_fornecedores = array();
		array_push($array_codigo_fornecedores,$fornecedor['Fornecedor']['codigo']);
		if($fornecedor['Fornecedor']['tipo_unidade'] == 'O'){
			//atribuindo o codigo do fornecedor matriz ao array
			array_push($array_codigo_fornecedores, $fornecedor['Fornecedor']['codigo_fornecedor_fiscal']);
		}

        //NOTAS POR FORNECEDOR
        $query_notas_fiscais = $this->NotaFiscalServico->notas_por_fornecedor($array_codigo_fornecedores);

        $this->paginate['NotaFiscalServico'] = array(
            'fields' => $query_notas_fiscais['fields'],
            'conditions' => $query_notas_fiscais['conditions'],
        );

        $notas_fornecedor = $this->NotaFiscalServico->find('all',$this->paginate['NotaFiscalServico']);

        //REGISTRO DE PEDIDOS DE EXAMES A CONSOLIDAR
        $condicoes['codigo_item_pedido_exame'] = $codigos;
        $query_notas_fiscais = $this->ConsolidadoNfsExame->getExamesNaoConsolidados($condicoes);
        $this->paginate['ItemPedidoExame'] = array(
            'fields' => $query_notas_fiscais['fields'],
            'conditions' => $query_notas_fiscais['conditions'],
            'limit' => 50,
            'joins' => $query_notas_fiscais['joins'],
        );

        $nao_consolidadas = $this->ItemPedidoExame->find('all',$this->paginate['ItemPedidoExame']);
        $dados = array();
        $dados['nao_consolidadas'] = $nao_consolidadas;
        $dados['notas_fornecedor'] = $notas_fornecedor;
        echo json_encode($dados);
        
    }

    public function salvar_consolidacao()
    {
        $this->layout = 'ajax'; 

        $renderiza = $_POST['renderiza']; 

  		$usuario = $this->BAuth->user();
        //pegando os exames válidos
        if(isset($_POST['exames_selecionados'])){
            $exames = array();
            foreach($_POST['exames_selecionados'] as $exame){
                if($exame){
                    array_push($exames,$exame);
                }            
            }
        }        
        
        //pegando a nota fiscal selecionada
        if(isset($_POST['nota_fiscal'])){
            $nota_fiscal = $_POST['nota_fiscal'];
        }

        $exames_consolidar = array();
        $glosas_consolidar = array();
        $auditorias_consolidar = array();
        
        //Esse find é utilizado para fornecer um numero de nota na auditoria, caso ela não tenha
        if ($nota_fiscal) {
            $nota_consolidar = $this->NotaFiscalServico->find('first',array('conditions' => array('codigo' => $nota_fiscal)));
        }

        foreach($exames as $key => $exame){

            $exame_existe = $this->ConsolidadoNfsExame->find('first',array('conditions' => array('codigo_item_pedido_exame' => $exame[0]['codigo_item_pedido_exame'])));

            $exame_consolidar = array();

            //Incluir o dado na tabela de consolidacoes
            if($exame_existe){                
                $exame_consolidar['codigo']                      = $exame_existe['ConsolidadoNfsExame']['codigo'];                            
            }

            $exame_consolidar['codigo_empresa']              = 1;  
            $exame_consolidar['codigo_nota_fiscal_servico']  = $nota_fiscal;                              
            $exame_consolidar['codigo_pedido_exame']         = $exame[0]['codigo_pedido_exame'];                      
            $exame_consolidar['codigo_fornecedor']           = $exame[0]['codigo_credenciado'];                  
            $exame_consolidar['codigo_item_pedido_exame']    = $exame[0]['codigo_item_pedido_exame'];                          
            $exame_consolidar['codigo_exame']                = $exame[0]['codigo_exame'];              
            $exame_consolidar['valor']                       = $exame[0]['valor_custo'];  
            $exame_consolidar['valor_corrigido']             = isset($exame[0]['valor_corrigido']) ? $exame[0]['valor_corrigido'] : null;
            $exame_consolidar['status']                      = 1;
            $exame_consolidar['ativo']                       = 1;      
            $exame_consolidar['codigo_usuario_inclusao']     = $usuario['Usuario']['codigo'];                          
            $exame_consolidar['codigo_usuario_alteracao']    = $usuario['Usuario']['codigo'];                          
            $exame_consolidar['data_alteracao']              = date('Y-m-d h:i:s');  
            $exame_consolidar['data_consolidacao']           = date('Y-m-d h:i:s');  
            $exame_consolidar['codigo_usuario_consolidacao'] = $usuario['Usuario']['codigo'];  
                       
            array_push($exames_consolidar, $exame_consolidar);
            
            /*Verifica se existe algum exame glosado por imagem em que não foi atribuido uma NFS na tela de auditoria.
            Se houver um exame nessas condições, ele atribui a mesma nota que a consolidação */

            $glosa_existe = $this->Glosas->find('first',array('conditions' => array('codigo_itens_pedidos_exames' => $exame[0]['codigo_item_pedido_exame'], 'codigo_classificacao_glosa' => 2)));
            $glosa_consolidar = array();
            if ($glosa_existe) {
                $glosa_consolidar['codigo'] =  $glosa_existe['Glosas']['codigo'];
                $glosa_consolidar['codigo_nota_fiscal_servico'] =  $nota_fiscal;
            }

            array_push($glosas_consolidar,$glosa_consolidar);


            //se existe uma auditoria, ele atualizar a nota na tabela de auditorias também
		    $auditoria_existe = $this->AuditoriaExame->find('first', array('conditions' => array('AuditoriaExame.codigo_item_pedido_exame' => $exame[0]['codigo_item_pedido_exame'])));

            $auditoria_consolidar = array();
            //verifica se existe dados
            if(!empty($auditoria_existe)) {
                //seta os dados para atualizar
                $auditoria_consolidar = array(
                    'AuditoriaExame' => array(
                        'codigo' => $auditoria_existe['AuditoriaExame']['codigo'],
                        'codigo_nota_fiscal_servico' => $nota_fiscal,
                        'numero_nota_fiscal' => $nota_consolidar['NotaFiscalServico']['numero_nota_fiscal'],
                        'recebimento_fisico' => $exame[0]['recebimento_fisico'],
                    )
                );
            }//fim auditoria_exames

            array_push($auditorias_consolidar,$auditoria_consolidar);


        }

        if($this->ConsolidadoNfsExame->saveAll($exames_consolidar)){
            if($this->Glosas->saveAll($glosas_consolidar)){
                $retorno = true;
                //sucesso
            }else{
                $retorno = false;
                //erro
            }

            if($this->AuditoriaExame->saveAll($auditorias_consolidar)){
                $retorno = true;
                //sucesso
            }else{
                $retorno = false;
                //erro
            }

        }else{
            $retorno = false;
            //erro
        }
      

        if ($renderiza) {
            $this->set(compact('dados'));
        }else{
            echo json_encode($retorno);
        }
    }


    public function formataExames($retornos)
    {
        foreach ($retornos as $key1 => $retorno){
            foreach ($retorno as $key2 => $value) {
                $retornos[$key1][$key2]['data_vencimento_nfs']   = Comum::formataData($value['data_vencimento_nfs'],'ymd','dmy');     
                $retornos[$key1][$key2]['data_pagamento_nfs']    = Comum::formataData($value['data_pagamento_nfs'],'ymd','dmy');     
                $retornos[$key1][$key2]['data_vencimento_cne']   = Comum::formataData($value['data_vencimento_cne'],'ymd','dmy');     
                $retornos[$key1][$key2]['data_pagamento_cne']    = Comum::formataData($value['data_pagamento_cne'],'ymd','dmy');    
                $retornos[$key1][$key2]['data_realizacao']       = Comum::formataData($value['data_realizacao'],'ymd','dmy');     
                $retornos[$key1][$key2]['data_baixa']            = Comum::formataData($value['data_baixa'],'timestamp','dmyhms');    
                $retornos[$key1][$key2]['valor_custo']           = Comum::moeda($value['valor_custo']);     
                $retornos[$key1][$key2]['valor_corrigido']       = !empty($value['valor_corrigido']) ? Comum::moeda($value['valor_corrigido']): $value['valor_corrigido'];     
                $retornos[$key1][$key2]['funcionario_cpf']       = !empty($value['funcionario_cpf']) ? Comum::formatarDocumento($value['funcionario_cpf']) : '-';     
                $retornos[$key1][$key2]['credenciado_cnpj']      = !empty($value['credenciado_cnpj']) ? Comum::formatarDocumento($value['credenciado_cnpj']) : '-';     
  
            }   
        }

        return $retornos;
    }

    public function corrigir_valor_exame($codigo_nf)
    {
        $this->pageTitle = 'AUDITAR VALOR DOS EXAMES';
        $this->layout = 'new_window';
        //condição para trazer apenas notas fiscais consolidadas
        $conditions = array();
        $conditions['ConsolidadoNfsExame.status'] = 1;
        $conditions['ConsolidadoNfsExame.codigo_nota_fiscal_servico'] = $codigo_nf;

        //Trazendo a query
        $query_notas_fiscais = $this->ConsolidadoNfsExame->getDadosNfsExame();

        $this->paginate['PedidoExame'] = array(
            'fields' => $query_notas_fiscais['fields'],
            'conditions' => $conditions,
            'joins' => $query_notas_fiscais['joins'],
            'order' => 'Exame.descricao ASC'
        );
        //Executando
        $retornos = $this->PedidoExame->find('all',$this->paginate['PedidoExame']);

        //Formata data, cpf e cnpj dos exames
        $retornos_formatado = $this->formataExames($retornos);

        if(!empty($retornos_formatado)) {
            foreach($retornos_formatado as $key1 => $consolidada){
                $array_exames[$consolidada[0]['codigo_exame']]['exame'] = $consolidada[0]['exame'];
                $consolidadas[$consolidada[0]['codigo_exame']][] = $consolidada[0];
            }
        }

        $this->set(compact('array_exames','consolidadas'));        
    }

    public function salvar_valor_corrigido()
    {
        $this->layout = 'ajax'; 

  		$usuario = $this->BAuth->user();
        //pegando os exames válidos
        if(isset($_POST['exames_selecionados'])){
            $exames = array();
            foreach($_POST['exames_selecionados'] as $exame){
                if($exame){
                    array_push($exames,$exame);
                }            
            }
        }
        //pegando os valores corrigidos
        if(isset($_POST['valor_corrigido'])){
            $valor_corrigido = array();
            foreach($_POST['valor_corrigido'] as $valor){
                if($valor){
                    array_push($valor_corrigido,str_replace(',','.',$valor));
                }            
            }
        }
        $glosas = array();
        try{
            foreach($exames as $key => $exame){
                $exame_existe = $this->ConsolidadoNfsExame->find('first',array('conditions' =>array('codigo_item_pedido_exame' => $exame)));

                $exame_consolidar = array();

                //Atualizando o valor corrigido na tabela de consolidacao
                if($exame_existe){
                    $exame_consolidar['valor_corrigido']             = $valor_corrigido[$key];
                    $exame_consolidar['codigo']                      = $exame_existe['ConsolidadoNfsExame']['codigo'];          
                    $exame_consolidar['codigo_usuario_alteracao']    = $usuario['Usuario']['codigo'];                          
                    $exame_consolidar['data_alteracao']              = date('Y-m-d h:i:s');   
                }
                if($this->ConsolidadoNfsExame->save($exame_consolidar)){
                    //aqui inicia a geração de uma glosa para a correção no valor
                    if((!empty($exame_consolidar['valor_corrigido'])) && ($exame_consolidar['valor_corrigido'] > $exame_existe['ConsolidadoNfsExame']['valor'])){
                        $valor = str_replace(',','.',Comum::moeda($exame_existe['ConsolidadoNfsExame']['valor'])); 
                        $valor_corrigido_consolidado = str_replace(',','.',$exame_consolidar['valor_corrigido']);
                        $acrescimo = $valor_corrigido_consolidado - $valor;

                        
                        $glosa_existe = $this->Glosas->find('first',array('conditions' =>array('codigo_itens_pedidos_exames' => $exame,'codigo_classificacao_glosa' => 3)));

                        $glosa = array();
                        if($glosa_existe){
                            $glosa['codigo']                      = $glosa_existe['Glosas']['codigo'];
                            $glosa['valor']                       = $acrescimo;
                            $glosa['motivo_glosa']                = null;
                            $glosa['codigo_tipo_glosa']           = 8;//Tipo de glosa reservado para glosas de valor
                            $glosa['ativo']                       = 1;
                            $glosa['codigo_usuario_alteracao']    = $usuario['Usuario']['codigo'];                          
                            $glosa['data_alteracao']              = date('Y-m-d h:i:s');   

                        }else{
                            $glosa['codigo_pedidos_exames']         = $exame_existe['ConsolidadoNfsExame']['codigo_pedido_exame'];
                            $glosa['codigo_itens_pedidos_exames']   = $exame_existe['ConsolidadoNfsExame']['codigo_item_pedido_exame'];
                            $glosa['valor']                         = $acrescimo;
                            $glosa['data_glosa']                    = date('Y-m-d');
                            $glosa['codigo_status_glosa']           = 4;
                            $glosa['motivo_glosa']                  = null;
                            $glosa['codigo_tipo_glosa']             = 8;//Tipo de glosa reservado para glosas de valor
                            $glosa['ativo']                         = 1;
                            $glosa['codigo_empresa']                = 1;
                            $glosa['data_inclusao']                 = date('Y-m-d h:i:s');   
                            $glosa['data_alteracao']                = date('Y-m-d h:i:s');   
                            $glosa['codigo_usuario_inclusao']       = $usuario['Usuario']['codigo'];                          
                            $glosa['codigo_usuario_alteracao']      = $usuario['Usuario']['codigo'];                          
                            $glosa['codigo_fornecedor']             = $exame_existe['ConsolidadoNfsExame']['codigo_fornecedor'];                          
                            $glosa['codigo_nota_fiscal_servico']    = $exame_existe['ConsolidadoNfsExame']['codigo_nota_fiscal_servico'];
                            $glosa['codigo_classificacao_glosa'] = 3;
                        }

                        array_push($glosas,$glosa);                           
                        
                    }
                    // Caso o usuario tenha gerado uma glosa de valor e voltado atrás do valor corrigido, esse if irá desativar a glosa de valor
                    if((empty($exame_consolidar['valor_corrigido']))  || ($exame_consolidar['valor_corrigido'] <= $exame_existe['ConsolidadoNfsExame']['valor'])){
                        $glosa_existe = $this->Glosas->find('first',array('conditions' =>array('codigo_itens_pedidos_exames' => $exame,'codigo_classificacao_glosa' => 3)));
                        
                        $glosa = array();
                        if($glosa_existe){
                            $glosa['codigo']                      = $glosa_existe['Glosas']['codigo'];
                            $glosa['valor']                       = null;
                            $glosa['motivo_glosa']                = "Glosa desativada devido a correção no valor";
                            $glosa['ativo']                       = 0;
                            
                            array_push($glosas,$glosa);
                        }

                    }

                    $retorno = true;
                }else{
                    $retorno = false;
                }
            }

            if($this->Glosas->saveAll($glosas)){
                $retorno = true;
            }else{
                $retorno = false;
            }
        } catch (\Exception $e) {
            return $this->responseJsonError($e->getMessage());
        }

        echo json_encode($retorno);
        
    }

    public function finalizar_nf($codigo_nf){

        $this->layout = 'ajax'; 
  		$usuario = $this->BAuth->user();

        $gestor_operacao = $usuario['Usuario']['flag_notas_fiscais_servicos_acrescimo_desconto'];

        //recuperando os dados da nota fiscal
        $nota_fiscal = $this->NotaFiscalServico->obterPorCodigo($codigo_nf);
        //recuperando as glosas ativas por classificação
        $valor_glosas_manuais = 0;
        $valor_glosas_imagens = 0;
        $valor_glosas_valor   = 0;
        
        $glosas_manuais = $this->Glosas->find('all',array('conditions' =>array('ativo'=> 1,'codigo_nota_fiscal_servico' => $codigo_nf,'codigo_classificacao_glosa' => 1)));
        $glosas_imagens = $this->Glosas->find('all',array('conditions' =>array('ativo'=> 1,'codigo_nota_fiscal_servico' => $codigo_nf,'codigo_classificacao_glosa' => 2)));
        $glosas_valor = $this->Glosas->find('all',array('conditions' =>array('ativo'=> 1,'codigo_nota_fiscal_servico' => $codigo_nf,'codigo_classificacao_glosa' => 3)));

        if ($glosas_manuais) {
            foreach ($glosas_manuais as $key => $value) {
                $valor_glosas_manuais += $value['Glosas']['valor'];
            }
        }
        
        if ($glosas_imagens) {
            foreach ($glosas_imagens as $key => $value) {
                $valor_glosas_imagens += $value['Glosas']['valor'];
            }
        }

        if ($glosas_valor) {
            foreach ($glosas_valor as $key => $value) {
                $valor_glosas_valor += $value['Glosas']['valor'];
            }
        }

        $total_glosas = ($valor_glosas_manuais + $valor_glosas_imagens + $valor_glosas_valor);

        //trazendo os exames consolidados para a nota, sem glosas
         $valor_exames_consolidados = 0;
         $exames_consolidados = $this->exibir_exames_consolidados($codigo_nf, true);

        foreach ($exames_consolidados['consolidadas'] as $key => $value) {
            $valor_exames_consolidados += str_replace(',','.',$value[0]['valor_custo']);
        }
        
        $nota_fiscal['NotaFiscalServico']['valor'] = Comum::moeda($nota_fiscal['NotaFiscalServico']['valor']);
        $nota_fiscal['NotaFiscalServico']['valor_acrescimo'] = Comum::moeda($nota_fiscal['NotaFiscalServico']['valor_acrescimo']);
        $nota_fiscal['NotaFiscalServico']['valor_desconto'] = Comum::moeda($nota_fiscal['NotaFiscalServico']['valor_desconto']);

        //Pegando motivos de acrescimo e desconto
        $motivos_acrescimo = $this->MotivosAcrescimo->find('all',array('conditions' =>array('ativo' => 1)));
        $motivos_desconto = $this->MotivosDesconto->find('all',array('conditions' =>array('ativo' => 1)));
        $dados = array(
            'nota_fiscal'               => $nota_fiscal,
            'valor_glosas_manuais'      => Comum::moeda($valor_glosas_manuais),
            'valor_glosas_imagens'      => Comum::moeda($valor_glosas_imagens),
            'valor_glosas_valor'        => Comum::moeda($valor_glosas_valor),
            'total_glosas'              => Comum::moeda($total_glosas),
            'valor_exames_consolidados' => Comum::moeda($valor_exames_consolidados),
            'motivos_acrescimo'         => $motivos_acrescimo,
            'motivos_desconto'          => $motivos_desconto,
            'gestor_operacao'           => $gestor_operacao,

        );

        echo json_encode($dados);

    }

    public function salvar_conclusao_nf()
    {      
        $this->layout = 'ajax'; 

        try {
            $usuario = $this->BAuth->user();
            $usuario_codigo = $usuario['Usuario']['codigo'];


            $codigo_nf = $_POST['nota_fiscal'];
            $nota_fiscal = $this->NotaFiscalServico->find('first',array('conditions' => array('codigo' => $codigo_nf)));
            

            if($nota_fiscal){
                $nota_conclusao = array();
                $nota_conclusao['codigo']                    =  $codigo_nf;
                $nota_conclusao['codigo_motivo_acrescimo']   =  $_POST['motivo_acrescimo'];
                $nota_conclusao['codigo_motivo_desconto']    =  $_POST['motivo_desconto'];
                $nota_conclusao['valor_acrescimo']           =  $_POST['acrescimo'];
                $nota_conclusao['valor_desconto']            =  $_POST['desconto'];
                $nota_conclusao['valor_glosas_valor']        =  $_POST['valor_glosas_valor'];
                $nota_conclusao['valor_glosas_imagens']      =  $_POST['valor_glosas_imagens'];
                $nota_conclusao['valor_glosas_manuais']      =  $_POST['valor_glosas_manuais'];
                $nota_conclusao['valor_total_glosas']        =  $_POST['total_glosas'];
                $nota_conclusao['valor_total_exames']        =  $_POST['total_exames'];
                $nota_conclusao['codigo_usuario_conclusao']  =  $usuario_codigo;
                $nota_conclusao['data_conclusao']            =  date('Y-m-d H:i:s');
                $nota_conclusao['codigo_nota_fiscal_status'] =  5; //STATUS DE NOTA CONCLUÍDA
    
                if($this->NotaFiscalServico->save($nota_conclusao)){
                    $retorno = true;
                }else{
                    $retorno = false;
                }
            }
        } catch (\Exception $e) {
            return $this->responseJsonError($e->getMessage());
        }
        
        echo json_encode($retorno);

    }

    public function imprimir_capa_de_lote($nota_fiscal) {
		$this->__jasperConsulta($nota_fiscal);
	}
	
	private function __jasperConsulta($nota_fiscal) {
		
		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/relatorio_capa_de_lote', // especificar qual relatório
			'FILE_NAME'=> basename( 'ordem_de_pagamento.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array( 
			'CODIGO_NOTA_FISCAL' => $nota_fiscal
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

	}

    public function reabrir_nota_fiscal()
    {
        $this->render(false,false);
        $codigo_nota = $_POST['codigo_nota'];
        $usuario = $this->BAuth->user();
        $usuario_codigo = $usuario['Usuario']['codigo'];

        $nota_fiscal = $this->NotaFiscalServico->find('first',array('conditions' => array('codigo' => $codigo_nota)));            

        if($nota_fiscal){
            $nota_conclusao = array();
            $nota_conclusao['codigo']                    =  $nota_fiscal['NotaFiscalServico']['codigo'];
            $nota_conclusao['data_alteracao']            =  date('Y-m-d h:i:s');
            $nota_conclusao['codigo_usuario_alteracao']  =  $usuario_codigo;
            $nota_conclusao['codigo_nota_fiscal_status'] =  2;

            if($this->NotaFiscalServico->save($nota_conclusao)){
                $retorno = true;
            }else{
                $retorno = false;
            }
        }

            return $retorno;

    }
    
}