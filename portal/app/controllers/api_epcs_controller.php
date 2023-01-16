<?php
class ApiEpcsController extends AppController
{
    
    public $name = '';
    
    public $ApiAutorizacao;
    
    var $uses = array();
    var $components = array('RequestHandler');
    
    public $dados = array();
    public $campos_obrigatorios = array();
    
    public function beforeFilter()
    {
        parent::beforeFilter();
        //$this->BAuth->allow(array('*'));
        $this->BAuth->allow('consulta_epc', 'valida_autorizacao');
        //$this->RequestHandler->setContent("json", "application/json");
        App::import('Component', 'ApiAutorizacao');
        Configure::write('debug', 0);
        $this->ApiAutorizacao = new ApiAutorizacaoComponent();
    }
    
    /**
     * Metodo para validar a autorizacao pelo token cpnj
     */
    private function valida_autorizacao($token = null, $cnpj = null)
    {
        //verifica se tem os get passados
        if (!empty($token) && !empty($cnpj)) {
            
            //componente para validar o token e cnpj            
            // $ApiAutorizacao = new ApiAutorizacaoComponent();
            
            //verifica se pode prosseguir com o processo
            if ($this->ApiAutorizacao->autoriza($token, $cnpj)) {
                //foi validado
                return true;
            } else {
                /**
                 * Erro 3 é quando o token e o cnpj passadno não tem relacao ou está errado
                 */
                $this->dados['status'] = '3';
                $this->dados['msg']    = 'Token ou CNPJ invalido';
                
                return false;
                
            } //fim verificacao dos dados
        } else {
            /**
             * Get cnpj ou tokem em branco
             */
            $this->dados['status'] = "2";
            $this->dados['msg']    = 'Token ou Cnpj vazio';
            
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
    
    public function consulta_epc()
    {
        
        $this->autoRender = false;
        if (isset($_GET['token']) && isset($_GET['cnpj'])) {
            
            //valida o usuario + cnpj
            $cnpj  = $_GET['cnpj'];
            $token = $_GET['token'];
            
            //verifica se esta validado a autorizacao
            if ($this->valida_autorizacao($token, $cnpj)) {
                
                $this->loadModel('Epc');
                $this->loadModel('EpcExterno');
                $this->loadModel('Cliente');
                
                //Código do cliente
                $codCliente = $this->Cliente->find('first', array(
                    'fields' => array('Cliente.codigo'),
                    'conditions' => array('Cliente.codigo_documento' => $cnpj)
                ));
                
                
                $fields = array(
                    'Epc.codigo',
                    'EpcExterno.codigo_externo',
                    'Epc.nome as descricao'
                );
                
                $this->Epc->bindModel(array(
                    'hasOne' => array(
                        'EpcExterno' => array(
                            'foreignKey' => 'codigo_epc',
                            'conditions' => array(
                                'EpcExterno.codigo_cliente' => $codCliente['Cliente']['codigo']
                            )
                        )
                    )
                ), false);
                
                $join = array(
                    'table' => 'RHHealth.dbo.epc_externo',
                    'alias' => 'epc_externo',
                    'type' => 'LEFT',
                    'conditions' => 'epc_externo.codigo_epc = Epc.codigo',
                );
                
                $epc = $this->Epc->find('all', array(
                    'join' => $join,
                    'fields' => $fields,
                    'order' => 'Epc.codigo'
                ));
                                
                $this->dados['epc'] = '';

                if (!empty($epc)) {
                    //Percorre o array
                    foreach ($epc as $chave => $valor):
                        $item = new stdClass();
                        $item->codigo = $valor['Epc']['codigo'];
                        $item->codigo_externo = $valor['EpcExterno']['codigo_externo'];
                        $item->descricao = $valor[0]['descricao'];
                        $this->dados['epc'][$chave] = $item;
                    endforeach;
                    
                    $this->dados['status'] = '0';
                    $this->dados['msg']    = 'SUCESSO';
                    
                } else {
                    $this->dados['status'] = "4";
                    $this->dados['msg']    = 'Nao identificado, nao trouxe nenhum resultado!';
                }
                
                //Fim do valida a autorização
            }
            
            //Fim do isset TOKEN/CNPJ
        } else {
            $this->dados['status'] = '1';
            $this->dados['msg']    = 'CNPJ ou Token nao foram passados';
        }
        
        //EM JSON
        $retorno = json_encode($this->dados);

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