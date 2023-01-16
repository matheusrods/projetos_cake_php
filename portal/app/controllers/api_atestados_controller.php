<?php
class ApiAtestadosController extends AppController {
    
    public $name = '';

    var $uses = array();

    public $dados = array();
    
    // public function beforeFilter() {
    //     parent::beforeFilter();
    //     $this->BAuth->allow(array('*'));

    //     App::import('Component', 'ApiAutorizacao');
    //     $this->ApiAutorizacao = new ApiAutorizacaoComponent();
    // }


    private $ApiAutorizacao;

    /**
     * @var ApiDataFormat $ApiDataFormat
     */
    private $ApiDataFormat;

    /**
     * @var ApiFields $ApiFields
     */
    private $fields;

    public $campos_obrigatorios = array();
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));

        App::import('Component', 'ApiAutorizacao');
        $this->ApiAutorizacao = new ApiAutorizacaoComponent();

        App::import('Component', 'ApiDataFormat');
        $this->ApiDataFormat = new ApiDataFormatComponent();

        App::import('Component', 'ApiFields');
        $this->fields = new ApiFieldsComponent();

        $this->ApiDataFormat->setData(file_get_contents('php://input'));
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
            $this->dados["status"] = "2";

            return false;

        } //fim verificacao do get    


        return false;

    } //fim valida_autorizacao


    /**
     * Metodo para retornar os dados para a API codigo/motivo
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * indice motivos_licenca => retorna os dados do motivo licenca
     * 
     */ 
    public function motivo_licenca()
    {

        //verifica se existe os gets obrigatorios
        if(isset($_GET['token']) && isset($_GET['cnpj'])) {
        
            //valida o usuario + cnpj
            $cnpj   = $_GET['cnpj'];
            $token  = $_GET['token'];

            //verifica se esta validado a autorizacao
            if($this->valida_autorizacao($token, $cnpj)) {

                //instancia a motivo_afastamento
                $this->loadModel('MotivoAfastamento');

                //pega os motivos da licencao que esta na tabela motivos_afastamentos
                $fields = array('MotivoAfastamento.codigo AS codigo', 'SUBSTRING(MotivoAfastamento.descricao,0,60) AS Motivo');
                $motivoLicenca = $this->MotivoAfastamento->find('all', array('recursive' => -1, 'fields' => $fields));

                //seta como valor nulo o motivo
                $this->dados['motivo_licenca'] = '';

                //verifica se existe valores
                if(!empty($motivoLicenca)) {
                    //variavel auxiliar
                    $dados = array();
                    //varre para montar corretamente o array/json que irá devolver
                    foreach($motivoLicenca as $key => $ml) {
                        $dados[$key] = $ml[0];
                    }//fim foreach

                    //pega os motivos licenças
                    $this->dados['motivo_licenca'] = $dados;

                } //fim verificacao motivo_licenca

                //status de sucesso
                $this->dados["status"] = '0';
                $this->dados["msg"] = 'SUCESSO';

            } //fim valida_autorizacao

        } 
        else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */ 
            $this->dados["status"] = "1";

        } //fim verificacao gets

        //joga na log_integracao/ codigos das ocorrencias
        $entrada   = implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);       
        
        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta
        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        //componente para log da api        
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_ATESTADOS_MOTIVO_LICENCA");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_ATESTADOS_MOTIVO_LICENCA',
                'conteudo' => $entrada,
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim motivo_licenca


    /**
     * Metodo para retornar os dados para a API codigo/motivo da licenca do esocial
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * indice motivos_licenca_esocial => retorna os dados codigo e motivo licenca do esocial
     * 
     */ 
    public function motivo_licenca_esocial()
    {

        //verifica se existe os gets obrigatorios
        if(isset($_GET['token']) && isset($_GET['cnpj'])) {
        
            //valida o usuario + cnpj
            $cnpj   = $_GET['cnpj'];
            $token  = $_GET['token'];

            //verifica se esta validado a autorizacao
            if($this->valida_autorizacao($token, $cnpj)) {

                //instancia a motivo_afastamento
                $this->loadModel('Esocial');

                //pega os motivos da licencao que esta na tabela motivos_afastamentos
                $motivoLicencaEsocial = $this->Esocial->carrega_motivo_afastamento_esocial();

                //seta como valor nulo o motivo
                $this->dados['motivo_licenca_esocial'] = '';

                //verifica se existe valores
                if(!empty($motivoLicencaEsocial)) {

                    //variavel auxiliar
                    $dados = array();                    

                    //varre para montar corretamente o array/json que irá devolver
                    foreach($motivoLicencaEsocial as $key => $ml) {
                        $dados[$key]['codigo'] = $ml['Esocial']['codigo'];
                        $dados[$key]['motivo'] = substr($ml['0']['descricao'],0,60);
                    }//fim foreach

                    //pega os motivos licenças do esocial
                    $this->dados['motivo_licenca_esocial'] = $dados;

                } //fim verificacao motivo_licenca

                //status de sucesso
                $this->dados["status"] = '0';
                $this->dados["msg"] = 'SUCESSO';

            } //fim valida_autorizacao

        } 
        else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */ 
            $this->dados["status"] = "1";

        } //fim verificacao gets

        //joga na log_integracao/ codigos das ocorrencias
        $entrada   = implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);       
        
        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta
        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        //componente para log da api        
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_ATESTADOS_MOTIVO_LICENCA_ESOCIAL");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_ATESTADOS_MOTIVO_LICENCA_ESOCIAL',
                'conteudo' => $entrada,
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim motivo_licenca_esocial

    /**
     * Metodo para retornar os dados para a API codigo, nome do profissional, conselho, numero conselho, uf conselho
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * 4 => Campo obrigatorio, numero_conselho e|ou nome
     * 5 => combinacao numero_conselho + nome não encontrado
     * 
     * indice profissional => retorna os dados do profissional codigo, nome do profissional, conselho, numero conselho, uf conselho
     * 
     */ 
    public function profissional()
    {

        //verifica se existe os gets obrigatorios
        if(isset($_GET['token']) && isset($_GET['cnpj'])) {
        
            //valida o usuario + cnpj
            $cnpj   = $_GET['cnpj'];
            $token  = $_GET['token'];

            //verifica se esta validado a autorizacao
            if($this->valida_autorizacao($token, $cnpj)) {

                //variaveis auxiliares
                $nome               = null;
                $numero_conselho    = null;
                $uf_conselho        = null;

                //verifica se tem o numero do conselho
                if(!empty($_GET["numero_conselho"])) {                    
                    $numero_conselho = $_GET["numero_conselho"];
                }

                //verifica se tem o nome do profissional
                if(!empty($_GET["nome"])) {
                    $nome = $_GET["nome"];
                }

                //verifica se tem a uf do profissional
                if(!empty($_GET['uf_conselho'])){
                    $uf_conselho = $_GET['uf_conselho'];
                }

                //verifica se o nome e numero do conselho nao estao como nulos
                $error = array();
                if(is_null($nome) && is_null($numero_conselho)) {


                    $error[] = "nome ou numero_conselho";
                }

                if(is_null($uf_conselho)) {
                    $error[] = "uf_conselho";
                }


                if(!empty($error)) {

                    $error = implode(",", $error);

                    //msg de erro
                    $this->dados["status"] = "4";
                    $this->dados['msg']     = 'Campo obrigatorio: '.$error;

                }
                else {

                    //instancia a profissional
                    $this->loadModel('Medico');

                    //pega o medico "profissional"
                    //campos
                    $field = array('Medico.codigo AS codigo', 
                                    'Medico.nome AS nome_profissional',
                                    'ConselhoProfissional.descricao AS conselho',
                                    'Medico.numero_conselho as numero_conselho',
                                    'Medico.conselho_uf AS uf_conselho');

                    //monta as condições
                    $conditions = array();
                    if(!empty($numero_conselho)) {
                        $conditions['Medico.numero_conselho'] = $numero_conselho;
                    }

                    if(!empty($nome)) {
                        $conditions['Medico.nome LIKE'] = '%' . trim($nome) . '%';
                    }

                    $conditions['Medico.conselho_uf'] = $uf_conselho;

                    //pega os dados do profissional
                    $medicos = $this->Medico->find('first', array('fields' => $field, 'conditions' => $conditions));

                    //seta como valor nulo o motivo
                    $this->dados['profissional'] = '';

                    //verifica se existe valores
                    if(!empty($medicos)) {

                        //variavel auxiliar                        
                        //varre para montar corretamente o array/json que irá devolver
                        $dados['codigo']                    = substr($medicos[0]['codigo'],0,10); 
                        $dados['nome_profissional']         = substr($medicos[0]['nome_profissional'],0,50); 
                        $dados['conselho']                  = substr($medicos[0]['conselho'],0,10); 
                        $dados['numero_conselho']           = substr($medicos[0]['numero_conselho'],0,15); 
                        $dados['uf_conselho']               = substr($medicos[0]['uf_conselho'],0,2); 

                        
                        //status de sucesso
                        $this->dados["status"] = '0';
                        $this->dados["msg"] = 'SUCESSO';

                        //pega o profissional
                        $this->dados['profissional'] = $dados;

                    }
                    else {
                        //codigo para indicar que os dados passados nao trouxe retorno
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = 'Numero do conselho e Nome, nao trouxe nenhum resultado!';

                    } //fim medico

                } //fim is null nome e numero conselho

            } //fim valida_autorizacao

        } 
        else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */ 
            $this->dados["status"] = "1";

        } //fim verificacao gets

        //joga na log_integracao/ codigos das ocorrencias
        $entrada   = implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);       
        
        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta
        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        //componente para log da api        
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_ATESTADOS_PROFISSIONAL");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_ATESTADOS_PROFISSIONAL',
                'conteudo' => $entrada,
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim profissional

    /**
     * Metodo para retornar os dados para a API codigo, descricao do cid
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * 4 => Campo obrigatorio, codigo e|ou descricao
     * 5 => combinacao codigo cid10 + descricao não encontrado
     * 
     * indice profissional => retorna os dados do profissional codigo, descricao
     * 
     */ 
    public function cid10()
    {

        //verifica se existe os gets obrigatorios
        if(isset($_GET['token']) && isset($_GET['cnpj'])) {
        
            //valida o usuario + cnpj
            $cnpj   = $_GET['cnpj'];
            $token  = $_GET['token'];

            //verifica se esta validado a autorizacao
            if($this->valida_autorizacao($token, $cnpj)) {

                //variaveis auxiliares
                $codigo_cid         = null;
                $descricao          = null;

                //verifica se tem o codigo cid
                if(isset($_GET["codigo_cid"])) {
                    $codigo_cid = $_GET["codigo_cid"];
                }

                //verifica se tem a descricao
                if(isset($_GET["descricao"])) {
                    $descricao = $_GET["descricao"];
                }

                //verifica se o codigo cid e a descricao nao estao como nulos
                if(is_null($codigo_cid) && is_null($descricao)) {
                    //msg de erro
                    $this->dados["status"] = "4";
                    $this->dados['msg']     = 'Campo obrigatorio, codigo_cid e|ou descricao';
                    
                }
                else {

                    //instancia a profissional
                    $this->loadModel('Cid');

                    //campos
                    $field = array('Cid.codigo AS codigo', 
                                    'Cid.descricao AS descricao',
                                    'Cid.codigo_cid10 as codigo_cid10');                  

                    //monta as condições
                    $conditions = array();
                    if(!empty($codigo_cid)) {
                        $conditions['Cid.codigo_cid10 LIKE'] = '%' . $codigo_cid . '%';
                    }

                    if(!empty($descricao)) {
                        $conditions['Cid.descricao LIKE'] = '%' . $descricao . '%';
                    }

                    //pega os dados do profissional
                    $cid10 = $this->Cid->find('all', array('fields' => $field, 'conditions' => $conditions));

                    //seta como valor nulo o motivo
                    $this->dados['cid10'] = '';

                    //verifica se existe valores
                    if(!empty($cid10)) {

                        //variavel auxiliar
                        $dados = array();

                        //varre para montar corretamente o array/json que irá devolver
                        foreach($cid10 as $key => $val) {
                            $dados[$key]['codigo']      = substr($val[0]['codigo'],0,10);
                            $dados[$key]['descricao']   = substr($val[0]['descricao'],0,60);
                            // $dados[$key]['cid10']       = substr($val[0]['codigo_cid10'],0,20);
                        }
                        
                        //status de sucesso
                        $this->dados["status"] = '0';
                        $this->dados["msg"] = 'SUCESSO';
                        
                        //pega o profissional
                        $this->dados['cid10'] = $dados;

                    }
                    else {
                        //codigo para indicar que os dados passados nao trouxe retorno
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = 'Nenhum Cid encontrado!';

                    } //fim medico

                } //fim is null nome e numero conselho

            } //fim valida_autorizacao

        } 
        else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */ 
            $this->dados["status"] = "1";

        } //fim verificacao gets

        //joga na log_integracao/ codigos das ocorrencias
        $entrada   = implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);       
        
        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta
        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        //componente para log da api        
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_ATESTADOS_CID10");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_ATESTADOS_CID10',
                'conteudo' => $entrada,
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim cid10


     /**
     * Metodo para retornar os dados para a API do tipo de local dos atendimentos
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * indice motivos_licenca => retorna os dados do tipo local atendimento codigo, descricao
     * 
     */ 
    public function tipo_local_atendimento()
    {

        //verifica se existe os gets obrigatorios
        if(isset($_GET['token']) && isset($_GET['cnpj'])) {
        
            //valida o usuario + cnpj
            $cnpj   = $_GET['cnpj'];
            $token  = $_GET['token'];

            //verifica se esta validado a autorizacao
            if($this->valida_autorizacao($token, $cnpj)) {

                //instancia a tipo local atendimento
                $this->loadModel('TipoLocalAtendimento');

                //pega os locais de atendimentos
                $fields = array('TipoLocalAtendimento.codigo AS codigo', 'TipoLocalAtendimento.descricao AS descricao');
                $tipos = $this->TipoLocalAtendimento->find('all', array('recursive' => -1, 'fields' => $fields));

                //seta como valor nulo os locais
                $this->dados['tipo_local_atendimento'] = '';

                //verifica se existe valores
                if(!empty($tipos)) {
                    //variavel auxiliar
                    $dados = array();
                    //varre para montar corretamente o array/json que irá devolver
                    foreach($tipos as $key => $tp) {
                        $dados[$key]['codigo'] = substr($tp[0]['codigo'],0,10);
                        $dados[$key]['descricao'] = substr($tp[0]['descricao'],0,60);
                    }//fim foreach

                    //pega os motivos licenças
                    $this->dados['tipo_local_atendimento'] = $dados;

                } //fim verificacao motivo_licenca

                //status de sucesso
                $this->dados["status"] = '0';
                $this->dados["msg"] = 'SUCESSO';

            } //fim valida_autorizacao

        } 
        else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */ 
            $this->dados["status"] = "1";

        } //fim verificacao gets

        //joga na log_integracao/ codigos das ocorrencias
        $entrada   = implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);       
        
        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta
        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        //componente para log da api        
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_ATESTADOS_TIPO_LOCAL_ATENDIMENTO");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_ATESTADOS_TIPO_LOCAL_ATENDIMENTO',
                'conteudo' => $entrada,
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim tipo_local_atendimento

    /**
     * Metodo para retornar os dados para a API codigo, descricao do setor
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * indice setor => retorna os dados do setor codigo, descricao
     * 
     */ 
    public function setor()
    {
        //verifica se existe os gets obrigatorios
        if(isset($_GET['token']) && isset($_GET['cnpj'])) {
        
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

                //instancia a profissional
                $this->loadModel('Setor');
                $this->loadModel('GrupoEconomico');
                $this->loadModel('GrupoEconomicoCliente');
                $this->loadModel('Cliente');
                $this->loadModel('SetorExterno');

                //campos
                $field = array('Setor.codigo AS codigo', 
                                'Setor.descricao AS descricao,
                                SetorExterno.codigo_externo AS codigo_externo');
                $join = array(
                        array(
                            'table' => "{$this->GrupoEconomico->databaseTable}.{$this->GrupoEconomico->tableSchema}.{$this->GrupoEconomico->useTable}",
                            'alias' => 'GrupoEconomico',
                            'conditions' => 'GrupoEconomico.codigo_cliente = Setor.codigo_cliente',
                            'type' => 'INNER',
                        ),
                        array(
                            'table' => "{$this->GrupoEconomicoCliente->databaseTable}.{$this->GrupoEconomicoCliente->tableSchema}.{$this->GrupoEconomicoCliente->useTable}",
                            'alias' => 'GrupoEconomicoCliente',
                            'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
                            'type' => 'INNER',
                        ),
                        array(
                            'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                            'alias' => 'Cliente',
                            'conditions' => 'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente',
                            'type' => 'INNER',
                        ),
                        array(
                            'table' => "{$this->SetorExterno->databaseTable}.{$this->SetorExterno->tableSchema}.{$this->SetorExterno->useTable}",
                            'alias' => 'SetorExterno',
                            'conditions' => 'SetorExterno.codigo_setor = Setor.codigo',
                            'type' => 'LEFT',
                        )
                    );

                //monta as condições
                $conditions['Cliente.codigo_documento'] = $cnpj;

                if(!empty($descricao)) {
                    $conditions['Setor.descricao LIKE'] = '%' . $descricao . '%';
                }

                //pega os dados do setor
                $setor = $this->Setor->find('all', array('fields' => $field, 'joins' => $join, 'conditions' => $conditions));

                //seta como valor nulo o setor
                $this->dados['setor'] = '';

                //verifica se existe valores
                if(!empty($setor)) {

                    //variavel auxiliar
                    $dados = array();

                    //varre para montar corretamente o array/json que irá devolver
                    foreach($setor as $key => $val) {
                        $dados[$key]['codigo']         = substr($val[0]['codigo'],0,10);
                        $dados[$key]['codigo_externo'] = $val[0]['codigo_externo'] !== null ? $val[0]['codigo_externo'] : '';
                        $dados[$key]['descricao']      = substr($val[0]['descricao'],0,60);
                    }
                    
                    //status de sucesso
                    $this->dados["status"] = '0';
                    $this->dados["msg"] = 'SUCESSO';
                    
                    //seta na variavel que vai ser disponibilizada os setores
                    $this->dados['setor'] = $dados;

                }//fim if


            } //fim valida_autorizacao

        } 
        else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */ 
            $this->dados["status"] = "1";

        } //fim verificacao gets


        //joga na log_integracao/ codigos das ocorrencias
        $entrada   = implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);       
        
        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta
        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        //componente para log da api        
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_ATESTADOS_SETOR");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_ATESTADOS_SETOR',
                'conteudo' => $entrada,
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

        // Retorna sucesso ou erro de acordo com o tipo de conteudo usado para consumir a API
        if ($type == 'xml') {
            // Retorna finalmente o XML
            App::import('Helper', 'Xml');
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version'=>'1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno), 
                array(
                    'root' => 'setor', 
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

    }//fim setor


    /**
     * Metodo para retornar os dados para a API codigo, descricao do cargo
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * indice cargo => retorna os dados do cargo codigo, descricao
     * 
     */ 
    public function cargo()
    {

        // Verifica se o tipo de retorno será xml ou json. Default JSON.
        $type   = isset($_GET['type']) && $_GET['type'] == 'xml' ? 'xml' : 'json';

        //verifica se existe os gets obrigatorios
        if(isset($_GET['token']) && isset($_GET['cnpj'])) {
        
            //valida o usuario + cnpj
            $cnpj   = $_GET['cnpj'];
            $token  = $_GET['token'];

            //verifica se esta validado a autorizacao
            if($this->valida_autorizacao($token, $cnpj)) {

                //variaveis auxiliares
                $descricao = "";
                if(isset($_GET["descricao"])) {
                    $descricao = $_GET["descricao"];
                }

                //instancia a cargos e as auxiliares
                $this->loadModel('Cargo');
                $this->loadModel('GrupoEconomico');
                $this->loadModel('GrupoEconomicoCliente');
                $this->loadModel('Cliente');
                $this->loadModel('CargoExterno');

                //campos
                $field = array('Cargo.codigo AS codigo', 'Cargo.descricao AS descricao', 'CargoExterno.codigo_externo AS codigo_externo');
                $join = array(
                        array(
                            'table' => "{$this->GrupoEconomico->databaseTable}.{$this->GrupoEconomico->tableSchema}.{$this->GrupoEconomico->useTable}",
                            'alias' => 'GrupoEconomico',
                            'conditions' => 'GrupoEconomico.codigo_cliente = Cargo.codigo_cliente',
                            'type' => 'INNER',
                        ),
                        array(
                            'table' => "{$this->GrupoEconomicoCliente->databaseTable}.{$this->GrupoEconomicoCliente->tableSchema}.{$this->GrupoEconomicoCliente->useTable}",
                            'alias' => 'GrupoEconomicoCliente',
                            'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
                            'type' => 'INNER',
                        ),
                        array(
                            'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                            'alias' => 'Cliente',
                            'conditions' => 'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente',
                            'type' => 'INNER',
                        ),
                        array(
                            'table' => "{$this->CargoExterno->databaseTable}.{$this->CargoExterno->tableSchema}.{$this->CargoExterno->useTable}",
                            'alias' => 'CargoExterno',
                            'conditions' => 'CargoExterno.codigo_cargo = Cargo.codigo',
                            'type' => 'LEFT',
                        )
                    );

                //monta as condições
                $conditions['Cliente.codigo_documento'] = $cnpj;

                if(!empty($descricao)) {
                    $conditions['Cargo.descricao LIKE'] = '%' . $descricao . '%';
                }

                //pega os dados do cargo
                $cargo = $this->Cargo->find('all', array('fields' => $field, 'joins' => $join, 'conditions' => $conditions));

                //seta como valor nulo o cargo
                $this->dados['cargo'] = '';

                //verifica se existe valores
                if(!empty($cargo)) {

                    //variavel auxiliar
                    $dados = array();

                    //varre para montar corretamente o array/json que irá devolver
                    foreach($cargo as $key => $val) {
                        $dados[$key]['codigo']         = substr($val[0]['codigo'],0,10);
                        $dados[$key]['codigo_externo'] = $val[0]['codigo_externo'] !== null ? $val[0]['codigo_externo'] : '';;
                        $dados[$key]['descricao']      = substr($val[0]['descricao'],0,60);
                    }
                    
                    //status de sucesso
                    $this->dados["status"] = '0';
                    $this->dados["msg"] = 'SUCESSO';
                    
                    //seta na variavel que vai ser disponibilizada os cargos
                    $this->dados['cargo'] = $dados;

                }//fim if


            } //fim valida_autorizacao

        } 
        else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */ 
            $this->dados["status"] = "1";

        } //fim verificacao gets

        //joga na log_integracao/ codigos das ocorrencias
        $entrada   = implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);       
        
        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta
        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        //componente para log da api        
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_ATESTADOS_CARGO");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_ATESTADOS_CARGO',
                'conteudo' => $entrada,
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

        // Retorna sucesso ou erro de acordo com o tipo de conteudo usado para consumir a API
        if ($type == 'xml') {
            // Retorna finalmente o XML
            App::import('Helper', 'Xml');
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version'=>'1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno), 
                array(
                    'root' => 'cargo', 
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

    }//fim cargo

    /**
     * 
     * Metodo para incluir um novo atestado via api
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * 
     */ 
    public function incluir_atestado()
    {

        $this->autoRender = false;
        $dadosRecebidos = '';
        $this->ApiDataFormat->setContentType();
        // Pega os campos via json ou Form url-encoded
        $dadosRecebidos = $this->ApiDataFormat->getDataRequest();

        //verifica se existe os gets obrigatorios
        if(isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {

            //valida o usuario + cnpj
            $cnpj   = trim($dadosRecebidos->cnpj);
            $token  = trim($dadosRecebidos->token);

            //verifica se esta validado a autorizacao
            if($this->valida_autorizacao($token, $cnpj)) {

                // debug($dadosRecebidos);exit;
                // debug($this->ApiAutorizacao);exit;

                //pega os campos do post e valida os obrigatorios
                if($this->valida_campos_obrigatorios($dadosRecebidos)) {

                    //verifica a matriz através do cliente do token
                    $this->loadModel('GrupoEconomico');
                    $matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);

                    $erros = array();

                    //pesquisa para saber se tem o codigo externo ou o codigo setor ou cargo
                    // Setor 
                    if (isset($dadosRecebidos->codigo_setor) && !empty($dadosRecebidos->codigo_setor)) {

                        $setor = $this->Setor->find('first',
                                                array('conditions' => array(
                                                    'Setor.codigo' => $dadosRecebidos->codigo_setor, 
                                                    'Setor.codigo_cliente' => $matriz
                                                ), 
                                                'fields' => 'Setor.codigo'));
                        //caso nao exista o codigo do setor retorna o erro
                        if (!empty($setor)) {
                            $codigo_setor = $dadosRecebidos->codigo_setor;   
                        } else {
                            $erros[] = "codigo_setor não encontrado";
                        }

                    } else {
                        $this->loadModel('SetorExterno');
                        $result = $this->SetorExterno->find('first', array('conditions' => array('SetorExterno.codigo_externo' => $dadosRecebidos->codigo_externo_setor, 'SetorExterno.codigo_cliente' => $matriz), 'fields' => 'SetorExterno.codigo_setor'));

                        //verifica se existe o relacionamento do codigo externo com o codigo setor para gravar na aplicacao de exames
                        if(!empty($result['SetorExterno']['codigo_setor'])){
                            $codigo_setor = $result['SetorExterno']['codigo_setor'];
                        }
                        else {
                            //cadastra o cargo
                            $codigo_setor = $this->fields->verifica_inclui_setor($dadosRecebidos->codigo_externo_setor,$matriz);
                        }
                    }//fim setor

                    // Cargo
                    if (isset($dadosRecebidos->codigo_cargo) && !empty($dadosRecebidos->codigo_cargo)) {
                        $cargo = $this->Cargo->find('first',
                                                array('conditions' => array(
                                                    'Cargo.codigo' => $dadosRecebidos->codigo_cargo
                                                ), 
                                                'fields' => 'Cargo.codigo'));

                        if (!empty($cargo)) {
                            $codigo_cargo = $dadosRecebidos->codigo_cargo;
                        } else {                                
                            $erros[] = "codigo_cargo não encontrado.";
                        }

                    } else {
                        $this->loadModel('CargoExterno');
                        $result = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo_externo' => $dadosRecebidos->codigo_externo_cargo, 'CargoExterno.codigo_cliente' => $matriz), 'fields' => 'CargoExterno.codigo_cargo'));
                        
                        //verifica se existe o relacionamento do codigo externo com o codigo cargo para gravar na aplicacao de exames
                        if(!empty($result['CargoExterno']['codigo_cargo'])){
                            $codigo_cargo = $result['CargoExterno']['codigo_cargo'];
                        }
                        else {
                            //cadastra o cargo
                            $codigo_cargo = $this->fields->verifica_inclui_cargo($dadosRecebidos->codigo_externo_cargo,$matriz);
                        }
                    }//fim cargo


                    // Motivos Afastamento / Motivos Licença
                    if (isset($dadosRecebidos->codigo_motivo_licenca) && !empty($dadosRecebidos->codigo_motivo_licenca)) {
                        $this->loadModel('MotivoAfastamento');
                        $motivo_licenca = $this->MotivoAfastamento->find('first',
                                                array('conditions' => array(
                                                    'MotivoAfastamento.codigo' => $dadosRecebidos->codigo_motivo_licenca
                                                ), 
                                                'fields' => 'MotivoAfastamento.codigo'));

                        if (!empty($motivo_licenca)) {
                            $codigo_motivo_licenca = $dadosRecebidos->codigo_motivo_licenca;
                        } else {                                
                            $erros[] = "codigo_motivo_licenca não encontrado.";
                        }

                    } else {
                        $this->loadModel('MotivoAfastamentoExterno');
                        $result = $this->MotivoAfastamentoExterno->find('first', array('conditions' => array('MotivoAfastamentoExterno.codigo_externo' => $dadosRecebidos->codigo_externo_motivo_licenca, 'MotivoAfastamentoExterno.codigo_cliente' => $matriz), 'fields' => 'MotivoAfastamentoExterno.codigo_motivos_afastamento'));
                        
                        //verifica se existe o relacionamento do codigo externo com o codigo motivo para gravar na aplicacao de atestados
                        if(!empty($result['MotivoAfastamentoExterno']['codigo_motivos_afastamento'])){
                            $codigo_motivo_licenca = $result['MotivoAfastamentoExterno']['codigo_motivos_afastamento'];
                        }
                        else {
                            $erros[] = "codigo_externo_motivo_licenca não encontrado.";
                        }
                    }//fim codigo_externo_motivo_licenca

                    //verifica erros
                    if(empty($erros)) {

                        $cnpj_alocacao                      = $dadosRecebidos->cnpj_alocacao; //"32560466813"; //obrigatorio
                        $cpf                                = $dadosRecebidos->cpf; //"32560466813"; //obrigatorio
                        $codigo_profissional                = $dadosRecebidos->codigo_profissional; //"6526"; //opcional
                        $afastamento_em_horas               = $dadosRecebidos->afastamento_em_horas; //"1"; //obrigatorio
                        $data_inicio                        = $dadosRecebidos->data_inicio; //"2017-08-03"; //obrigatorio
                        $data_fim                           = $dadosRecebidos->data_fim; //"2017-08-03"; //obrigatorio
                        $hora_inicio                        = $dadosRecebidos->hora_inicio; //"08:00"; //opcional
                        $hora_fim                           = $dadosRecebidos->hora_fim; //"18:00"; //opcional
                        $codigo_motivo_licenca              = $codigo_motivo_licenca; //"17"; //opcional
                        $codigo_motivo_licenca_esocial      = $dadosRecebidos->codigo_motivo_licenca_esocial; //"1015"; //opcional

                        $pk_externo                         = (!empty($dadosRecebidos->pk_externo)) ? $dadosRecebidos->pk_externo : null;
                        
                        //endereco
                        $cep           = (!empty($dadosRecebidos->local_atendimento[0]->cep)) ? $dadosRecebidos->local_atendimento[0]->cep : null;
                        $endereco      = (!empty($dadosRecebidos->local_atendimento[0]->endereco)) ? $dadosRecebidos->local_atendimento[0]->endereco : null;
                        $numero        = (!empty($dadosRecebidos->local_atendimento[0]->numero)) ? $dadosRecebidos->local_atendimento[0]->numero : null;
                        $complemento   = (!empty($dadosRecebidos->local_atendimento[0]->complemento)) ? $dadosRecebidos->local_atendimento[0]->complemento : null;
                        $bairro        = (!empty($dadosRecebidos->local_atendimento[0]->bairro)) ? $dadosRecebidos->local_atendimento[0]->bairro : null;
                        $cidade        = (!empty($dadosRecebidos->local_atendimento[0]->cidade)) ? $dadosRecebidos->local_atendimento[0]->cidade : null;
                        $estado        = (!empty($dadosRecebidos->local_atendimento[0]->estado)) ? $dadosRecebidos->local_atendimento[0]->estado : null;
                        $tipo_local    = (!empty($dadosRecebidos->local_atendimento[0]->tipo_local)) ? $dadosRecebidos->local_atendimento[0]->tipo_local : null;

                        //instancia as models necessarias
                        $this->loadModel('Funcionario');
                        $this->loadModel('ClienteFuncionario');
                        $this->loadModel('FuncionarioSetorCargo');
                        $this->loadModel('Cliente');

                        //pega o cliente alocacao
                        $cliente_alocacao = $this->Cliente->find('first', array('fields' => array('Cliente.codigo'),'conditions' => array('Cliente.codigo_documento' => $cnpj_alocacao)));

                        //verifica se existe o cliente alocacao na base
                        if(isset($cliente_alocacao["Cliente"]['codigo'])) {


                            $codigo_cliente_alocacao = $cliente_alocacao["Cliente"]['codigo'];

                            //monta para pegar os dados de cliente_funcionario e funcionario setor e cargo
                            $joinFuncionario = array(
                                    array(
                                        'table' => "{$this->ClienteFuncionario->databaseTable}.{$this->ClienteFuncionario->tableSchema}.{$this->ClienteFuncionario->useTable}",
                                        'alias' => 'ClienteFuncionario',
                                        'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
                                        'type' => 'INNER',
                                    ),
                                    array(
                                        'table' => "{$this->FuncionarioSetorCargo->databaseTable}.{$this->FuncionarioSetorCargo->tableSchema}.{$this->FuncionarioSetorCargo->useTable}",
                                        'alias' => 'FuncionarioSetorCargo',
                                        'conditions' => 'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
                                        'type' => 'INNER',
                                    ),
                                );
                            //campos de retorno
                            $fieldsFuncionario = array('Funcionario.codigo as codigo_funcionario', 
                                                        'ClienteFuncionario. codigo as codigo_cliente_funcionario',
                                                        'FuncionarioSetorCargo.codigo as codigo_funcionario_setor_cargo');
                            //condições do where
                            $conditionsFuncionario = array(
                                    'Funcionario.cpf'                               => $cpf,
                                    'FuncionarioSetorCargo.codigo_setor'            => $codigo_setor,
                                    'FuncionarioSetorCargo.codigo_cargo'            => $codigo_cargo,
                                    'FuncionarioSetorCargo.codigo_cliente_alocacao' => $codigo_cliente_alocacao

                                );

                            //buscar o codigo do funcionario pelo cpf
                            $funcionario = $this->Funcionario->find('first', array('fields' => $fieldsFuncionario,
                                                                                    'joins' => $joinFuncionario,
                                                                                    'conditions' => $conditionsFuncionario
                                ));

                            //verifica se encontrou o funcionario na base com os dados passados
                            if(!empty($funcionario)) {

                                //seta as variaveis
                                $codigo_funcionario                 = $funcionario[0]['codigo_funcionario'];
                                $codigo_cliente_funcionario         = $funcionario[0]['codigo_cliente_funcionario'];
                                $codigo_funcionario_setor_cargo     = $funcionario[0]['codigo_funcionario_setor_cargo'];

                                //instancia o atestado médico
                                $this->loadModel('Atestado');
                                $this->loadModel('Usuario');
                                $this->loadModel('VEndereco');

                                //pega o usuario inclusao
                                $usuario = $this->Usuario->find('first',array('fields' => array('Usuario.codigo'), 'conditions' => array('Usuario.token' => $token)));
                                $codigo_usuario_inclusao = $usuario['Usuario']['codigo'];

                                //pega a cidade
                                $conditionsCidade['VEndereco.endereco_cidade like'] = "%".utf8_decode($cidade)."%";
                                $conditionsCidade['VEndereco.endereco_estado'] = $estado;
                                $cidade = $this->VEndereco->find('first', array('fields' => array('VEndereco.endereco_codigo_cidade'),'conditions' => $conditionsCidade));
                                $codigo_cidade = $cidade["VEndereco"]["endereco_codigo_cidade"];

                                //monta o array para insercao
                                $atestado["Atestado"]['codigo_cliente_funcionario']     = $codigo_cliente_funcionario;
                                $atestado["Atestado"]['codigo_func_setor_cargo']        = $codigo_funcionario_setor_cargo;
                                $atestado["Atestado"]['codigo_medico']                  = $codigo_profissional;
                                $atestado["Atestado"]['data_afastamento_periodo']       = $data_inicio;
                                $atestado["Atestado"]['data_retorno_periodo']           = $data_fim;
                                $atestado["Atestado"]['afastamento_em_horas']           = ($hora_fim - $hora_inicio); 
                                $atestado["Atestado"]['hora_afastamento']               = $hora_inicio;
                                $atestado["Atestado"]['hora_retorno']                   = $hora_fim;
                                $atestado["Atestado"]['codigo_motivo_esocial']          = $codigo_motivo_licenca_esocial;
                                $atestado["Atestado"]['codigo_motivo_licenca']          = $codigo_motivo_licenca;
                                $atestado["Atestado"]['codigo_tipo_local_atendimento']  = $tipo_local;
                                $atestado["Atestado"]['habilita_afastamento_em_horas']  = $afastamento_em_horas;
                                $atestado["Atestado"]['pk_externo']                     = $pk_externo;

                                $atestado["Atestado"]['cep']                            = $cep;
                                $atestado["Atestado"]['endereco']                       = $endereco;
                                $atestado["Atestado"]['numero']                         = $numero;
                                $atestado["Atestado"]['complemento']                    = $complemento;
                                $atestado["Atestado"]['bairro']                         = $bairro;
                                
                                $atestado["Atestado"]['codigo_cidade']                  = (!empty($codigo_cidade)) ? $codigo_cidade : null;
                                
                                //usuario inclusao
                                $atestado["Atestado"]['codigo_usuario_inclusao']        = $codigo_usuario_inclusao;
                                $atestado["Atestado"]['ativo']                          = 1;

                                //inclui os dados
                                if($this->Atestado->incluir($atestado)) {

                                    $codigo_atestado = $this->Atestado->id;

                                     //instancia o atestadp_cid
                                    $this->loadModel('AtestadoCid');

                                    //error cid
                                    $error_cid = false;

                                    if(!empty($dadosRecebidos->cid)) {
                                        
                                        //varre os cids enviados no post
                                        foreach ($dadosRecebidos->cid as $cid) {

                                            if(!empty($cid->codigo_cid10)) {
                                                //seta os dados do cid do atestado    
                                                $atestado_cid["AtestadoCid"]['codigo_atestado']         = $codigo_atestado;
                                                $atestado_cid["AtestadoCid"]['codigo_cid']              = $cid->codigo_cid10;

                                                if(!$this->AtestadoCid->incluir($atestado_cid)) {
                                                    $error_cid = true;
                                                } //fim atestados cid
                                            }
                                            
                                        }//fim foreach cid

                                    }//fim $dadosRecebidos->cid

                                    if(!$error_cid) {
                                        //retorna com sucesso
                                        $this->dados["status"] = "0";
                                        $this->dados['msg']    = 'SUCESSO';
                                    }
                                    else {
                                        //erro do codigo do cliente alocacao
                                        $this->dados["status"] = "7";
                                        $this->dados['msg']     = 'Erro ao inserir atestado cid!';
                                    }

                                }
                                else {

                                    $erros = implode(",", $this->Atestado->validationErrors);

                                    //erro do codigo do cliente alocacao
                                    $this->dados["status"] = "6";
                                    $this->dados['msg']     = 'Erro ao inserir atestado! Possiveis erros: '.$erros;
                                } // fim atestados

                            }
                            else {
                                //erro do codigo do cliente alocacao
                                $this->dados["status"] = "5";
                                $this->dados['msg']     = 'Erro ao recuperar os dados do funcionario com a seguinte combinação  cpf, codigo_setor, codigo_cargo, cnpj_alocacao!';

                            } //fim empty funcionario
                        } 
                        else {
                             //erro do codigo do cliente alocacao
                            $this->dados["status"] = "4";
                            $this->dados['msg']     = 'Erro ao recuperar o codigo para o cnpj_alocacao passado, verificar se é o cnpj_alocacao correto!';

                        } //fim isset cliente codigo
                    }
                    else {

                        $erros = implode(",", $erros);

                        //erro do codigo do cliente alocacao
                        $this->dados["status"] = "6";
                        $this->dados['msg']     = 'Erro ao inserir atestado! Possiveis erros: '.$erros;

                    }

                } 
                else {

                    //msg de erro
                    $this->dados["status"] = "4";
                    $this->dados['msg']     = 'os seguintes campos são obrigatorios: ' . implode(',',$this->data['campos_error']);

                    //cnpj_alocacao, codigo_setor, codigo_cargo, cpf, afastamento_em_horas, data_inicio, data_fim, codigo_cid10, cep, endereco, numero, bairro, cidade, tipo_local.';

                } //fim valida campos obrigatorios

            } //fim valida_autorizacao

        } 
        else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */ 
            $this->dados["status"] = "1";

        } //fim verificacao gets

        //joga na log_integracao/ codigos das ocorrencias
        $entrada   = "url;";
        $entrada  .= implode(";", array_keys($_POST));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);
        $entrada  .= ";";
        $entrada  .= implode(";", $_POST);
        
        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_ATESTADO_INCLUIR_ATESTADO");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_ATESTADO_INCLUIR_ATESTADO',
                'conteudo' => $entrada,
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim incluir atestado

    /**
     * Método para validar os campos obrigatorios da inserção d
     */ 
    private function valida_campos_obrigatorios($dados)
    {   

        $campo_error = array();

        //valida os campos
        if(!isset($dados->cnpj_alocacao)) {
            if(empty($dados->cnpj_alocacao)) {
                $campo_error[] = 'cnpj_alocacao';
            }
        }

        // Inicializa propriedades para prevenir Warnings e Notices, facilitando assim o debug
        $dados->codigo_setor = (isset($dados->codigo_setor) ? $dados->codigo_setor : null);
        $dados->codigo_externo_setor = (isset($dados->codigo_externo_setor) ? $dados->codigo_externo_setor : null);
        $dados->codigo_cargo = (isset($dados->codigo_cargo) ? $dados->codigo_cargo : null);
        $dados->codigo_externo_cargo = (isset($dados->codigo_externo_cargo) ? $dados->codigo_externo_cargo : null);

        $dados->codigo_motivo_licenca = (isset($dados->codigo_motivo_licenca) ? $dados->codigo_motivo_licenca : null);
        $dados->codigo_externo_motivo_licenca = (isset($dados->codigo_externo_motivo_licenca) ? $dados->codigo_externo_motivo_licenca : null);
        

        $this->fields->verificaCodigoExterno($dados->codigo_setor,
            $dados->codigo_externo_setor, 
            "campo codigo_setor ou codigo_externo_setor obrigatorio"
        );
        $this->fields->verificaCodigoExterno($dados->codigo_cargo,
            $dados->codigo_externo_cargo, 
            "campo codigo_cargo ou codigo_externo_cargo obrigatorio"
        );
        $this->fields->verificaCodigoExterno($dados->codigo_motivo_licenca,
            $dados->codigo_externo_motivo_licenca, 
            "campo codigo_motivo_licenca ou codigo_externo_motivo_licenca obrigatorio"
        );

        $error = $this->fields->campos_obrigatorios;        
        if(!empty($error)) {
            $campo_error[] = implode(", ",$error);
        }        

        
        if(!isset($dados->cpf)) {
            if(empty($dados->cpf)) {
                $campo_error[] = 'cpf';
            }
        }

        if(!isset($dados->afastamento_em_horas)) {
            if(empty($dados->afastamento_em_horas)) {
                $campo_error[] = 'afastamento_em_horas';
            }
        }

        if(!isset($dados->data_inicio)) {
            if(empty($dados->data_inicio)) {
                $campo_error[] = 'data_inicio';
            }
        }

        if(!isset($dados->data_fim)) {
            if(empty($dados->data_fim)) {
                $campo_error[] = 'data_fim';
            }
        }

        if(!isset($dados->codigo_profissional)) {
            if(empty($dados->codigo_profissional)) {
                $campo_error[] = 'codigo_profissional';
            }
        }


        if(!empty($campo_error)) {
            $this->data['campos_error'] = $campo_error;
            return false;
        }

        //retorna que os campos obrigatórios estao corretos.
        return true;

    } //fim valida_campos_obrigatorios($_POST)



    /**
     * Metodo para retornar os dados do atestado do cpf criado
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * indice atestado => retorna os dados do atestado
     * 
     */ 
    public function consulta_atestado()
    {

        //verifica se existe os gets obrigatorios
        if(isset($_GET['token']) && isset($_GET['cnpj'])) {
        
            //valida o usuario + cnpj
            $cnpj   = $_GET['cnpj'];
            $token  = $_GET['token'];

            //verifica se esta validado a autorizacao
            if($this->valida_autorizacao($token, $cnpj)) {

                //variaveis auxiliares
                $cpf = "";
                if(isset($_GET["cpf"])) {
                    $cpf = $_GET["cpf"];

                    $data = null;
                    if(isset($_GET['data'])) {
                        $data = $_GET['data'];
                    }

                    $pk_externo = null;
                    if(isset($_GET['pk_externo'])) {
                        $pk_externo = $_GET['pk_externo'];
                    }


                    //instancia a cargos e as auxiliares
                    $this->loadModel('Atestado');
                    $this->loadModel('AtestadoCid');
                    $this->loadModel('ClienteFuncionario');
                    $this->loadModel('Funcionario');

                    //campos
                    $fieldAtestado = array('Atestado.codigo as codigo', 
                                            'Atestado.codigo_medico as codigo_profissional',
                                            'Atestado.habilita_afastamento_em_horas as afastamento_em_horas',
                                            'Atestado.data_afastamento_periodo as data_inicio',
                                            'Atestado.data_retorno_periodo as data_fim',
                                            'Atestado.hora_afastamento as hora_inicio',
                                            'Atestado.hora_retorno as hora_fim',
                                            'Atestado.codigo_motivo_licenca as codigo_motivo_licenca',
                                            'Atestado.codigo_motivo_esocial as codigo_motivo_licenca_esocial',
                                            'Atestado.afastamento_em_dias as dias_afastamento',
                                            'Atestado.afastamento_em_horas as afastamento_horas',
                                            'AtestadoCid.codigo_cid as codigo_cid10',                                            
                                            'Atestado.cep as cep',
                                            'Atestado.endereco as endereco',
                                            'Atestado.numero as numero',
                                            'Atestado.complemento as complemento',
                                            'Atestado.bairro as bairro',
                                            // 'Atestado.cidade as cidade',
                                            'Atestado.codigo_tipo_local_atendimento as tipo_local',
                                            );

                    $joinAtestado = array(
                            array(
                                'table' => "{$this->AtestadoCid->databaseTable}.{$this->AtestadoCid->tableSchema}.{$this->AtestadoCid->useTable}",
                                'alias' => 'AtestadoCid',
                                'conditions' => 'AtestadoCid.codigo_atestado = Atestado.codigo',
                                'type' => 'LEFT',
                            ),
                            array(
                                'table' => "{$this->ClienteFuncionario->databaseTable}.{$this->ClienteFuncionario->tableSchema}.{$this->ClienteFuncionario->useTable}",
                                'alias' => 'ClienteFuncionario',
                                'conditions' => 'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario',
                                'type' => 'INNER',
                            ),
                            array(
                                'table' => "{$this->Funcionario->databaseTable}.{$this->Funcionario->tableSchema}.{$this->Funcionario->useTable}",
                                'alias' => 'Funcionario',
                                'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
                                'type' => 'INNER',
                            ),
                        );

                    //monta as condições
                    $conditions['Funcionario.cpf'] = $cpf;
                    $conditions['Atestado.ativo'] = 1;

                    if(!is_null($data)) {
                        $conditions['Atestado.data_afastamento_periodo'] = $data;
                    }

                    if(!is_null($pk_externo)) {
                        $conditions['Atestado.pk_externo'] = $pk_externo;
                    }

                    //pega os dados do cargo
                    $atestados = $this->Atestado->find('all', array('fields' => $fieldAtestado, 'joins' => $joinAtestado, 'conditions' => $conditions));

                    // debug($atestados);exit;


                    //seta como valor nulo o cargo
                    $this->dados['atestados'] = '';

                    //verifica se existe valores
                    if(!empty($atestados)) {

                        //variavel auxiliar
                        $dados = array();

                        //varre para montar corretamente o array/json que irá devolver
                        foreach($atestados as $key => $val) {
                            $dados[$key] = $val[0];
                        }
                        //seta na variavel que vai ser disponibilizada os cargos
                        $this->dados['atestados'] = $dados;

                    }//fim if

                    //status de sucesso
                    $this->dados["status"] = '0';
                    $this->dados["msg"] = 'SUCESSO';

                }
                else {

                    //erro do codigo do cliente alocacao
                    $this->dados["status"] = "4";
                    $this->dados['msg']     = 'Obrigatorio passar o CPF do funcionario!';

                } //fim verificacao cpf


            } //fim valida_autorizacao

        } 
        else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */ 
            $this->dados["status"] = "1";

        } //fim verificacao gets

         //joga na log_integracao/ codigos das ocorrencias
        $entrada   = implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);       
        
        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta
        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        //componente para log da api        
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_ATESTADOS_CONSULTA_ATESTADO");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_ATESTADOS_CONSULTA_ATESTADO',
                'conteudo' => $entrada,
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim consulta_atestados


    /**
     * Metodo para deletar um determinado atestado
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * 
     * 
     * indice atestado => retorna a mensaegem de sucesso ou erro
     * 
     */ 
    public function delete_atestado()
    {

        $this->autoRender = false;
        $dadosRecebidos = '';
        $this->ApiDataFormat->setContentType();
        // Pega os campos via json ou Form url-encoded
        $dadosRecebidos = $this->ApiDataFormat->getDataRequest();
        $dados = array();

        //verifica se existe os gets obrigatorios
        if(isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {

            //valida o usuario + cnpj
            $cnpj   = trim($dadosRecebidos->cnpj);
            $token  = trim($dadosRecebidos->token);

            //verifica se esta validado a autorizacao
            if($this->valida_autorizacao($token, $cnpj)) {

                //pega os campos do post e valida os obrigatorios
                if($this->valida_campos_obrigatorios_delete($dadosRecebidos)) {
                    //pega os campos que foi passado para o rest
                    $cpf                = $dadosRecebidos->cpf; //obrigatorio
                    $codigo_atestado    = $dadosRecebidos->codigo_atestado; //opcional
                    
                    //instancia as models necessarias
                    $this->loadModel('Funcionario');
                    $this->loadModel('ClienteFuncionario');
                    $this->loadModel('FuncionarioSetorCargo');

                    //monta para pegar os dados de cliente_funcionario e funcionario setor e cargo
                    $joinFuncionario = array(
                            array(
                                'table' => "{$this->ClienteFuncionario->databaseTable}.{$this->ClienteFuncionario->tableSchema}.{$this->ClienteFuncionario->useTable}",
                                'alias' => 'ClienteFuncionario',
                                'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
                                'type' => 'INNER',
                            ),
                            array(
                                'table' => "{$this->FuncionarioSetorCargo->databaseTable}.{$this->FuncionarioSetorCargo->tableSchema}.{$this->FuncionarioSetorCargo->useTable}",
                                'alias' => 'FuncionarioSetorCargo',
                                'conditions' => 'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
                                'type' => 'INNER',
                            ),
                        );
                    //campos de retorno
                    $fieldsFuncionario = array('Funcionario.codigo as codigo_funcionario', 
                                                'ClienteFuncionario. codigo as codigo_cliente_funcionario',
                                                'FuncionarioSetorCargo.codigo as codigo_funcionario_setor_cargo');
                    //condições do where
                    $conditionsFuncionario = array('Funcionario.cpf' => $cpf);

                    //buscar o codigo do funcionario pelo cpf
                    $funcionario = $this->Funcionario->find('first', array('fields' => $fieldsFuncionario,
                                                                            'joins' => $joinFuncionario,
                                                                            'conditions' => $conditionsFuncionario
                        ));

                    //verifica se encontrou o funcionario na base com os dados passados
                    if(!empty($funcionario)) {

                        //seta as variaveis
                        $codigo_funcionario                 = $funcionario[0]['codigo_funcionario'];
                        $codigo_cliente_funcionario         = $funcionario[0]['codigo_cliente_funcionario'];
                        $codigo_funcionario_setor_cargo     = $funcionario[0]['codigo_funcionario_setor_cargo'];

                        //instancia o atestado médico
                        $this->loadModel('AtestadoCid');

                        if($this->AtestadoCid->deleteAll(array('codigo_atestado' => $codigo_atestado))) {

                            //instancia o atestado
                            $this->loadModel('Atestado');

                            if($this->Atestado->excluir($codigo_atestado)) {
                                $this->dados["status"] = "0";
                                $this->dados['msg']    = 'Aestado excluido com sucesso!';
                            }
                            else {
                                //erro do codigo atestado
                                $this->dados["status"] = "7";
                                $this->dados['msg']     = 'Erro ao excluir os dados do atestado!';
                            }//fim atestado excluir

                        }
                        else {
                            //erro do codigo atestado
                            $this->dados["status"] = "6";
                            $this->dados['msg']     = 'Erro ao excluir os cids do atestado!';
                        } //fim atestados_cid excluir

                        

                    }
                    else {
                        //erro do codigo do cliente alocacao
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = 'Erro ao recuperar os dados do funcionario com os dados de cpf!';

                    } //fim empty funcionario
                   
                } 
                else {

                    //msg de erro
                    $this->dados["status"] = "4";
                    $this->dados['msg']     = 'os seguintes campos são obrigatorios: cpf.';

                } //fim valida campos obrigatorios

            } //fim valida_autorizacao

        } 
        else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */ 
            $this->dados["status"] = "1";

        } //fim verificacao gets

        //joga na log_integracao/ codigos das ocorrencias
        $entrada   = "url;";
        $entrada  .= implode(";", array_keys($dados));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);
        $entrada  .= ";";
        $entrada  .= implode(";", $dados);
        
        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'],$ret_mensagem,"API_ATESTADO_DELETE_ATESTADO");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_ATESTADO_DELETE_ATESTADO',
                'conteudo' => $entrada,
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    } // fim delete atestado


    /**
     * Método para validar os campos obrigatorios da inserção d
     */ 
    private function valida_campos_obrigatorios_delete($dados)
    {   
        //valida os campos
        if(!isset($dados->cpf)) {
            if(empty($dados->cpf)) {
                return false;
            }
        }

        if(!isset($dados->codigo_atestado)) {
            if(empty($dados->codigo_atestado)) {
                return false;
            }
        }

        //retorna que os campos obrigatórios estao corretos.
        return true;

    } //fim valida_campos_obrigatorios_delete($_POST)



}//fim controller motivos_adastamento