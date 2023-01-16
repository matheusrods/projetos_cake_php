<?php
class ApiGruposRiscosController extends AppController {
    
    public $name = '';
    
    public $ApiAutorizacao;

    var $uses = array();
    var $components = array('RequestHandler');

    public $dados = array();
    public $campos_obrigatorios = array();
    
    public function beforeFilter() {
        parent::beforeFilter();
        //$this->BAuth->allow(array('*'));
        $this->BAuth->allow('consulta_grupo_risco', 'valida_autorizacao');
        $this->RequestHandler->setContent("json", "application/json"); 
        App::import('Component', 'ApiAutorizacao');
        Configure::write('debug', 0); 
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
                 * Erro 3 é quando o token e o cnpj passadno não tem relacao ou está errado
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
            $this->dados['status'] = "2";
            $this->dados['msg'] = 'Token ou Cnpj vazio';

            return false;

        } //fim verificacao do get    


        return false;

    } //fim valida_autorizacao
   

    /**
     * Metodo para retornar os dados da api do grupos_riscos
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: token e/ou cnpj inválido
     * 4 => Não identificado, não trouxe nenhum resultado!
     * 
     * indice funcionario => retorna os dados do funcionario
     * 
     */

    public function consulta_grupo_risco() {
      $this->autoRender = false;

      if (isset($_GET['token']) && isset($_GET['cnpj'])) {
        //valida o usuario + cnpj
        $cnpj   = $_GET['cnpj'];
        $token  = $_GET['token'];

        // Verifica se o tipo de retorno será xml ou json. Default JSON.
        $type   = isset($_GET['type']) && $_GET['type'] == 'xml' ? 'xml' : 'json';

        //verifica se esta validado a autorizacao
        if($this->valida_autorizacao($token, $cnpj)) {
          //variaveis auxiliares
          $descricao = "";
          if(isset($_GET["descricao"])) {
              $descricao = $_GET["descricao"];
          }

          $this->loadModel('GrupoRisco');
          $this->loadModel('GrupoRiscoExterno');
          $this->loadModel('Cliente');

          $codCliente = $this->Cliente->find('first', array('fields' => array('Cliente.codigo'), 'conditions' => array('Cliente.codigo_documento' => $cnpj)));

          //campos
          $field = array('GrupoRisco.codigo AS codigo', 
                        'GrupoRisco.descricao AS descricao', 
                        'GrupoRiscoExterno.codigo_externo AS codigo_externo');

          $join = array(
            array(
                'table' => "{$this->GrupoRiscoExterno->databaseTable}.{$this->GrupoRiscoExterno->tableSchema}.{$this->GrupoRiscoExterno->useTable}",
                'alias' => 'GrupoRiscoExterno',
                'conditions' => 'GrupoRiscoExterno.codigo_grupos_riscos = GrupoRisco.codigo AND GrupoRiscoExterno.codigo_cliente = '.$codCliente['Cliente']['codigo'],
                'type' => 'LEFT'
            )
          );

          $conditions = array();
          if(!empty($descricao)) {
              $conditions['GrupoRisco.descricao LIKE'] = '%' . $descricao . '%';
          }

          //pega os dados do cargo
          $grupo = $this->GrupoRisco->find('all', array('fields' => $field, 'joins' => $join, 'conditions' => $conditions));

          $this->dados['grupo_risco'] = '';

          //verifica se existe valores
          if(!empty($grupo)) {

              //variavel auxiliar
              $dados = array();

              //varre para montar corretamente o array/json que irá devolver
              foreach($grupo as $key => $val) {
                  $dados[$key]['codigo']         = substr($val[0]['codigo'],0,10);
                  $dados[$key]['codigo_externo'] = $val[0]['codigo_externo'] !== null ? $val[0]['codigo_externo'] : '';;
                  $dados[$key]['descricao']      = substr($val[0]['descricao'],0,60);
              }
              
              //status de sucesso
              $this->dados["status"] = '0';
              $this->dados["msg"] = 'SUCESSO';
              
              //seta na variavel que vai ser disponibilizada os cargos
              $this->dados['grupo_risco'] = $dados;

          }//fim if

        //Fim do valida a autorização
        }
      //Fim do isset TOKEN/CNPJ
      } else {
        $this->dados['status'] = '1';
        $this->dados['msg'] = 'CNPJ ou Token nao foram passados';
      }

      //EM JSON
      $retorno = json_encode($this->dados);

      // Retorna sucesso ou erro de acordo com o tipo de conteudo usado para consumir a API
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