<?php
/*
$tmpClient = new SoapClient("http://{$_SERVER['HTTP_HOST']}/portal/wsdl/incluir_ficha.wsdl");
foreach($tmpClient->__getTypes() as $type){
	$array = split(" ", $type);
	if($array[0] == "struct" && !class_exists($array[1])) eval('class '.$array[1].' {var $teste;}');
} 
*/
class TeleconsultSoapComponent extends Component{

	var $name = 'TeleconsultSoap';
	private $msg_erro = array();
	private $tag_descricoes = array();

	function __construct(){
		parent::__construct();

	}
	
	private function imports(){
		$this->Cliente = ClassRegistry::init('Cliente');
		$this->StoredProcedure = ClassRegistry::init('StoredProcedure');
		$this->CargaValor = ClassRegistry::init('CargaValor');
		$this->EnderecoCidade = ClassRegistry::init('EnderecoCidade');
		$this->EnderecoEstado = ClassRegistry::init('EnderecoEstado');
		$this->TeleconsultIntegracao = ClassRegistry::init('TeleconsultIntegracao');
		$this->Profissional = ClassRegistry::init('Profissional');
	}

	private function loadModels() {
        $this->modelsCarregados = func_get_args();
        foreach($this->modelsCarregados as $model) {
        	//$this->loadModel($model);
        	//$this->$model = new $model;
        	if (!isset($this->$model)) $this->$model = ClassRegistry::init($model);
        }
	}



	public function autenticar($token, $codigo_documento){
		$this->Usuario = ClassRegistry::init('Usuario');
		$return = $this->Usuario->autenticarToken($token, $codigo_documento);
		if ($return) {
			unset($return['Usuario']['senha']);
			$_SESSION['Auth']['Usuario'] = $return['Usuario'];
		}
		return $return;
	}

	private function getCliente($consulta, $retorna_dados_usuario = false) {
		$arrSM = array();
		$msg_erro = &$this->msg_erro;
		
		//$codigo_cliente_pg = '510416';	
		$cnpj_empresa = $consulta->cnpj_cliente;

		if (empty($cnpj_empresa)) {
			$msg_erro[] = 'CNPJ Cliente não informado ou XML Inválido';
			return false;
		}

		$token = $consulta->autenticacao->token;
		$dados_usuario = $this->autenticar($token, $cnpj_empresa);
		if(!$dados_usuario){
			$msg_erro[] =  'O token informado não confere com o cnpj do cliente'."\n".$token." - ".$cnpj_empresa;
			return false;
		}

		if(!Comum::validarCNPJ($cnpj_empresa)){
			$msg_erro[] = 'O CNPJ do cliente é invalido';
			return false;
		}
		
		$dados_cliente = $this->Cliente->carregarPorDocumento($cnpj_empresa);
		if (empty($dados_cliente['Cliente']['codigo'])) {
			$msg_erro[] = 'O CNPJ do cliente não está cadastrado';
			return false;
		}

		if ($dados_cliente['Cliente']['ativo']==0) {
			$msg_erro[] = 'O Cliente está inativo';
			return false;
		}		

		if ($retorna_dados_usuario) {
			$retorno = Array(
				'Cliente' => $dados_cliente['Cliente'],
				'Usuario' => $dados_usuario['Usuario']
			);
			return $retorno;
		}

		return $dados_cliente['Cliente']['codigo'];
	}

	private function montaArrayTagDescricao() {
		$this->tag_descricoes = Array(
			'cnpj_embarcador' => 'CNPJ Embarcador',
			'cnpj_transportador' => 'CNPJ Transportador'
		);
	}

	private function getDescricaoTag($tag) {
		if (empty($this->tag_descricoes)) $this->montaArrayTagDescricao();
		if (isset($this->tag_descricoes[$tag])) return $this->tag_descricoes[$tag];

		return '';
	}

	private function getCodigoEmbarcadorTransportador($consulta, $tag) {
		$arrSM = array();
		$msg_erro = &$this->msg_erro;
		$descricao_tag = $this->getDescricaoTag($tag);
		
		//$codigo_cliente_pg = '510416';	
		$cnpj_empresa = $consulta->$tag;

		if (empty($cnpj_empresa)) {
			$msg_erro[] = 'O '.$descricao_tag.' não informado ou XML Inválido';
			return false;
		}

		if(!Comum::validarCNPJ($cnpj_empresa)){
			$msg_erro[] = 'O '.$descricao_tag.' é invalido';
			return false;
		}
		
		$dados_cliente = $this->Cliente->carregarPorDocumento($cnpj_empresa);
		if (empty($dados_cliente['Cliente']['codigo'])) {
			$msg_erro[] = 'O '.$descricao_tag.' não está cadastrado';
			return false;
		}

		if ($dados_cliente['Cliente']['ativo']==0) {
			$msg_erro[] = 'O '.$descricao_tag.' é de um cliente inativo';
			return false;
		}		

		return $dados_cliente['Cliente']['codigo'];
	}	

	public function getCodigoValor($valor) {
		$msg_erro = &$this->msg_erro;

		$conditions = Array(
			$valor.'>= valor_de',
			$valor.'<= valor_ate'
		);

		/*
		$query = $this->CargaValor->find('sql',compact('conditions'));
		$msg_erro[] = $query;
		return false;
		*/

		$dados_valor = $this->CargaValor->find('first',compact('conditions'));
		if (empty($dados_valor['CargaValor']['codigo'])) {
			$msg_erro[] = 'Valor não pertence a nenhuma faixa definida.';
			return false;
		}

		return $dados_valor['CargaValor']['codigo'];
	}

	public function getCodigoUF($uf = "") {
		$msg_erro = &$this->msg_erro;

		$conditions = Array(
			'abreviacao' => $uf
		);

		$dados_uf = $this->EnderecoEstado->find('first',compact('conditions'));

		if (empty($dados_uf['EnderecoEstado']['codigo'])) {
			$msg_erro[] = 'O Estado informado é inválido.';
			return false;
		}

		return $dados_uf['EnderecoEstado']['codigo'];

	}


	public function getCodigoCidade($nome_cidade = "", $uf = "", $pais = "", $retorna_cep = false) {
		$msg_erro = &$this->msg_erro;

		$this->EnderecoCidade->bindPais();
		if ($retorna_cep) $this->EnderecoCidade->bindCep();

		$conditions = Array(
			'EnderecoCidade.invalido' => 0,
			'UPPER(EnderecoCidade.descricao) COLLATE sql_latin1_general_cp1251_ci_as' => strtoupper(Comum::trata_nome($nome_cidade)),
		);

		if (!empty($uf)) $conditions['EnderecoEstado.abreviacao'] = $uf;
		if (!empty($pais)) $conditions['EnderecoPais.abreviacao'] = $pais;

		/*
		$query = $this->EnderecoCidade->find('sql',compact('conditions'));
		$msg_erro[] = $query;
		return false;
		*/
		$dados_cidade = $this->EnderecoCidade->find('first',compact('conditions'));

		if (empty($dados_cidade['EnderecoCidade']['codigo'])) {
			$msg_erro[] = 'A Cidade informada é inválida.';
			return false;
		}

		if ($retorna_cep) {
			return array(
				'codigo' => $dados_cidade['EnderecoCidade']['codigo'],
				'cep' => $dados_cidade['EnderecoCep']['cep']
			);
		} else {
			return $dados_cidade['EnderecoCidade']['codigo'];
		}

	}

	private function bindTabelasEndereco() {
		$this->Endereco->bindCEP();
		$this->Endereco->bindBairro();
		$this->Endereco->bindCidade();
		$this->Endereco->bindEnderecoEstado();
	}

	public function getCodigoEndereco($dados_pesquisa) {
		$msg_erro = &$this->msg_erro;


		$pesquisa = array();
		if (!empty($dados_pesquisa['logradouro'])) {
			$pesquisa['Endereco.descricao'] = $dados_pesquisa['logradouro'];
		}

		if (!empty($dados_pesquisa['bairro'])) {
			$pesquisa['EnderecoBairro.descricao'] = $dados_pesquisa['bairro'];
		}

		if (!empty($dados_pesquisa['cidade'])) {
			$pesquisa['EnderecoCidade.descricao'] = $dados_pesquisa['cidade'];
		}

		if (!empty($dados_pesquisa['uf'])) {
			$pesquisa['EnderecoEstado.abreviacao'] = $dados_pesquisa['uf'];
		}

		if (!empty($dados_pesquisa['cep'])) {
			$pesquisa['EnderecoCep.cep'] = $dados_pesquisa['cep'];
		}		

		$conditions = $pesquisa;
		$this->bindTabelasEndereco();
		$dados_uf = $this->Endereco->find('first',compact('conditions'));
		if (!empty($dados_uf['Endereco']['codigo'])) {
			return $dados_uf['Endereco']['codigo'];
		}

		$conditions = Array('EnderecoCep.cep'=>$dados_pesquisa['cep']);
		$this->bindTabelasEndereco();
		$dados_uf = $this->Endereco->find('first',compact('conditions'));			
		if (!empty($dados_uf['Endereco']['codigo'])) {
			return $dados_uf['Endereco']['codigo'];
		}

		$conditions = $pesquisa;
		unset($conditions['EnderecoCep.cep']);
		$this->bindTabelasEndereco();
		$dados_uf = $this->Endereco->find('first',compact('conditions'));			
		if (!empty($dados_uf['Endereco']['codigo'])) {
			return $dados_uf['Endereco']['codigo'];
		}

		//if (empty($dados_uf['EnderecoEstado']['codigo'])) {
		$msg_erro[] = 'O Endereço informado é inválido.';
		return false;
		//}

		

	}

	private function getDadosVeiculo($placa) {

		$this->loadModels('Veiculo','EnderecoCidade');

		$msg_erro = &$this->msg_erro;

		$fields = array(
			'Veiculo.*',
			'Tecnologia.descricao',
			'VeiculoCor.descricao',
			'VeiculoModelo.descricao',
			'VeiculoModelo.codigo_veiculo_fabricante',
			'VeiculoFabricante.descricao',
			'VeiculoTipo.descricao',
			'EnderecoCidade.descricao',
			'EnderecoEstado.abreviacao',
			'EnderecoPais.abreviacao',
		);

		$veiculo = $this->Veiculo->bucaVeiculoPorPlaca($placa, $fields);

		if (!empty($veiculo['Veiculo']['codigo_cidade_emplacamento'])) {
			$this->EnderecoCidade->bindCep();
			$conditions = Array('EnderecoCidade.codigo' => $veiculo['Veiculo']['codigo_cidade_emplacamento']);
			$fields = Array("EnderecoCidade.codigo", "EnderecoCep.cep");

			$dados_cidade = $this->EnderecoCidade->find('first',compact('conditions','fields'));

			$veiculo['EnderecoCep'] = Array(
				'cep' => $dados_cidade['EnderecoCep']['cep']
			);
		}

		return $veiculo;
	}	

	public function consultaMotoristaTeleconsult($codigo_cliente, &$data){
		$msg_erro = &$this->msg_erro;
		$placa 			= isset($data['placa'])   ? str_replace('-', '', $data['placa'])	: NULL;
		$placa_carreta 	= isset($data['placa_carreta']) 	  ? str_replace('-', '', $data['placa_carreta']) 	: NULL;		
		$cpf_motorista 	= isset($data['codigo_documento']) ? $data['codigo_documento'] 						: $data['motorista_cpf'];
		$categoria 		= isset($data['categoria']) && ($data['categoria'] !=1 ) ? $data['categoria'] : 'NULL';
		$codigo_carga_tipo = isset($data['codigo_carga_tipo']) ? $data['codigo_carga_tipo'] : NULL;
		$codigo_carga_valor = isset($data['codigo_carga_valor']) ? $data['codigo_carga_valor'] : NULL;
		$codigo_endereco_cidade_origem = isset($data['codigo_endereco_cidade_origem']) ? $data['codigo_endereco_cidade_origem'] : NULL;
		$codigo_endereco_cidade_destino = isset($data['codigo_endereco_cidade_destino']) ? $data['codigo_endereco_cidade_destino'] : NULL;
		$condicoes = Array(
			"@codigo_cliente = ".$codigo_cliente,
			"@codigo_documento = '".$cpf_motorista."'",
			"@placa = '".$placa."'",
			"@codigo_produto = '".$data['codigo_produto']."'",
			"@departamento = 'T'",
			"@gera_cobranca = 0",
			"@codigo_usuario_inclusao = 1",
			"@senha = ''",
			"@placa_carreta = '".$placa_carreta."'",
			"@codigo_carga_tipo = ".$codigo_carga_tipo,
			"@codigo_endereco_cidade_origem = ".$codigo_endereco_cidade_origem,
			"@codigo_endereco_cidade_destino = ".$codigo_endereco_cidade_destino,
			"@codigo_carga_valor = ".$codigo_carga_valor,
			"@consulta_web = 1",
			"@categoria = ". $categoria
		);
		$sql = sprintf("exec dbteleconsult.informacoes.usp_consulta_status_motorista %s",implode(', ', $condicoes));
		$retorno = $this->StoredProcedure->query($sql);
		if($retorno){
			$observacao = $retorno[0][0]['observacao'];
			$observacao = str_replace(', CLICANDO NO MENU ACIMA (CADASTRO DE PROFISSIONAL)','',$observacao);
			$arrRetorno = Array(
				'consulta' => $retorno[0][0]['numero_liberacao'],
				'status' => iconv('iso-8859-1','utf-8',$retorno[0][0]['mensagem']),
				'mensagem' => iconv('iso-8859-1','utf-8',$observacao),
			);
			if( $categoria == 2 ){
				$arrRetorno['validade'] = date('d/m/Y', strtotime($retorno[0][0]['validade_ficha']));
				$arrRetorno['consulta'] = '';
			}
			return $arrRetorno;

		} else {
			$msg_erro[] = "Não foi possível pesquisar os dados do motorista.";
			return false;
		}
	}


	public function validaDocumento($documento) {
		$msg_erro = &$this->msg_erro;
			
		if ((!isset($documento)) || (empty($documento)) ) {
			$msg_erro[] = "CPF profissional não informado";
			return false;
		}

		if(!Comum::validarCPF($documento)) {
			$msg_erro[] = "O CPF do profissional é inválido";
			return false;
		}

		$dados_profissional_cadastro = $this->Profissional->buscaPorCPF($documento);
		if (empty($dados_profissional_cadastro['Profissional']['codigo'])) {
			$msg_erro[] = "O CPF do profissional não consta no banco de dados. Enviar ficha para cadastro.";
			return false;
		}

		return true;
	}

	function consultarProfissional($consulta) {
		$sucesso 	= NULL;
		$erro 		= Array();
		$this->imports();

		App::import('Vendor', 'xml'.DS.'array2_xml');
		App::import('Vendor', 'xml'.DS.'xml2_array');

		try{

			$this->TeleconsultIntegracao->arquivo 		= 'ConsultarProfissional';
			$this->TeleconsultIntegracao->conteudo 		= Comum::objectToXML($consulta,'consulta');
			$this->TeleconsultIntegracao->name 			= 'TeleconsultIntegracao';

			$codigo_cliente = $this->getCliente($consulta);
			if ($codigo_cliente===false) {
				throw new Exception();
			}

			$this->TeleconsultIntegracao->cliente_portal = $codigo_cliente;

			$arrConsulta = Array();
			$arrConsulta['codigo_produto'] 		= $consulta->produto;
			$arrConsulta['categoria'] 			= NULL;
			if (isset($consulta->profissional->carreteiro) && $consulta->profissional->carreteiro ){
				$arrConsulta['categoria'] = ($consulta->profissional->carreteiro=='S' ? 1 : 2 );
			}
			$arrConsulta['codigo_documento'] 	= $consulta->profissional->documento;

			if (!$this->validaDocumento($consulta->profissional->documento)) {
				throw new Exception();
			}			
			$placas = $consulta->veiculos->placa;

			if (is_array($placas) && count($placas)>0) {
				foreach ($placas as $seq => $placa) {
					switch($seq) {
						case 0:
							$arrConsulta['placa'] = $placa;
							break;
						case 1:
							$arrConsulta['placa_carreta'] = $placa;
							break;
					}
				}
			} else {
				$arrConsulta['placa'] = $placas;
			}

			$arrConsulta['codigo_carga_tipo'] = $consulta->carga_tipo;

			$arrConsulta['codigo_carga_valor'] = $this->getCodigoValor($consulta->carga_valor);
			if ($arrConsulta['codigo_carga_valor']===false) {
				throw new Exception();
			}

			$arrConsulta['codigo_endereco_cidade_origem'] = $this->getCodigoCidade($consulta->cidade_origem, $consulta->uf_origem, $consulta->pais_origem);
			if ($arrConsulta['codigo_endereco_cidade_origem']===false) {
				throw new Exception();
			}

			$arrConsulta['codigo_endereco_cidade_destino'] = $this->getCodigoCidade($consulta->cidade_destino, $consulta->uf_destino, $consulta->pais_destino);
			if ($arrConsulta['codigo_endereco_cidade_destino']===false) {
				throw new Exception();
			}

			$retorno = $this->consultaMotoristaTeleconsult($codigo_cliente,$arrConsulta);
			if ($retorno===false) {
				throw new Exception();
			}

			//$this->msg_erro[] = var_export($arrConsulta,true);
			//$this->msg_erro[] = 'teste';
			//throw new Exception();


			$arrayRet = Array(
				'retorno' => $retorno
			);

			$my_xml = Array2XML::createXML('ns1:consultaResponse',$arrayRet);
			$mensagem = $my_xml->saveXml();			
			
			$mensagem = iconv('utf-8', 'iso-8859-1', $mensagem);

			$parametros = array(
				'status'		=> TeleconsultIntegracao::SUCESSO,
				'descricao'		=> 'Consulta Profissional realizada com sucesso',
				'mensagem'		=> $mensagem,
				'tipo_operacao' => 'ConsultaProfissional'
			);
			$this->TeleconsultIntegracao->cadastrarLog($parametros);


			return $arrayRet;			



		} catch (Exception $ex ){
			
			$erro = implode("\n", $this->msg_erro);
			
			$msg_erro = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg_erro)) {
				$erro .= "\n".$msg_erro;
			}
			
			//$erro = 'aqui';
			//$erro = iconv('iso-8859-1', 'utf-8', $erro);
			$erro_r = iconv('utf-8', 'iso-8859-1', $erro);

			$parametros = array(
				'mensagem'		=> $erro_r,
				'status'		=> TeleconsultIntegracao::ERRO,
				'descricao'		=> $erro_r,
				'tipo_operacao' => 'Consulta Profissional'
			);

			$this->TeleconsultIntegracao->cadastrarLog($parametros);

			$codigo_erro = (string)TeleconsultIntegracao::ERRO;
			//$codigo_erro = '1';
			$result = new SoapFault($codigo_erro,$erro,'',array('erro'=>$erro));
			return $result;

		}

	}

	private function validaCategoria($codigo_categoria = "") {
		try {
			$msg_erro = &$this->msg_erro;

			if ($codigo_categoria=="") throw new Exception("Categoria não informada");

			if (!$this->ProfissionalTipo->getProfissionalTipoByCodigo($codigo_categoria)) throw new Exception("Categoria informada não se encontra no cadastro");

			return true;

		} catch(Exception $ex) {
			$msg_erro = &$this->msg_erro;

			$msg = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg)) {
				$msg_erro[] = $msg;
			}
			return false;
		}
	}

	private function montaArrayCNH($cnh) {
		try {
			$msg_erro = &$this->msg_erro;
			$this->loadModels('TipoCnh');

			$dados_cnh = Array('cnh'=>'','codigo_tipo_cnh'=>'','cnh_vencimento'=>'','codigo_endereco_estado_emissao_cnh'=>'','data_primeira_cnh'=>'','codigo_seguranca_cnh'=>'');

			if (!empty($cnh)) {

				if(empty($cnh->cnh)) throw new Exception("CNH não informada");
				if(!Validation::numeric($cnh->cnh)) throw new Exception("CNH inválida");
				$dados_cnh['cnh'] = $cnh->cnh;

				if(empty($cnh->categoria)) throw new Exception("Categoria CNH não informada");
				$conditions = Array('descricao'=>$cnh->categoria);

				$dados_tipo_cnh = $this->TipoCnh->find('first',compact('conditions'));
				if (empty($dados_tipo_cnh['TipoCnh']['codigo'])) throw new Exception("Categoria CNH inválida");
				
				$dados_cnh['codigo_tipo_cnh'] = $dados_tipo_cnh['TipoCnh']['codigo'];

				if(empty($cnh->vencimento)) throw new Exception("Vencimento CNH não informado");
				if(!Validation::date($cnh->vencimento,'dmy')) throw new Exception("Vencimento CNH inválido");
				$dados_cnh['cnh_vencimento'] = $cnh->vencimento;

				if(empty($cnh->uf_emissao)) throw new Exception("UF Emissão CNH não informado");
				$codigo_uf_emissao = $this->getCodigoUF($cnh->uf_emissao);

				if ($codigo_uf_emissao===false) throw new Exception();
				$dados_cnh['codigo_endereco_estado_emissao_cnh'] = $codigo_uf_emissao;

				if (!empty($cnh->data_primeira_cnh)) {
					if(!Validation::date($cnh->data_primeira_cnh,'dmy')) throw new Exception("Data Primeira CNH inválido");
					$dados_cnh['data_primeira_cnh'] = $cnh->data_primeira_cnh;
				}

				if (!empty($cnh->codigo_seguranca)) {
					if(!Validation::numeric($cnh->codigo_seguranca)) throw new Exception("Código Segurança CNH inválido");
					$dados_cnh['codigo_seguranca_cnh'] = $cnh->codigo_seguranca;
				}

				if (!empty($cnh->data_inicio_mopp)) {
					if(!Validation::date($cnh->data_inicio_mopp,'dmy')) throw new Exception("Data Início MOPP inválido");
					$dados_cnh['possui_mopp'] = 1;
					$dados_cnh['data_inicio_mopp'] = $cnh->data_inicio_mopp;
				} else {
					$dados_cnh['possui_mopp'] = 0;
				}


			}

			return $dados_cnh;

		} catch(Exception $ex) {
			$msg_erro = &$this->msg_erro;

			$msg = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg)) {
				$msg_erro[] = $msg;
			}
			return false;
		}
	}

	private function montaArrayEndereco($endereco, $tipo_endereco = 'profissional') {
		try {
			$msg_erro = &$this->msg_erro;
			$this->loadModels('Endereco');

			$dados_endereco = Array('cep'=>'','codigo_endereco'=>'','numero'=>'','complemento'=>'');
			$tipo_endereco = ucfirst($tipo_endereco);

			if (!empty($endereco)) {

				if(empty($endereco->uf)) throw new Exception("UF $tipo_endereco não informada");
				if(empty($endereco->cidade)) throw new Exception("Cidade $tipo_endereco não informada");
				if(empty($endereco->bairro)) throw new Exception("Bairro $tipo_endereco não informado");
				if(empty($endereco->logradouro)) throw new Exception("Logradouro $tipo_endereco não informado");
				if(empty($endereco->cep)) throw new Exception("CEP $tipo_endereco não informado");
				if(!Validation::numeric($endereco->cep)) throw new Exception("CEP $tipo_endereco inválido");

				$dados_pesquisa_endereco = array(
					'logradouro' => $endereco->logradouro,
					'bairro' => $endereco->bairro,
					'cidade' => $endereco->cidade,
					'uf' => $endereco->uf,
					'cep' => $endereco->cep
				);

				$codigo_endereco = $this->getCodigoEndereco($dados_pesquisa_endereco);
				if ($codigo_endereco===false) throw new Exception();

				$dados_endereco['cep'] = $endereco->cep;
				$dados_endereco['endereco_cep'] = $endereco->cep;
				$dados_endereco['codigo_endereco'] = $codigo_endereco;

				if(empty($endereco->numero)) throw new Exception("Número $tipo_endereco não informado");
				$dados_endereco['numero'] = $endereco->numero;

				if (!empty($endereco->complemento)) {
					$dados_endereco['complemento'] = $endereco->complemento;
				} else {
					$dados_endereco['complemento'] = '';
				}
				
			}

			return $dados_endereco;

		} catch(Exception $ex) {
			$msg_erro = &$this->msg_erro;

			$msg = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg)) {
				$msg_erro[] = $msg;
			}
			return false;
		}
	}

	private function montaArrayContato($contato, $tipo_contato = 'profissional', $seq_contato = 0) {
		try {
			$msg_erro = &$this->msg_erro;
			$this->loadModels('TipoContato','TipoRetorno');

			//$dados_contato = Array('nome'=>'','codigo_tipo_contato'=>'','codigo_tipo_retorno'=>'','descricao'=>'');
			$tipo = $tipo_contato;
			$tipo_contato = ucfirst($tipo_contato);

			if(empty($contato->nome)) throw new Exception("Nome do Contato(".($seq_contato+1).") do $tipo_contato não informado");
			$dados_contato['nome'] = $contato->nome;

			if(empty($contato->tipo_contato)) throw new Exception("Tipo do Contato(".($seq_contato+1).") do $tipo_contato não informado");

			$conditions = array('codigo'=>$contato->tipo_contato);
			$dados_tipo_contato = $this->TipoContato->find('first',compact('conditions'));
			if (empty($dados_tipo_contato['TipoContato']['codigo'])) throw new Exception("Tipo do Contato(".($seq_contato+1).") do $tipo_contato é inválido");

			$dados_contato['codigo_tipo_contato'] = $dados_tipo_contato['TipoContato']['codigo'];

			$conditions = array('codigo'=>$contato->tipo_retorno);
			if ($tipo=='profissional') {
				$conditions['profissional'] = 1;
			} else {
				$conditions['proprietario'] = 1;
			}
			$dados_tipo_retorno = $this->TipoRetorno->find('first',compact('conditions'));
			if (empty($dados_tipo_retorno['TipoRetorno']['codigo'])) throw new Exception("Tipo do Retorno(".($seq_contato+1).") do $tipo_contato é inválido");

			$dados_contato['codigo_tipo_retorno'] = $dados_tipo_retorno['TipoRetorno']['codigo'];
			
			if(empty($contato->descricao)) throw new Exception("Descrição do Contato(".($seq_contato+1).") do $tipo_contato não informado");
			$dados_contato['descricao'] = $contato->descricao;

			return $dados_contato;

		} catch(Exception $ex) {
			$msg_erro = &$this->msg_erro;

			$msg = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg)) {
				$msg_erro[] = $msg;
			}
			return false;
		}
	}

	private function montaArrayProfissional($profissional) {
		try {
			$msg_erro = &$this->msg_erro;

			$dados_profissional = Array();

			if (empty($profissional->categoria)) throw new Exception("Categoria do profissional não informada");
			if (!$this->validaCategoria($profissional->categoria)) throw new Exception();

			$dados_profissional['codigo_profissional_tipo'] = $profissional->categoria;

			if ((!isset($profissional->documento)) || (empty($profissional->documento)) ) throw new Exception("CPF profissional não informado");

			if(!Comum::validarCPF($profissional->documento)) throw new Exception("O CPF do profissional é inválido");

			$dados_profissional['codigo_documento'] = $profissional->documento;
			$dados_profissional_cadastro = $this->Profissional->buscaPorCPF($profissional->documento); 

			$dados_profissional['codigo'] = (isset($dados_profissional_cadastro['Profissional']['codigo']) ? $dados_profissional_cadastro['Profissional']['codigo'] : '');

			if (empty($profissional->nome)) throw new Exception("Nome do profissional não informado");
			$dados_profissional['nome'] = $profissional->nome;
			$dados_profissional['nome_pai'] = $profissional->nome_pai;//(isset($profissional->nome_pai) ? $profissional->nome_pai : '');
			if (empty($profissional->nome_mae)) throw new Exception("Nome da mãe do profissional não informado");
			$dados_profissional['nome_mae'] = $profissional->nome_mae;

			$dados_profissional['data_inclusao'] = (isset($dados_profissional_cadastro['Profissional']['data_inclusao']) ? $dados_profissional_cadastro['Profissional']['data_inclusao'] : date('d/m/Y'));
			
			if (empty($profissional->rg)) throw new Exception("RG não informado");
			$dados_profissional['rg'] = $profissional->rg;

			if ((!isset($profissional->uf_rg)) || (empty($profissional->uf_rg)) ) throw new Exception("UF de emissão não informado");
			$codigo_uf = $this->getCodigoUF($profissional->uf_rg);
			if ($codigo_uf===false) throw new Exception();
			
			$dados_profissional['codigo_estado_rg'] = $codigo_uf;

			if (empty($profissional->data_emissao)) throw new Exception("Data Emissão RG não informado");
			if(!Validation::date($profissional->data_emissao,'dmy')) throw new Exception("Data Emissão RG inválida");
			$dados_profissional['rg_data_emissao'] = $profissional->data_emissao;

			if (empty($profissional->data_nascimento)) throw new Exception("Data de Nascimento não informado");
			if(!Validation::date($profissional->data_nascimento,'dmy')) throw new Exception("Data de Nascimento inválida");
			$dados_profissional['data_nascimento'] = $profissional->data_nascimento;

			if (empty($profissional->cidade_naturalidade)) throw new Exception("Cidade Naturalidade não informada");
			if (empty($profissional->uf_naturalidade)) throw new Exception("UF Naturalidade não informada");
			if (empty($profissional->pais_naturalidade)) throw new Exception("País Naturalidade não informada");

			$codigo_cidade_naturalidade = $this->getCodigoCidade($profissional->cidade_naturalidade, $profissional->uf_naturalidade, $profissional->pais_naturalidade);
			if ($codigo_cidade_naturalidade===false) throw new Exception();

			$dados_profissional['cep_unico_naturalidade'] = '0';
			$dados_profissional['descricao_endereco_cidade_naturalidade'] = $profissional->cidade_naturalidade;
			$dados_profissional['abreviacao_endereco_estado_naturalidade'] = $profissional->uf_naturalidade;
			$dados_profissional['abreviacao_endereco_pais_naturalidade'] = $profissional->pais_naturalidade;
			$dados_profissional['codigo_endereco_cidade_naturalidade'] = $codigo_cidade_naturalidade;

			$dados_cnh = $this->montaArrayCNH($profissional->cnh);
			if ($dados_cnh===false) throw new Exception();

			$dados_profissional = array_merge($dados_profissional,$dados_cnh);

			$dados_endereco = $this->montaArrayEndereco($profissional->endereco,'profissional');
			if ($dados_endereco===false) throw new Exception();		
			
			$dados_profissional['cep'] = $dados_endereco['cep'];
			$dados_profissional['ProfissionalEndereco'] = $dados_endereco;

			$contatos = $profissional->contatos->contato;

			if (count($contatos)==1) {
				list($key, $val) = each($contatos);
				if ($key!='0') $contatos = Array(0=>$contatos);
			}

			$seq = 0;
			foreach ($contatos as $contato) {
				$dados_contato = $this->montaArrayContato($contato,'profissional',$seq);
				if ($dados_contato===false) throw new Exception();

				$dados_profissional['ProfissionalContato'][] = $dados_contato;

				$seq++;
			}

			return $dados_profissional;

		} catch(Exception $ex) {
			$msg_erro = &$this->msg_erro;

			$msg = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg)) {
				$msg_erro[] = $msg;
			}
			return false;
		}
	}


	private function montaArrayProprietario($proprietario, $documento_profissional, $seq = null) {
		try {
			$this->loadModels('Proprietario');

			$msg_erro = &$this->msg_erro;

			$tipo_proprietario = (is_null($seq) ? "do proprietário":"do proprietário do veículo (".($seq+1).")" );
			$dados_proprietario = Array();

			if (empty($proprietario->documento))  throw new Exception("CPF $tipo_proprietario não informado");
			if(!Comum::validarCPF($proprietario->documento)) throw new Exception("O CPF $tipo_proprietario é inválido");
			$dados_proprietario['codigo_documento'] = $proprietario->documento;

			$dados_proprietario['proprietario'] = ($dados_proprietario['codigo_documento']==$documento_profissional?'1':'0');

			$codigo_proprietario_cadastro = $this->Proprietario->buscaCodigoProprietario($proprietario->documento); 

			$dados_proprietario['codigo'] = (isset($codigo_proprietario_cadastro) ? $codigo_proprietario_cadastro : '');
			if (empty($proprietario->razao_social)) throw new Exception("Nome / Razão Social $tipo_proprietario não informado");
			$dados_proprietario['nome_razao_social'] = $proprietario->razao_social;
			
			$dados_proprietario['rg'] = "";
			if (!empty($proprietario->rg_ie)) {
				if(!Validation::numeric($proprietario->rg_ie)) throw new Exception("RG / IE $tipo_proprietario inválida");
				$dados_proprietario['rg'] = $proprietario->rg_ie;
			}
			
			$dados_proprietario['rntrc'] = "";
			if (!empty($proprietario->rntrc)) {
				if(!Validation::numeric($proprietario->rntrc)) throw new Exception("RNTRC $tipo_proprietario inválida");
				$dados_proprietario['rntrc'] = $proprietario->rntrc;
			}

			$dados_endereco = $this->montaArrayEndereco($proprietario->endereco,'proprietario');
			if ($dados_endereco===false) throw new Exception();		
			
			$dados_proprietario['cep'] = $dados_endereco['cep'];
			$dados_proprietario['ProprietarioEndereco'][0] = $dados_endereco;


			$contatos = (isset($proprietario->contatos->contato) ? $proprietario->contatos->contato : null);

			if (is_array($contatos)) {
				if (count($contatos)==1) {
					list($key, $val) = each($contatos);
					if ($key!='0') $contatos = Array(0=>$contatos);
				}

				$seq = 0;
				foreach ($contatos as $contato) {
					$dados_contato = $this->montaArrayContato($contato,'proprietario',$seq);
					if ($dados_contato===false) throw new Exception();

					$dados_proprietario['ProprietarioContato'][] = $dados_contato;

					$seq++;
				}
			}
			return $dados_proprietario;

		} catch(Exception $ex) {
			$msg_erro = &$this->msg_erro;

			$msg = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg)) {
				$msg_erro[] = $msg;
			}
			return false;
		}
	}

	private function montaArrayVeiculo($veiculo, $seq = null) {
		try {
			$this->loadModels('Tecnologia','VeiculoCor','VeiculoFabricante','VeiculoModelo');

			$msg_erro = &$this->msg_erro;

			$dados_veiculo = Array();

			if (empty($veiculo->placa))  throw new Exception("Placa do veículo ".($seq+1)." não informada");
			if (!Comum::isVeiculo($veiculo->placa)) throw new Exception("Placa do veículo ".($seq+1)." é inválida");
			$dados_veiculo['placa'] = $veiculo->placa;

			$dados_veiculo_cadastro = $this->getDadosVeiculo($veiculo->placa);
			if (!empty($dados_veiculo_cadastro['Veiculo']['codigo'])) {
				$dados_veiculo['codigo'] 							= $dados_veiculo_cadastro['Veiculo']['codigo'];
				$dados_veiculo['chassi'] 							= $dados_veiculo_cadastro['Veiculo']['chassi'];
				$dados_veiculo['renavam'] 							= $dados_veiculo_cadastro['Veiculo']['renavam'];
				$dados_veiculo['descricao_cidade_emplacamento'] 	= $dados_veiculo_cadastro['EnderecoCidade']['descricao'];
				$dados_veiculo['cep_unico_emplacamento'] 			= $dados_veiculo_cadastro['EnderecoCep']['cep'];
				$dados_veiculo['codigo_cidade_emplacamento'] 		= $dados_veiculo_cadastro['Veiculo']['codigo_cidade_emplacamento'];
				$dados_veiculo['abreviacao_estado_emplacamento'] 	= $dados_veiculo_cadastro['EnderecoEstado']['abreviacao'];
				$dados_veiculo['abreviacao_pais_emplacamento'] 		= $dados_veiculo_cadastro['EnderecoPais']['abreviacao'];
				$dados_veiculo['codigo_tecnologia'] 				= $dados_veiculo_cadastro['Veiculo']['codigo_veiculo_tecnologia'];
				$dados_veiculo['codigo_veiculo_cor'] 				= $dados_veiculo_cadastro['Veiculo']['codigo_veiculo_cor'];
				$dados_veiculo['ano_fabricacao'] 					= $dados_veiculo_cadastro['Veiculo']['ano_fabricacao'];
				$dados_veiculo['ano'] 								= $dados_veiculo_cadastro['Veiculo']['ano'];
				$dados_veiculo['codigo_veiculo_fabricante'] 		= $dados_veiculo_cadastro['VeiculoModelo']['codigo_veiculo_fabricante'];
				$dados_veiculo['codigo_veiculo_modelo'] 			= $dados_veiculo_cadastro['Veiculo']['codigo_veiculo_modelo'];
			} else {
				$dados_veiculo['codigo'] 							= '';
				$dados_veiculo['chassi'] 							= '';
				$dados_veiculo['renavam'] 							= '';
				$dados_veiculo['descricao_cidade_emplacamento'] 	= '';
				$dados_veiculo['cep_unico_emplacamento'] 			= '';
				$dados_veiculo['codigo_cidade_emplacamento'] 		= '';
				$dados_veiculo['abreviacao_estado_emplacamento'] 	= '';
				$dados_veiculo['abreviacao_pais_emplacamento'] 		= '';
				$dados_veiculo['codigo_tecnologia'] 				= '';
				$dados_veiculo['codigo_veiculo_cor'] 				= '';
				$dados_veiculo['ano_fabricacao'] 					= '';
				$dados_veiculo['ano'] 								= '';
				$dados_veiculo['codigo_veiculo_fabricante'] 		= '';
				$dados_veiculo['codigo_veiculo_modelo'] 			= '';
			}
			//$codigo_veiculo = $this->Veiculo->buscaCodigodaPlaca($dados_veiculo['placa']);
			/*if (!empty($codigo_veiculo)) {

			}*/
			//if (empty($codigo_veiculo))  throw new Exception("Veículo ".($seq+1)." não encontrado");

			if (!empty($veiculo->renavam)) $dados_veiculo['renavam'] = $veiculo->renavam;
			if (!empty($veiculo->chassi)) $dados_veiculo['chassi'] = $veiculo->chassi;

			if (!empty($veiculo->cidade_emplacamento)) {
				if (empty($veiculo->uf_emplacamento)) throw new Exception("UF do emplacamento do veículo ".($seq+1)." não informada");

				if (empty($veiculo->pais_emplacamento)) $veiculo->pais_emplacamento = 'BR';
				if (!Validation::maxLength($veiculo->pais_emplacamento,3)) throw new Exception("País do veículo ".($seq+1)." inválido");

				$dados_cidade = $this->getCodigoCidade($veiculo->cidade_emplacamento,$veiculo->uf_emplacamento,$veiculo->pais_emplacamento,true);
				if (empty($dados_cidade['codigo'])) throw new Exception("Cidade do emplacamento do veículo ".($seq+1)." inválida");

				$dados_veiculo['descricao_cidade_emplacamento'] = $veiculo->cidade_emplacamento;
				$dados_veiculo['codigo_cidade_emplacamento'] = $dados_cidade['codigo'];
				$dados_veiculo['abreviacao_estado_emplacamento'] = $veiculo->uf_emplacamento;
				$dados_veiculo['abreviacao_pais_emplacamento'] = $veiculo->pais_emplacamento;
				$dados_veiculo['cep_unico_emplacamento'] = $dados_cidade['cep'];

			} else {
				if (!empty($veiculo->uf_emplacamento)) $dados_veiculo['abreviacao_estado_emplacamento'] = $veiculo->uf_emplacamento;
				if (!empty($veiculo->pais_emplacamento)) {
					if (!Validation::maxLength($veiculo->pais_emplacamento,3)) throw new Exception("País do veículo ".($seq+1)." inválido");
					$dados_veiculo['abreviacao_pais_emplacamento'] = $veiculo->pais_emplacamento;
				}
				
				/*
				$dados_veiculo['descricao_cidade_emplacamento'] = '';
				$dados_veiculo['cep_unico_emplacamento'] = '';
				$dados_veiculo['codigo_cidade_emplacamento'] = '';
				$dados_veiculo['abreviacao_estado_emplacamento'] = $veiculo->uf_emplacamento;
				if (!empty($veiculo->pais_emplacamento)) {
					if (!Validation::maxLength($veiculo->pais_emplacamento,3)) throw new Exception("País do veículo ".($seq+1)." inválido");
				}
				$dados_veiculo['abreviacao_pais_emplacamento'] = $veiculo->pais_emplacamento;
				*/
			}

			if (!empty($veiculo->tecnologia)) {
				$conditions = array('descricao'=>$veiculo->tecnologia);
				$dados_tecnologia = $this->Tecnologia->find('first',compact('conditions'));
				if (empty($dados_tecnologia['Tecnologia']['codigo'])) throw new Exception("Tecnologia do veículo ".($seq+1)." inválida");

				$dados_veiculo['codigo_tecnologia'] = $dados_tecnologia['Tecnologia']['codigo'];
			} else {
				//$dados_veiculo['codigo_tecnologia'] = '';
			}
			
			if (!empty($veiculo->veiculo_cor)) {
				$conditions = array('descricao'=>$veiculo->veiculo_cor);
				$dados_cor = $this->VeiculoCor->find('first',compact('conditions'));
				if (empty($dados_cor['VeiculoCor']['codigo'])) throw new Exception("Cor do veículo ".($seq+1)." inválida");

				$dados_veiculo['codigo_veiculo_cor'] = $dados_cor['VeiculoCor']['codigo'];
			} else {
				//$dados_veiculo['codigo_veiculo_cor'] = '';
			}

			if (!empty($veiculo->ano_fabricacao)) {
				if (!Validation::numeric($veiculo->ano_fabricacao)) throw new Exception("Ano de Fabricação do veículo ".($seq+1)." inválido");
				$dados_veiculo['ano_fabricacao'] = $veiculo->ano_fabricacao;
			} else {
				//$dados_veiculo['ano_fabricacao'] = '';
			}

			if (!empty($veiculo->ano_modelo)) {
				if (!Validation::numeric($veiculo->ano_modelo)) throw new Exception("Ano do Modelo do veículo ".($seq+1)." inválido");
				$dados_veiculo['ano'] = $veiculo->ano_modelo;
			} else {
				//$dados_veiculo['ano_modelo'] = '';
			}

			if (!empty($veiculo->fabricante)) {
				$conditions = array('descricao'=>$veiculo->fabricante);
				$dados_fabricante = $this->VeiculoFabricante->find('first',compact('conditions'));
				if (empty($dados_fabricante['VeiculoFabricante']['codigo'])) throw new Exception("Fabricante do veículo ".($seq+1)." inválida");

				$dados_veiculo['codigo_veiculo_fabricante'] = $dados_fabricante['VeiculoFabricante']['codigo'];

				if (!empty($veiculo->modelo)) {
					$conditions = array('VeiculoModelo.descricao'=>$veiculo->modelo,'VeiculoModelo.codigo_veiculo_fabricante'=>$dados_veiculo['codigo_veiculo_fabricante']);
					$dados_modelo = $this->VeiculoModelo->find('first',compact('conditions'));
					if (empty($dados_modelo['VeiculoModelo']['codigo'])) throw new Exception("Modelo do veículo ".($seq+1)." inválida");

					$dados_veiculo['codigo_veiculo_modelo'] = $dados_modelo['VeiculoModelo']['codigo'];
				} else {
					//$dados_veiculo['codigo_veiculo_modelo'] = '';
				}				
			} else {
				//$dados_veiculo['codigo_veiculo_fabricante'] = '';
				//$dados_veiculo['codigo_veiculo_modelo'] = '';
			}

			if (!empty($dados_veiculo['abreviacao_estado_emplacamento'])) {
				$dados_veiculo['codigo_estado'] = $this->getCodigoUF($dados_veiculo['abreviacao_estado_emplacamento']);
				if ($dados_veiculo['codigo_estado']==false) throw new Exception();
			}

			return $dados_veiculo;

		} catch(Exception $ex) {
			$msg_erro = &$this->msg_erro;

			$msg = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg)) {
				$msg_erro[] = $msg;
			}
			return false;
		}
	}

	private function montaArrayComplementares($complementares, $seq = null) {
		try {

			$msg_erro = &$this->msg_erro;

			$dados_complementares = Array();


			// Questão 1 - Foi vítima de roubo
			if (empty($complementares->foi_vitima_de_roubo))  throw new Exception("Indicação de Foi vítima de roubo não informada");
			if (!Validation::multiple($complementares->foi_vitima_de_roubo,Array('in'=>Array('S','N')))) throw new Exception("Indicação de Foi vítima de roubo inválida");

			$dados_complementares[1]['codigo_questao_resposta'] = ($complementares->foi_vitima_de_roubo=='S'?1:2);
			
			if ($complementares->foi_vitima_de_roubo=='S') {
				if (empty($complementares->quantidade_vezes_roubado)) throw new Exception("Indicação de número de vezes roubado não informada");
				if (!Validation::numeric($complementares->quantidade_vezes_roubado)) throw new Exception("Indicação de número de vezes roubado inválida");
				$dados_complementares[1]['observacao'] = $complementares->quantidade_vezes_roubado;
			} else $dados_complementares[1]['observacao'] = '';

			// Questão 2 - Sofreu acidente
			if (empty($complementares->ja_sofreu_acidente))  throw new Exception("Indicação de Já sofreu acidente não informada");
			if (!Validation::multiple($complementares->ja_sofreu_acidente,Array('in'=>Array('S','N')))) throw new Exception("Indicação de Já sofreu acidente inválida");

			$dados_complementares[2]['codigo_questao_resposta'] = ($complementares->ja_sofreu_acidente=='S'?3:4);

			if ($complementares->ja_sofreu_acidente=='S') {
				if (empty($complementares->quantidade_acidentes)) throw new Exception("Indicação de número de acidentes não informada");
				if (!Validation::numeric($complementares->quantidade_acidentes)) throw new Exception("Indicação de número de acidentes inválida");
				$dados_complementares[2]['observacao'] = $complementares->quantidade_acidentes;
			} else $dados_complementares[2]['observacao'] = '';

			// Questão 3 - Transportou para empresa
			if (empty($complementares->transportou_empresa))  throw new Exception("Indicação de Já Transportou para empresa não informada");
			if (!Validation::multiple($complementares->transportou_empresa,Array('in'=>Array('5','6','7','40')))) throw new Exception("Indicação de Já Transportou para empresa inválida");

			$dados_complementares[3]['codigo_questao_resposta'] = $complementares->transportou_empresa;

			if ($complementares->transportou_empresa!='40') {
				if (empty($complementares->quantidade_tempo_transportou_para_a_empresa)) throw new Exception("Indicação de quantidade de tempo que transportou para empresa não informada");
				if (!Validation::numeric($complementares->quantidade_tempo_transportou_para_a_empresa)) throw new Exception("Indicação de quantidade de tempo que transportou para empresa inválida");
				$dados_complementares[3]['observacao'] = $complementares->quantidade_tempo_transportou_para_a_empresa;
			} else $dados_complementares[3]['observacao'] = '';

			// Questão 4 - Possui sistema rastreamento
			if (empty($complementares->possui_sistema_rastreamento))  throw new Exception("Indicação de Possui sistema rastreamento não informada");
			if (!Validation::multiple($complementares->possui_sistema_rastreamento,Array('in'=>Array('S','N')))) throw new Exception("Indicação de Possui sistema rastreamento inválida");

			$dados_complementares[4]['codigo_questao_resposta'] = ($complementares->possui_sistema_rastreamento=='S'?8:9);

			if ($complementares->possui_sistema_rastreamento=='S') {
				if (empty($complementares->nome_sistema_rastreamento)) throw new Exception("Sistema de rastreamento não informado");
				$dados_complementares[4]['observacao'] = $complementares->nome_sistema_rastreamento;
			} else $dados_complementares[4]['observacao'] = '';


			return $dados_complementares;

		} catch(Exception $ex) {
			$msg_erro = &$this->msg_erro;

			$msg = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg)) {
				$msg_erro[] = $msg;
			}
			return false;
		}
	}	

	private function montaArrayCarga($ficha) {
		try {
			$msg_erro = &$this->msg_erro;
			$this->loadModels('CargaTipo','CargaValor');

			$dados_carga = Array();

			if(empty($ficha->carga_tipo)) throw new Exception("Tipo da Carga não informada");

			$conditions = array('codigo'=>$ficha->carga_tipo);
			$dados_tipo_carga = $this->CargaTipo->find('first',compact('conditions'));
			if (empty($dados_tipo_carga['CargaTipo']['codigo'])) throw new Exception("Tipo da Carga é inválida");

			$dados_carga['codigo_carga_tipo'] = $dados_tipo_carga['CargaTipo']['codigo'];

			if(empty($ficha->carga_valor)) throw new Exception("Valor da Carga não informado");
			if (!Validation::numeric($ficha->carga_valor)) throw new Exception("Valor da Carga inválido");
			$codigo_valor_carga = $this->getCodigoValor($ficha->carga_valor);
			if ($codigo_valor_carga==false) throw new Exception();
			$dados_carga['codigo_carga_valor'] = $codigo_valor_carga;

			if (empty($ficha->cidade_origem)) throw new Exception("Cidade de Origem não informada");
			if (empty($ficha->uf_origem)) throw new Exception("UF de Origem não informada");
			if (empty($ficha->pais_origem)) $ficha->pais_origem = 'BR';
			if (!Validation::maxLength($ficha->pais_origem,3)) throw new Exception("País de Origem inválido");
			
			$dados_cidade = $this->getCodigoCidade($ficha->cidade_origem,$ficha->uf_origem,$ficha->pais_origem,true);
			if (empty($dados_cidade['codigo'])) throw new Exception("Cidade de Origem inválida");

			$dados_carga['descricao_endereco_cidade_carga_origem'] = $ficha->cidade_origem;
			$dados_carga['codigo_endereco_cidade_carga_origem'] = $dados_cidade['codigo'];
			$dados_carga['abreviacao_endereco_estado_carga_origem'] = $ficha->uf_origem;
			$dados_carga['abreviacao_endereco_pais_carga_origem'] = $ficha->pais_origem;
			$dados_carga['cep_unico_carga_origem'] = $dados_cidade['cep'];

			if (empty($ficha->cidade_destino)) throw new Exception("Cidade de Destino não informada");
			if (empty($ficha->uf_destino)) throw new Exception("UF de Destino não informada");
			if (empty($ficha->pais_destino)) $ficha->pais_destino = 'BR';
			if (!Validation::maxLength($ficha->pais_destino,3)) throw new Exception("País de Destino inválido");
			
			$dados_cidade = $this->getCodigoCidade($ficha->cidade_destino,$ficha->uf_destino,$ficha->pais_destino,true);
			if (empty($dados_cidade['codigo'])) throw new Exception("Cidade de Destino inválida");

			$dados_carga['descricao_endereco_cidade_carga_destino'] = $ficha->cidade_destino;
			$dados_carga['codigo_endereco_cidade_carga_destino'] = $dados_cidade['codigo'];
			$dados_carga['abreviacao_endereco_estado_carga_destino'] = $ficha->uf_destino;
			$dados_carga['abreviacao_endereco_pais_carga_destino'] = $ficha->pais_destino;
			$dados_carga['cep_unico_carga_destino'] = $dados_cidade['cep'];

			return $dados_carga;

		} catch(Exception $ex) {
			$msg_erro = &$this->msg_erro;

			$msg = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg)) {
				$msg_erro[] = $msg;
			}
			return false;
		}
	}

	private function montaArraysFicha($ficha) {
		try {
			$msg_erro = &$this->msg_erro;

			$data = Array(
				'Ficha' => array()
			);

			$dados_cliente = $this->getCliente($ficha,true);
			if ($dados_cliente===false) {
				throw new Exception();
			}

			$codigo_cliente = $dados_cliente['Cliente']['codigo'];

			$data['Cliente'] = Array(
				'codigo_documento' => $dados_cliente['Cliente']['codigo_documento'],
				'razao_social' => $dados_cliente['Cliente']['razao_social'],
			);

			$data['Ficha']['apelido_usuario_solicitacao'] = $dados_cliente['Usuario']['apelido'];
			$data['Ficha']['codigo_usuario_solicitacao'] = $dados_cliente['Usuario']['codigo'];
			$data['Ficha']['codigo_cliente'] = $codigo_cliente;
			$data['Usuario']['codigo_cliente'] = $codigo_cliente;


			$codigo_embarcador = $this->getCodigoEmbarcadorTransportador($ficha,'cnpj_embarcador');
			if ($codigo_embarcador===false) {
				throw new Exception();
			}

			$data['Ficha']['codigo_cliente_embarcador'] = $codigo_embarcador;

			$codigo_transportador = $this->getCodigoEmbarcadorTransportador($ficha,'cnpj_transportador');
			if ($codigo_transportador===false) {
				throw new Exception();
			}

			$data['Ficha']['codigo_cliente_transportador'] = $codigo_transportador;

			if (empty($ficha->produto)) throw new Exception('Produto não informado');
			$data['Ficha']['codigo_produto'] = $ficha->produto;

			$dados_carga = $this->montaArrayCarga($ficha);
			if ($dados_carga===false) {
				throw new Exception();
			}
			$data['Ficha'] = array_merge($data['Ficha'],$dados_carga);

			/********************************************/
			// Dados do Retorno não passados - Verificar
			/********************************************/

			if (!isset($ficha->profissional)) {
				throw new Exception("Dados do Profissional não informados");
			}

			$dados_profissional = $this->montaArrayProfissional($ficha->profissional);
			if ($dados_profissional===false) {
				throw new Exception();
			}
			$data['Ficha']['codigo_profissional_tipo'] = $dados_profissional['codigo_profissional_tipo'];

			$data['ProfissionalEndereco'] = $dados_profissional['ProfissionalEndereco'];
			unset($dados_profissional['ProfissionalEndereco']);

			$data['ProfissionalContato'] = $dados_profissional['ProfissionalContato'];
			unset($dados_profissional['ProfissionalContato']);

			$data['Profissional'] = $dados_profissional;

			$data['Ficha']['observacao'] = $ficha->observacao;


			$veiculos = $ficha->veiculos->veiculo;
			if (count($veiculos)==1) {
				list($key, $val) = each($veiculos);
				if ($key!='0') $veiculos = Array(0=>$veiculos);
			}

			$seq = 0;
			foreach ($veiculos as $veiculo) {

				if (in_array($data['Ficha']['codigo_produto'], Array(1,2))) {
					if ($seq==0) {
						if (!empty($veiculo->proprietario)) {
							$dados_proprietario = $this->montaArrayProprietario($veiculo->proprietario,$data['Profissional']['codigo_documento']);
							if ($dados_proprietario===false) throw new Exception();
						}
						//throw new Exception(var_export($dados_proprietario,true));
					}
				} else {
					if (!empty($veiculo->proprietario)) {
						$dados_proprietario = $this->montaArrayProprietario($veiculo->proprietario,$data['Profissional']['codigo_documento'],$seq);
						if ($dados_proprietario===false) throw new Exception();
					}
				}
				$data['Motorista']['proprietario'] = $dados_proprietario['proprietario'];
				unset($dados_proprietario['proprietario']);

				//throw new Exception(var_export($dados_proprietario,true));
				$data['ProprietarioContato'] = (isset($dados_proprietario['ProprietarioContato']) ? $dados_proprietario['ProprietarioContato'] : null);
				$data['ProprietarioEndereco'] = $dados_proprietario['ProprietarioEndereco'];
				$data['Proprietario'] = $dados_proprietario;

				$dados_veiculo = $this->montaArrayVeiculo($veiculo,$seq);
				//throw new Exception(var_export($dados_veiculo,true));
				if ($dados_veiculo===false) throw new Exception();

				$data['FichaVeiculo'][$seq] = array('codigo_tecnologia'=>$dados_veiculo['codigo_tecnologia']);
				unset($dados_veiculo['codigo_tecnologia']);

				$data['Veiculo'][] = $dados_veiculo;

				$seq++;
			}

			$dados_complementares = $this->montaArrayComplementares($ficha->complementares);
			if ($dados_complementares===false) throw new Exception();

			$data['FichaQuestaoResposta'] = $dados_complementares;

			return $data;
		} catch(Exception $ex) {
			$msg_erro = &$this->msg_erro;

			$msg = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg)) {
				$msg_erro[] = $msg;
			}
			return false;
		}
	}



	function IncluirFicha($ficha) {
		$sucesso 	= NULL;
		$erro 		= Array();
		$this->imports();
		$this->loadModels('ProfissionalTipo','Profissional','Ficha');

		$this->montaArrayTagDescricao();

		App::import('Vendor', 'xml'.DS.'array2_xml');
		App::import('Vendor', 'xml'.DS.'xml2_array');

		$xml = Comum::objectToXML($ficha,'ficha');

		$objXml = XML2Array::createArray($xml);	
		//$this->msg_erro[] = var_export($objXml,true);
		//throw new Exception();

		try{

			$this->TeleconsultIntegracao->arquivo 		= 'IncluirFicha';
			$this->TeleconsultIntegracao->conteudo 		= Comum::objectToXML($ficha,'ficha');
			$this->TeleconsultIntegracao->name 			= 'TeleconsultIntegracao';

			$arrayFicha = $this->montaArraysFicha($ficha);
			if ($arrayFicha===false) {

				throw new Exception();
			}
			
			$this->TeleconsultIntegracao->cliente_portal = $arrayFicha['Ficha']['codigo_cliente'];
			
			$retorno = $this->Ficha->incluirFicha($arrayFicha);
			if ($retorno===false) {
				$msg_erro = &$this->msg_erro;
				$validationErrors = $this->Ficha->validationErrors;
				$msg_erro = array_merge($msg_erro, $validationErrors);
				throw new Exception();
			}


			$retorno = Array(
				'sucesso' => 'Ficha de Profissional incluida com sucesso'
			);

			$arrayRet = Array(
				'retorno' => $retorno
			);

			$my_xml = Array2XML::createXML('ns1:fichaResponse',$arrayRet);
			$mensagem = $my_xml->saveXml();			
			

			$parametros = array(
				'status'		=> TeleconsultIntegracao::SUCESSO,
				'descricao'		=> 'Ficha Inclusa com sucesso',
				'mensagem'		=> $mensagem,
				'tipo_operacao' => 'IncluirFicha'
			);
			$this->TeleconsultIntegracao->cadastrarLog($parametros);


			return $arrayRet;				

		} catch (Exception $ex ){
			
			$erro = implode("\n", $this->msg_erro);
			
			$msg_erro = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg_erro)) {
				$erro .= "\n".$msg_erro;
			}
			
			//$erro = 'aqui';
			//$erro = iconv('iso-8859-1', 'utf-8', $erro);
			$erro_r = iconv('utf-8', 'iso-8859-1', $erro);
			$parametros = array(
				'mensagem'		=> $erro_r,
				'status'		=> TeleconsultIntegracao::ERRO,
				'descricao'		=> $erro_r,
				'tipo_operacao' => 'IncluirFicha'
			);

			$this->TeleconsultIntegracao->cadastrarLog($parametros);
			//$codigo_erro = (string)SmIntegracao::ERRO;
			$codigo_erro = '1';
			$result = new SoapFault($codigo_erro,$erro,'',array('erro'=>$erro));
			return $result;

		}

	}

	function ConsultarFabricantes($consultaFabricantes) {
		$sucesso 	= NULL;
		$erro 		= Array();

		$this->loadModels('VeiculoFabricante','Veiculo');

		App::import('Vendor', 'xml'.DS.'array2_xml');
		App::import('Vendor', 'xml'.DS.'xml2_array');

		$xml = Comum::objectToXML($consultaFabricantes,'consultaFabricantes');

		$objXml = XML2Array::createArray($xml);	

		try{

			$descricao = $consultaFabricantes->descricao;

			$veiculos = $this->VeiculoFabricante->lista($descricao);

			$retorno = Array('fabricantes'=> Array());
			
			foreach ($veiculos as $key => $veiculo) {
				$retorno['fabricantes'][] = $veiculo;
			}

			return (object)$retorno;


		} catch (Exception $ex ){
			
			$erro = implode("\n", $this->msg_erro);
			
			$msg_erro = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg_erro)) {
				$erro .= "\n".$msg_erro;
			}
			
			$codigo_erro = '1';
			$result = new SoapFault($codigo_erro,$erro,'',array('erro'=>$erro));
			return $result;

		}
	}

	function ConsultarModelos($consultaModelos) {
		$sucesso 	= NULL;
		$erro 		= Array();

		$this->loadModels('VeiculoFabricante','VeiculoModelo');

		App::import('Vendor', 'xml'.DS.'array2_xml');
		App::import('Vendor', 'xml'.DS.'xml2_array');

		$xml = Comum::objectToXML($consultaModelos,'consultaModelos');

		$objXml = XML2Array::createArray($xml);	

		try{

			$descricao = $consultaModelos->descricao;
			$fabricante = $consultaModelos->fabricante;

			$conditions = Array('descricao'=>$fabricante);
			$dados_fabricante = $this->VeiculoFabricante->find('first',compact('conditions'));

			if (empty($dados_fabricante)) {
				throw new Exception("Fabricante não existe no cadastro");				
			}

			$codigo_fabricante = $dados_fabricante['VeiculoFabricante']['codigo'];

			$veiculos = $this->VeiculoModelo->lista($codigo_fabricante, $descricao);

			$retorno = Array('modelos'=> Array());
			
			foreach ($veiculos as $key => $veiculo) {
				$retorno['modelos'][] = $veiculo;
			}

			return (object)$retorno;


		} catch (Exception $ex ){
			
			$erro = implode("\n", $this->msg_erro);
			
			$msg_erro = (!empty($ex) ? $ex->getmessage() : '');
			if (!empty($msg_erro)) {
				$erro .= "\n".$msg_erro;
			}
			
			$codigo_erro = '1';
			$result = new SoapFault($codigo_erro,$erro,'',array('erro'=>$erro));
			return $result;

		}
	}
	

}
?>