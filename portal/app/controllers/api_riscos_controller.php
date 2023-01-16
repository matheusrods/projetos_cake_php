<?php 

/**
 * 
 */
class ApiRiscosController extends AppController
{
	public $name = '';

	public $ApiAutorizacao;

	var $uses = array();

	public $dados = array();
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

            //componente para validar o token e cnpj            
            // $ApiAutorizacao = new ApiAutorizacaoComponent();

            //verifica se pode prosseguir com o processo
    		if($this->ApiAutorizacao->autoriza($token, $cnpj)) {
                //foi validado
    			return true;
    		}
    		else {
                /**
                 * Erro 3 é quando o token e o cnpj passado não tem relacao ou está errado
                 */ 
                $this->dados['status']  = '3';
                $this->dados['msg']     = 'Token ou CNPJ invalido';

                return false;

            }//fim verificacao dos dados
        } 
        else {
            /**
             * Get cnpj ou tokem em branco
             */ 
            $this->dados["status"] = "2";

            return false;

        } //fim verificacao do get    


        return false;

    } //fim valida_autorizacao

    public function consulta_risco()
    {
        $type   = isset($_GET['type']) && $_GET['type'] == 'xml' ? 'xml' : 'json';

        if(isset($_GET['token']) && isset($_GET['cnpj']) && isset($_GET['cod_ext_grupo'])) {

            $cnpj = $_GET['cnpj'];
            $token = $_GET['token'];
            $codigo_externo_grupo = $_GET['cod_ext_grupo'];

            if ($this->valida_autorizacao($token, $cnpj)) {

                $this->loadModel('Risco');
                $this->loadModel('RiscoExterno');
                $this->loadModel('GrupoRisco');
                $this->loadModel('GrupoRiscoExterno');
                $this->loadModel('Cliente');
                $cliente = $this->Cliente->findByCodigoDocumento($cnpj);
                //$grupo_externo = $this->GrupoRiscoExterno->findByCodigoExterno($codigo_externo_grupo);

                //fazer a consulta ao banco fazendo o join com o codigo externo
                $field = array( 'Risco.codigo as codigo',
                    'Risco.nome_agente as descricao',
                    're.codigo_externo as codigo_externo'
                );

                $join = array(
                    array(
                        'table' => 'dbo.riscos_externo',
                        'alias' => 're',
                        'type' => 'left',   //inner para retornar somente quem tem codigo externo
                        'conditions' => 're.codigo_riscos = Risco.codigo',
                    ),
                    array(
                        'table' => 'dbo.grupos_riscos',
                        'alias' => 'grupos_riscos',
                        'type' => 'inner',
                        'conditions' => 'grupos_riscos.codigo = Risco.codigo_grupo',
                    ),
                    array(
                        'table' => 'dbo.grupos_riscos_externo',
                        'alias' => 'grupos_riscos_externo',
                        'type' => 'inner',
                        'conditions' => 'grupos_riscos.codigo = grupos_riscos_externo.codigo_grupos_riscos'
                    )
                );

                $conditions = array();
                $conditions['grupos_riscos_externo.codigo_externo'] = $codigo_externo_grupo;
                $conditions['re.codigo_cliente'] = $cliente['Cliente']['codigo'];

                $risco = $this->Risco->find('all', array('fields' => $field, 'conditions' => $conditions, 'joins' => $join));
                $this->dados['risco'] = '';

                if(!empty($risco)) {

                    foreach ($risco as $key => $value) {
                       $this->dados['risco'][$key] = $value;
                    }
                    //status de sucesso
                    $this->dados['status'] = '200';
                    $this->dados['msg'] = 'SUCESSFULL OPERATION';
                } else{
                    $this->dados['status'] = '5';
                    $this->dados['msg']     = 'Sem resultados!';
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
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_RISCO_CONSULTA_RISCO");
        
         if ($type == 'xml') {
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