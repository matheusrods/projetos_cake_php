<?php 

/**
 * 
 */
class ApiRiscosAtributosDetalhesController extends AppController
{
	public $name = '';
	public $dados = array();
	public $ApiAutorizacao;

	var $uses = array();

	public $campos_obrigatorios = array();

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('*'));

		App::import('Component', 'ApiAutorizacao');
		$this->ApiAutorizacao = new ApiAutorizacaoComponent();
	}

    /**
     * Metodo para validar a autorizacao pelo token cpnj
     */ 
    private function valida_autorizacao($token = null, $cnpj =null)
    {

        //verifica se tem os get passados
    	if(!empty($token) && !empty($cnpj)) {

            //verifica se pode prosseguir com o processo
    		if($this->ApiAutorizacao->autoriza($token, $cnpj)) {
                //foi validado
    			return true;
    		} else {
                /**
                 * Erro 3 é quando o token e o cnpj passado não tem relacao ou está errado
                 */
                $this->dados['status']  = '3';
                $this->dados['msg']     = 'Token ou CNPJ invalido';

                return false;

            }//fim verificacao dos dados
        } else {
            /**
             * Get cnpj ou tokem em branco
             */ 
            $this->dados["status"] = "2";
            $this->dados['msg'] = 'cnpj ou token em branco';

            return false;

        } //fim verificacao do get    

        return false;

    } //fim valida_autorizacao

    public function consulta_risco_atributo_detalhe()
    {


    	if(isset($_GET['token']) && isset($_GET['cnpj'])){

    		$cnpj = $_GET['cnpj'];
    		$token = $_GET['token'];

    		if ($this->valida_autorizacao($token, $cnpj)) {
    			
    			$this->loadModel('RiscoAtributoDetalhe');
                $this->loadModel('RiscoAtributoDetalheExterno');
                $this->loadModel('Cliente');
                $cliente = $this->Cliente->findByCodigoDocumento($cnpj);

    			//fazer a consulta ao banco fazendo o join com o codigo externo
                $field = array( 'RiscoAtributoDetalhe.codigo as codigo',
                    'RiscoAtributoDetalheExterno.codigo_externo as codigo_externo',
                    'RiscoAtributoDetalhe.descricao as descricao'
                );

                $this->RiscoAtributoDetalhe->bindModel(array('hasOne' => array(
                    'RiscoAtributoDetalheExterno' => array(
                        'foreignKey' => 'codigo_riscos_atributos_detalhes',
                        'conditions' => array('RiscoAtributoDetalheExterno.codigo_cliente' => $cliente['Cliente']['codigo'])
                    )
                )), false);

                $risco_atributo_detalhe = $this->RiscoAtributoDetalhe->find('all', array(
                    'fields' => $field,
                    'order' => 'RiscoAtributoDetalhe.codigo'
                ));


                $this->dados['risco_atributo_detalhe'] = '';

                if(!empty($risco_atributo_detalhe)) {

                    foreach ($risco_atributo_detalhe as $chave => $valor) {
                        $item = new stdClass();
                        $item->codigo = $valor[0]['codigo'];
                        $item->codigo_externo = $valor[0]['codigo_externo'];
                        $item->descricao = $valor[0]['descricao'];
                        $this->dados['risco_atributo_detalhe'][$chave] = $item;
                    }

                    //$this->dados['risco_atributo_detalhe']['retorno'] = $risco_atributo_detalhe;

                    //status de sucesso
                    $this->dados['status'] = '200';
                    $this->dados['msg'] = 'SUCESSFULL OPERATION';

                } else{
                    $this->dados['status'] = '5';
                    $this->dados['msg']		= 'Sem resultados!';
                }
            }

        } else {
            $this->dados["status"] = "1";
            $this->dados['msg'] = 'cnpj ou token nao passados!';
        }

        $entrada   = "url;";
        $entrada  .= implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);

        $retorno = json_encode($this->dados);

        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FORAM PASSADOS OS PARAMETROS CNPJ/TOKEN';

        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_RISCO_ATRIBUTO_DETALHE_CONSULTA_RISCO_ATRIBUTO_DETALHE");

        // Retorna sucesso ou erro de acordo com o tipo de conteudo usado para consumir a API
        $contentType = 'json';
        if (isset($_GET['type']) && !empty($_GET['type'])) {
            $contentType = $_GET['type'];
        }

        if ($contentType == 'xml') {
            // Retorna finalmente o XML
            App::import('Helper', 'Xml');
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version'=>'1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno), 
                array(
                    'root' => 'retorno', 
                    'format' => 'tags',
                    'cdata' => false/*, 'whitespace' => true*/
                )
            );
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        } else {
            // Retorna finalmente o JSON        
            header('Content-type: application/json; charset=UTF-8');
            echo $retorno;
        }
        exit;

    }

}