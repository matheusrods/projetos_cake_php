<?php
class ProfissionaisLogsController extends AppController {
	public $name = 'ProfissionaisLogs';
	public $layout = 'cliente';
	public $components = array('Filtros', 'RequestHandler');
	public $helpers = array('Html', 'Ajax');
	public $uses = array( 'Profissional', 'ProfissionalLog', 'Usuario', 'EnderecoEstado','TipoCnh', 'TipoContato', 'TipoRetorno', 'EnderecoCidade', 'Endereco');
	
	function index( ) {
		$this->layout = 'new_window';
		$this->data['ProfissionalLog'] = $this->Filtros->controla_sessao($this->data, 'ProfissionalLog');		
    }

    function listar( ) {
		$this->layout = 'ajax';
		$filtros      = $this->Filtros->controla_sessao($this->data, 'ProfissionalLog');
		$conditions   = $this->ProfissionalLog->converteFiltroEmCondition( $filtros );
        $this->paginate['ProfissionalLog'] = array(
			'conditions' => $conditions,
			'limit'  => 50,
            'joins' => array(
                array(
                    "table" => "{$this->Usuario->databaseTable}.{$this->Usuario->tableSchema}.{$this->Usuario->useTable}",
                    "alias" => "Usuario",
                    "type" => "LEFT",
                    "conditions" => array("Usuario.codigo = ProfissionalLog.codigo_usuario_alteracao")
                ),
        	),
			'order'  => 'ProfissionalLog.data_inclusao DESC',
			'fields' => array(
				'ProfissionalLog.codigo', 'ProfissionalLog.codigo_documento', 'ProfissionalLog.nome',
				'Usuario.apelido', 'ProfissionalLog.data_inclusao',
			)
    	);
        $profissional_log = $this->paginate('ProfissionalLog');
        $this->set(compact('profissional_log'));        
    }

	public function visualizar_profissional_log( $codigo ){
		$filtros = $this->Filtros->controla_sessao($this->data, 'ProfissionalLog');		
		$this->layout = 'new_window';
		$this->pageTitle = 'Dados do Profissional';
		$this->data = $this->ProfissionalLog->carregarDadosCadastraisLog( $codigo );
		$this->data['ProfissionalLog']['rg_data_emissao'] 	= substr($this->data['ProfissionalLog']['rg_data_emissao'], 0, 10);
		$this->data['ProfissionalLog']['data_nascimento'] 	= substr($this->data['ProfissionalLog']['data_nascimento'], 0, 10);
		$this->data['ProfissionalLog']['cnh_vencimento'] 	= substr($this->data['ProfissionalLog']['cnh_vencimento'], 0, 10);
		$this->data['ProfissionalLog']['data_primeira_cnh'] = substr($this->data['ProfissionalLog']['data_primeira_cnh'], 0, 10);
		$this->data['ProfissionalLog']['data_inicio_mopp']  = substr($this->data['ProfissionalLog']['data_inicio_mopp'], 0, 10);
    	$endereco_estado = $this->EnderecoEstado->comboPorPais(1);
		if( isset( $this->data['ProfissionalLog']['codigo_endereco_cidade_naturalidade']) ) {
			$end = $this->EnderecoCidade->find('first', array('conditions'=>array('EnderecoCidade.codigo' => $this->data['ProfissionalLog']['codigo_endereco_cidade_naturalidade'])));
			$this->data['ProfissionalLog']['codigo_estado_naturalidade'] = $end['EnderecoEstado']['codigo'];
	    }
    	$cidades_profissional_log = array();
		if(isset( $this->data['ProfissionalLog']['codigo_estado_naturalidade']) ) {
    		$cidades_profissional_log = $this->EnderecoCidade->combo( $this->data['ProfissionalLog']['codigo_estado_naturalidade'] );
    	}
		$tipo_cnh = $this->TipoCnh->find('list', array('fields'=>array('codigo', 'descricao')));
		$profissional_enderecos_log = array();
		if (isset($this->data['ProfissionalEnderecoLog']['endereco_cep'])) {
			$cidade_endereco_log = $this->Endereco->carregarEnderecoCompleto( $this->data['ProfissionalEnderecoLog']['codigo_endereco'] );
			$profissional_enderecos_log[$cidade_endereco_log['EnderecoCidade']['codigo']] = $cidade_endereco_log['EnderecoCidade']['descricao'];
		}
		$tipo_retorno = $this->TipoRetorno->listar();
		$tipo_contato = $this->TipoContato->listar();
		$this->set(compact('endereco_estado', 'cidades_profissional_log', 'tipo_cnh','profissional_enderecos_log', 
				'tipo_retorno', 'tipo_contato', 'codigo'));
	}    
}