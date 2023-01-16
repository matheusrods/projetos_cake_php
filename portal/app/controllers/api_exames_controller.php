<?php
class ApiExamesController extends AppController
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
        $this->BAuth->allow('consulta_exame', 'valida_autorizacao');
        //$this->RequestHandler->setContent("json", "application/json");
        App::import('Component', 'ApiAutorizacao');
        Configure::write('debug', 0);
        $this->ApiAutorizacao = new ApiAutorizacaoComponent();
    }
    
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
    
    public function consulta_exame()
    {
        
        $this->autoRender = false;
        if (isset($_GET['token']) && isset($_GET['cnpj'])) {
            
            //valida o usuario + cnpj
            $cnpj  = $_GET['cnpj'];
            $token = $_GET['token'];
            
            //verifica se esta validado a autorizacao
            if ($this->ApiAutorizacao->validaAutorizacao($token, $cnpj)) {
                
                $this->loadModel('Exame');
                $this->loadModel('ExameExterno');
                $this->loadModel('Cliente');
                
                //Código do cliente
                $codCliente = $this->Cliente->find('first', array(
                    'fields' => array(
                        'Cliente.codigo'
                    ),
                    'conditions' => array(
                        'Cliente.codigo_documento' => $cnpj
                    )
                ));
                
                $fields = array(
                    'Exame.codigo',
                    'ExameExterno.codigo_externo',
                    'Exame.descricao'
                );
                
                $this->Exame->bindModel(array(
                    'hasOne' => array(
                        'ExameExterno' => array(
                            'foreignKey' => 'codigo_exame',
                            'conditions' => array(
                                'ExameExterno.codigo_cliente' => $codCliente['Cliente']['codigo']
                            )
                        )
                    )
                ), false);
                
                $join = array(
                    'table' => 'RHHealth.dbo.exames_externo',
                    'alias' => 'exames_externos',
                    'type' => 'LEFT',
                    'conditions' => 'exames_externos.codigo_exame = Exame.codigo'
                );
                                
                $exames = $this->Exame->find('all', array(
                    'join' => $join,
                    'fields' => $fields,
                    'order' => 'Exame.codigo'
                ));
                                
                $this->dados['exame'] = '';

                if (!empty($exames)) {
                    //Percorre o array
                    foreach ($exames as $d => $exame) {
                        $objExame = new StdClass();
                        $objExame->codigo           = $exame['Exame']['codigo'];
                        $objExame->codigo_externo   = $exame['ExameExterno']['codigo_externo'];
                        $objExame->descricao        = $exame['Exame']['descricao'];
                        $this->dados['exame'][$d]   = $objExame;
                    }

                    $this->dados['status'] = '0';
                    $this->dados['msg']    = 'SUCESSO';
                    
                } else {
                    $this->dados['status'] = "4";
                    $this->dados['msg']    = 'Nao identificado, nao trouxe nenhum resultado!';
                }
                
                //Fim do valida a autorização
            } else { //Fim do valida a autorização            
                $this->dados = $this->ApiAutorizacao->getStatus();
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