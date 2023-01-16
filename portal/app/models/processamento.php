<?php
class Processamento extends AppModel {
    public $name            = 'Processamento';
    public $tableSchema     = 'dbo';
    public $databaseTable   = 'RHHealth';
    public $useTable        = 'processamentos';
    public $primaryKey      = 'codigo';
    public $actsAs          = array('Secure', 'Containable');
    public $recursive       = -1;

    public $belongsTo = array(
        'ProcessamentoStatus' => array(
            'ClassName' => 'ProcessamentoStatus',
            'foreignKey' => 'codigo_processamento_status'
        ),
        'ProcessamentoTipoArquivo' => array(
            'ClassName' => 'ProcessamentoTipoArquivo',
            'foreignKey' => 'codigo_processamento_tipo_arquivo'
        ),
        'Cliente' => array(
            'ClassName' => 'Cliente',
            'foreignKey' => 'codigo_cliente'
        ),
        'Usuario' => array(
            'ClassName' => 'Usuario',
            'foreignKey' => 'codigo_usuario_inclusao'
        ),
    );

    public function getByUser($codigo_usuario, array $FILTROS, $pagination = false){
        $joins = array(
            array(
                'table' => 'processamentos_status',
                'alias' => 'ProcessamentoStatus',
                'type' => 'INNER',
                'conditions' => array('Processamento.codigo_processamento_status = ProcessamentoStatus.codigo')
            ),
            array(
                'table' => 'processamentos_tipos_arquivos',
                'alias' => 'ProcessamentoTipoArquivo',
                'type' => 'INNER',
                'conditions' => array('Processamento.codigo_processamento_tipo_arquivo = ProcessamentoTipoArquivo.codigo')
            ),
            array(
                'table' => 'usuario',
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => array('Processamento.codigo_usuario_inclusao = Usuario.codigo')
            )
        );
        $fields = array(
            'Processamento.codigo as codigo',
            'Usuario.nome as usuario',
            'ProcessamentoTipoArquivo.descricao as tipo_arquivo',
            'ProcessamentoStatus.descricao as [status]',
            'Processamento.caminho as caminho',
            'Processamento.baixado as baixado',
            'CONCAT(Convert(varchar(10), Processamento.data_inclusao,103),\' \',convert(char(10), Processamento.data_inclusao, 108)) as data_inclusao'            
        );
        $where = array('Processamento.deletado IS NULL');
        if(isset($FILTROS['codigo_cliente'])){
            if(!empty($FILTROS['codigo_cliente']))
                $where[] = 'Processamento.codigo_cliente = '.$FILTROS['codigo_cliente'];
                $where[] = 'Processamento.codigo_usuario_inclusao = ' . $codigo_usuario;
        }else{
            $where[] = 'Processamento.codigo_usuario_inclusao = ' . $codigo_usuario;
        }

        if(!empty($FILTROS['tipo_arquivo']))
            $where[] = "Processamento.codigo_processamento_tipo_arquivo = ".$FILTROS['tipo_arquivo'];
        if(!empty($FILTROS['status']))
            $where[] = "Processamento.codigo_processamento_status = ".$FILTROS['status'];
        if(!empty($FILTROS['data_de']) && empty($FILTROS['data_ate']))
            $where[] = "CAST(Processamento.data_inclusao AS DATE) >= '".date_format(date_create_from_format('d/m/Y', $FILTROS['data_de']), 'Y-m-d')."'";
        if(!empty($FILTROS['data_de']) && !empty($FILTROS['data_ate'])){
            $where[] = "CAST(Processamento.data_inclusao AS DATE) >= '".date_format(date_create_from_format('d/m/Y', $FILTROS['data_de']), 'Y-m-d')."'"
                        ." AND CAST(Processamento.data_inclusao AS DATE) <= '".date_format(date_create_from_format('d/m/Y', $FILTROS['data_ate']), 'Y-m-d')."'";
        }

        // pr($this->find('sql', array('joins' => $joins, 'fields' => $fields, 'conditions' => $where)));

        if($pagination){
            $paginate = array(
                'joins' => $joins, 'fields' => $fields, 'conditions' => $where,
                'limit' => (!empty($FILTROS['limit']) ? $FILTROS['limit'] : 50),
                'order' => 'Processamento.data_inclusao DESC, Processamento.codigo DESC'
            );
            return $paginate;
        }else{
            return $this->find('all', array('joins' => $joins, 'fields' => $fields, 'conditions' => $where));
        }
    }

    public function contagem($codigo){
        try{
            $p = $this->find('first', array('fields' => array('Processamento.baixado', 'Processamento.codigo'), 'conditions' => array('Processamento.codigo' => $codigo)));
            $p['Processamento']['baixado'] = $p['Processamento']['baixado'] + 1;
            $this->save($p);
            return true;
        }catch(Exception $ex){
            $this->log("ERROR - Erro ao tentar atualizar contagem do processamento: " . $ex->getMessage());
            return false;
        }
    }

    public function shellGetAllFilesFromDaysAgo($days = 4){
        $where = array(
            "DATEDIFF(day, Processamento.data_inclusao, CURRENT_TIMESTAMP) >= ".$days,
            "deletado IS NULL"
        );
        $fields = array(
            "Processamento.codigo as codigo",
            "Processamento.caminho as caminho"
        );
        $urls = $this->find('all', array('fields' => $fields, 'conditions' => $where));
        return $urls;
    }

    public function shellSetAsDeletedFile($codigo){
        try{
            $this->updateAll(array(
                'Processamento.deletado' => "CURRENT_TIMESTAMP"
            ),
            array(
                'Processamento.codigo' => $codigo
            ));
            return true;
        }catch(Exception $ex){
            $this->log("ERROR - SHELL: Não foi possivel atualizar processamento como deletado - codigo:{$codigo} - ".$ex->getMessage());
            return false;
        }
    }
}