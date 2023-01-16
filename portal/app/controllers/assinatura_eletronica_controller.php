<?php
class AssinaturaEletronicaController extends AppController
{
    public $name = 'AssinaturaEletronica';
    public $components = array('Filtros', 'RequestHandler','ExportCsv', 'Upload');
    public $helpers = array('Html', 'Ajax', 'Highcharts','Buonny');
    
    public $uses = array(
        'Medico',
        'ConselhoProfissional',
        'EnderecoEstado',
        'EnderecoCidade',
        'MedicoEndereco',
        'AnexoAssinaturaEletronica',
        'Uperfil',
        'FornecedorMedico'
    );
        
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow();
    }

    public function index()
    {
        $this->pageTitle = 'Profissionais';

        $this->retorna_combos();
    }

    public function retorna_combos()
    {
        $conselho_profissional = $this->ConselhoProfissional->find('list', array('fields' => array('codigo', 'descricao'),'order' => 'codigo'));
        $estado = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1),'fields' => array('abreviacao', 'descricao'),'order' => 'descricao'));
        
        $this->set(compact('conselho_profissional', 'estado'));
    }

    public function listagem()
    {
        $this->layout = 'ajax';
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Medico->name);

        $conditions = $this->Medico->converteFiltroEmCondition($filtros);

        $fields = array(
			// 'DISTINCT Medico.codigo', 
			'Medico.codigo', 
            'Medico.nome', 
			'Medico.numero_conselho', 
			'Medico.conselho_uf', 
			'Medico.codigo_conselho_profissional', 
			'Medico.ativo',

            'ConselhoProfissional.descricao',
        );
        
        $joins  = array();

        // Lista todo o corpo clinico
        if (
            $this->BAuth->user('codigo_uperfil') == Uperfil::RH_CLIENTE
            || $this->BAuth->user('codigo_uperfil') == Uperfil::ENFERMAGEM_CLIENTE
        ) {
            $joins[] = array(
                'table' => 'RHHealth.dbo.fornecedores_medicos',
                'alias' => 'FornecedorMedico',
                'type' => 'INNER',
                'conditions' => 'FornecedorMedico.codigo_medico = Medico.codigo AND Medico.ativo = 1',
            );

            $joins[] = array(
                'table' => 'Rhhealth.dbo.fornecedores',
                'alias' => 'Fornecedor',
                'type' => 'INNER',
                'conditions' => 'FornecedorMedico.codigo_fornecedor = Fornecedor.codigo',
            );

            $joins[] = array(
                'table' => 'Rhhealth.dbo.clientes_fornecedores',
                'alias' => 'ClienteFornecedor',
                'type' => 'INNER',
                'conditions' => 'ClienteFornecedor.codigo_fornecedor = Fornecedor.codigo AND ClienteFornecedor.ativo = 1',
            );

            $joins[] = array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'ClienteFornecedor.codigo_cliente = Cliente.codigo',
            );

            $conditions['Cliente.codigo'] = $this->BAuth->user('codigo_cliente');

            $conditions[] = "Medico.codigo_conselho_profissional = 1";
            $conditions[] = "FornecedorMedico.codigo = (SELECT TOP 1 codigo
								FROM fornecedores_medicos
								WHERE codigo_medico = Medico.codigo
								ORDER BY codigo ASC)";
        }

        // Lista todos daquela clinica
        if (
            $this->BAuth->user('codigo_uperfil') == Uperfil::PRESTADOR
            || $this->BAuth->user('codigo_uperfil') == Uperfil::ENFERMAGEM_PRESTADOR
        ) {
            $joins[] = array(
                'table' => 'RHHealth.dbo.fornecedores_medicos',
                'alias' => 'FornecedorMedico',
                'type' => 'INNER',
                'conditions' => 'FornecedorMedico.codigo_medico = Medico.codigo AND Medico.ativo = 1',
            );

            $fields[] = 'FornecedorMedico.codigo_fornecedor';
            
            $conditions['FornecedorMedico.codigo_fornecedor'] = $this->BAuth->user('codigo_fornecedor');
        }

        // Lista somente seus respectivos conselhos
        if (
            $this->BAuth->user('codigo_uperfil') == Uperfil::MEDICO_CLIENTE
            || $this->BAuth->user('codigo_uperfil') == Uperfil::MEDICO_PRESTADOR
            || $this->BAuth->user('codigo_uperfil') == Uperfil::MEDICO_COORDENADOR
            || $this->BAuth->user('codigo_uperfil') == Uperfil::ENGENHARIA_CLIENTE
            || $this->BAuth->user('codigo_uperfil') == Uperfil::ENGENHARIA_PRESTADOR
            || $this->BAuth->user('codigo_uperfil') == Uperfil::FONO_PRESTADOR
        ) {
            $joins[] = array(
                'table' => 'RHHealth.dbo.usuario_multi_conselho',
                'alias' => 'UsuarioMultiConselho',
                'type' => 'INNER',
                'conditions' => 'UsuarioMultiConselho.codigo_medico = Medico.codigo',
            );
            
            $conditions['UsuarioMultiConselho.codigo_usuario'] = $this->BAuth->user('codigo');
        }

        $joins[] = array(
            'table' => 'Rhhealth.dbo.conselho_profissional',
            'alias' => 'ConselhoProfissional',
            'type' => 'INNER',
            'conditions' => 'Medico.codigo_conselho_profissional = ConselhoProfissional.codigo',
        );

        if (isset($filtros['assinatura_eletronica']) && $filtros['assinatura_eletronica'] != "") {
            $joins[] = array(
                'table' => 'RHHealth.dbo.anexos_assinatura_eletronica',
                'alias' => 'AnexoAssinaturaEletronica',
                'type' => 'LEFT',
                'conditions' => 'AnexoAssinaturaEletronica.codigo_medico = Medico.codigo',
            );
        }


        $group = $fields;


        $this->paginate['Medico'] = array(
			'recursive' => -1,
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => 'Medico.conselho_uf ASC, Medico.nome ASC, Medico.codigo ASC',
            'groupBy' =>$group
        );

        // debug($this->Medico->find('sql',$this->paginate['Medico'])); exit;
       
        $medicos = $this->paginate('Medico');

        // debug($medicos); exit;

        foreach ($medicos as $key => $medico) {
            $anexo = $this->AnexoAssinaturaEletronica->find('first', 
                array(
                    'conditions' => array('AnexoAssinaturaEletronica.codigo_medico' => $medico['Medico']['codigo']), 
                    'order' => 'AnexoAssinaturaEletronica.codigo desc'
                )
            );

            $medicos[$key]['Medico']['anexo'] = ($anexo) ? $anexo : array();
        }

        $this->set(compact('medicos'));
    }  
    
    public function editar()
    {
        $this->pageTitle = 'Incluir Assinatura Eletrônica';
        
        if ($this->RequestHandler->isPost()) {
            $post_params = isset($this->data['AnexoAssinaturaEletronica']['caminho_arquivo']) && !empty($this->data['AnexoAssinaturaEletronica']['caminho_arquivo']) ? $this->data['AnexoAssinaturaEletronica']['caminho_arquivo'] : null ;

            if(empty($post_params)){
                $this->BSession->setFlash('save_error');
                return;
            }

            $this->Upload->setOption('field_name', 'caminho_arquivo');            
            $this->Upload->setOption('accept_extensions', array('jpg','jpeg', 'png'));
            $this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! Favor escolher arquivo jpg, jpeg ou png');
            $this->Upload->setOption('size_max', 5242880);
            $this->Upload->setOption('size_max_message', 'Tamanho máximo excedido! Só é permitido arquivos de até 5MB');
            
            $retorno = $this->Upload->fileServer($this->data['AnexoAssinaturaEletronica']);

            // se ocorreu algum erro de comunicação com o fileserver
            if (isset($retorno['error']) && !empty($retorno['error']) ){
                $chave = key($retorno['error']);
                $this->BSession->setFlash(array(MSGT_ERROR, $retorno['error'][$chave]));
            } else {
                $nome_arquivo = $this->data['AnexoAssinaturaEletronica']['caminho_arquivo']['name'];

                unset($this->data['AnexoAssinaturaEletronica']['caminho_arquivo']);

                $this->data['AnexoAssinaturaEletronica']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
                $this->data['AnexoAssinaturaEletronica']['codigo_medico'] = $this->data['Medico']['codigo'];
                $this->data['AnexoAssinaturaEletronica']['caminho_arquivo'] = "https://api.rhhealth.com.br" . $retorno['data'][$nome_arquivo]['path'];
                // $this->data['AnexoAssinaturaEletronica']['caminho_arquivo'] = Ambiente::getUrlServidorFileServer() . $retorno['data'][$nome_arquivo]['path'];

                if ($this->AnexoAssinaturaEletronica->incluir($this->data)) {                
                    $this->BSession->setFlash('save_success');                
                    $this->redirect(array('controller' => 'assinatura_eletronica', 'action' => 'index'));
                } else {
                    $this->BSession->setFlash('save_error');
                }
            }           

            $this->redirect("editar/{$this->data['Medico']['codigo']}");
        } else {
            if (isset($this->passedArgs[0])) {
                $this->data = $this->Medico->find('first', 
                    array(
                        'fields' => array('*'),
                        'conditions' => array('Medico.codigo' => $this->passedArgs[0])
                    )
                );
            }
        }
    }

    public function listagem_log($codigo_medico){
        //titulo da pagina
        $this->pageTitle = 'Log de Anexo';
        $this->layout = 'new_window';
  
        //campos
        $fields = array(
            'AnexoAssinaturaEletronica.codigo_medico',
            'AnexoAssinaturaEletronica.caminho_arquivo',
            'AnexoAssinaturaEletronica.data_inclusao',
            'AnexoAssinaturaEletronica.data_alteracao',
            'UsuarioInclusao.apelido',
            'UsuarioInclusao.nome',
            'UperfilInclusao.descricao',
            'UsuarioAlteracao.apelido',
            'UsuarioAlteracao.nome',
            'UperfilAlteracao.descricao',
        );

        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioInclusao',
                'type' => 'LEFT',
                'conditions' => 'AnexoAssinaturaEletronica.codigo_usuario_inclusao = UsuarioInclusao.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.uperfis',
                'alias' => 'UperfilInclusao',
                'type' => 'LEFT',
                'conditions' => 'UsuarioInclusao.codigo_uperfil = UperfilInclusao.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioAlteracao',
                'type' => 'LEFT',
                'conditions' => 'AnexoAssinaturaEletronica.codigo_usuario_alteracao = UsuarioAlteracao.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.uperfis',
                'alias' => 'UperfilAlteracao',
                'type' => 'LEFT',
                'conditions' => 'UsuarioAlteracao.codigo_uperfil = UperfilAlteracao.codigo',
            )           
        );
  
        //dados do log
        $dados = $this->AnexoAssinaturaEletronica->find('all', 
            array(
                'fields' => $fields, 
                'joins' => $joins,
                'conditions' => array('AnexoAssinaturaEletronica.codigo_medico' => $codigo_medico),
                'order' => 'AnexoAssinaturaEletronica.codigo desc' 
            )
        );
  
        $this->set(compact('dados', 'codigo_medico'));
    }
}
