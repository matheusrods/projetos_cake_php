<?php
/**
 * Classe para atender os Elementos
 *  
 * TODO: necessário criar uma forma de validação do usuário logado
 * 
 */
App::import('Core', 'Sanitize');

class IthealthHelperController extends AppController {

	public $name = 'IthealthHelper';

	public $helpers = array('Ithealth');

	public $components = array('Filtros', 'RequestHandler','ExportCsv', 'Upload');
	
	var $uses = array('Fornecedores', 'Cliente');
    
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array(
			'obter_credenciado',
			'obter_cliente',
			'obter_nfs'
		));
	}


	/**
	 * Obter dados de credenciado passando um código, documento (CNPJ) ou Razão Social 
	 *
	 * @return responseJson
	 */
	public function obter_credenciado()
    {
        $this->layout = 'ajax';
        $this->autoLayout = false;
		$this->autoRender = false;
		
		if(!isset($this->RequestHandler->params['url']) ){
			$data['error'] = 'Request inválido';
			return $this->responseJson($data);
		}

		$params = $this->RequestHandler->params['url'];
		
		$data = array('data'=>null, 'pagination'=>null);
		$conditions = array();
		
		$page = (isset($params['page']) && !empty($params['page'])) ? $params['page'] : 1;
		$limit = (isset($params['limit']) && !empty($params['limit'])) ? $params['limit'] : 50;
		
		
		$this->loadModel('Credenciado');

		// Valida se é pesquisa por documento (CNPJ)
		if(isset($params['codigo_documento']))
		{

			$codigo_documento  = Comum::soNumero($params['codigo_documento']);

			if(empty($codigo_documento)){
				$data['error'] = 'Documento inválido';
				return $this->responseJson($data);
			}
			
			// quantidade de caracteres mínimos para autocompletar
			if(strlen($codigo_documento) <= 2){
				$data['error'] = 'Quantidade de caracteres insuficientes';
				return $this->responseJson($data);
			}

			$conditions['codigo_documento LIKE'] = $codigo_documento . '%';
		}

		// Valida se é pesquisa por codigo credenciado/fornecedor
		if(isset($params['codigo_credenciado']))
		{
			
			$codigo_credenciado  = Comum::soNumero($params['codigo_credenciado']);

			if(empty($codigo_credenciado)){
				$data['error'] = 'Documento inválido';
				return $this->responseJson($data);
			}
			
			// quantidade de caracteres mínimos para autocompletar
			if(strlen($codigo_credenciado) <= 1){
				$data['error'] = 'Quantidade de caracteres insuficientes';
				return $this->responseJson($data);
			}

			$conditions['codigo LIKE'] = $codigo_credenciado . '%';
			$conditions['tipo_unidade'] = 'F';
		}

		// Valida se é pesquisa por Razão Social
		if(isset($params['razao_social']) && Validation::alphaNumeric($params['razao_social']))
		{
			$razao_social  = $params['razao_social'];

			if(empty($razao_social)){
				$data['error'] = 'Razão Social inválido';
				return $this->responseJson($data);
			}
			
			// quantidade de caracteres mínimos para autocompletar
			if(strlen($razao_social) <= 3){
				$data['error'] = 'Quantidade de caracteres insuficientes';
				return $this->responseJson($data);
			}

			$razao_social = Sanitize::clean($razao_social);
			$conditions['razao_social LIKE'] = '%' . $razao_social . '%';
		}		

		
		$fields = array('codigo','codigo_documento','nome','razao_social', 'ativo', 'data_inclusao', 'data_alteracao');
		
		$order = array('Credenciado.ativo DESC', 'Credenciado.data_alteracao DESC');

		$this->paginate['Credenciado'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => $limit,
			'order' =>  $order
		);


		$credenciadoData = $this->paginate('Credenciado');
		
		$total = $this->Credenciado->find('count', compact('conditions'));

		if(!empty($credenciadoData))
		{

			$tmp = array();

			foreach ($credenciadoData as $key => $value) {
				$tmp['codigo'] = $value['Credenciado']['codigo'];
				$tmp['codigo_documento'] = Comum::formatarDocumento($value['Credenciado']['codigo_documento']);
				$tmp['nome'] = $value['Credenciado']['nome'];
				$tmp['razao_social'] = $value['Credenciado']['razao_social'];
				$tmp['ativo'] = $value['Credenciado']['ativo'];
				$tmp['data_inclusao'] = $value['Credenciado']['data_inclusao'];
				$tmp['data_alteracao'] = $value['Credenciado']['data_alteracao'];
				
				$data['data'][] = $tmp;
				
			}

			$pagina_atual = ($page > 0) ? intval($page) : 1;
			$offset = !empty($pagina_atual) ? ($pagina_atual - 1) * $limit : 0;
			$more = !(($offset + $limit) > $total);	
			
			$data['pagination'] = array('offset'=>$offset, 'total'=>$total, 'more'=>$more);

		}

		return $this->responseJson($data);
	}


	/**
	 * Obter dados de um cliente passando um código, documento (CNPJ) ou Razão Social 
	 *
	 * @return responseJson
	 */
	public function obter_cliente()
    {
        $this->layout = 'ajax';
        $this->autoLayout = false;
		$this->autoRender = false;
		
		if(!isset($this->RequestHandler->params['url']) ){
			$data['error'] = 'Request inválido';
			return $this->responseJson($data);
		}

		$params = $this->RequestHandler->params['url'];
		
		$data = array('data'=>null, 'pagination'=>null);
		$conditions = array();
		
		$page = (isset($params['page']) && !empty($params['page'])) ? $params['page'] : 1;
		$limit = (isset($params['limit']) && !empty($params['limit'])) ? $params['limit'] : 50;
		

		// Valida se é pesquisa por documento (CNPJ)
		if(isset($params['codigo_documento']))
		{

			$codigo_documento  = Comum::soNumero($params['codigo_documento']);

			if(empty($codigo_documento)){
				$data['error'] = 'Documento inválido';
				return $this->responseJson($data);
			}
			
			// quantidade de caracteres mínimos para autocompletar
			if(strlen($codigo_documento) <= 2){
				$data['error'] = 'Quantidade de caracteres insuficientes';
				return $this->responseJson($data);
			}

			$conditions['codigo_documento LIKE'] = $codigo_documento . '%';
		}

		// Valida se é pesquisa por codigo credenciado/fornecedor
		if(isset($params['codigo_cliente']))
		{
			
			$codigo_cliente  = Comum::soNumero($params['codigo_cliente']);

			if(empty($codigo_cliente)){
				$data['error'] = 'Documento inválido';
				return $this->responseJson($data);
			}
			
			// quantidade de caracteres mínimos para autocompletar
			if(strlen($codigo_cliente) <= 1){
				$data['error'] = 'Quantidade de caracteres insuficientes';
				return $this->responseJson($data);
			}

			$conditions['codigo LIKE'] = $codigo_cliente . '%';
		}

		// Valida se é pesquisa por Razão Social
		if(isset($params['razao_social']) && Validation::alphaNumeric($params['razao_social']))
		{
			$razao_social  = $params['razao_social'];

			if(empty($razao_social)){
				$data['error'] = 'Razão Social inválido';
				return $this->responseJson($data);
			}
			
			// quantidade de caracteres mínimos para autocompletar
			if(strlen($razao_social) <= 3){
				$data['error'] = 'Quantidade de caracteres insuficientes';
				return $this->responseJson($data);
			}

			$razao_social = Sanitize::clean($razao_social);
			$conditions['razao_social LIKE'] = '%' . $razao_social . '%';
		}		

		
		$fields = array('codigo','codigo_documento','nome_fantasia','razao_social', 'ativo', 'data_inclusao', 'data_alteracao');
		
		$order = array('Cliente.ativo DESC', 'Cliente.data_alteracao DESC');

		$this->paginate['Cliente'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => $limit,
			'order' =>  $order
		);


		$clienteData = $this->paginate('Cliente');
		
		$total = $this->Cliente->find('count', compact('conditions'));

		if(!empty($clienteData))
		{

			$tmp = array();

			foreach ($clienteData as $key => $value) {
				$tmp['codigo'] = $value['Cliente']['codigo'];
				$tmp['codigo_documento'] = Comum::formatarDocumento($value['Cliente']['codigo_documento']);
				$tmp['nome'] = $value['Cliente']['nome_fantasia'];
				$tmp['razao_social'] = $value['Cliente']['razao_social'];
				$tmp['ativo'] = $value['Cliente']['ativo'];
				$tmp['data_inclusao'] = $value['Cliente']['data_inclusao'];
				$tmp['data_alteracao'] = $value['Cliente']['data_alteracao'];
				
				$data['data'][] = $tmp;
				
			}

			$pagina_atual = ($page > 0) ? intval($page) : 1;
			$offset = !empty($pagina_atual) ? ($pagina_atual - 1) * $limit : 0;
			$more = !(($offset + $limit) > $total);	
			
			$data['pagination'] = array('offset'=>$offset, 'total'=>$total, 'more'=>$more);

		}

		return $this->responseJson($data);
	}


	
	/**
	 * Obter notas fiscais disponiveis de um credenciado passando um código, documento (CNPJ) ou Razão Social 
	 *
	 * @return responseJson
	 */
	public function obter_nfs()
    {
        $this->layout = 'ajax';
        $this->autoLayout = false;
		$this->autoRender = false;
		
		if(!isset($this->RequestHandler->params['url']) ){
			$data = array('error' => 'Request inválido');
			return $this->responseJson($data);
		}

		$params = $this->RequestHandler->params['url'];
		
		$data = array('data'=>null, 'pagination'=>null);
		$conditions = array();
		
		$page = (isset($params['page']) && !empty($params['page'])) ? $params['page'] : 1;
		$limit = (isset($params['limit']) && !empty($params['limit'])) ? $params['limit'] : 50;
		
		$column = (isset($params['column']) && !empty($params['column'])) ? $params['column'] : 'codigo';

		if(isset($params['q']))
		{
			
			$codigo_fornecedor  = Comum::soNumero($params['q']);

			if(empty($codigo_fornecedor)){
				$data['error'] = 'Documento inválido';
				return $this->responseJson($data);
			}
			
			// quantidade de caracteres mínimos para autocompletar
			if(strlen($codigo_fornecedor) <= 1){
				$data['error'] = 'Quantidade de caracteres insuficientes';
				return $this->responseJson($data);
			}
			
			$conditions[$column.' LIKE'] = $codigo_fornecedor . '%';
		}

		// Valida se é pesquisa por codigo credenciado/fornecedor
		if(isset($params['codigo_fornecedor']))
		{
			
			$codigo_fornecedor  = Comum::soNumero($params['codigo_fornecedor']);

			if(empty($codigo_fornecedor)){
				$data['error'] = 'Documento inválido';
				return $this->responseJson($data);
			}
			
			// quantidade de caracteres mínimos para autocompletar
			if(strlen($codigo_fornecedor) <= 1){
				$data['error'] = 'Quantidade de caracteres insuficientes';
				return $this->responseJson($data);
			}

			$conditions['codigo LIKE'] = $codigo_fornecedor . '%';
		}
		
		

		$this->loadModel('NotaFiscalServico');

		$fields = array('codigo', 'codigo_fornecedor', 'data_emissao','numero_nota_fiscal','codigo_nota_fiscal_status', 'ativo', 'data_inclusao', 'data_alteracao');
		
		$order = array('NotaFiscalServico.ativo DESC', 'NotaFiscalServico.data_alteracao DESC');

		$this->paginate['NotaFiscalServico'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => $limit,
			'order' =>  $order
		);


		$modeloData = $this->paginate('NotaFiscalServico');
		
		$total = $this->NotaFiscalServico->find('count', compact('conditions'));

		if(!empty($modeloData))
		{

			$tmp = array();

			foreach ($modeloData as $key => $value) {
				$tmp['codigo'] = $value['NotaFiscalServico']['codigo'];
				$tmp['codigo_fornecedor'] = $value['NotaFiscalServico']['codigo_fornecedor'];
				$tmp['numero_nota_fiscal'] =$value['NotaFiscalServico']['numero_nota_fiscal'];
				$tmp['codigo_nota_fiscal_status'] = $value['NotaFiscalServico']['codigo_nota_fiscal_status'];
				$tmp['data_emissao'] = $value['NotaFiscalServico']['data_emissao'];
				$tmp['ativo'] = $value['NotaFiscalServico']['ativo'];
				$tmp['data_inclusao'] = $value['NotaFiscalServico']['data_inclusao'];
				$tmp['data_alteracao'] = $value['NotaFiscalServico']['data_alteracao'];
				
				$data['data'][] = $tmp;
				
			}

			$pagina_atual = ($page > 0) ? intval($page) : 1;
			$offset = !empty($pagina_atual) ? ($pagina_atual - 1) * $limit : 0;
			$more = !(($offset + $limit) > $total);	
			
			$data['pagination'] = array('offset'=>$offset, 'total'=>$total, 'more'=>$more);

		}

		return $this->responseJson($data);
	}

}