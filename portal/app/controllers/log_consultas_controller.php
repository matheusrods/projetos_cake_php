<?php
class LogConsultasController extends AppController {
    public $name = 'LogConsultas';
    
    function beforeFilter() {
        parent::beforeFilter();
        //$this->BAuth->allow('*');
    }
    
    function index() {
        $this->loadModel('LogConsultaTipo');
        $this->pageTitle = 'Rastreabilidade de Veículos';
        
        if ($this->RequestHandler->isPost()) {
            $this->Filtros->limpa_sessao('LogConsulta');
        } else {
            $this->data['LogConsulta'] = Array(
                'data_inclusao_inicial' => date('d/m/Y'),
                'data_inclusao_final' => date('d/m/Y'),
                'hora_inclusao_inicial' => '00:00',
                'hora_inclusao_final' => '23:59',
            );
        }
        
        $this->data['LogConsulta'] = $this->Filtros->controla_sessao($this->data, "LogConsulta");

        $tipos_consulta = $this->LogConsultaTipo->listarTipoConsulta('list');

        
        $this->set(compact('tipos_consulta'));
    }
    
    function listagem() {
        $this->loadModel('LogConsultaTipo');
        $this->loadModel('Usuario');
        $this->loadModel('Cliente');
        $this->loadModel('Departamento');
        $this->loadModel('Uperfil');
        $this->layout = 'ajax';
        
        $filtros = $this->Filtros->controla_sessao($this->data, "LogConsulta");        

        if (empty($filtros['data_inclusao_inicial']) || 
                empty($filtros['data_inclusao_final']) ||
                empty($filtros['hora_inclusao_inicial']) ||
                empty($filtros['hora_inclusao_final']) ) {
            exit;
        }

        if(!empty($filtros['data_inclusao_inicial']) && !empty($filtros['data_inclusao_final'])){
            $data_inicial = strtotime(AppModel::dateToDbDate($filtros['data_inclusao_inicial']));
            $data_final = strtotime(AppModel::dateToDbDate($filtros['data_inclusao_final']));
            if (floor(($data_final - $data_inicial)/(60*60*24)) > 31){
                exit;
            }
        }       
        $fields = Array(
            'LogConsulta.codigo',
            'LogConsulta.login',
            'LogConsulta.ip',
            'LogConsulta.codigo_usuario_inclusao',
            'LogConsulta.codigo_tipo_consulta',
            'LogConsulta.foreign_key',
            'CONVERT(varchar, LogConsulta.data_inclusao, 103) as dt_inclusao',
            'CONVERT(varchar, LogConsulta.data_inclusao, 108) as hora_inclusao',
            'Usuario.codigo',
            'Usuario.codigo_cliente',
            'Cliente.razao_social',
            'Departamento.descricao',
            'Uperfil.descricao',
        );
        
        $conditions = $this->LogConsulta->converteFiltrosEmConditions($filtros);

        $joins = Array(
            array(
                'table'      => "{$this->Usuario->databaseTable}.{$this->Usuario->tableSchema}.{$this->Usuario->useTable}",
                'alias'      => 'Usuario',
                'type'       => 'INNER',
                'conditions' => 'LogConsulta.codigo_usuario_inclusao = Usuario.codigo'
            ),
            array(
                'table'      => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                'alias'      => 'Cliente',
                'type'       => 'LEFT',
                'conditions' => 'Usuario.codigo_cliente = Cliente.codigo'
            ),
            array(
                'table'      => "{$this->Departamento->databaseTable}.{$this->Departamento->tableSchema}.{$this->Departamento->useTable}",
                'alias'      => 'Departamento',
                'type'       => 'LEFT',
                'conditions' => 'Usuario.codigo_departamento = Departamento.codigo'
            ),
            array(
                'table'      => "{$this->Uperfil->databaseTable}.{$this->Uperfil->tableSchema}.{$this->Uperfil->useTable}",
                'alias'      => 'Uperfil',
                'type'       => 'LEFT',
                'conditions' => 'Usuario.codigo_uperfil = Uperfil.codigo'
            ),            

        );

        $this->paginate = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => Array('LogConsulta.data_inclusao','LogConsulta.codigo'),
        );

        $logs = $this->paginate();
        $tipos_consulta = $this->LogConsultaTipo->listarTipoConsulta('list');
        $this->set(compact('logs','tipos_consulta','filtros'));
    }
    
}
