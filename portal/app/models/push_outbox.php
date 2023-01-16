<?php
class PushOutbox extends AppModel {
    var $name           = 'PushOutbox';
    var $tableSchema    = 'dbo';
    var $databaseTable  = 'RHHealth';
    var $useTable       = 'push_outbox';
    var $primaryKey     = 'codigo';
    var $actsAs         = array('Secure');
    var $belongsTo      = array('PushKeys' => array('foreignKey' => 'codigo_key'));

    var $validate       = array(
        'codigo_key' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o código da chave',
                'required' => true
            )
        ),
        'token' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o token do cliente',
                'required' => true
            )
        ),
        'fone_para' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o telefone',
                'required' => true
            )
        ),
        // 'titulo' => array(
        //     'notEmpty' => array(
        //         'rule' => 'notEmpty',
        //         'message' => 'Informe o título da notificação',
        //         'required' => true
        //     )
        // ),
        // 'mensagem' => array(
        //     'notEmpty' => array(
        //         'rule' => 'notEmpty',
        //         'message' => 'Informe a mensagem da notificação',
        //         'required' => true
        //     )
        // ),
        'liberar_envio_em' => array(
            'rule' => 'validaLiberarEnvio',
            'message' => 'Data de agendamento não pode ser inferior a data atual'
        )
    );

    public function agendar($projeto, $fone_para, $token, $titulo, $mensagem, $extra_data = null, $data_agenda = null, $sistema_origem = null, $modulo_origem = null, $platform = ''){
        // debug($projeto);
        $conditions = array(
            'tipo'      => PushKeys::TIPO_GOOGLE_GMC, 
            "convert(varbinary, projeto) = convert(varbinary, '{$projeto}')", 
            'ativo'     => true
        );
        $pushKey = $this->PushKeys->find('first', array('conditions' => $conditions));
        //debug($pushKey); exit;
        if($pushKey){
            $extra_data     = !is_array($extra_data) ? array() : $extra_data;
            $sistema_origem = trim($sistema_origem) ? trim($sistema_origem) : 'Portal Buonny';
            $modulo_origem  = trim($modulo_origem) ? trim($modulo_origem) : null;
            $data_agenda    = !trim($data_agenda) ? date("Y-m-d H:i:s") : $data_agenda;

            $item = array(
                'codigo_key'              => $pushKey[$this->PushKeys->name]['codigo'],
                'token'                   => $token,
                'fone_para'               => preg_replace('/[^\d]*/', '', $fone_para),
                'titulo'                  => trim($titulo),
                'mensagem'                => trim($mensagem),
                'extra_data'              => serialize($extra_data),
                'liberar_envio_em'        => $data_agenda,
                'codigo_usuario_inclusao' => 0,
                'sistema_origem'          => $sistema_origem,
                'modulo_origem'           => $modulo_origem,
                'platform'                => $platform
            );

            return $this->incluir($item);
        } else {
            $this->invalidate('codigo_key', 'Chave não encontrada ou projeto inválido');

            return false;
        }
    }

    public function validaLiberarEnvio() {
        if(!empty($this->data['PushOutbox']['liberar_envio_em'])){
            $this->data['PushOutbox']['liberar_envio_em'] = AppModel::dateToDbDate($this->data['PushOutbox']['liberar_envio_em']);
            if(strtotime($this->data['PushOutbox']['liberar_envio_em']) < strtotime(date('Ymd'))) return false;
        }

        return true;
    }

    public function getListaBlocoEnvio()
    {

        $this->Usuario = ClassRegistry::init('Usuario');
        $bloco = array();

        $fields = array(
            'PushKeys.projeto',
            'PushOutbox.titulo',
            'PushOutbox.mensagem',
            'PushOutbox.extra_data',
            'PushKeys.server_key',
            'PushOutbox.codigo',
            'PushOutbox.token',
            'PushOutbox.titulo',
            'PushOutbox.mensagem',
            'UsuarioSistema.platform',
            'UsuarioSistema.token_push',
            'PushOutbox.liberar_envio_em',
            'PushOutbox.data_inclusao'
        );

        $joins = array(
            array(
                'table' => 'RHHealth.dbo.usuario_sistema',
                'alias' => 'UsuarioSistema',
                'type' => 'INNER',
                'conditions' => 'UsuarioSistema.codigo_usuario = PushOutbox.codigo_usuario',
            ),
        );

        $lista = $this->find('all', array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => array(
                'UsuarioSistema.platform IS NOT NULL',
                'OR' => array(
                    'PushOutbox.liberar_envio_em <=' => date("Y-m-d H:i:s"),
                    'PushOutbox.liberar_envio_em IS NULL'
                ),
                'PushOutbox.data_envio' => null
                // "[PushOutbox].[data_envio] >= '2021-02-23 06:10:20'",
            ),
            'order' => array(
                'PushOutbox.liberar_envio_em ASC',
                'PushOutbox.data_inclusao'
            )
        ));

        // debug($lista);exit;

        // if (Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
        //     $tokens_desenvolvimento = $this->Usuario->getTokensPushDesenvolvimento();
        //     $tokens_desenvolvimento = array_flip($tokens_desenvolvimento);
        // }

        foreach($lista as $item){
            $enviar = true;
            // if (Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
            //     if (!isset($tokens_desenvolvimento[$item['PushOutbox']['token']]) && $item['PushKeys']['projeto'] != 'NinaApp') {
            //         $enviar = false;
            //         $this->setEnviadoCodigo($item['PushOutbox']['codigo']); //6019
            //     }
            // }
            
            if ($enviar) {
                $idx = md5($item['PushKeys']['projeto'] . $item['PushOutbox']['titulo'] . $item['PushOutbox']['mensagem'] . $item['PushOutbox']['extra_data']);

                if(!isset($bloco[$idx])){

                    $bloco[$idx] = array(
                        'serverKey' => $item['PushKeys']['server_key'],
                        'codigos'   => array($item['PushOutbox']['codigo']),
                        'regIds'    => array($item['PushOutbox']['token']),
                        'titulo'    => $item['PushOutbox']['titulo'],
                        'mensagem'  => $item['PushOutbox']['mensagem'],
                        // 'extra'     => ($item['PushOutbox']['extra_data'] != '') ? unserialize($item['PushOutbox']['extra_data']) : '',
                        'extra' => '',
                        'platform'  => $item['UsuarioSistema']['platform']
                    );
                } else {
                    $bloco[$idx]['codigos'][] = $item['PushOutbox']['codigo'];
                    $bloco[$idx]['regIds'][]  = $item['PushOutbox']['token'];
                }
            }
        }

        return $bloco;
    }

    /**
     * [doEnvioPushPendentes description]
     * 
     * pega todos os pushs pendentes para enviar
     * 
     * @return [type] [description]
     */
    public function doEnvioPushPendentes()
    {

        $lista = $this->getListaBlocoEnvio();
        // debug($lista);exit;

        //verifica se tem dados para enviar o push
        if(empty($lista)) {
            return false;
        }

        // $this->log(print_r($lista,1),'push_outbox');

        foreach($lista as $item){
            
            // $res = $this->sendPushNotificationFCM($item['serverKey'], $item['regIds'], $item['titulo'], $item['mensagem'], $item['extra'], $item['platform']);
            // if($res->failure > 0){
            //     $item['observacao'] = $res->results[0]->error;
            // }

            $res = $this->sendPushNotificationAzure($item['serverKey'], $item['regIds'], $item['titulo'], $item['mensagem'], $item['extra'], $item['platform']);

            // log para analisar duplicidade
            // $logs = "Codigo:".$item['codigos'][0]." --->RegId:" . $item['regIds'][0] . "--->Titulo:" . $item['titulo'] . "--->Mensagem:" . $item['mensagem'] . "--->Platform:" .$item['platform'];
            // $this->log($logs,'push_outbox');

            if(empty($res)) {
                $item['observacao'] = "Erro ao enviar mensagem";
            }

            $this->setEnvioGrupoCodigo($item);
        }
    }

    public function setEnviadoCodigo($codigo) {
        $dados = $this->carregar($codigo);
        $dados['PushOutbox']['data_envio'] = date("Y-m-d H:i:s");
        $this->atualizar($dados);
    }

    public function setEnvioGrupoCodigo($arrCodigos){
        $itens = $this->find('all', array('conditions' => array('[PushOutbox].[codigo] IN (' . implode(',', $arrCodigos['codigos']) . ')')));

        foreach($itens as $item){
            $item['PushOutbox']['data_envio'] = date("Y-m-d H:i:s");
            $item['PushOutbox']['observacao'] = (isset($arrCodigos['observacao'])) ? $arrCodigos['observacao'] : null;
            if(!$this->atualizar($item,false)){
                echo("não atualizou \n");
            }
        }
    }

    public function sendPushNotification($serverKey, $regIds, $titulo, $msg, $extra = array()){
        $regIds = is_array($regIds) ? $regIds : array($regIds);
        $extra  = is_array($extra)  ? $extra  : array();

        if(empty($serverKey) || !count($regIds) || empty($titulo) || empty($msg)) return false;

        $fields = array(
            'priority' => 'high',

            'registration_ids' => $regIds,

            'notification' => array(
                'title' => $titulo,
                'body'  => $msg,
                'sound' => 1
            ),
        );

        if(count($extra)) $fields['data'] = $extra;

        $headers = array('Authorization: key=' . $serverKey, 'Content-Type: application/json');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function sendPushNotificationFCM($serverKey, $regIds, $titulo, $mensagem, $extra = array(), $platform = 'ios'){
        $regIds = is_array($regIds) ? $regIds : array($regIds);
        $extra  = is_array($extra)  ? $extra  : array();

        if(empty($serverKey) || !count($regIds)) return false;

        $msg = array(
            'sound' => 1,
            //'content-available' => '1',

        );

        $mensagem = trim($mensagem);
        $titulo = trim($titulo);

        //if (!empty($mensagem)) $msg['message'] = $mensagem;
        if (!empty($mensagem)) $msg['body'] = $mensagem;
        if (!empty($titulo)) $msg['title'] = $titulo;

        // if($extra != '') {
        //     $msg = array_merge($msg, $extra);
        // }

        $fields = array(
            // 'to' => $regIds[0],
            'priority' => 'high',
            'registration_ids' => $regIds,
        );

        $hasExtra = false;
        if(count($extra) > 0) {
            $hasExtra = true;
            if((isset($extra['content-available'])) && (strtolower($platform)!='android') ){
                $fields['content-available'] = $extra['content-available'];//Joga na raiz
                unset($extra['content-available']);
            }
        }

        // if(strtolower($platform) == 'android') {
        //     $fields['data'] = array_merge($msg,$extra);

        // }else {
            $fields['notification'] = array_merge($msg,$extra);
            if($hasExtra){
                $fields['data'] = $extra;
            }
        // }

        // debug($fields);exit;

        $headers = array(
            'Authorization: key=' . $serverKey,            
            'Content-Type: application/json'
        );

        // echo json_encode( $fields );exit;
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        //print_r($result); exit;
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );

        curl_close( $ch );
        
        return $result;
    }

    public function send($serverKey, $fields){

        $headers = array(
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json'
        );

        //echo json_encode( $fields );exit;
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );

        return $result;
    }

    /**
     * [sendPushNotificationAzure metodo para disparar o push pela azure]
     * @param  [type] $serverKey [description]
     * @param  [type] $regIds    [description]
     * @param  [type] $titulo    [description]
     * @param  [type] $mensagem  [description]
     * @param  array  $extra     [description]
     * @param  string $platform  [description]
     * @return [type]            [description]
     */
    public function sendPushNotificationAzure($serverKey, $regIds, $titulo, $mensagem, $extra = array(), $platform = 'android')
    {

        // debug(array($serverKey, $regIds, $titulo, $mensagem, $extra, $platform));

        // $regIds = is_array($regIds) ? $regIds : array($regIds);
        $regIds = is_array($regIds) ? $regIds[0] : $regIds;
        $extra  = is_array($extra)  ? $extra  : array();

        if(empty($serverKey) || !count($regIds)) return false;
        
        $mensagem = trim($mensagem);
        $titulo = trim($titulo);

        //if (!empty($mensagem)) $msg['message'] = $mensagem;
        if (!empty($mensagem)) $msg['body'] = $mensagem;
        if (!empty($titulo)) $msg['title'] = $titulo;

        //verifica a platform
        $plataforma = 'apns'; //apple
        $fields = array(        
            'RegId' => $regIds,
            'Message' => $mensagem,
            "Silent" => false,
            "Action" => "action_a"
        );
        if($platform == 'android') {
            $plataforma = 'fcm';//android
            $fields['Title'] = (!empty($titulo)) ? $titulo : '';
        }

        $fields['PNS'] = $plataforma;

        $headers = array(
            'Content-Type: application/json'
        );

        // echo json_encode( $fields );exit;
        
        // $url_azure = "https://ithealthnoficationhub.azurewebsites.net/api/DirectSend?code=".$serverKey;
        $url_azure = "https://ithealthhubnotification.azurewebsites.net/api/DirectSend?code=".$serverKey;

        // debug(json_encode( $fields ));//exit;

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $url_azure );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_VERBOSE, false);
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );

        // print_r($ch); print_r($result); // exit;

        curl_close( $ch );
        
        return $result;
    }

    /**
     * [generateSasToken token]
     * @param  [type] $uri         [description]
     * @param  [type] $sasKeyName  [description]
     * @param  [type] $sasKeyValue [description]
     * @return [type]              [description]
     */
    public function generateSasToken($uri, $sasKeyName, $sasKeyValue) 
    { 
        $targetUri = strtolower(rawurlencode(strtolower($uri))); 
        $expires = time();     
        $expiresInMins = 60; 
        $week = 60*60*24*7;
        $expires = $expires + $week; 
        $toSign = $targetUri . "\n" . $expires; 
        $signature = rawurlencode(base64_encode(hash_hmac('sha256', $toSign, $sasKeyValue, TRUE))); 

        $token = "SharedAccessSignature sr=" . $targetUri . "&sig=" . $signature . "&se=" . $expires .         "&skn=" . $sasKeyName; 
        return $token; 
    }//fim generateSasToken($uri, $sasKeyName, $sasKeyValue)

    public function get_registration($auth,$reg_id = null) 
    {

        $url = "https://ithealth.servicebus.windows.net/ithealth-notification-hub/registrations/?api-version=2015-01";
        if(!is_null($reg_id)) {
            $url = "https://ithealth.servicebus.windows.net/ithealth-notification-hub/registrations/{$reg_id}?api-version=2015-01";
        }

        $headers = array(
            'Authorization: '.$auth,
            'Content-Type: application/atom+xml;type=entry;charset=utf-8',
            'x-ms-version: 2015-01'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $url );
        curl_setopt( $ch,CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_VERBOSE, false);
        // curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        // print_r($ch); print_r($result); // exit;

        curl_close( $ch );
        
        return $result;


    }

    public function delete_registration($auth,$reg_id) 
    {

        $url = "https://ithealth.servicebus.windows.net/ithealth-notification-hub/registrations/{$reg_id}?api-version=2015-01";

        $headers = array(
            'Authorization: '.$auth,
            'Content-Type: application/atom+xml;type=entry;charset=utf-8',
            'x-ms-version: 2015-01',
            'If-Match: *'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $url );
        curl_setopt( $ch,CURLOPT_CUSTOMREQUEST, 'DELETE' );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_VERBOSE, false);
        // curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        // print_r($ch); print_r($result); // exit;

        curl_close( $ch );
        
        return $result;


    }

    public function get_instalation($auth,$reg_id) 
    {

        $url = "https://ithealth.servicebus.windows.net/ithealth-notification-hub/installations/{$reg_id}?api-version=2015-01";

        $headers = array(
            'Authorization: '.$auth,
            // 'Content-Type: application/atom+xml;type=entry;charset=utf-8',
            'Content-Type: application/json',
            'x-ms-version: 2015-01'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $url );
        curl_setopt( $ch,CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_VERBOSE, false);
        // curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        
  // CURLOPT_ENCODING => '',
  // CURLOPT_MAXREDIRS => 10,
  // CURLOPT_TIMEOUT => 0,
  // CURLOPT_FOLLOWLOCATION => true,
  // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,


        // print_r($ch); print_r($result); // exit;

        curl_close( $ch );
        
        return $result;

    }

    public function delete_instalation($auth,$reg_id) 
    {

        $url = "https://ithealth.servicebus.windows.net/ithealth-notification-hub/installations/{$reg_id}?api-version=2015-01";

        $headers = array(
            'Authorization: '.$auth,
            'Content-Type: application/atom+xml;type=entry;charset=utf-8',
            'x-ms-version: 2015-01',
            'If-Match: *'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $url );
        curl_setopt( $ch,CURLOPT_CUSTOMREQUEST, 'DELETE' );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_VERBOSE, false);
        // curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        // print_r($ch); print_r($result); // exit;

        curl_close( $ch );
        
        return $result;


    }

    public function get_tags($auth,$reg_id) 
    {

        $url = "https://ithealth.servicebus.windows.net/ithealth-notification-hub/tags/{$reg_id}/registrations?api-version=2015-01";

        $headers = array(
            'Authorization: '.$auth,
            'Content-Type: application/atom+xml;type=entry;charset=utf-8',
            'x-ms-version: 2015-01'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $url );
        curl_setopt( $ch,CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_VERBOSE, false);
        // curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        
  // CURLOPT_ENCODING => '',
  // CURLOPT_MAXREDIRS => 10,
  // CURLOPT_TIMEOUT => 0,
  // CURLOPT_FOLLOWLOCATION => true,
  // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,


        // print_r($ch); print_r($result); // exit;

        curl_close( $ch );
        
        return $result;

    }


}
?>