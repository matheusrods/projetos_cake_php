<?php
class FichasPcdController extends AppController {
	public $name = 'FichasPcd';
	public $uses = array('FichaClinica', 'PedidoExame');  

	public function index(){
		$this->pageTitle = 'Consulta de Fichas PCD';
        $this->data['FichaClinica'] = $this->Filtros->controla_sessao($this->data, "FichaClinica");
	}

	public function listagem(){
		$this->layout = 'ajax';
		
		$this->FichaClinica->bindListagemFichaPcd();

		$filtros = $this->Filtros->controla_sessao($this->data, "FichaClinica");
		$conditions = $this->FichaClinica->converteFiltroEmCondition($filtros);
		
		if(!is_null($this->BAuth->user('codigo_cliente'))) {
			$conditions['Cliente.codigo'] = $this->BAuth->user('codigo_cliente');
		}

		if(!is_null($this->BAuth->user('codigo_fornecedor'))){
			$conditions['ItensPedidosExames.codigo_fornecedor'] = $this->BAuth->user('codigo_fornecedor');
		}

		$fields = array(
			'FichaClinica.codigo',
			'FichaClinica.codigo_pedido_exame',
			'PedidoExame.codigo',
			'PedidoExame.codigo_cliente',
			'Cliente.razao_social',
			'PedidoExame.codigo_funcionario',
			'Funcionario.codigo',
			'Funcionario.nome',
			'FichaClinica.codigo_medico',
			'Medico.nome'
		);

		$order = 'FichaClinica.codigo';

		$this->paginate['FichaClinica'] = array(
			'conditions' => $conditions,
			'fields' => $fields,
			'recursive' => 1, 
			'limit' => 50,
			'order' => $order
		);

		$fichas_pcd = $this->paginate('FichaClinica');
		$this->set(compact('fichas_pcd'));
	}

	public function imprimir_relatorio($codigo_ficha_clinica)
	{
		$this->autoRender = false;
                                                                                        
		//SALVA NA TABELA TEMPORÁRIA OS DADOS SERIALIZADOS PARA A CONSTRUÇÃOI DO RELATORIO PDF
		$this->FichaClinica->criaTabelaTemporaria($codigo_ficha_clinica);

		// GERA O RELATORIO PDF

		$this->__jasperConsulta($codigo_ficha_clinica);
	}

	private function __jasperConsulta($codigo_ficha_clinica) {

		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/relatorio_ficha_pcd', // especificar qual relatório
			'FILE_NAME'=> basename( 'relatorio_ficha_pcd.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array(
			'CODIGO_FICHA_CLINICA' => $codigo_ficha_clinica
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
}
?>