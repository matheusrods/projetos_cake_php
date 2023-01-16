<?php
class ReferenciasController extends appController {
	var $name = 'Referencias';
	public $components = array('Maplink');
	var $helpers = array('Javascript');
	var $uses = array('TRefeReferencia','Cliente','ClientEmpresa','TRefeReferenciaHistorico', 'TCodeConfOrigemDestino');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('mapa_redirect','mapa','consultar_sistema_origem', 'busca_endereco','verifica_novo_alvo'));
    }

	function listagem($export = false, $somente_ativos = null, $destino = null) {

		$this->loadModel('TPjurPessoaJuridica');
		$filtros = $this->Filtros->controla_sessao($this->data, 'Referencia');
		$authUsuario = $this->BAuth->user();
		$this->pageTitle = 'Alvos';
		
		if(!empty($authUsuario['Usuario']['codigo_cliente']))
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

		$listagem 	= array();
		$cliente 	= !empty($filtros['codigo_cliente']) ? $this->Cliente->buscaPorCodigo($filtros['codigo_cliente']) : null;
		$cliente2 	= !empty($filtros['codigo_cliente2']) ? $this->Cliente->buscaPorCodigo($filtros['codigo_cliente2']) : null;

		if($cliente || $cliente2){
			$clientes_pjur = array();
			if($cliente){				
				$clientes_pjur[] = $this->TPjurPessoaJuridica->buscaClienteCentralizador($filtros['codigo_cliente']);
				$tipoCliente  = $clientes_pjur[0]['TPessPessoa']['pess_tipo'];
			}
			if($cliente2)
				$clientes_pjur[] = $this->TPjurPessoaJuridica->buscaClienteCentralizador($filtros['codigo_cliente2']);

			if(count($clientes_pjur) > 0){
				$this->paginate['TRefeReferencia'] = $this->TRefeReferencia->listagemParams($filtros, $somente_ativos);
				$listagem = $this->paginate('TRefeReferencia');
			}
		}
		
		/**
		*  Rotina para gera o relatório em Excel
		*  @author  Ailton Ribeiro <ailton.ribeiro@buonny.com.br>
		*  @version 0.1 / 2014-03-13
		* 
		*/
		if($export){
			$sql = $this->TRefeReferencia->listagemParams($filtros);
			$conditions = isset($sql['conditions']) ? $sql['conditions'] : array();

			if(!isset($tipoCliente)){
				Configure::write('Message.export_tipo_erro', array(MSGT_ERROR, 'Não foi possível identificar o cliente. Tente novamente'));
				$this->BSession->setFlash('export_tipo_erro');
				$this->redirect(array('conditions'=>'referencias','action'=>'adicionar_referencia'));
			}

			// Adicionando campos  que não veio da consulta
			if(isset($sql['fields'])){
				array_unshift($sql['fields'],'TRefeReferencia.refe_data_cadastro');
				array_push($sql['fields'],'TRefeReferencia.refe_cnpj_empresa_terceiro');
				array_push($sql['fields'],'TRefeReferencia.refe_cep');
				array_push($sql['fields'],'TRefeReferencia.refe_bairro_empresa_terceiro');
				array_push($sql['fields'],'TRefeReferencia.refe_numero');
				array_push($sql['fields'],'TRefeReferencia.refe_cref_codigo');
				array_push($sql['fields'],'TRefeReferencia.refe_regi_codigo');
				array_push($sql['fields'],'TRefeReferencia.refe_raio');
				if($tipoCliente == 'emb'){
				array_push($sql['fields'],'TElocEmbarcadorLocal.eloc_refe_depara');
				array_push($sql['fields'],'TElocEmbarcadorLocal.eloc_refe_critico');
				array_push($sql['fields'],'TElocEmbarcadorLocal.eloc_refe_permanente');
				} elseif($tipoCliente == 'tra') {
				array_push($sql['fields'],'TTlocTransportadorLocal.tloc_refe_depara');
				array_push($sql['fields'],'TTlocTransportadorLocal.tloc_refe_critico');
				array_push($sql['fields'],'TTlocTransportadorLocal.tloc_refe_permanente');
				}
				array_push($sql['fields'],'TTlocTipoLocal.tloc_descricao');
			}
			$fields = isset($sql['fields']) ? $sql['fields'] : array(); 
			$joins = isset($sql['joins']) ? $sql['joins'] : array(); 
			
			// Adicionando condição que não veio da consulta
			if($tipoCliente == 'emb'){
				array_push($joins,array(
						'table'=>'trafegus.public.tloc_tipo_local',
						'alias'=>'TTlocTipoLocal',
						'conditions'=>'TElocEmbarcadorLocal.eloc_tloc_codigo = TTlocTipoLocal.tloc_codigo',
						'type'=>'LEFT'
					)
				);
			} elseif ($tipoCliente == 'tra') {
				array_push($joins,array(
						'table'=>'trafegus.public.tloc_tipo_local',
						'alias'=>'TTlocTipoLocal',
						'conditions'=>'TTlocTransportadorLocal.tloc_tloc_codigo = TTlocTipoLocal.tloc_codigo',
						'type'=>'LEFT'
					)
				);
			}
			$dados = $this->TRefeReferencia->find('sql', compact('conditions','fields','joins'));
			$this->exportAlvosExcel($dados,$tipoCliente);	
		}


		$this->set(compact('cliente','listagem', 'destino'));
	}

	/** 
	* Método para exportar alvos em Excel
	*
	* @author  Ailton Ribeiro <ailton.ribeiro@buonny.com.br>   
	* @version 0.1 / 2014-03-12 
	* @param   query string
	* @param   tipo  string
	* @return  Arquivo Excel
	* @access  public 
	* 
	*/
	private function exportAlvosExcel($query,$tipo) {
		$dbo = $this->TRefeReferencia->getDataSource();
		$dbo->results = $dbo->_execute($query);
		
		header('Content-type: application/vnd.ms-excel');
		header(sprintf('Content-Disposition: attachment; filename="%s"', basename('relatorio_alvo.csv')));
	    header('Pragma: no-cache');
	    echo iconv('UTF-8', 'ISO-8859-1', '"Descrição";"CNPJ";"CEP";"Endereço";"Bairro";"Estado";"Cidade";"Número";"Latitude";"Longitude";"Código Alvo Cliente";"Alvo Crítico";"Alvo Permanente";"Classe";"Bandeira";"Região";"Tipo";"Raio";"Data Cadastro";')."\n";
		
	    
	    while ($dado = $dbo->fetchRow()) {
           	$linha = '"'.$dado['TRefeReferencia']['refe_descricao'].'";';
           	$linha .= '"'.$dado['TRefeReferencia']['refe_cnpj_empresa_terceiro'].'";';
           	$linha .= '"'.$dado['TRefeReferencia']['refe_cep'].'";';
           	$linha .= '"'.$dado['TRefeReferencia']['refe_endereco_empresa_terceiro'].'";';
           	$linha .= '"'.$dado['TRefeReferencia']['refe_bairro_empresa_terceiro'].'";';
           	$linha .= '"'.$dado['TEstaEstado']['esta_sigla'].'";';
           	$linha .= '"'.$dado['TCidaCidade']['cida_descricao'].'";';
           	$linha .= '"'.$dado['TRefeReferencia']['refe_numero'].'";';
           	$linha .= '"'.str_replace('.', ',', $dado['TRefeReferencia']['refe_latitude']).'";';
            $linha .= '"'.str_replace('.', ',', $dado['TRefeReferencia']['refe_longitude']).'";';
           	if($tipo == 'emb'){
           	$linha .= '"'.$dado['TElocEmbarcadorLocal']['eloc_refe_depara'].'";';
           	$linha .= '"'.$dado['TElocEmbarcadorLocal']['eloc_refe_critico'].'";';
           	$linha .= '"'.$dado['TElocEmbarcadorLocal']['eloc_refe_permanente'].'";';
           	} elseif ($tipo == 'tra') {
           	$linha .= '"'.$dado['TTlocTransportadorLocal']['tloc_refe_depara'].'";';
           	$linha .= '"'.$dado['TTlocTransportadorLocal']['tloc_refe_critico'].'";';
           	$linha .= '"'.$dado['TTlocTransportadorLocal']['tloc_refe_permanente'].'";';	
           	}
           	$linha .= '"'.$dado['TCrefClasseReferencia']['cref_descricao'].'";'; 
           	$linha .= '"'.$dado['TBandBandeira']['band_descricao'].'";';
           	$linha .= '"'.$dado['TRegiRegiao']['regi_descricao'].'";';
           	$linha .= '"'.$dado['TTlocTipoLocal']['tloc_descricao'].'";';
           	$linha .= '"'. number_format($dado['TRefeReferencia']['refe_raio']).'";'; 
           	$linha .= '"'.$dado['TRefeReferencia']['refe_data_cadastro'].'";'; 	
		    $linha .= "\n";
		    echo iconv("UTF-8", "ISO-8859-1", $linha);
        }
        die();
	}

	/**
	* Método que verifica existência de arquivo
	* 
	* @author   Ailton Ribeiro <ailton.ribeiro@buonny.com.br>
	* @version  0.1 / 2014-03-12
	* @param    caminho string
	* @param    nome    string
	* @return   void
	* @access   public
	* 
	*/
	private function existeArquivo($caminho,$nome){
		if(file_exists($caminho.$nome)){
			return true;
		}
		return false;
	}

	/** 
	* Método para remover os arquivos de alvos
	*
	* @author  Ailton Ribeiro <ailton.ribeiro@buonny.com.br>   
	* @version 0.1 / 2014-03-12 
	* @param   caminho string
	* @param   nome    string
	* @return  void
	* @access  public
	* 
	*/
	private function removeArquivo($caminho,$nome){
		if(file_exists($caminho.$nome)){
			$apaga = unlink ($caminho.$nome);
			if($apaga)
				return true;
			else
				return false;
		}
		return false;	
	}

	/**
	* Método que cria o arquivo de ocorrência
	*
	* @author  Ailton Ribeiro <ailton.ribeiro@buonny.com.br>
	* @version 0.1 / 2014-03-12
	* @param   caminho  string
	* @param   nome     string
	* @return  void
	* @access  public
	*
	*/
	private function criarArquivoOcorrencia($caminho,$nome){
		$fp = fopen($caminho.$nome, "w");
		if(!$fp){ return false; }
		fclose($fp);
		return true;
	}

	/**
	* Método para registrar ocorrência
	* 
	* @author   Ailton Ribeiro <ailton.ribeiro@buonny.com.br>
	* @version  0.1 / 2014-03-12
	* @param    caminho string
	* @param    nome    string
	* @param    p_dados array
	* @return   void
	* @access   public
	* 
	*/
	private function registrarOcorrencia($caminho,$nomecsv,$p_dados){
		$indice = array_keys($p_dados);
		if($this->existeArquivo($caminho,$nomecsv)){
			// Abrir o arquivo de ocorrência e inserir o registro
			$regOcorrecia = $p_dados;
			$this->geraListaOcorrencia($regOcorrecia,$caminho,$nomecsv,$indice[0]);
		} else {
			// Criar o arquivo e inserir o registro
			if($this->criarArquivoOcorrencia($caminho,$nomecsv)){
				$regOcorrecia = $p_dados;
				$this->geraListaOcorrencia($regOcorrecia,$caminho,$nomecsv,$indice[0]);
			} else {
				Configure::write('Message.upload_arquivo_nao_criar', array(MSGT_ERROR, 'Não foi possível criar o arquivo de log! - Tente novamente.'));
				$this->BSession->setFlash('upload_arquivo_nao_criar');
			}
		}
	}

	/**
	* Método que gravar/gera a lista de ocorrência
	* 
	* @author   Ailton Ribeiro <ailton.ribeiro@buonny.com.br>
	* @version  0.1 / 2014-03-12
	* @param    data array
	* @param    caminho string
	* @param    nome string
	* @param    indice int
	* @return   void
	* @access   public
	* 
	*/
	private function geraListaOcorrencia($data, $caminho, $nome, $indice, $delimitador = ';', $aspas = '"'){
 	  	$linhaReg = '';
 	  	$linhaReg = 'Registro ('.(($indice)+1).') >> ';
		foreach ($data as $key => $d) {
			$totMens = count($d);
			if($totMens > 0){
				for($i=0; $i<$totMens;$i++){
					$linhaReg .= $d[$i].' | ';
				}
			}
		}
		$fp = fopen($caminho.$nome, "a+");
		fwrite($fp, "$linhaReg \r\n");
		if(!$fp){ return false;	}
		fclose($fp);
		return true;
	}

	/**
	* Método que mostra arquivo de ocorrência
	* 
	* @author   Ailton Ribeiro <ailton.ribeiro@buonny.com.br>
	* @version  0.1 / 2014-03-12
	* @param    caminho string
	* @param    nome    string
	* @return   arquivo
	* @access   public
	* 
	*/
	private function mostrarListaOcorrencia($caminho,$nome){
		// Aqui abre o arquivo pra download para visualização do usuário
		//Configure::write('debug',0);

		header("Content-Type: application/force-download");
		header('Content-Disposition: attachment; filename="'.$nome.'"');
		echo '--------------------------------------------------------------------'."\r\n";
		echo '      Resultado Final da Importação - ('.date('d/m/Y H:i').')'."\r\n";
		echo '--------------------------------------------------------------------'."\r\n";
		echo ''."\r\n";
		echo ''."\r\n";
		
		foreach ($this->TRefeReferencia->ocorrenciasImportacao as $numero_registro => $ocorrencia) {
			echo '('.$numero_registro.') ';
			foreach ($ocorrencia as $key => $value) {
				echo $value.' | ';
			}
			echo "\n";
		}
		exit;
	}
	
	/**
	* Método que ajusta latitude e longitude
	* 
	* @author   Ailton Ribeiro <ailton.ribeiro@buonny.com.br>
	* @version  0.1 / 2014-03-14
	* @param    latlong string
	* @return   latlong
	* @access   public
	* 
	*/
	public function ajustaLatLong($latlong){
		$latlong = str_replace(',','',str_replace('.','',trim($latlong)));
	    $total = strlen($latlong);
	    $sinal = substr($latlong, 0,1);
	    if($sinal == '-'){
	    	$inicio  = substr($latlong,1,2);
	    	$final   = substr($latlong,3,$total);
	    	$latlong = $sinal.$inicio.'.'.$final;
	    } 
	    elseif(($sinal == '+') || (!($sinal == '+'))){
	    	$inicio  = substr($latlong,1,2);
	    	$final   = substr($latlong,3,$total);
	    	$latlong = $inicio.'.'.$final;
	    	$latlong = substr($latlong,0.9);
	    }
	    return $latlong;
	}

	/** 
	* Método para importar alvos na base de dados
	*
	* @author  Ailton Ribeiro <ailton.ribeiro@buonny.com.br>   
	* @version 0.1 / 2014-03-07 
	* @return  void
	* @access  public 
	* 
	*/
	public function importar_alvo($codigo_cliente){
		ini_set('max_execution_time', 0);
        set_time_limit(0);
		$this->pageTitle = 'Importar Alvo';
		$this->loadModel('TPjurPessoaJuridica');
		// Verificar a existência do arquivo
		if(isset($this->data)){
			if(!empty($this->data['TRefeReferencia']['arquivo_csv']['name'])){
				$dados   = $this->data;
				$nome    = trim($dados['TRefeReferencia']['arquivo_csv']['name']);
				$arquivo = trim($dados['TRefeReferencia']['arquivo_csv']['tmp_name']);
				$erro    = trim($dados['TRefeReferencia']['arquivo_csv']['error']);
				$tamanho = trim($dados['TRefeReferencia']['arquivo_csv']['size']);
				// Verificando se arquivo veio sem erro
				if(!($erro <> 0)){
					// Validando o tipo do arquivo
					$extensao = strtolower(end(explode('.',$nome)));
					if($extensao == 'csv'){
						// Movendo o arquivo para pasta temporária da aplicação
						$caminho = ROOT.DS.'app'.DS.'tmp'.DS;
						$nomecsv = 'log_'.$this->authUsuario['Usuario']['codigo'].'_'.str_replace('.','_',$nome).'.txt';
						$nome    = $this->authUsuario['Usuario']['codigo'].'_'.$nome;
						if(move_uploaded_file($arquivo,$caminho.$nome)){
							$arrFull = $this->TRefeReferencia->converteArquivoEmArray($caminho, $nome);
						    $totalRegistro = count($arrFull);
						    if(!empty($totalRegistro)){
						    	$usuario = $this->BAuth->user();
						    	if (isset($usuario['Usuario']['codigo_cliente']) && !empty($usuario['Usuario']['codigo_cliente'])) {
						    		$codigo_cliente = $usuario['Usuario']['codigo_cliente'];
						    	}
						    	$this->TRefeReferencia->importarAlvos($arrFull, $codigo_cliente);
							    $this->removeArquivo($caminho,$nome);
								$this->BSession->setFlash('save_success');
								$this->mostrarListaOcorrencia($caminho,$nomecsv);
						    } else {
						    	Configure::write('Message.upload_arquivo_estrutura_invalida', array(MSGT_ERROR, 'A estrutura no documento está errada. Favor verificar'));
								$this->BSession->setFlash('upload_arquivo_estrutura_invalida');
								$this->removeArquivo($caminho,$nome);
						    }
						} else {
							Configure::write('Message.upload_arquivo_mover', array(MSGT_ERROR, 'Houve um erro - Tente novamente'));
							$this->BSession->setFlash('upload_arquivo_mover');
						}
					} else {
						Configure::write('Message.upload_arquivo_extensao', array(MSGT_ERROR, 'Tipo de arquivo inválido. Informe arquivo do tipo CSV.'));
						$this->BSession->setFlash('upload_arquivo_extensao');
					}
				}
			} else {
				Configure::write('Message.upload_arquivo', array(MSGT_ERROR, 'Informe o arquivo a ser utilizado na importação.'));
				$this->BSession->setFlash('upload_arquivo');
			}
		}
		$cliente = $this->Cliente->carregar($codigo_cliente);
		$this->set(compact('cliente'));
	}

	function adicionar_referencia() {
		$this->loadModel('TPaisPais');
		$this->loadModel('TEstaEstado');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TBandBandeira');
		$this->loadModel('TRegiRegiao');
		$this->loadModel('TCrefClasseReferencia');
		$authUsuario = $this->BAuth->user();
		$this->pageTitle = 'Alvos';
		$filtros 	= $this->Filtros->controla_sessao($this->data, 'Referencia');
		$classes	= $this->TCrefClasseReferencia->combo();
		$estados 	= $this->TEstaEstado->comboPorPais( TPaisPais::BRASIL );
		$bandeiras 	= array();
		$regioes	= array();
		if(!empty($authUsuario['Usuario']['codigo_cliente']))
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

		if($filtros['codigo_cliente']){
			$cliente_pjur 	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($filtros['codigo_cliente']);
			if($cliente_pjur){
				$bandeiras		= $this->TBandBandeira->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
				$regioes		= $this->TRegiRegiao->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
			}
		}		
		$this->data['Referencia'] = $filtros;
		$this->set(compact('estados','bandeiras','regioes','classes'));
	}

	function buscar_codigo() {
		
		$this->loadModel('TEstaEstado');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TBandBandeira');
		$this->loadModel('TRegiRegiao');
		$this->loadModel('TCrefClasseReferencia');
		$this->loadModel('TPaisPais');
		
		$this->pageTitle = 'Alvos';
		$codigo_cliente = $this->passedArgs['codigo'];
		$codigo_cliente2 = !empty($this->passedArgs['codigo2']) ? $this->passedArgs['codigo2'] : null;

		$this->data['Referencia']['codigo_cliente'] = $codigo_cliente != 'undefined' ? $codigo_cliente : NULL;
		$this->data['Referencia']['codigo_cliente2'] = $codigo_cliente2 != 'undefined' ? $codigo_cliente2 : NULL;

		$filtros 	= $this->Filtros->controla_sessao($this->data, 'Referencia');		
		$estados 	= $this->TEstaEstado->comboPorPais( TPaisPais::BRASIL );
		$classes	= $this->TCrefClasseReferencia->combo();
		$bandeiras 	= array();
		$regioes	= array();

		if($filtros['codigo_cliente'] > 0 || $filtros['codigo_cliente2'] > 0){
			$clientes_pjur = array();
			if($filtros['codigo_cliente'] > 0)
				$clientes_pjur[]	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($filtros['codigo_cliente']);
			if($filtros['codigo_cliente2'] > 0)
				$clientes_pjur[]	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($filtros['codigo_cliente2']);
			$clientes = array();
			if(count($clientes_pjur) > 0){				
				foreach($clientes_pjur as $cliente_pjur){
					$clientes[] = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];					
				}
				$bandeiras	= $this->TBandBandeira->lista($clientes);
				$regioes	= $this->TRegiRegiao->lista($clientes);				
			}
		}
		
		$this->data['Referencia'] = $filtros;
		$this->set(compact('estados','bandeiras','regioes','classes', 'codigo_cliente'));

	}

	function listagem_visualizar() {
		$this->listagem(false, true);
	}

	function incluir($codigo_cliente,$new_window = false) {
		$this->loadModel('TEstaEstado');
		$this->loadModel('Cliente');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TCrefClasseReferencia');
		$this->loadModel('TBandBandeira');
		$this->loadModel('TRegiRegiao');
		$this->loadModel('TTlocTipoLocal');
		$this->loadModel('TCidaCidade');
		$this->loadModel('TPaisPais');
		$this->pageTitle = 'Incluir Alvo';
		$fechar = false;
		$id = false;
		if($new_window){
			$this->layout = 'new_window';
		}			
		$authUsuario 	= $this->BAuth->user();
		if(!empty($authUsuario['Usuario']['codigo_cliente']))
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

		$cliente 		= $this->Cliente->buscaPorCodigo($codigo_cliente);
		$cliente_pjur 	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente);

		if(!$cliente_pjur){
			$this->BSession->setFlash('cadastro_com_problema');
			if(!$new_window){
				$this->redirect(array('controller' => 'Referencias','action' => 'adicionar_referencia'));
				exit;
			}
		} 

		$estados 		= $this->TEstaEstado->comboPorPais( TPaisPais::BRASIL );
		$classes		= $this->TCrefClasseReferencia->combo();
		$tipos			= $this->TTlocTipoLocal->lista();

		$bandeiras		= $this->TBandBandeira->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
		$regioes		= $this->TRegiRegiao->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);

		$menssagem		= null;

		if ($this->RequestHandler->isPost()) {
			$this->data['TRefeReferencia']['refe_cida_codigo'] = null;
			if(isset($this->data['TRefeReferencia']['refe_cidade']) && 
			   isset($this->data['TRefeReferencia']['refe_estado']) && 
			   $this->data['TRefeReferencia']['refe_cidade'] && 
			   $this->data['TRefeReferencia']['refe_estado']){
			   	
				$cida_cidade = $this->TCidaCidade->buscaPorDescricao($this->data['TRefeReferencia']['refe_cidade'],$this->data['TRefeReferencia']['refe_estado']);
				if($cida_cidade){
					$this->data['TRefeReferencia']['refe_cida_codigo'] = $cida_cidade['TCidaCidade']['cida_codigo'];
				}
			}

			if(!isset($this->data['TRefeReferencia']['refe_raio']))
				$this->data['TRefeReferencia']['refe_raio'] = 150;

			$this->data['TRefeReferencia']['refe_raio'] = (float)(trim(str_replace(',', '.', $this->data['TRefeReferencia']['refe_raio'])));
			if(!$this->data['TRefeReferencia']['refe_raio']){
				$this->data['TRefeReferencia']['refe_raio'] = 150;
			}

			$retorno = $this->TRefeReferencia->incluirReferencia($this->data['TRefeReferencia'],true);

			if(isset($retorno['sucesso'])){
				$this->BSession->setFlash('save_success');
				if(!$new_window){
					$this->redirect(array('controller' => 'Referencias','action' => 'adicionar_referencia'));
				}else{
					$id = $this->TRefeReferencia->getLastInsertID();
					$fechar = true;
				}
			} else {
				$menssagem = $retorno['erro'];
				$this->BSession->setFlash('save_error');

			}
		} else {
			$this->data['TRefeReferencia']['refe_raio'] = 150;
		}

		$this->set(compact('id','fechar','cliente','estados', 'classes', 'bandeiras', 'regioes', 'cliente_pjur','tipos','menssagem','new_window'));
		
	}

	function alterar($codigo_cliente,$refe_codigo) {
		$this->loadModel('TEstaEstado');
		$this->loadModel('Cliente');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TCrefClasseReferencia');
		$this->loadModel('TBandBandeira');
		$this->loadModel('TRegiRegiao');
		$this->loadModel('TCidaCidade');
		$this->loadModel('TTlocTipoLocal');
		$this->loadModel('TTranTransportador');
		$this->loadModel('TTlocTransportadorLocal');
		$this->loadModel('TElocEmbarcadorLocal');
		$this->loadModel('TRefeReferenciaHistorico');
		$this->loadModel('TPaisPais');
		$this->pageTitle = 'Alterar Alvo';

		$authUsuario 	= $this->BAuth->user();
		if(!empty($authUsuario['Usuario']['codigo_cliente']))
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

		$cliente 		= $this->Cliente->buscaPorCodigo($codigo_cliente);
		$cliente_pjur 	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente);
		if(!$cliente_pjur){
			$this->BSession->setFlash('cadastro_com_problema');
			$this->redirect(array('controller' => 'Referencias','action' => 'adicionar_referencia'));
			exit;
		} 

		$estados 		= $this->TEstaEstado->comboPorPais( TPaisPais::BRASIL );
		$classes		= $this->TCrefClasseReferencia->combo();
		$tipos			= $this->TTlocTipoLocal->lista();

		$bandeiras		= $this->TBandBandeira->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
		$regioes		= $this->TRegiRegiao->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);

		$menssagem		= null;

		if (isset($this->data['TRefeReferencia'])) {
			$this->data['TRefeReferencia']['refe_cida_codigo'] = null;
			if($this->data['TRefeReferencia']['refe_cidade'] && $this->data['TRefeReferencia']['refe_estado']){
				$cida_cidade = $this->TCidaCidade->buscaPorDescricao($this->data['TRefeReferencia']['refe_cidade'],$this->data['TRefeReferencia']['refe_estado']);
				if($cida_cidade){
					$this->data['TRefeReferencia']['refe_cida_codigo'] = $cida_cidade['TCidaCidade']['cida_codigo'];
				}
			}

			$this->data['TRefeReferencia']['refe_raio'] = (float)(trim(str_replace(',', '.', $this->data['TRefeReferencia']['refe_raio'])));
			$retorno = $this->TRefeReferencia->atualizarReferencia($this->data['TRefeReferencia']);
			if(isset($retorno['sucesso'])){
				$this->BSession->setFlash('save_success');
				$this->redirect('adicionar_referencia');
			} else {
				$menssagem = $retorno['erro'];
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data = $this->TRefeReferencia->buscaPorCodigo($refe_codigo);
			
			$cidade 	= $this->TCidaCidade->buscaPorCodigo($this->data['TRefeReferencia']['refe_cida_codigo']);
			if($cidade){
				$this->data['TRefeReferencia']['refe_estado'] = $cidade['TEstaEstado']['esta_sigla'];
				$this->data['TRefeReferencia']['refe_cidade'] = $cidade['TCidaCidade']['cida_descricao'];
			}

			$this->data['TRefeReferencia']['tloc_tloc_codigo'] 	= NULL;
			$this->data['TRefeReferencia']['refe_critico'] 		= NULL;
			$this->data['TRefeReferencia']['refe_permanente'] 	= NULL;
			$this->data['TRefeReferencia']['refe_depara'] 		= NULL;

			$tloc = $this->TTlocTransportadorLocal->carregarPorReferencia($this->data['TRefeReferencia']['refe_codigo']);
			$eloc = $this->TElocEmbarcadorLocal->carregarPorReferencia($this->data['TRefeReferencia']['refe_codigo']);

			if($tloc){
				$this->data['TRefeReferencia']['tloc_tloc_codigo'] 	= $tloc['TTlocTransportadorLocal']['tloc_tloc_codigo'];
				$this->data['TRefeReferencia']['refe_critico'] 		= $tloc['TTlocTransportadorLocal']['tloc_refe_critico'];
				$this->data['TRefeReferencia']['refe_permanente'] 	= $tloc['TTlocTransportadorLocal']['tloc_refe_permanente'];
				$this->data['TRefeReferencia']['refe_depara'] 		= $tloc['TTlocTransportadorLocal']['tloc_refe_depara'];
			} elseif($eloc) {
				$this->data['TRefeReferencia']['tloc_tloc_codigo'] 	= $eloc['TElocEmbarcadorLocal']['eloc_tloc_codigo'];
				$this->data['TRefeReferencia']['refe_critico'] 		= $eloc['TElocEmbarcadorLocal']['eloc_refe_critico'];
				$this->data['TRefeReferencia']['refe_permanente'] 	= $eloc['TElocEmbarcadorLocal']['eloc_refe_permanente'];
				$this->data['TRefeReferencia']['refe_depara'] 		= $eloc['TElocEmbarcadorLocal']['eloc_refe_depara'];
			}


			$dados_historico = $this->TRefeReferenciaHistorico->listarHistorico($refe_codigo);
			$this->TRefeReferencia->joinCidadeEstado();
			$referencia = $this->TRefeReferencia->carregar($refe_codigo);
			$referencia['TRefeReferenciaHistorico'] =& $referencia['TRefeReferencia'];
			array_unshift($dados_historico,$referencia);

			$this->set(compact('dados_historico'));


		}

		$this->set(compact('cliente','empresa','estados','cidades','classes','tipos','cliente_pjur','bandeiras','regioes','menssagem'));
		
	}

	function inativar($codigo_referencia) {
		$authUsuario = $this->BAuth->user();
		$origem_destino = null;
		if (isset($this->data['TRefeReferencia'])) {
			$alvo = $this->TRefeReferencia->carregar($this->data['TRefeReferencia']['refe_codigo']);
			$alvo['TRefeReferencia']['refe_cida_codigo'] = !empty($alvo['TRefeReferencia']['refe_cida_codigo']) ? $alvo['TRefeReferencia']['refe_cida_codigo'] : 9699;
			$alvo['TRefeReferencia']['refe_inativo'] = 'S';
			
			if($this->TRefeReferencia->save($alvo)) {
				$this->TRefeReferencia->atualizarHistorico($codigo_referencia, $authUsuario['Usuario']['apelido']);
				$this->BSession->setFlash('save_success');

			}

		} else {
			
			$conditions = array('refe_codigo' => $codigo_referencia);
			$referencia = $this->TRefeReferencia->find('first',compact('conditions'));
			$this->TCodeConfOrigemDestino->bindModel(array(
						'belongsTo' => array(
								'Origem' => array(
										'className' => 'TRefeReferencia',
										'foreignKey' => false,
										'conditions' => array('Origem.refe_codigo = TCodeConfOrigemDestino.code_refe_codigo_origem')
									),
								'Destino' => array(
										'className' => 'TRefeReferencia',
										'foreignKey' => false,
										'conditions' => array('Destino.refe_codigo = TCodeConfOrigemDestino.code_refe_codigo_destino')
									),
							)
					)
				);
			$origem_destino = $this->TCodeConfOrigemDestino->find(
				'all', 
				array(
					'fields' => array(
						'Origem.refe_descricao',
						'Destino.refe_descricao',
						'Origem.refe_codigo',
						'Destino.refe_codigo'
					),
					'conditions' => array(
						'TCodeConfOrigemDestino.code_refe_codigo_origem = '.$codigo_referencia.' or TCodeConfOrigemDestino.code_refe_codigo_destino = '.$codigo_referencia,
					)
				)
			);
			
			$this->data = $referencia;
		}			
		$this->set(compact('codigo_referencia', 'origem_destino'));
		
	}

	function ativar($codigo_referencia) {
		$authUsuario = $this->BAuth->user();

		if (isset($this->data['TRefeReferencia'])) {
			$alvo = $this->TRefeReferencia->carregar($this->data['TRefeReferencia']['refe_codigo']);
			$alvo['TRefeReferencia']['refe_cida_codigo'] = !empty($alvo['TRefeReferencia']['refe_cida_codigo']) ? $alvo['TRefeReferencia']['refe_cida_codigo'] : 9699;
			$alvo['TRefeReferencia']['refe_inativo'] = NULL;			
			if($this->TRefeReferencia->save($alvo)) {
				$this->TRefeReferencia->atualizarHistorico($codigo_referencia, $authUsuario['Usuario']['apelido']);
				$this->BSession->setFlash('save_success');
			}
		} else {			
			$conditions = array('refe_codigo' => $codigo_referencia);
			$referencia = $this->TRefeReferencia->find('first',compact('conditions'));
			$this->data = $referencia;			
		}
		$this->set(compact('codigo_referencia'));		
	}

	function buscaXY() {
		$this->loadModel('TCidaCidade');
		$this->loadModel('TEstaEstado');
		
		$data =& $this->data['TRefeReferencia'];

		$new_local = array();
		if(isset($data['refe_endereco_empresa_terceiro']))
			$new_local['endereco'] = $this->data['TRefeReferencia']['refe_endereco_empresa_terceiro'];

		if(isset($this->data['TRefeReferencia']['refe_bairro_empresa_terceiro']))
			$new_local['bairro'] = $this->data['TRefeReferencia']['refe_bairro_empresa_terceiro'];

		if(isset($this->data['TRefeReferencia']['refe_numero']))
			$new_local['numero'] = $this->data['TRefeReferencia']['refe_numero'];

		if(isset($this->data['TRefeReferencia']['refe_cep']))
			$new_local['cep'] = $this->data['TRefeReferencia']['refe_cep'];

		if(isset($this->data['TRefeReferencia']['refe_cidade']) && !empty($this->data['TRefeReferencia']['refe_cidade'])){
			$new_local['cidade']['nome'] = $this->data['TRefeReferencia']['refe_cidade'];
		}

		if(isset($this->data['TRefeReferencia']['refe_estado']) && !empty($this->data['TRefeReferencia']['refe_estado'])){
			$new_local['cidade']['estado'] = $this->data['TRefeReferencia']['refe_estado'];
		}
		
		$new_xy = $this->Maplink->busca_xy($new_local);
		
		echo json_encode($new_xy);
		exit;
	}

	public function autocomplete_referencias(){
		$this->layout		= false;
		$this->loadModel('TPjurPessoaJuridica');
		$codigo_cliente = $this->passedArgs['codigo'];
		$codigo_cliente2 = 0;
		$retorno 			= array();
		if ($codigo_cliente > 0) {
			$cliente_pjur	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente);
			if ($cliente_pjur)
				$cliente_pjur = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
			if (isset($this->passedArgs['codigo2']) && $this->passedArgs['codigo2']) {
				$codigo_cliente2 = $this->passedArgs['codigo2'];
				if ($codigo_cliente2 > 0) {
					$cliente_pjur2	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente2);
					if ($cliente_pjur2) {
						$cliente_pjur2 = $cliente_pjur2['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
						$cliente_pjur = array($cliente_pjur, $cliente_pjur2);
					}
				}
			}
		} else {
			$cliente_pjur = false;
		}
		$referencias	= $this->TRefeReferencia->listarAutocomplete($cliente_pjur, $_GET['term']);

		if($referencias){
			foreach($referencias as $key => $value){
				$retorno[] 	= array('label' => $value, 'value' => $key);
			}
		}

		echo json_encode($retorno);
		exit;

	}

	public function autocomplete_referencias_embarcador_transportador($codigo_cliente_embarcador,$codigo_cliente_transportador){
		$this->layout		= false;
		$this->loadModel('TPjurPessoaJuridica');
		$retorno 			= array();
		
		$clientes_pjur = array();
		if($codigo_cliente_embarcador){
			$cliente_pjur_emb	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente_embarcador);
			$clientes_pjur[]	= $cliente_pjur_emb['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
		}

		if($codigo_cliente_transportador){
			$cliente_pjur_tra	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente_transportador);
			$clientes_pjur[]	= $cliente_pjur_tra['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
		}
		
		$referencias	= $this->TRefeReferencia->listarAutocomplete($clientes_pjur,$_GET['term']);

		if($referencias){
			foreach($referencias as $key => $value){
				$retorno[] 	= array('label' => $value, 'value' => $key);
			}
		}

		echo json_encode($retorno);
		exit;

	}

	public function busca_latitude_longitude($refe_codigo){
		$this->layout = false;

		$referencias = $this->TRefeReferencia->buscaPorCodigo($refe_codigo, array('refe_latitude', 'refe_longitude'));

		echo json_encode($referencias['TRefeReferencia']);
		exit;

	}


	public function busca_endereco($refe_codigo){
		$this->layout = false;

		$referencias = $this->TRefeReferencia->buscaPorCodigo($refe_codigo, array(), true);

		echo json_encode($referencias);
		exit;
	}


	public function historico_veiculos() {
		$this->pageTitle = 'Histórico de Veículos no Alvo';
		if (!empty($this->data)) {
			if ($this->historico_veiculos_validate()) {
				$authUsuario = $this->BAuth->user();
				if(!empty($authUsuario['Usuario']['codigo_cliente']))
					$this->data['TRefeReferencia']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

				$this->loadModel('Cliente');
				$cliente = $this->Cliente->read('codigo_documento', $this->data['TRefeReferencia']['codigo_cliente']);
				$this->data['TRefeReferencia']['data_final'] = $this->data['TRefeReferencia']['data_inicial'];
				if ($cliente) {
					if ($this->data['TRefeReferencia']['base_cnpj']) {
						$this->data['TRefeReferencia']['pjur_cnpj'] = substr($cliente['Cliente']['codigo_documento'],0,8);
					} else {
						$this->data['TRefeReferencia']['pjur_cnpj'] = $cliente['Cliente']['codigo_documento'];
					}
					$veiculos = $this->TRefeReferencia->veiculosPorPeriodo($this->data['TRefeReferencia']);
					$this->set(compact('veiculos'));
				} else {
					$this->TRefeReferencia->invalidate('codigo_cliente', 'Cliente inexistente');
				}
			}
		} else {
			$authUsuario 	= $this->BAuth->user();
			if(!empty($authUsuario['Usuario']['codigo_cliente']))
				$this->data['TRefeReferencia']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			$this->data['TRefeReferencia']['data_inicial'] = date('d/m/Y');
		}
	}	

	private function historico_veiculos_validate() {
		$no_error = true;
		if (empty($this->data['TRefeReferencia']['codigo_cliente'])) {
			$this->TRefeReferencia->invalidate('codigo_cliente', 'Informe o cliente');
			$no_error = false;
		}
		if (empty($this->data['TRefeReferencia']['refe_codigo'])) {
			$this->TRefeReferencia->invalidate('refe_codigo', 'Informe o alvo');
			$no_error = false;
		}
		if (empty($this->data['TRefeReferencia']['data_inicial'])) {
			$this->TRefeReferencia->invalidate('data_inicial', 'Informe a data');
			$no_error = false;
		}
		return $no_error;
	}

	public function historico_alvo_veiculo() {
		$this->layout = 'new_window';
		$this->pageTitle = 'Histórico de Veículo no Alvo';
		if (!empty($this->data)) {
			if ($this->historico_alvo_veiculo_validate()) {
				$this->loadModel('TVeicVeiculo');
				$refe_referencia = $this->TRefeReferencia->carregar($this->data['TRefeReferencia']['refe_codigo']);
				$this->TVeicVeiculo->bindModel(array('belongsTo' => array('TPessPessoa' => array('foreignKey' => 'veic_pess_oras_codigo_propri'))));
				$veic_veiculo = $this->TVeicVeiculo->carregar($this->data['TRefeReferencia']['veic_oras_codigo']);
				$veiculos = $this->TRefeReferencia->historicoVeiculoPorPeriodo($this->data['TRefeReferencia']);
				$this->set(compact('veiculos', 'veic_veiculo', 'refe_referencia'));
			}
		} else {
			$authUsuario 	= $this->BAuth->user();
			if(!empty($authUsuario['Usuario']['codigo_cliente']))
				$this->data['TRefeReferencia']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}
	}

	private function historico_alvo_veiculo_validate() {
		$no_error = true;
		if (empty($this->data['TRefeReferencia']['veic_oras_codigo'])) {
			$this->TRefeReferencia->invalidate('codigo_cliente', 'Informe o veículo');
			$no_error = false;
		}
		if (empty($this->data['TRefeReferencia']['refe_codigo'])) {
			$this->TRefeReferencia->invalidate('refe_codigo', 'Informe o alvo');
			$no_error = false;
		}
		if (empty($this->data['TRefeReferencia']['data_inicial']) || empty($this->data['TRefeReferencia']['data_final'])) {
			$this->TRefeReferencia->invalidate('data_inicial', 'Informe o período');
			$no_error = false;
		}
		return $no_error;
	}

	public function historico_alvo_veiculos() {
		$this->pageTitle = 'Histórico de Veículos no Alvo';
		if ($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			if ($this->historico_alvo_veiculos_validate()) {
				$refe_referencia['TRefeReferenciaHistoricoAlvo'] = $this->data['TRefeReferencia'];
				$this->data['TRefeReferencia'] = $this->Filtros->controla_sessao($refe_referencia, 'TRefeReferenciaHistoricoAlvo');
			}
		} else {
			$this->Filtros->limpa_sessao('TRefeReferenciaHistoricoAlvo');
			$this->data['TRefeReferencia'] = $this->Filtros->controla_sessao(null, 'TRefeReferenciaHistoricoAlvo');
			$authUsuario 	= $this->BAuth->user();
			if(!empty($authUsuario['Usuario']['codigo_cliente']))
				$this->data['TRefeReferencia']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}
	}

	public function historico_alvo_veiculos_listagem() {
		$this->pageTitle = 'Histórico de Veículos no Alvo';
		$refe_referencia['TRefeReferenciaHistoricoAlvo'] = $this->data['TRefeReferencia'];
		$this->data['TRefeReferencia'] = $this->Filtros->controla_sessao($refe_referencia, 'TRefeReferenciaHistoricoAlvo');
		if (!empty($this->data)) {
			$authUsuario 	= $this->BAuth->user();
			if(!empty($authUsuario['Usuario']['codigo_cliente']))
				$this->data['TRefeReferencia']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

			if ($this->historico_alvo_veiculos_validate()) {
				$this->loadModel('Cliente');
				$cliente = $this->Cliente->read('codigo_documento', $this->data['TRefeReferencia']['codigo_cliente']);
				$this->data['TRefeReferencia']['data_final'] = $this->data['TRefeReferencia']['data_inicial'];
				if ($cliente) {
					if ($this->data['TRefeReferencia']['base_cnpj']) {
						$this->data['TRefeReferencia']['pjur_cnpj'] = substr($cliente['Cliente']['codigo_documento'],0,8);
					} else {
						$this->data['TRefeReferencia']['pjur_cnpj'] = $cliente['Cliente']['codigo_documento'];
					}
					$this->paginate['TRefeReferencia'] = array(
						'limit' => 200,
						'conditions' => $this->data['TRefeReferencia'],
						'historicoVeiculosPorPeriodo' => 'historicoVeiculosPorPeriodo',
						'order' => (isset($this->passedArgs['sort']) ? array($this->passedArgs['sort'] => $this->passedArgs['direction']) : array('veic_placa', 'data_entrada')),
					);
					unset($this->passedArgs['sort']);
					$veiculos = $this->paginate('TRefeReferencia');
					$this->set(compact('veiculos'));
				}
			}
		}
	}

	private function historico_alvo_veiculos_validate() {//debug($this->data);die;
		$no_error = true;
		if (empty($this->data['TRefeReferencia']['codigo_cliente'])) {
			$this->TRefeReferencia->invalidate('codigo_cliente', 'Informe o cliente');
			$no_error = false;
		}
		if (empty($this->data['TRefeReferencia']['refe_codigo'])) {
			$this->TRefeReferencia->invalidate('refe_codigo_visual', 'Informe o alvo');
			$no_error = false;
		}
		if (empty($this->data['TRefeReferencia']['data_inicial'])) {
			$this->TRefeReferencia->invalidate('data_inicial', 'Informe a data');
			$no_error = false;
		}
		return $no_error;
	}

	function mapa_redirect($latitude = 0, $longitude = 0, $raio = 0) {		
		$this->loadModel('TRefeReferencia');
		$this->layout = false;		
		if($raio == 'undefined')
			$raio = 0;

		$data = array(
			'refe_latitude' => $latitude,
			'refe_longitude'=> $longitude,
			'refe_raio' 	=> $raio
		);
		$area = $this->TRefeReferencia->calculaLatLongMinMax($data);
		$this->set(compact('latitude', 'longitude','area'));
	}

	function mapa($latitude = 0, $longitude = 0, $raio = 0) {
		$this->layout = false;		
		$this->set(compact('latitude', 'longitude','raio'));
	}

	function consultar_sistema_origem(){
		$this->loadModel('TVlocViagemLocal');
		$listagem = array();
		if ($this->RequestHandler->isPost())
			$listagem = $this->TVlocViagemLocal->listarSistemaOrigem($this->data['TRefeReferencia']['refe_codigo']);

		$this->set(compact('listagem'));

	}

	function adicionar_referencia_compartilhada() {
		$this->loadModel('TEstaEstado');
		$this->loadModel('TBandBandeira');
		$this->loadModel('TRegiRegiao');
		$this->loadModel('TCrefClasseReferencia');
		$this->loadModel('TPaisPais');
		$authUsuario = $this->BAuth->user();
		$this->pageTitle = 'Alvos Compartilhados';
		$filtros 	= $this->Filtros->controla_sessao($this->data, 'Referencia');
		$estados 	= $this->TEstaEstado->comboPorPais( TPaisPais::BRASIL );
		$classes	= $this->TCrefClasseReferencia->combo();
		$bandeiras 	= array();
		$regioes	= array();		
		$this->data['Referencia'] = $filtros; 
		$this->set(compact('estados','bandeiras','regioes','classes'));
	}


	function listagem_compartilhados($export = false, $somente_ativos = null) {
		$filtros = $this->Filtros->controla_sessao($this->data, 'Referencia');
		$this->paginate['TRefeReferencia'] = $this->TRefeReferencia->listagemParamsAlvosCompartilhados($filtros, $somente_ativos);
		$listagem = $this->paginate('TRefeReferencia');
		$this->set(compact('listagem'));
	}


	function incluir_referencia_compartilhada( ) {
		$this->loadModel('TEstaEstado');
		$this->loadModel('TCrefClasseReferencia');
		$this->loadModel('TBandBandeira');
		$this->loadModel('TRegiRegiao');
		$this->loadModel('TTlocTipoLocal');
		$this->loadModel('TCidaCidade');
		$this->loadModel('TPaisPais');
		$this->pageTitle = 'Incluir Alvo Compartilhado';
		$estados 	= $this->TEstaEstado->comboPorPais( TPaisPais::BRASIL );
		$classes		= $this->TCrefClasseReferencia->combo();
		$tipos			= $this->TTlocTipoLocal->lista();
		$menssagem		= null;
		if ($this->RequestHandler->isPost()) {
			$this->data['TRefeReferencia']['refe_cida_codigo'] = null;
			if(isset($this->data['TRefeReferencia']['refe_cidade']) 
				&& isset($this->data['TRefeReferencia']['refe_estado']) && 
				$this->data['TRefeReferencia']['refe_cidade'] && 
				$this->data['TRefeReferencia']['refe_estado']){			   	
				$cida_cidade = $this->TCidaCidade->buscaPorDescricao($this->data['TRefeReferencia']['refe_cidade'], $this->data['TRefeReferencia']['refe_estado']);
				if($cida_cidade){
					$this->data['TRefeReferencia']['refe_cida_codigo'] = $cida_cidade['TCidaCidade']['cida_codigo'];
					$this->data['TRefeReferencia']['refe_esta_codigo'] = $cida_cidade['TCidaCidade']['cida_esta_codigo'];					
				}
			}
			$this->data['TRefeReferencia']['refe_raio'] = 150;
			$this->data['TRefeReferencia'] = $this->TRefeReferencia->calculaLatLongMinMax( $this->data['TRefeReferencia'] );
			$this->data['TRefeReferencia']['refe_data_cadastro'] = date("Ymd H:i:s");			
			if( $this->TRefeReferencia->incluir($this->data) ) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'Referencias','action' => 'adicionar_referencia_compartilhada'));
				exit;
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		$this->set(compact('estados', 'classes', 'bandeiras', 'regioes', 'tipos','menssagem'));		
	}

	function alterar_referencia_compartilhada($refe_codigo) {
		$this->loadModel('TEstaEstado');
		$this->loadModel('TCrefClasseReferencia');
		$this->loadModel('TRegiRegiao');
		$this->loadModel('TCidaCidade');
		$this->loadModel('TTlocTipoLocal');
		$this->loadModel('TRefeReferenciaHistorico');
		$this->loadModel('TPaisPais');
		$this->pageTitle = 'Alterar Alvo Compartilhado';
		$estados 		= $this->TEstaEstado->comboPorPais( TPaisPais::BRASIL );
		$classes		= $this->TCrefClasseReferencia->combo();
		$tipos			= $this->TTlocTipoLocal->lista();
		$menssagem		= null;
		if (isset($this->data['TRefeReferencia'])) {
			$this->data['TRefeReferencia']['refe_cida_codigo'] = null;
			if($this->data['TRefeReferencia']['refe_cidade'] && $this->data['TRefeReferencia']['refe_estado']){
				$cida_cidade = $this->TCidaCidade->buscaPorDescricao($this->data['TRefeReferencia']['refe_cidade'],$this->data['TRefeReferencia']['refe_estado']);
				if($cida_cidade){
					$this->data['TRefeReferencia']['refe_cida_codigo'] = $cida_cidade['TCidaCidade']['cida_codigo'];
				}
			}
			$this->data['TRefeReferencia']['refe_data_alteracao']  = date("Ymd H:i:s");
			$this->data['TRefeReferencia']['refe_raio'] = 150;
			$this->data['TRefeReferencia'] = $this->TRefeReferencia->calculaLatLongMinMax( $this->data['TRefeReferencia'] );
			
			if( $this->TRefeReferencia->atualizar($this->data) ) {
				$this->BSession->setFlash('save_success');
				$this->redirect('adicionar_referencia_compartilhada');
				exit;
			} else {
				$menssagem = $retorno['erro'];
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data = $this->TRefeReferencia->buscaPorCodigo($refe_codigo);			
			$cidade 	= $this->TCidaCidade->buscaPorCodigo($this->data['TRefeReferencia']['refe_cida_codigo']);
			if($cidade){
				$this->data['TRefeReferencia']['refe_estado'] = $cidade['TEstaEstado']['esta_sigla'];
				$this->data['TRefeReferencia']['refe_cidade'] = $cidade['TCidaCidade']['cida_descricao'];
			}
			$dados_historico = $this->TRefeReferenciaHistorico->listarHistorico($refe_codigo);
			$this->TRefeReferencia->joinCidadeEstado();
			$referencia = $this->TRefeReferencia->carregar($refe_codigo);
			$referencia['TRefeReferenciaHistorico'] =& $referencia['TRefeReferencia'];
			array_unshift($dados_historico,$referencia);
			$this->set(compact('dados_historico'));
		}
		$this->set(compact('estados','cidades','classes','tipos','menssagem'));
		
	}

	function configurar_origem_destino(){
		$authUsuario 	 	= $this->BAuth->user();
		$this->pageTitle 	= 'Configuração de Origem e Destino';
		$filtros 			= $this->Filtros->controla_sessao($this->data, 'Referencia');		
		if(!empty($authUsuario['Usuario']['codigo_cliente']))
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];		
		$this->data['Referencia'] = $filtros;
	}	

	function listagem_configuracao_origem_destino( $tela_inclusao = FALSE ){
		$this->loadModel('TCodeConfOrigemDestino');
		$filtros 	 = $this->Session->read('FiltrosReferencia');		
		$authUsuario = $this->BAuth->user();
		if(!empty($authUsuario['Usuario']['codigo_cliente']) )
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		$cliente 	 = $this->Cliente->buscaPorCodigo( $filtros['codigo_cliente'] );
		$refe_codigo_origem = isset($filtros['refe_codigo_origem'])?$filtros['refe_codigo_origem']:NULL;
		$conditions = $this->TCodeConfOrigemDestino->convertFiltrosEmConditions( $filtros );
		if( $conditions ){
			$this->paginate['TCodeConfOrigemDestino'] = array(
				'limit'  => 50,
				'order'  => 'TRefeReferenciaOrigem.refe_descricao',
				'fields' => array('TRefeReferenciaOrigem.refe_descricao', 'TRefeReferenciaOrigem.refe_latitude','TRefeReferenciaOrigem.refe_longitude',
					'TRefeReferenciaDestino.refe_descricao', 'TRefeReferenciaDestino.refe_latitude', 'TRefeReferenciaDestino.refe_longitude', 
					'TCodeConfOrigemDestino.code_codigo'),
				'joins' => array( 
					array(
						"table"     => $this->TRefeReferencia->databaseTable.'.'.$this->TRefeReferencia->tableSchema.'.'.$this->TRefeReferencia->useTable,
						"alias"     => "TRefeReferenciaOrigem",
						"type"      => "INNER",
						"conditions"=> array("TRefeReferenciaOrigem.refe_codigo = TCodeConfOrigemDestino.code_refe_codigo_origem")
					),
					array(
						"table"     => $this->TRefeReferencia->databaseTable.'.'.$this->TRefeReferencia->tableSchema.'.'.$this->TRefeReferencia->useTable,
						"alias"     => "TRefeReferenciaDestino",
						"type"      => "INNER",
						"conditions"=> array("TRefeReferenciaDestino.refe_codigo = TCodeConfOrigemDestino.code_refe_codigo_destino")
					)				
				),
				'conditions' => $conditions,
			);
			$listagem 		= $this->paginate('TCodeConfOrigemDestino');
			$codigo_cliente = $filtros['codigo_cliente'];
			$this->set(compact('cliente', 'listagem', 'refe_codigo_origem', 'tela_inclusao', 'codigo_cliente'));
		}
	}

	function incluir_configuracao_origem_destino(  ){
		$filtros = $this->Session->read('FiltrosReferencia');
		if( empty($this->data['TRefeReferencia']['codigo_cliente'])){
			$codigo_cliente = $this->params['url']['codigo_cliente'];
			$codigo_cliente = htmlentities(Comum::descriptografarLink( $codigo_cliente ));
		} else{
			$codigo_cliente = $this->data['TRefeReferencia']['codigo_cliente'];
		}
		$refe_codigo_origem = !empty($filtros['refe_codigo_origem']) ? $filtros['refe_codigo_origem'] : NULL;
		$this->loadModel('Cliente');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TCodeConfOrigemDestino');
		$this->pageTitle = 'Configurar Origem e Destino';
		$authUsuario 	= $this->BAuth->user();		
		if(!empty($authUsuario['Usuario']['codigo_cliente']))
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		$cliente 		= $this->Cliente->buscaPorCodigo($codigo_cliente);
		$cliente_pjur 	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente);
		$refe_origem['TRefeReferencia']['refe_codigo'] = NULL;
		if(!$cliente_pjur){
			$this->BSession->setFlash('cadastro_com_problema');
			if(!$this->RequestHandler->isAjax()){
				$this->redirect(array('controller' => 'Referencias','action' => 'configurar_origem_destino'));
				exit;
			}
		}
		if( !empty($refe_codigo_origem)){
			$refe_origem = $this->TRefeReferencia->carregar($refe_codigo_origem);
			$this->data['TRefeReferencia']['refe_codigo_origem_visual'] = $refe_origem['TRefeReferencia']['refe_descricao'];
			$this->data['TRefeReferencia']['refe_codigo_origem'] = $refe_origem['TRefeReferencia']['refe_codigo'];
			$refe_origem['TRefeReferencia']['refe_codigo'] = $refe_origem['TRefeReferencia']['refe_codigo'];
		}

		if ($this->RequestHandler->isPost()) {
			$this->Filtros->limpa_sessao( 'Referencia' );
			$this->Session->write('FiltrosReferencia', $this->data['TRefeReferencia']);			
			if( empty($this->data['TRefeReferencia']['refe_codigo_destino']) ){
				$this->TRefeReferencia->invalidate('refe_codigo_destino_visual', 'Informe o Destino');
				$this->BSession->setFlash('save_error');
			} else {
				if( $this->data['TRefeReferencia']['refe_codigo_destino'] == $this->data['TRefeReferencia']['refe_codigo_origem']){
					$this->TRefeReferencia->invalidate('refe_codigo_destino_visual', 'Informe um Destino diferente da Origem');
					$this->BSession->setFlash('save_error');					
				} else {
					App::Import('Component',array('DbbuonnyGuardian'));
					$pjur_pess_oras_codigo = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian( $this->data['TRefeReferencia']['codigo_cliente'] );
					$data['TCodeConfOrigemDestino']['code_pjur_pess_oras_codigo'] = $pjur_pess_oras_codigo[0];
					$data['TCodeConfOrigemDestino']['code_refe_codigo_origem'] 	  = $this->data['TRefeReferencia']['refe_codigo_origem'];
					$data['TCodeConfOrigemDestino']['code_refe_codigo_destino']   = $this->data['TRefeReferencia']['refe_codigo_destino'];
					if( $this->TCodeConfOrigemDestino->incluir( $data ) ) {
						$this->BSession->setFlash('save_success');
						$this->data['TRefeReferencia']['refe_codigo_destino'] = NULL;
						$this->data['TRefeReferencia']['refe_codigo_destino_visual'] = NULL;					
					} else {
						if( !empty($this->TCodeConfOrigemDestino->validationErrors['code_refe_codigo_origem']))
							$this->TRefeReferencia->invalidate('refe_codigo_origem_visual', $this->TCodeConfOrigemDestino->validationErrors['code_refe_codigo_origem']);
						if( !empty($this->TCodeConfOrigemDestino->validationErrors['code_refe_codigo_destino']))
							$this->TRefeReferencia->invalidate('refe_codigo_destino_visual', $this->TCodeConfOrigemDestino->validationErrors['code_refe_codigo_destino']);

						$this->BSession->setFlash('save_error');
					}
				}
			}
		}
		$this->data['TRefeReferencia']['codigo_cliente'] = $codigo_cliente;
		$this->set(compact('cliente','cliente_pjur', 'refe_origem'));
	}

	function excluir_configuracao_origem_destino( $code_codigo ){
		$this->loadModel('TCodeConfOrigemDestino');
		if ($this->TCodeConfOrigemDestino->excluir($code_codigo))
			$this->BSession->setFlash('delete_success');
		else
			$this->BSession->setFlash('delete_error');
		$this->redirect(array('action' => 'configurar_origem_destino'));
	}


	function verifica_novo_alvo() {
		$retorno = true;
		echo json_encode($retorno);
		exit;
	}

}