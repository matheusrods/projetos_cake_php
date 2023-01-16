<?php
/**
 * 
 * Teste unitário dos relatórios
 * 
 *    • ASO
 *    • Audiometria
 *    • Cat
 *    • Ficha Assistencial Atestados Médicos
 *    • Ficha Assistencial Exame 
 *    • Ficha Assistencial 
 *    • Ficha Assistencial Receita Médica
 *    • Ficha Clínica
 *    • Ficha Psicossocial
 *    • Laudo PCD 
 *    • Pedido de Exames 
 *    • Psicossocial 
 *    • Recomendações 
 *    • Relatório Anual 
 *    • Relatório Audiometria 
 *    • Relatório Ficha Clínica 
 *    • Relatório Ficha PCD 
 *    • PCMSO  
 *    • PCMSO Versões 
 *    • PPP
 *    • PGR
 *    • PGR Versões
 * 
 */
class JasperTestController extends AppController {
	
	public $name = '';
	var $uses = array();
	public $autoRender = false;
	
	public $components = array('RequestHandler', 'Jasper');

	public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
	}

	public function obterUrlLogo($params){
		return "https://api.rhhealth.com.br/default/2019/11/14/C54140E7-0BB5-A0D8-A142-84F9D56B091F.png";
	}

	public function urlLogoMultiEmpresa($params){
		return "https://api.rhhealth.com.br/ithealth/2021/03/02/29907613-9A83-8E68-B4F2-83AE65F932DE.png";
	}

	public function obterRelatorios(){

		$URL_MATRIZ_LOGOTIPO = $this->obterUrlLogo(array('CODIGO_CLIENTE'=>71758));
		$URL_LOGO_MULTI_EMPRESA = $this->urlLogoMultiEmpresa(array('CODIGO_EMPRESA' => 1));

		//pega a data do ano que vem
		$proximo_ano = mktime(0, 0, 0, date("m"), date("d"), date("Y")+1);
		$data_ano_que_vem = date('Y-m-d', $proximo_ano);
			
		$relatorios = array(
			
			// • ASO
			'aso' => array( 
				'NOME' => 'ASO',
				'FILE_NAME' => 'aso.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/ASO',
				
				// é necessário definir valores de teste, mas pode passar por url também
				'args' => array(
					'CODIGO_CLIENTE_FUNCIONARIO' => 37519,
					'CODIGO_FORNECEDOR' => 911,
					'CODIGO_PEDIDO_EXAME' => 143390,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Audiometria
			'audiometria' => array(
				'NOME' => 'audiometria', 
				'FILE_NAME' => 'audiometria.pdf',
				'REPORT_NAME' => '/reports/RHHealth/audiometria',
				'args' => array(
					'CODIGO_PEDIDO_EXAME' => 112078,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			'audiometria_1' => array(
				'NOME' => 'audiometria', 
				'FILE_NAME' => 'audiometria.pdf',
				'REPORT_NAME' => '/reports/RHHealth/audiometria_1',
				'args' => array(
					'CODIGO_PEDIDO_EXAME' => 35788,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Cat
			'cat' => array( 
				'NOME' => 'cat', 
				'FILE_NAME' => 'cat.pdf',
				'REPORT_NAME' => '/reports/RHHealth/cat',
				'args' => array(
					'CODIGO_CAT'=> 2, 
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Ficha Assistencial Atestados Médicos
			'ficha_assistencial_atestado_medico' => array( 
				'NOME' => 'relatorio_ficha_assistencial', 
				'FILE_NAME' => 'relatorio_ficha_assistencial.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/ficha_assistencial_atestado_medico',
				'args' => array(
					'CODIGO_FICHA_ASSISTENCIAL' => 24,
					'CODIGO_PEDIDO_EXAME' => 35788,
					'CODIGO_FUNCIONARIO' => 68798,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Ficha Assistencial Exame 
			'relatorio_ficha_assistencial_exame' => array(
				'NOME' => 'relatorio_ficha_assistencial', 
				'FILE_NAME' => 'relatorio_ficha_assistencial.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/ficha_assistencial_exame',
				'args' => array(
					'CODIGO_CLIENTE_FUNCIONARIO' => 37519,
					'CODIGO_PEDIDO_EXAME' => 143390,
					'CODIGO_FUNC_SETOR_CARGO' => 217662,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

   		    // • Ficha Assistencial 
			'ficha_assistencial' => array(
				'NOME' => 'relatorio_ficha_assistencial', 
				'FILE_NAME' => 'relatorio_ficha_assistencial.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/ficha_assitencial',
				'args' => array(
					'CODIGO_FICHA_ASSISTENCIAL' => 24,
					'CODIGO_PEDIDO_EXAME' => 35788,
					'CODIGO_FUNCIONARIO' => 68798,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Ficha Assistencial Receita Médica
			'ficha_assitencial_receita_medica' => array(
				'NOME' => 'ficha_assitencial_receita_medica', 
				'FILE_NAME' => 'ficha_assitencial_receita_medica.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/ficha_assitencial_receita_medica',
				'args' => array(
					'CODIGO_FICHA_ASSISTENCIAL' => 24,
					'CODIGO_PEDIDO_EXAME' => 35788,
					'CODIGO_FUNCIONARIO' => 68798,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Ficha Clínica
			'ficha_clinica' => array(
				'NOME' => 'ficha_clinica', 
				'FILE_NAME' => 'ficha_clinica.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/ficha_clinica',
				'args' => array(
					'CODIGO_CLIENTE_FUNCIONARIO' => 37519,
					'CODIGO_PEDIDO_EXAME' => 143390,
					'CODIGO_FUNC_SETOR_CARGO' => 217662,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Ficha Psicossocial
			'ficha_psicossocial' => array(
				'NOME' => 'ficha_psicossocial', 
				'FILE_NAME' => 'avaliacao_psicossocial.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/ficha_psicossocial',
				'args' => array(
					'CODIGO_FICHA_PSICOSSOCIAL' => 14,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA,
				)
			),

			// • Laudo PCD 
			'laudo_pcd' => array(
				'NOME' => 'laudo_pcd', 
				'FILE_NAME' => 'laudo_pcd.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/laudo_pcd',
				'args' => array(
					'CODIGO_CLIENTE_FUNCIONARIO' => 37519,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Pedido de Exames 
			'pedidos_exame' => array( 
				'NOME' => 'pedidos_exame', 
				'FILE_NAME' => 'pedidos_exames.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/pedidos_exame',
				'args' => array(
					'CODIGO_FORNECEDOR' => 911,
					'CODIGO_CLIENTE_FUNCIONARIO' => 37519,
					'CODIGO_PEDIDO_EXAME' => 143390,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA,
				)
			),

			// • Psicossocial 
			'psicossocial' => array( 
				'NOME' => 'psicossocial', 
				'FILE_NAME' => 'psicossocial.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/psicossocial',
				'args' => array(
					'CODIGO_PEDIDO_EXAME' => 143390,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Recomendações 
			'recomendacoes' => array( 
				'NOME' => 'recomendacoes', 
				'FILE_NAME' => 'recomendacoes.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/Recomendacoes',
				'args' => array(
					'CODIGO_PEDIDO_EXAME' => 143390,
					'CODIGO_CLIENTE_FUNCIONARIO' => 37519,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Relatório Anual 
			'relatorio_anual' => array( 
				'NOME' => 'relatorio_anual', 
				'FILE_NAME' => 'relatorio_anual.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/relatorio_anual',
				'args' => array(
					'CODIGO_CLIENTE' => 10011,
					'CODIGO_EXAME' => 1000,
					'TIPO_AGRUPAMENTO'=> 'tipo_pedido', 
					'DATA_INICIO' => '20190101', 
					'DATA_FIM' => '20191101',
					'DATA_ANO_QUE_VEM' => $data_ano_que_vem,
					'TIPO_EXAME' => 1,
					'CODIGO_UNIDADE' => 0,
					'CODIGO_SETOR' => 0,				
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA,
				)
			),
			
			// • Relatório Audiometria 
			'relatorio_audiometria' => array( 
				'NOME' => 'relatorio_audiometria', 
				'FILE_NAME' => 'relatorio_audiometria.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/relatorio_audiometria',
				'args' => array(
					'CODIGO' => 3182,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Relatório Ficha Clínica 
			'relatorio_ficha_clinica' => array(
				'NOME' => 'relatorio_ficha_clinica', 
				'FILE_NAME' => 'relatorio_ficha_clinica.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/relatorio_ficha_clinica',
				'args' => array(
					'CODIGO_FICHA_CLINICA' => 26416,
					'CODIGO_FUNCIONARIO' => 12295,
					'CODIGO_PEDIDO_EXAME' => 143390,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • Relatório Ficha PCD 
			'relatorio_ficha_pcd' => array(
				'NOME' => 'relatorio_ficha_pcd', 
				'FILE_NAME' => 'relatorio_ficha_pcd.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/relatorio_ficha_pcd',
				'args' => array(
					'CODIGO_FICHA_CLINICA' => 26416,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • PCMSO  
			'relatorio_pcmso' => array( 
				'NOME' => 'relatorio_pcmso', 
				'FILE_NAME' => 'relatorio_pcmso.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/relatorio_pcmso',
				'args' => array(
					'CODIGO_CLIENTE' => 10011, 
					'IMP_SETOR_CARGO_VAZIO' => 0, 
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • PCMSO Versões 
			'relatorio_pcmso_versao' => array(
				'NOME' => 'relatorio_pcmso_versao', 
				'FILE_NAME' => 'relatorio_pcmso_versao.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/relatorio_pcmso_versao',
				'args' => array(
					'CODIGO_CLIENTE' => 10011,
					'CODIGO_PCMSO_VERSAO' => 209,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • PPP
			'relatorio_ppp' => array(
				'NOME' => 'relatorio_ppp', 
				'FILE_NAME' => 'relatorio_ppp.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/relatorio_ppp',
				'args' => array(
					'CODIGO_CLIENTE' => 10011,
					'CODIGO_FUNCIONARIO' => 12295,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • PGR
			'relatorio_ppra' => array(
				'NOME' => 'relatorio_ppra', 
				'FILE_NAME' => 'relatorio_ppra.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/relatorio_ppra',
				'args' => array(
					'CODIGO_CLIENTE' => 10011,
					'IMP_SETOR_CARGO_VAZIO' => 1, 
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),

			// • PGR Versões			
			'relatorio_ppra_versoes' => array(
				'NOME' => 'relatorio_ppra_versoes', 
				'FILE_NAME' => 'relatorio_ppra_versoes.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/relatorio_ppra_versoes',
				'args' => array(
					'CODIGO_PPRA_VERSOES' => 1357,
					'CODIGO_CLIENTE' => 10011,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA,
				)
			),

			// • Nova Ficha Clínica
			'ficha_clinica_1' => array(
				'NOME' => 'ficha_clinica_1', 
				'FILE_NAME' => 'ficha_clinica.pdf', 
				'REPORT_NAME' => '/reports/RHHealth/ficha_clinica_1',
				'args' => array(
					'CODIGO_CLIENTE_FUNCIONARIO' => 37519,
					'CODIGO_PEDIDO_EXAME' => 143390,
					'CODIGO_FUNC_SETOR_CARGO' => 217662,
					'URL_MATRIZ_LOGOTIPO' => $URL_MATRIZ_LOGOTIPO,
					'URL_LOGO_MULTI_EMPRESA' => $URL_LOGO_MULTI_EMPRESA
				)
			),
			

		);

		return $relatorios;
	}

	public function relatorio( $report = null ) {
		
		$url_params = array();

		// parametros recebidos na url
		if(isset($this->RequestHandler->params['url']) && !empty($this->RequestHandler->params['url'])) {
			$url_params = $this->RequestHandler->params['url'];
		}
		
		$relatorios = $this->obterRelatorios();
		
		if(empty($report) 
			|| !isset($relatorios[$report])
			|| empty($url_params)){

			$this->responseJson(array('Teste não encontrado'));
		}

		// parametros definidos
		$args = isset($relatorios[$report]['args']) ? $relatorios[$report]['args'] : array();
		
		$parametros = array();

		// interar parametros definidos
		foreach ($args as $key => $value) {

			$new = $this->recursiveFind($url_params, $key);
			$parametros[$key] = isset($new[0]) ? $new[0] : $value;
		}

		$filename = basename($relatorios[$report]['FILE_NAME']);
		$reportname = $relatorios[$report]['REPORT_NAME'];

		$opcoes = array(
			'REPORT_NAME'=>$reportname,
			'FILE_NAME'=> $filename
		);

		try {

			//$this->Jasper->setUsuarioAutenticado($this->authUsuario);

			$url = $this->Jasper->generate( $parametros, $opcoes );	

		} catch (Exception $e) {
			debug($e); exit;
		}
		

		if($url){

			header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
			header('Pragma: no-cache');
			header('Content-type: application/pdf');
			echo $url; exit;
		}
		
		$data = array('Relatório não gerado');
		$this->responseJson($data);
        
	}


	/**
     * 
     * Encontrar um valor por chave informada
     * https://stackoverflow.com/questions/3975585/search-for-a-key-in-an-array-recursively
     *
     * @param array $array
     * @param [type] $needle
     * @return void
     */
    function recursiveFind(array $array, $needle) {
        $iterator = new RecursiveArrayIterator($array);
        $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
        $return = array();
        foreach ($recursive as $key => $value) {
          if ($key === $needle) {
            $return[] = $value;
          }
        } 
        return $return;
    }
}
?>