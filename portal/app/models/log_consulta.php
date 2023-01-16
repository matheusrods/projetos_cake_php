<?php
class LogConsulta extends AppModel {

	public $name = 'LogConsulta';
	public $tableSchema = 'portal';
	public $databaseTable = 'dbBuonny';
	public $useTable = 'log_consulta';
	public $primaryKey = 'codigo';
    public $displayField = 'descricao';
	public $actsAs = array('Secure');

    public $validate = array(
        'login' => array(
            'rule' => 'notEmpty',
            'message' => 'Login não informado',
        ),
        'url' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a URL',
        ),
        'ip' => array(
            'rule' => 'isIP',
            'message' => 'IP Inválido',
        ),
        'codigo_tipo_consulta' => array(
            'rule' => 'numeric',
            'message' => 'Tipo da Consulta não informado ou inválido',
        ),
        'foreign_key' => array(
            'rule' => 'notEmpty',
            'message' => 'Registro acessado não informado',
        ),        
    );

    /**
     * beforeSave callback
     *
     * @param $options array
     * @return boolean
     */
    public function beforeSave($options) {
        $this->LogConsultaTipo = ClassRegistry::init('LogConsultaTipo');
        $descricao_consulta = $this->LogConsultaTipo->obtemDescricao($this->data[$this->name]['codigo_tipo_consulta']);
        if (is_null($descricao_consulta)) {
            $this->invalidate('codigo_tipo_consulta','Tipo da Consulta inválido');
            return false;
        }
        $data_formatada = AppModel::dbDateToDate($this->data[$this->name]['data_inclusao']);
        $descricao = 'Consulta '.$descricao_consulta.' '.$this->data[$this->name]['foreign_key'].
                        ' realizada por '.$this->data[$this->name]['login'].' em '.$data_formatada;
        
        $this->data[$this->name]['descricao'] = $descricao;
        return true;
    }
    

    public function incluir($data) {
        $authUsuario = $_SESSION['Auth'];
        //debug($_SERVER);
        $data['login'] = $authUsuario['Usuario']['apelido'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['navegador'] = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($_SERVER['SERVER_PROTOCOL'],'HTTPS')) {
            $protocol = 'https';
        } elseif(strpos($_SERVER['SERVER_PROTOCOL'],'FTP')) {
            $protocol = 'ftp';
        } elseif(strpos($_SERVER['SERVER_PROTOCOL'],'TELNET')) {
            $protocol = 'telnet';
        } else {
            $protocol = 'http';
        }
        $data['url'] = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        if (parent::incluir($data,true)) {
            return true;
        }
        return false;
    }

    public function isIP() {
        $ip = $this->data[$this->name]['ip'];
        return Comum::isIP($ip,true);
    }

    public function converteFiltrosEmConditions($filtros) {
        $this->LogConsultaTipo = ClassRegistry::init('LogConsultaTipo');
        $conditions = Array();

        if (!empty($filtros['data_inclusao_inicial'])) {
            $hora = (!empty($filtros['hora_inclusao_inicial']) ? $filtros['hora_inclusao_inicial'].':00' : '00:00:00');
            $data_hora_inicial = $filtros['data_inclusao_inicial']." ".$hora;
            $conditions[] = Array(
                'LogConsulta.data_inclusao >='=>AppModel::dateTimeToDbDateTime2($data_hora_inicial)
            );
        }
        if (!empty($filtros['data_inclusao_final'])) {
            $hora = (!empty($filtros['hora_inclusao_final']) ? $filtros['hora_inclusao_final'].':59' : '23:59:59');
            $data_hora_final = $filtros['data_inclusao_final']." ".$hora;
            $conditions[] = Array(
                'LogConsulta.data_inclusao <='=>AppModel::dateTimeToDbDateTime2($data_hora_final)
            );
        }

        if (!empty($filtros['login'])) {
            $conditions[] = Array(
                'LogConsulta.login LIKE'=>'%'.$filtros['login'].'%'
            );
        }

        if (!empty($filtros['codigo_tipo_consulta'])) {
            $conditions[] = Array(
                'LogConsulta.codigo_tipo_consulta'=>$filtros['codigo_tipo_consulta']
            );
            if (!empty($filtros['foreign_key'])) {
                if ($filtros['codigo_tipo_consulta']==LogConsultaTipo::TIPO_CONSULTA_VEICULOS) {
                    $filtros['foreign_key'] = str_replace("-", "", $filtros['foreign_key']);
                }
                $conditions[] = Array(
                    'LogConsulta.foreign_key'=>$filtros['foreign_key']
                );                
            }
        }


        return $conditions;
    }

    public function listar($filtros) {
        $conditions = $this->converteFiltrosEmConditions($filtros);
        $order = Array('data_inclusao','codigo');
        $ret = $this->find('all',compact('conditions','order'));
        return $ret;
    }


}
?>
