<?php
class ImpressoesController extends AppController {
	public $name = 'Impressoes';
	public $helpers = array('BForm', 'Html', 'Ajax');
	
	var $uses = array(
		'PedidoExame', 		
		'Setor',
		'Cargo',
		'Cliente',
		'Funcionario',
		'ClienteFuncionario',
		'GrupoEconomico',
		'GrupoEconomicoCliente',		
		'ItemPedidoExame',
		'FuncionarioSetorCargo',
		'ItemPedidoExameBaixa',
		'Configuracao',
		'FichaAssistencial', 
		'Atestado',
		'AtestadoCid',
		'FichaClinica',
		'FichaPsicossocial',
		'Audiometria',
		'PsicossocialValidadeAnexo',
		'FichaPsicossocialHistorico',
        'Usuario',
        'MultiEmpresa'
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array(
			'imp_pedido_exame',
			'imp_aso',
			'imp_recomendacoes',
			'imp_audiometria',
			'imp_ficha_clinica',
			'imp_ficha_assistencial',
			'imp_atestado_medico',
			'imp_receita_medica',
			'imp_psicossocial',
            'imp_geral',
			'psicossocial',
			'get_relatorio',
			'get_dispara_email',
			'psicossocial'
		));
	}//FINAL FUNCTION beforeFilter

	public function imp_pedido_exame($codigo_pedido_exame = null, $codigo_fornecedor = null) 
	{
		$this->autoRender = false;

		//verifica se tem os parametro obrigatorios
		if(is_null($codigo_pedido_exame) || is_null($codigo_fornecedor)) { //} && is_null($codigo_cliente_funcionario)) {
			print "Favor passar os campos obrigatorios!";
			exit;
		}

		//pega pelo codigo do pedido de exame o codigo_cliente
		$pedido = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido_exame)));
		$codigo_cliente_funcionario = $pedido['PedidoExame']['codigo_cliente_funcionario'];

		// debug($codigo_cliente_funcionario);exit;

		$relatorio = 1;
		$codigo_func_setor_cargo = null;
		$codigo_exame_aso = null;

		$this->__jasperConsultaPedidoExame($codigo_pedido_exame, $codigo_fornecedor, $codigo_cliente_funcionario, $relatorio, $codigo_func_setor_cargo, $codigo_exame_aso);
	}//fim imp_pedido_exame

	/**
	 * [imp_geral metodo para gerar os pdfs do kit para o pedido de exame]
	 * @param  [type]  $codigo_pedido_exame        [description]
	 * @param  [type]  $codigo_fornecedor          [description]
	 * @param  [type]  $codigo_cliente_funcionario [description]
	 * @param  integer $relatorio                  [description]
	 * @param  [type]  $codigo_func_setor_cargo    [description]
	 * @return [type]                              [description]
	 */
	// public function imp_geral($codigo_pedido_exame = null, $codigo_fornecedor = null, $codigo_cliente_funcionario = null, $relatorio = 1, $codigo_func_setor_cargo=null) {
	// 	try{
 //            $codigo_exame_aso = null;
 //            if(is_null($codigo_pedido_exame) && $relatorio == 2) {//1 = ASO
 //                throw new Exception("O Codigo do Pedido do Exame precisa ser especificado!");
 //            }else if(!is_null($codigo_pedido_exame) && $relatorio == 2){//1 = ASO
 //                $codigo_empresa = $this->Session->read('Auth.Usuario.codigo_empresa');
 //                if(is_null($codigo_empresa) || empty($codigo_empresa))
 //                    throw new Exception("Efetue o login novamente no sistema!");

 //                $codigo_exame_aso = $this->Configuracao->field('valor', array('chave' => 'INSERE_EXAME_CLINICO', 'codigo_empresa' => $codigo_empresa));
 //                if(is_null($codigo_exame_aso) || empty($codigo_exame_aso) || $codigo_exame_aso == 0)
 //                    throw new Exception("Configuração sem valor/faltando em Administrativo > Cadastro > Configuração do sistema, para a chave INSERE_EXAME_CLINICO!");
 //            }
 //            $this->__jasperConsultaPedidoExame($codigo_pedido_exame, $codigo_fornecedor, $codigo_cliente_funcionario, $relatorio, $codigo_func_setor_cargo, $codigo_exame_aso);
 //        }catch(Exception $ex){
 //            $this->BSession->setFlash(array(MSGT_ERROR, $ex->getMessage()));
 //            $this->redirect(array('controller' => 'consultas_agendas', 'action' => 'index2'));
 //        }
	// }//FINAL FUNCTION imp_geral
	public function imp_aso($codigo_pedido_exame = null) 
	{
		$this->autoRender = false;

		//verifica se tem os parametro obrigatorios
		if(is_null($codigo_pedido_exame) ){ // && is_null($codigo_func_setor_cargo) && is_null($codigo_cliente_funcionario)) {
			print "Favor passar os campos obrigatorios!";
			exit;
		}

		//pega pelo codigo do pedido de exame o codigo_cliente
		$pedido = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido_exame)));
		$codigo_cliente_funcionario = $pedido['PedidoExame']['codigo_cliente_funcionario'];
		$codigo_func_setor_cargo 	= $pedido['PedidoExame']['codigo_func_setor_cargo'];

		// debug(array($codigo_cliente_funcionario, $codigo_func_setor_cargo));exit;
		
		$relatorio =2;
		$codigo_exame_aso = null;
        if(is_null($codigo_pedido_exame) && $relatorio == 2) {//1 = ASO
            print "O Codigo do Pedido do Exame precisa ser especificado!";
            exit;
        }
        else if(!is_null($codigo_pedido_exame) && $relatorio == 2){//1 = ASO
            $codigo_empresa = 1;
            if(is_null($codigo_empresa) || empty($codigo_empresa)) {
                print "Efetue o login novamente no sistema!";
            	exit;
            }

            $codigo_exame_aso = $this->Configuracao->field('valor', array('chave' => 'INSERE_EXAME_CLINICO', 'codigo_empresa' => $codigo_empresa));
            if(is_null($codigo_exame_aso) || empty($codigo_exame_aso) || $codigo_exame_aso == 0) {
                print "Configuração sem valor/faltando em Administrativo > Cadastro > Configuração do sistema, para a chave INSERE_EXAME_CLINICO!";
                exit;
            }
        }

        $codigo_fornecedor = null;
        $this->__jasperConsultaPedidoExame($codigo_pedido_exame, $codigo_fornecedor, $codigo_cliente_funcionario, $relatorio, $codigo_func_setor_cargo, $codigo_exame_aso);
	}//fim imp_pedido_exame

	public function imp_recomendacoes($codigo_pedido_exame = null, $codigo_fornecedor = null) 
	{
		$this->autoRender = false;
		//verifica se tem os parametro obrigatorios
		if(is_null($codigo_pedido_exame) || is_null($codigo_fornecedor)) { 
			print "Favor passar os campos obrigatorios!";
			exit;
		}

		//pega pelo codigo do pedido de exame o codigo_cliente
		$pedido = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido_exame)));
		$codigo_cliente_funcionario = $pedido['PedidoExame']['codigo_cliente_funcionario'];
		$codigo_func_setor_cargo 	= $pedido['PedidoExame']['codigo_func_setor_cargo'];

		// debug(array($codigo_cliente_funcionario, $codigo_func_setor_cargo));exit;

		$relatorio = 5;
		$this->__jasperConsultaPedidoExame($codigo_pedido_exame, $codigo_fornecedor, $codigo_cliente_funcionario, $relatorio, $codigo_func_setor_cargo, $codigo_exame_aso);
	}//fim imp_recomendacoes

	
	private function __jasperConsultaPedidoExame( $codigo_pedido_exame = null, $codigo_fornecedor = null, $codigo_cliente_funcionario = null, $relatorio = null, $codigo_func_setor_cargo = null, $codigo_exame_aso = null) 
	{

		$this->autoRender = false;

		$nome_relatorio['1'] = 'pedidos_exame';
		$nome_relatorio['2'] = 'ASO';
		$nome_relatorio['3'] = 'ficha_clinica_1';
		$nome_relatorio['4'] = 'laudo_pcd';
		$nome_relatorio['5'] = 'Recomendacoes';
		$nome_relatorio['6'] = 'audiometria_1';
		$nome_relatorio['7'] = 'ficha_assistencial_exame';
		$nome_relatorio['8'] = 'psicossocial';

		$report_name = '/reports/RHHealth/' . $nome_relatorio[$relatorio];
		$file_name = basename( $nome_relatorio[$relatorio].'.pdf' );

		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>$report_name, // especificar qual relatório
			'FILE_NAME'=> $file_name // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array(
			'CODIGO_FORNECEDOR' => $codigo_fornecedor,
			'CODIGO_CLIENTE_FUNCIONARIO' => $codigo_cliente_funcionario,
			'CODIGO_PEDIDO_EXAME' => $codigo_pedido_exame,
			'CODIGO_FUNC_SETOR_CARGO' => $codigo_func_setor_cargo,
            'CODIGO_EXAME_ASO' => $codigo_exame_aso,
		);

        $exibe_nome_fantasia_aso = 'false';
        $exibe_rqe_aso = 'false';

		if($relatorio == 2){//ASO

		    if(!empty($codigo_pedido_exame) && !is_null($codigo_pedido_exame)){

                $codigo_cliente = $this->PedidoExame->getCodigoCliente($codigo_pedido_exame);
                
                if(!is_null($codigo_cliente)){

                    $return = $this->GrupoEconomico->getCampoPorCliente('exibir_nome_fantasia_aso', $codigo_cliente);
                    $exibe_nome_fantasia_aso = ($return ? 'true' : 'false');

                    $retorno_rqe = $this->GrupoEconomico->getCampoPorClienteRqe('exibir_rqe_aso', $codigo_cliente);
					$exibe_rqe_aso = ($retorno_rqe ? 'true' : 'false');
                }
            }
        }

        $parametros['EXIBE_NOME_FANTASIA_ASO'] = $exibe_nome_fantasia_aso;

        $parametros['EXIBE_RQE_ASO'] = $exibe_rqe_aso;

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
		//codigo_empresa
		$codigo_empresa = $this->get_cod_empresa($codigo_pedido_exame);
		//url logo da multiempresa
		$parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);

		try {
			// envia dados ao componente para gerar
			$url = $this->Jasper->generate( $parametros, $opcoes );

			// debug($url);exit;

			if(!empty($url)){
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
	}//FINAL FUNCTION __jasperConsultaPedidoExame

	public function imp_audiometria($codigo_pedido_exame = null) 
	{
		$this->autoRender = false;

		//verifica se tem os parametro obrigatorios
		if(is_null($codigo_pedido_exame) ) {
			print "Favor passar os campos obrigatorios!";
			exit;
		}
		//pega o codigo do exame de audiometria
		$config = $this->Configuracao->getChaveEmpresa('INSERE_EXAME_AUDIOMETRICO');

		//pega o codigo do item na tabela de itens_pedidos_exames
		$ipe = $this->ItemPedidoExame->find('first', array('conditions' => array('codigo_pedidos_exames' => $codigo_pedido_exame, 'codigo_exame' => $config)));

		//verifica se tem audiometria para este pedido e cliente_funcionario
		$audio = $this->Audiometria->find('first', array('conditions' => array('codigo_itens_pedidos_exames' => $ipe['ItemPedidoExame']['codigo'])));
		if(empty($audio)) {
			print "Não existe ficha audiometrica para este pedido de exame";
			exit;
		}
		$codigo = $audio['Audiometria']['codigo'];
		// debug($codigo_cliente_funcionario);exit;

		$this->__jasperConsultaAudiometria($codigo, $codigo_pedido_exame);
	}//fim imp_recomendacoes
	
	private function __jasperConsultaAudiometria($codigo, $codigo_pedido_exame) {

		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/relatorio_audiometria', // especificar qual relatório
			'FILE_NAME'=> basename( 'relatorio_audiometria.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array('CODIGO' => $codigo);

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
		//codigo_empresa
		$codigo_empresa = $this->get_cod_empresa($codigo_pedido_exame);
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


	public function imp_ficha_clinica($codigo_pedido_exame)
	{
		$this->autoRender = false;

		//verifica se tem os parametro obrigatorios
		if( is_null($codigo_pedido_exame)) { //  &&  is_null($codigo_ficha_clinica) && is_null($codigo_funcionario)) {
			print "Favor passar os campos obrigatorios!";
			exit;
		}

		//pega pelo codigo do pedido de exame o codigo_cliente
		$pedido = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido_exame)));
		$codigo_funcionario = $pedido['PedidoExame']['codigo_funcionario'];

		$ficha = $this->FichaClinica->find('first', array('conditions' => array('codigo_pedido_exame' => $codigo_pedido_exame)));

		if(empty($ficha)) {
			print "Não existe ficha clinica para este pedido!";
			exit;
		}
		$codigo_ficha_clinica = $ficha['FichaClinica']['codigo'];

		// debug(array($codigo_funcionario, $codigo_ficha_clinica));exit;

		//SALVA NA TABELA TEMPORÁRIA OS DADOS SERIALIZADOS PARA A CONSTRUÇÃOI DO RELATORIO PDF
		$this->FichaClinica->criaTabelaTemporaria($codigo_ficha_clinica);

		$this->FichaClinica->temp_table_riscos($codigo_ficha_clinica);
		// debug('opa');exit;

		// GERA O RELATORIO PDF
		$this->__jasperConsultaFC($codigo_ficha_clinica, $codigo_pedido_exame, $codigo_funcionario);
	}//fim imp_ficha_clinica

	private function __jasperConsultaFC( $codigo_ficha_clinica, $codigo_pedido_exame, $codigo_funcionario) 
	{
        // opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/relatorio_ficha_clinica', // especificar qual relatório
			'FILE_NAME'=> basename( 'relatorio_ficha_clinica.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array(
			'CODIGO_FICHA_CLINICA' => $codigo_ficha_clinica, 
			'CODIGO_PEDIDO_EXAME' => $codigo_pedido_exame, 
			'CODIGO_FUNCIONARIO' => $codigo_funcionario
		);

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
		//codigo_empresa
		$codigo_empresa = $this->get_cod_empresa($codigo_pedido_exame);
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


	/**
	 * [imprimir_ficha_assistencial description] para impressao da ficha assistencial
	 * @param  [type] $codigo_ficha_assistencial [description]
	 * @param  [type] $codigo_pedido_exame       [description]
	 * @param  [type] $codigo_funcionario        [description]
	 * @return [type]                            [description]
	 */
	public function imp_ficha_assistencial($codigo_pedido_exame)
	{
		$this->autoRender = false;
		
		//verifica se tem os parametro obrigatorios
		if(is_null($codigo_pedido_exame)){ //is_null($codigo_ficha_assistencial) &&  && is_null($codigo_funcionario)) {
			print "Favor passar os campos obrigatorios!";
			exit;
		}

		//pega pelo codigo do pedido de exame o codigo_cliente
		$pedido = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido_exame)));
		$codigo_funcionario = $pedido['PedidoExame']['codigo_funcionario'];

		$ficha = $this->FichaAssistencial->find('first', array('conditions' => array('codigo_pedido_exame' => $codigo_pedido_exame)));
		if(empty($ficha)) {
			print "Não existe ficha assistencial para este pedido!";
			exit;
		}
		$codigo_ficha_assistencial = $ficha['FichaAssistencial']['codigo'];

		// debug(array($codigo_funcionario, $codigo_ficha_assistencial));exit;


		//SALVA NA TABELA TEMPORÁRIA OS DADOS SERIALIZADOS PARA A CONSTRUÇÃOI DO RELATORIO PDF
		$this->FichaAssistencial->criaTabelaTemporariaFichaAssistencial($codigo_ficha_assistencial);

		// debug("aqui");exit;

		// GERA O RELATORIO PDF
		$this->__jasperConsultaFA('ficha_assitencial',$codigo_ficha_assistencial, $codigo_pedido_exame, $codigo_funcionario);

	}//fim imprimir ficha assistencial

	/**
	 * [imprimir_atestado_medico description] imprimir o atestado médico
	 * @param  [type] $codigo_ficha_assistencial [description]
	 * @param  [type] $codigo_pedido_exame       [description]
	 * @param  [type] $codigo_funcionario        [description]
	 * @return [type]                            [description]
	 */
	public function imp_atestado_medico($codigo_pedido_exame)
	{
		$this->autoRender = false;

		//verifica se tem os parametro obrigatorios
		if(is_null($codigo_pedido_exame)) { //} && is_null($codigo_pedido_exame) && is_null($codigo_funcionario)) {
			print "Favor passar os campos obrigatorios!";
			exit;
		}
		
		//pega pelo codigo do pedido de exame o codigo_cliente
		$pedido = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido_exame)));
		$codigo_funcionario = $pedido['PedidoExame']['codigo_funcionario'];

		$ficha = $this->FichaAssistencial->find('first', array('conditions' => array('codigo_pedido_exame' => $codigo_pedido_exame)));
		if(empty($ficha)) {
			print "Não existe ficha assistencial para este pedido!";
			exit;
		}
		$codigo_ficha_assistencial = $ficha['FichaAssistencial']['codigo'];

		// debug(array($codigo_funcionario, $codigo_ficha_assistencial));exit;

		// GERA O RELATORIO PDF
		$this->__jasperConsultaFA('ficha_assistencial_atestado_medico',$codigo_ficha_assistencial, $codigo_pedido_exame, $codigo_funcionario);

	}//fim imprimir_atestado_medico

	/**
	 * [imprimir_receita_medica description] receita medica
	 * @param  [type] $codigo_ficha_assistencial [description]
	 * @param  [type] $codigo_pedido_exame       [description]
	 * @param  [type] $codigo_funcionario        [description]
	 * @return [type]                            [description]
	 */
	public function imp_receita_medica($codigo_pedido_exame)
	{
		$this->autoRender = false;

		//verifica se tem os parametro obrigatorios
		if(is_null($codigo_pedido_exame)) {
			print "Favor passar os campos obrigatorios!";
			exit;
		}

		//pega pelo codigo do pedido de exame o codigo_cliente
		$pedido = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido_exame)));
		$codigo_funcionario = $pedido['PedidoExame']['codigo_funcionario'];

		$ficha = $this->FichaAssistencial->find('first', array('conditions' => array('codigo_pedido_exame' => $codigo_pedido_exame)));
		if(empty($ficha)) {
			print "Não existe ficha assistencial para este pedido!";
			exit;
		}
		$codigo_ficha_assistencial = $ficha['FichaAssistencial']['codigo'];

		// debug(array($codigo_funcionario, $codigo_ficha_assistencial));exit;

		//SALVA NA TABELA TEMPORÁRIA OS DADOS SERIALIZADOS PARA A CONSTRUÇÃOI DO RELATORIO PDF
		$this->FichaAssistencial->criaTabelaTemporariaReceitaMedica($codigo_ficha_assistencial);
		
		// GERA O RELATORIO PDF
		$this->__jasperConsultaFA("ficha_assitencial_receita_medica",$codigo_ficha_assistencial, $codigo_pedido_exame, $codigo_funcionario);

	}//fim imprimir receita medica

	/**
	 * chamada do jasper
	 * @param  [type] $codigo_ficha_assistencial [description]
	 * @param  [type] $codigo_pedido_exame       [description]
	 * @param  [type] $codigo_funcionario        [description]
	 * @return [type]                            [description]
	 */
	private function __jasperConsultaFA($tipo, $codigo_ficha_assistencial, $codigo_pedido_exame, $codigo_funcionario) {
		
        // opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/'.$tipo, // especificar qual relatório
			'FILE_NAME'=> basename( $tipo.'.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array(
			'CODIGO_FICHA_ASSISTENCIAL' => $codigo_ficha_assistencial, 
			'CODIGO_PEDIDO_EXAME' => $codigo_pedido_exame, 
			'CODIGO_FUNCIONARIO' => $codigo_funcionario
		);

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
		//codigo_empresa
		$codigo_empresa = $this->get_cod_empresa($codigo_pedido_exame);
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
				
	}//FINAL FUNCTION __jasperConsulta


	/**
     * metodo para chamar o jasper e gerar o pdf da ficha psicossocial
     */
    public function imp_psicossocial($codigo_pedido_exame)
    {
        $this->autoRender = false;

        //verifica se tem os parametro obrigatorios
		if(is_null($codigo_pedido_exame)) {
			print "Favor passar os campos obrigatorios!";
			exit;
		}

		$ficha = $this->FichaPsicossocial->find('first', array('conditions' => array('codigo_pedido_exame' => $codigo_pedido_exame)));
		if(empty($ficha)) {
			print "Não existe ficha psicossocial para este pedido!";
			exit;
		}
		$codigo_ficha_psicossocial = $ficha['FichaPsicossocial']['codigo'];
		// debug($codigo_ficha_psicossocial);exit;

        $this->__jasperConsultaFP($codigo_ficha_psicossocial, $codigo_pedido_exame);

	}//fim imprimir_relatorio
	
	public function psicossocial($encoded)
    {
		$this->autoRender = false;

		// $encoded = urlencode( base64_encode($codigo_pedido_exame) );
		$codigo_pedido_exame = base64_decode(urldecode($encoded));

		if(is_null($codigo_pedido_exame)) {
			print "Favor passar os campos obrigatorios!";
			exit;
		}

		$fichaPsicossocial = $this->FichaPsicossocial->getByCodigoPedidoExame($codigo_pedido_exame);
		if(empty($fichaPsicossocial)) {
			print "Não existe ficha psicossocial para este pedido!";
			exit;
		}

		$validadeAnexo = $this->PsicossocialValidadeAnexo->getByCodigoFichaPsicossocial($fichaPsicossocial['FichaPsicossocial']['codigo']);
		
		$data_validade = DateTime::createFromFormat('d/m/Y H:i:s', $validadeAnexo['PsicossocialValidadeAnexo']['data_validade'], new DateTimeZone('UTC'));

		$dataAgora = new DateTime();

		if ($data_validade < $dataAgora) {
			print "A validade do link expirou"; 
			exit;
		}
		
        $url = '';
		if(Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) {
			$url = Ambiente::URL_SERVIDOR_PORTAL_PRODUCAO;
		} else {
			$url = Ambiente::URL_SERVIDOR_PORTAL_HOMOLOGACAO;
		}

        $dados['FichaPsicossocialHistorico']['codigo_ficha_psicossocial'] = $fichaPsicossocial['FichaPsicossocial']['codigo'];
        $dados['FichaPsicossocialHistorico']['link'] = $url . Router::url();
        $dados['FichaPsicossocialHistorico']['data_inclusao'] = date("Y-m-d H:i:s");

		$resultado = $this->FichaPsicossocialHistorico->incluir($dados);

        $this->__jasperConsultaFP($fichaPsicossocial['FichaPsicossocial']['codigo'], $codigo_pedido_exame);
    }

    private function __jasperConsultaFP($codigo, $codigo_pedido_exame) {

        // opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/ficha_psicossocial', // especificar qual relatório
			'FILE_NAME'=> basename( 'avaliacao_psicossocial.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
        $parametros = array('CODIGO_FICHA_PSICOSSOCIAL' => $codigo);
        
		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
		//codigo_empresa
		$codigo_empresa = $this->get_cod_empresa($codigo_pedido_exame);
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


    /**
     * [imp_geral metodo para gerar os pdfs do kit para o pedido de exame]
     * @param  [type]  $codigo_pedido_exame        [description]
     * @param  [type]  $codigo_fornecedor          [description]
     * @param  [type]  $codigo_cliente_funcionario [description]
     * @param  integer $relatorio                  [description]
     * @param  [type]  $codigo_func_setor_cargo    [description]
     * @return [type]                              [description]
     */
    public function imp_geral($codigo_pedido_exame = null, $codigo_fornecedor = null, $codigo_cliente_funcionario = null, $relatorio = 1, $codigo_func_setor_cargo=null) {
        try{

            $codigo_empresa = null;
            $codigo_exame_aso = null;

            if(is_null($codigo_pedido_exame) && $relatorio == 2) {//1 = ASO
                throw new Exception("O Codigo do Pedido do Exame precisa ser especificado!");
            }else if(!is_null($codigo_pedido_exame) && $relatorio == 2){//1 = ASO

                $codigo_empresa = $this->PedidoExame->field('codigo_empresa', array('codigo' => $codigo_pedido_exame));

                if (is_null($codigo_empresa)) {
                    $codigo_empresa = $this->Session->read('Auth.Usuario.codigo_empresa');
                    if(is_null($codigo_empresa) || empty($codigo_empresa))
                        throw new Exception("Efetue o login novamente no sistema!");
                }

                $codigo_exame_aso = $this->Configuracao->field('valor', array('chave' => 'INSERE_EXAME_CLINICO', 'codigo_empresa' => $codigo_empresa));
                if(is_null($codigo_exame_aso) || empty($codigo_exame_aso) || $codigo_exame_aso == 0)
                    throw new Exception("Configuração sem valor/faltando em Administrativo > Cadastro > Configuração do sistema, para a chave INSERE_EXAME_CLINICO!");
            }
            $this->__jasperConsultaPedidoExame($codigo_pedido_exame, $codigo_fornecedor, $codigo_cliente_funcionario, $relatorio, $codigo_func_setor_cargo, $codigo_exame_aso);
        }catch(Exception $ex){
            $this->BSession->setFlash(array(MSGT_ERROR, $ex->getMessage()));
            $this->redirect(array('controller' => 'consultas_agendas', 'action' => 'index2'));
        }
    }//FINAL FUNCTION imprime

    
    public function get_relatorio($params)
    {

    	$this->autoRender = false;
    	
		//Descumprimir hash da url
		$retorno = unserialize(gzuncompress(stripslashes(base64_decode(strtr($params, '-_,', '+/=')))));
		
    	$dados = base64_decode($retorno);
    	$dados = json_decode($dados);
		
		$parametros = (array)$dados->parametro;
		$opcoes = (array)$dados->opcoes;
    	
    	// envia dados ao componente para gerar
		$url = $this->Jasper->generate( $parametros, $opcoes );	
		echo $url;
		exit;
    }

    public function get_dispara_email($params)
    {

    	$this->autoRender = false;

    	$dados = base64_decode($params);
    	$dados = json_decode($dados);

    	// debug($dados);
		
    	$dados_email = (array)$dados->dados;
		$assunto = $dados->assunto;
		$template = $dados->template;
		$to = $dados->to;
		$attachment = $dados->attachment;

		$dados_email['tipo_notificacao'] = (array) $dados_email['tipo_notificacao'];
		foreach($dados_email['dados_exames'] AS $key => $exames) {
			$dados_email['dados_exames'][$key] = (array)$dados_email['dados_exames'][$key];
		}

		// debug($dados_email);
		// debug($assunto);
		// debug($template);
		// debug($to);
		// debug($attachment);
		// exit;

    	$this->PedidoExame->disparaEmail($dados_email, $assunto, $template, $to, $attachment);

    	echo '1';
		exit;
    }

    private function get_cod_empresa($codigo_pedido_exame){
    	//busca pedido exame
    	$get_pedido = $this->PedidoExame->find('first', array('fields' => array('codigo_empresa'), 'conditions' => array('codigo' => $codigo_pedido_exame)));
    	//seta codigo empresa
    	$codigo_empresa = $get_pedido['PedidoExame']['codigo_empresa'];

    	return $codigo_empresa;
    }
}// FINAL CLASS PedidosExamesController
