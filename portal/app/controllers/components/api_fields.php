<?php
/**
 * Realiza verificações básicas nos campos, à nível de Controller
 */
class ApiFieldsComponent {
	
    var $name = 'ApiFields';

    /**
     * @var array $campos_obrigatorios
     */
    public $campos_obrigatorios = array();
	

	function initialize(&$controller, $settings = array()) {
		// saving the controller reference for later use        
		$this->controller =& $controller;        
	}

    public function setCamposObrigatorios($msg) {
        $this->campos_obrigatorios[] = $msg;
    }
   
   
    /**
     * Verificação para variável não vazia
     * @param string|int|array
     * @return boolean
     */ 
    public function estaPreenchido($campo) {
        if (isset($campo)) {
            if (empty($campo) === false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se campo está setado e vazio, 
     * em caso positivo carrega a mensagem na propridade campos_obrigatorios
     * @param string $campo
     * @param string $mensagem
     * @return void
     */
    public function verificaPreenchimentoObrigatorio($campo, $msg) {
        if (!isset($campo)) {
            $this->setCamposObrigatorios($msg);
        } else {
            $campo = trim($campo);
            if (empty($campo)) {
                $this->setCamposObrigatorios($msg);
            }
        }
    }

    /**
     * Verifica se o array passado está setado e vazio, 
     * em caso positivo carrega a mensagem na propridade campos_obrigatorios
     * @param string $campo
     * @param string $mensagem
     * @return void
     */
    public function verificaPreenchimentoObrigatorioArray($campo, $msg) {
        
        if(count($campo) < 1) {            
            $this->setCamposObrigatorios($msg);
        }        
    }

    /**
     * Verifica o preenchimento de algum dos campos, o comum ou seu correspondente externo,
     * e não permitindo o preenchimento de ambos 
     * este método é utilizado nas validações preliminares da API
     * @param string $campo
     * @param string $campoExterno
     * @return void
     */
    public function verificaCodigoExterno($campo, $campoExterno, $msg) {
        if (!isset($campo) && !isset($campoExterno)) {
            $this->setCamposObrigatorios($msg);
        } else {
            if (empty($campo) && empty($campoExterno)) {
                $this->setCamposObrigatorios($msg);
            }
        }
    }

    /**
     * Verifica os dois campos estao sendo passado caso esteja deve ser retirado um
     * 
     * @param string $campo
     * @param string $campoExterno
     * @return void
     */
    public function verificaCodigoExternoDuplicado($campo, $campoExterno, $msg) {
        if (isset($campo) && isset($campoExterno)) {
            $this->setCamposObrigatorios($msg);
        }
    }

    /**
     * Verifica o preenchimento de um array de códigos obrigatorios. 
     * este método é utilizado nas validações preliminares da API
     * @param array $data
     * @param string $campoExterno
     * @return void
     */
    public function verificaCodigoObrigatorio($data, $msg) {
        $total = count($data);
        $invalido = 0;

        foreach($data AS $dt) {
            if (!isset($dt) || empty($dt)) {
                $invalido++;
            }
        }

        if ($total === $invalido) {
            $this->setCamposObrigatorios($msg);
        }
    }

    /**
     * Verifica o qual o codigo a ser usado, 
     * entre codigo e codigo externo
     * @param array $data
     * @return array
     */
    public function filtraCodigoExterno($data) {
        foreach($data AS $v) {
            foreach ($v AS $k => $vl) {
                if ($vl !== null) {
                    return array($k => $vl);
                }
            }
        }
        return array();
    }


    /**
     * Verifica se o CEP possui 8 dígitos
     * @param $data
     * @return void
     */
    public function verificaCEP($data, $msg) {
        if(!Comum::validaCEP($data)) {
            $this->setCamposObrigatorios($msg);
        } 
    }


    /**
     * Verifica se o UF é válido
     * @param $data
     * @return void
     */
    public function verificaUF($data, $msg) {
        if(!Comum::validaUF($data)){
            $this->setCamposObrigatorios($msg);
        }
    }

    /**
     * [verifcaInteiro description]
     * 
     * metodo para verificar se o campo tem o valor inteiro
     * 
     * @return [type] [description]
     */
    public function verificaInteiro($data, $msg)
    {   
        //verifica se esta setado com null para nao retornar o erro de inteiro
        if(!is_null($data)) {
            //validacao para não estourar o erro do cake php
            if(!(int)$data){
                $this->setCamposObrigatorios($msg);
            }//fim validacao int
        }

    }//fim verifica inteiro

    public function verificaCampoPreenchido($data, $msg){
        if(is_null($data)){
            $this->setCamposObrigatorios($msg);
            return false;
        }
        if(trim($data) == false){
            $this->setCamposObrigatorios($msg);
        }
    return true;
    }

    public function verificaArrayOuObject($data, $msg){
        if(is_null($data)){
            $this->setCamposObrigatorios($msg);
            return false;
        }
        if(!is_array($data) && !is_object($data)){
            $this->setCamposObrigatorios($msg);
            return false;
        }
    return true;
    }

    /**
     * [verificaDataDB description]
     * 
     * metodo para verificar se o campo data esta com o valor yyyy-mm-dd
     * 
     * @return [type] [description]
     */
    public function verificaDataDB($data, $msg)
    {   
        //verifica se esta setado com null para nao retornar o erro de inteiro
        if (!preg_match('/^(\d{4})[-](\d{2})[-](\d{2})$/', $data)) {
            $this->setCamposObrigatorios($msg);
        }

    }//fim verifica inteiro

    /**
     * [verifica_inclui_cargo description]
     * 
     * metodo para incluir cargo
     * 
     * @param  [type] $codigo_externo [description]
     * @param  [type] $matriz         [description]
     * @return [type]                 [description]
     */
    public function verifica_inclui_cargo($codigo_externo,$matriz){    
        
        //valida para cadastrar
        if(strlen($codigo_externo) > 60) {
            throw new Exception("Erro ao cadastrar cargo descrição maior que 60 caracteres.");            
        }

        //registra as classes
        $this->GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
        $this->Cargo = ClassRegistry::init('Cargo');
        $this->CargoExterno = ClassRegistry::init('CargoExterno');

        $verif_uni_bloqueado = $this->GrupoEconomicoCliente->find('first',array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $matriz),'fields' => array('bloqueado')));

        if( !$verif_uni_bloqueado['GrupoEconomicoCliente']['bloqueado'] ){

            $verif_cargo = $this->Cargo->find('first',array('conditions' => array('Cargo.descricao like' =>$codigo_externo,'Cargo.codigo_cliente' => $matriz)));
            $codigo_cargo = NULL;

            if(empty($verif_cargo)){

                $dados_cargo = array(
                    'Cargo' => array(
                        'descricao' => $codigo_externo,
                        'data_inclusao' => date('Ymd H:i:s'),
                        'codigo_usuario_inclusao' => 1,
                        'ativo' => 1,
                        'codigo_cliente' => $matriz,
                        'codigo_empresa' => 1,
                    )
                );

                if($this->Cargo->incluir($dados_cargo)){
                    $codigo_cargo = $this->Cargo->id;
                } else {
                    throw new Exception("Cargo não cadastrado.");
                }

            } 
            else {
                $codigo_cargo = $verif_cargo['Cargo']['codigo'];
            }

            
            $dados_cargo_externo = array(
                'CargoExterno' => array(
                    'codigo_cargo' => $codigo_cargo,
                    'codigo_cliente' => $matriz,
                    'codigo_externo' => $codigo_externo
                )
            );

            if(!$this->CargoExterno->incluir($dados_cargo_externo)){
                throw new Exception("CargoExterno não cadastrado.");
            }

            return $codigo_cargo;

        } else {
            throw new Exception("Cliente bloqueado para inclusão/alterações: codigo_externo_cargo não encontrado.");
        }

    } //fim verifica_inclui_cargo

    public function verifica_inclui_setor($codigo_externo,$matriz){

        //valida para cadastrar
        if(strlen($codigo_externo) > 60) {
            throw new Exception("Erro ao cadastrar setor descrição maior que 60 caracteres.");            
        }
        
        //registra as classes
        $this->GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
        $this->Setor = ClassRegistry::init('Setor');
        $this->SetorExterno = ClassRegistry::init('SetorExterno');

        $verif_uni_bloqueado = $this->GrupoEconomicoCliente->find('first',array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $matriz),'fields' => array('bloqueado')));
        if( !$verif_uni_bloqueado['GrupoEconomicoCliente']['bloqueado'] ){

            $verif_setor = $this->Setor->find('first',array('conditions' => array('Setor.descricao like' =>$codigo_externo,'Setor.codigo_cliente' => $matriz)));
            $codigo_setor = NULL;

            if(empty($verif_setor)){
                $dados_setor = array(
                    'Setor' => array(
                        'descricao' => $codigo_externo,
                        'data_inclusao' => date('Ymd H:i:s'),
                        'codigo_usuario_inclusao' => 1,
                        'ativo' => 1,
                        'codigo_cliente' => $matriz,
                        'codigo_empresa' => 1,
                    )
                );
                if($this->Setor->incluir($dados_setor)){
                    $codigo_setor = $this->Setor->id;
                } else {
                    throw new Exception("Setor não cadastrado.");
                }
            } 
            else {
                $codigo_setor = $verif_setor['Setor']['codigo'];
            }

            
            $dados_setor_externo = array(
                'SetorExterno' => array(
                    'codigo_setor' => $codigo_setor,
                    'codigo_cliente' => $matriz,
                    'codigo_externo' => $codigo_externo
                )
            );

            if(!$this->SetorExterno->incluir($dados_setor_externo)){
                throw new Exception("SetorExterno não cadastrado.");
            }
            

            return $codigo_setor;

        } else { 
            throw new Exception("Cliente bloqueado para inclusão/alterações: codigo_externo_setor não encontrado.");
        }                                         
    }

    /**
     * [verifcaInteiro description]
     * 
     * metodo para verificar se o campo tem o valor inteiro
     * 
     * @return [type] [description]
     */
    public function validaSoNumeros($data, $msg)
    {   
        //verifica se esta setado com null para nao retornar o erro de inteiro
        if(!is_null($data)) {
            //Verifica se contém somente números
            if (!preg_match('/^\d*$/', trim($data))) {
                $this->setCamposObrigatorios($msg);
            }//fim validacao int
        }

    }//fim validaSoNumeros

    
}