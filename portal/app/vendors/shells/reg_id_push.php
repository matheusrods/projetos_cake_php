<?php
class RegIdPushShell extends Shell {
    
    //atributo que instancia as models
    var $uses = array(
		'PushOutbox',
        'UsuarioSistema'
    	);

    /**
     * Metodo para iniciar o script como o contrutor da classe
     */
    public function main()
    {
        echo "Valida se o token está no reg_id\n";
        // $this->get_reg_id();

    } //fim main

    public function get_reg_id()
    {
        
        print "INICIO PROCESSO"."\n";

        $path_resultado = APP . 'tmp' . DS;
        $caminho_arquivo = $path_resultado."reg_ids.txt"; 

        $uri = "ithealth.servicebus.windows.net/ithealth-notification-hub";
        $sasKeyName = "RootManageSharedAccessKey";
        $sasKeyValue = "YT0BTVxAzLP2QoF6Tq0lo+Ic8nJ3TerTkm1DrKSjtZs=";

        $token = $this->PushOutbox->generateSasToken($uri, $sasKeyName, $sasKeyValue);

        $dados = $this->UsuarioSistema->find('all',array('conditions' => array('platform IS NOT NULL')));

        foreach($dados AS $d) {
            
            $arr_val = array();
            $arr_val['codigo'] = $d['UsuarioSistema']['codigo'];
            $arr_val['codigo_usuario'] = $d['UsuarioSistema']['codigo_usuario'];
            $arr_val['token_push'] = $d['UsuarioSistema']['token_push'];

            $data = $this->PushOutbox->get_registration($token,$d['UsuarioSistema']['token_push']);
            $xml = simplexml_load_string($data);

            $arr_val['registration'] = $xml;

            file_put_contents($caminho_arquivo, print_r($arr_val,1), FILE_APPEND);

            print "CODIGO USUARIO: " . $d['UsuarioSistema']['codigo_usuario']."\n";

        }

        print "FIM PROCESSO"."\n";

    }

    public function get_tag_id()
    {
        
        print "INICIO PROCESSO"."\n";

        $path_resultado = APP . 'tmp' . DS;
        $caminho_arquivo = $path_resultado."tags_ids.txt"; 

        $uri = "ithealth.servicebus.windows.net/ithealth-notification-hub";
        $sasKeyName = "RootManageSharedAccessKey";
        $sasKeyValue = "YT0BTVxAzLP2QoF6Tq0lo+Ic8nJ3TerTkm1DrKSjtZs=";

        $token = $this->PushOutbox->generateSasToken($uri, $sasKeyName, $sasKeyValue);

        $dados = $this->UsuarioSistema->find('all',array('conditions' => array('platform IS NOT NULL')));

        foreach($dados AS $d) {
            
            $arr_val = array();
            $arr_val['codigo'] = $d['UsuarioSistema']['codigo'];
            $arr_val['codigo_usuario'] = $d['UsuarioSistema']['codigo_usuario'];
            $arr_val['token_push'] = $d['UsuarioSistema']['token_push'];

            $data = $this->PushOutbox->get_tags($token,$d['UsuarioSistema']['token_push']);
            $xml = simplexml_load_string($data);

            $arr_val['registration'] = $xml;

            file_put_contents($caminho_arquivo, print_r($arr_val,1), FILE_APPEND);

            print "CODIGO USUARIO: " . $d['UsuarioSistema']['codigo_usuario']."\n";

        }

        print "FIM PROCESSO"."\n";

    }

    /**
     * [deleta_token metodo para varre e pagar os registrations duplicados]
     * @return [type] [description]
     */
    public function deleta_reg_id_inativo()
    {

        print "INICIO PROCESSO"."\n";

        $path_resultado = APP . 'tmp' . DS;
        $caminho_arquivo = $path_resultado."delete_reg_ids.txt"; 

        $uri = "ithealth.servicebus.windows.net/ithealth-notification-hub";
        $sasKeyName = "RootManageSharedAccessKey";
        $sasKeyValue = "YT0BTVxAzLP2QoF6Tq0lo+Ic8nJ3TerTkm1DrKSjtZs=";

        $token = $this->PushOutbox->generateSasToken($uri, $sasKeyName, $sasKeyValue);

        // $dados = $this->UsuarioSistema->find('all',array('conditions' => array('platform IS NOT NULL')));
        //para pegar os regs ids que não estão mais ativos na base de dados
        $query = "  SELECT po.codigo_usuario, u.nome
                        , (CASE WHEN us.token_push = po.token then 'ativo' else po.token end) as token
                    FROM push_outbox po
                        INNER JOIN usuario u on po.codigo_usuario = u.codigo
                        INNER JOIN usuario_sistema us on u.codigo = us.codigo_usuario
                            AND us.platform IS NOT NULL
                    WHERE (CASE WHEN us.token_push = po.token then 'ativo' else po.token end) <> 'ativo'
                    GROUP BY po.codigo_usuario, u.nome, po.token,us.token_push
                    ORDER BY u.nome";
        $dados = $this->PushOutbox->query($query);

        //varre os dados encontrados
        $arr_val = array();
        $count = 1;
        foreach($dados AS $key => $d) {
            

            $data = $this->PushOutbox->get_registration($token,$d[0]['token']);
            $xml = simplexml_load_string($data);

            if($xml->Code == '404') {
                print "CODIGO USUARIO: " . $d[0]['codigo_usuario']." -> TOKEN JA DELETADO:".$d[0]['token']."\n";
                // print_r($arr_val);
            }
            else {
                $arr_val[$count]['codigo_usuario'] = $d[0]['codigo_usuario'];
                $arr_val[$count]['nome_usuario'] = $d[0]['nome'];
                $arr_val[$count]['token'] = $d[0]['token'];
                $arr_val[$count]['registration'] = $xml;
                //DELETA O TOKEN 
                $data_delete = $this->PushOutbox->delete_registration($token,$d[0]['token']);
                $arr_val[$count]['delete_registration'] = $data_delete;

                $count++;
                print "TOKEN DELETADO --->>> ".$d[0]['token']." CODIGO USUARIO: " . $d[0]['codigo_usuario']."\n";
            }

        }

        print_r($arr_val);
        file_put_contents($caminho_arquivo, print_r($arr_val,1), FILE_APPEND);

        print "FIM PROCESSO"."\n";

    }//fim deleta_token

     /**
     * [deleta_token metodo para varre e pagar os registrations duplicados]
     * @return [type] [description]
     */
    public function deleta_instalation_inativo()
    {

        print "INICIO PROCESSO"."\n";

        $path_resultado = APP . 'tmp' . DS;
        $caminho_arquivo = $path_resultado."delete_reg_ids.txt"; 

        $uri = "ithealth.servicebus.windows.net/ithealth-notification-hub";
        $sasKeyName = "RootManageSharedAccessKey";
        $sasKeyValue = "YT0BTVxAzLP2QoF6Tq0lo+Ic8nJ3TerTkm1DrKSjtZs=";

        $token = $this->PushOutbox->generateSasToken($uri, $sasKeyName, $sasKeyValue);

        // $dados = $this->UsuarioSistema->find('all',array('conditions' => array('platform IS NOT NULL')));
        //para pegar os regs ids que não estão mais ativos na base de dados
        $query = "  SELECT po.codigo_usuario, u.nome
                        -- ,po.token
                         , (CASE WHEN us.token_push = po.token then 'ativo' else po.token end) as token
                    FROM push_outbox po
                        INNER JOIN usuario u on po.codigo_usuario = u.codigo
                        INNER JOIN usuario_sistema us on u.codigo = us.codigo_usuario
                            AND us.platform IS NOT NULL
                     WHERE (CASE WHEN us.token_push = po.token then 'ativo' else po.token end) <> 'ativo'
                    GROUP BY po.codigo_usuario, u.nome, po.token,us.token_push
                    ORDER BY u.nome";
        $dados = $this->PushOutbox->query($query);

        //varre os dados encontrados
        $arr_val = array();
        $count = 1;
        foreach($dados AS $key => $d) {
            
            print "Usuario Nome: ".$d[0]['nome']." CODIGO USUARIO: " . $d[0]['codigo_usuario']." TOKEN --->>> ".$d[0]['token']."\n";

            $reg_id = $this->PushOutbox->get_registration($token,$d[0]['token']);
            $xml = simplexml_load_string($reg_id);
            
            if($xml->Code == '404') {
                print "CODIGO USUARIO: " . $d[0]['codigo_usuario']." -> TOKEN JA DELETADO:".$d[0]['token']."\n";
                // print_r($arr_val);
            }
            else {
                $install_id = null;
                if(isset($xml->content->GcmRegistrationDescription)) {
                    $arr_dados_reg = explode(',',$xml->content->GcmRegistrationDescription->Tags);
                    $arr_install_id = explode('{',$arr_dados_reg[0]);
                    $install_id = substr($arr_install_id[1],0,-1);
                }
                else if(isset($xml->content->AppleRegistrationDescription)) {
                    $arr_dados_reg = explode(',',$xml->content->AppleRegistrationDescription->Tags);
                    $arr_install_id = explode('{',$arr_dados_reg[0]);
                    $install_id = substr($arr_install_id[1],0,-1);
                }
                else {
                    print "CODIGO USUARIO: " . $d[0]['codigo_usuario']." ----->>>> DADOS NAO ENCONTRADOS!!!!!!!!!!!!!!!!\n";
                }

                // debug($arr_install_id);
                // debug($install_id);
                
                if(!is_null($install_id)) {
                    //pega a instalacao
                    $data = $this->PushOutbox->get_instalation($token,$install_id);

                    $dados_instalation = json_decode($data);

                    debug($dados_instalation);

                }


            }//fim else




            // $data = $this->PushOutbox->get_instalation($token,$d[0]['token']);
            // $xml = simplexml_load_string($data);

            
            // print $data."\n";

            print "##############################################################################################\n";
            print "##############################################################################################\n";
            print "##############################################################################################\n";

            // if($xml->Code == '404') {
            //     print "CODIGO USUARIO: " . $d[0]['codigo_usuario']." -> TOKEN JA DELETADO:".$d[0]['token']."\n";
            //     // print_r($arr_val);
            // }
            // else {
            //     $arr_val[$count]['codigo_usuario'] = $d[0]['codigo_usuario'];
            //     $arr_val[$count]['nome_usuario'] = $d[0]['nome'];
            //     $arr_val[$count]['token'] = $d[0]['token'];
            //     $arr_val[$count]['registration'] = $xml;
            //     //DELETA O TOKEN 
            //     $data_delete = $this->PushOutbox->delete_registration($token,$d[0]['token']);
            //     $arr_val[$count]['delete_registration'] = $data_delete;

            //     $count++;
            //     print "TOKEN DELETADO --->>> ".$d[0]['token']." CODIGO USUARIO: " . $d[0]['codigo_usuario']."\n";
            // }

        }

        // print_r($arr_val);
        // file_put_contents($caminho_arquivo, print_r($arr_val,1), FILE_APPEND);

        print "FIM PROCESSO"."\n";

    }//fim deleta_instalation token

}//fim class
?>