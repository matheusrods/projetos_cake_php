<?php
class ModsIvrsPesquisasController extends AppController {
    public $name = 'ModsIvrsPesquisas';
    public $helpers = array('BForm', 'Buonny', 'Ajax','Highcharts');

    var $uses = array(
        'ModIvrPesquisa'
    );

    function carregar_combos() {
        $this->loadmodel('Departamento');
        $this->data['ModIvrPesquisa'] = $this->Filtros->controla_sessao($this->data, "ModIvrPesquisa");
        if (empty($this->data['ModIvrPesquisa'])) {
            $this->data['ModIvrPesquisa']['startq'] = date('d/m/Y');
            $this->data['ModIvrPesquisa']['endq'] = date('d/m/Y');
            $this->data['ModIvrPesquisa']['status'] = 2;
            $this->data['ModIvrPesquisa']['agrupamento'] = 1;
        }
        $status = array('1' => 'Não Avaliada', '2' => 'Avaliada');
        $pontuacao = array('0', '1', '2', '3', '4', '5');
        $agrupamento = array(1 => 'Departamentos', 2 => 'Ramal');
        $departamento = $this->Departamento->find('list');
        $departamento += array('99' => 'Sem Departamento');
        $this->set(compact('status', 'pontuacao', 'agrupamento', 'departamento'));
    }

    function sintetico() {
        $this->pageTitle = 'Avaliação Ura - Sintético';
        $this->carregar_combos();
        $filtrado = false;
        $this->set(compact('filtrado'));
    }

    function sintetico_listagem () {
        $this->pageTitle = 'Avaliação Ura - Sintético';
        $this->loadmodel('Departamento');
        $this->data['ModIvrPesquisa'] = $this->Filtros->controla_sessao($this->data, "ModIvrPesquisa");
        $filtros['ModIvrPesquisa'] = $this->Filtros->controla_sessao($this->data, "ModIvrPesquisa");
        if (empty($filtros['ModIvrPesquisa']['startq'])) {
            $filtros['ModIvrPesquisa']['startq'] = date('d/m/Y');
            $filtros['ModIvrPesquisa']['endq'] = date('d/m/Y');
            $filtros['ModIvrPesquisa']['status'] = 1;
            $filtros['ModIvrPesquisa']['agrupamento'] = 1;
        }
        $this->ModIvrPesquisa->bindUsuarioComDepartamento();


        $conditions = $this->ModIvrPesquisa->converteFiltroEmCondition($filtros);

        switch ($filtros['ModIvrPesquisa']['agrupamento']) {
            case 1: //Departamento
                $campo_agrupamento = 'Departamento.codigo';
                break;
            case 2: //Ramais    
                $campo_agrupamento = 'UsuarioContato.descricao';
                break;
            default:
                $campo_agrupamento = 'Departamento.codigo';
                break;
        }
        $fields = array(
            $campo_agrupamento.' AS codigo',
            'COUNT(ModIvrPesquisa.score) AS total',
            'SUM(CASE WHEN ModIvrPesquisa.score = 0 AND ModIvrPesquisa.status = 0 THEN 1 ELSE 0 END) AS qta_pt_null',
            'SUM(CASE WHEN ModIvrPesquisa.score = 0 AND ModIvrPesquisa.status = 1 THEN 1 ELSE 0 END) AS qta_pt0',
            'SUM(CASE WHEN ModIvrPesquisa.score = 1 THEN 1 ELSE 0 END) AS qta_pt1',
            'SUM(CASE WHEN ModIvrPesquisa.score = 2 THEN 1 ELSE 0 END) AS qta_pt2',
            'SUM(CASE WHEN ModIvrPesquisa.score = 3 THEN 1 ELSE 0 END) AS qta_pt3',
            'SUM(CASE WHEN ModIvrPesquisa.score = 4 THEN 1 ELSE 0 END) AS qta_pt4',
            'SUM(CASE WHEN ModIvrPesquisa.score = 5 THEN 1 ELSE 0 END) AS qta_pt5',
        );
        $group = array($campo_agrupamento);
        $order = 'codigo ASC';
        $limit = 50;

        $this->paginate['ModIvrPesquisa']  = compact('conditions', 'fields', 'order', 'joins', 'limit', 'group');
        //debug($this->ModIvrPesquisa->find('sql',compact('conditions', 'fields', 'order', 'joins', 'limit', 'group')));

        $registros_ura = $this->paginate('ModIvrPesquisa');
        $agrupamento = $filtros['ModIvrPesquisa']['agrupamento'];
         if($agrupamento == 1) {
            foreach ($registros_ura as $key => $registro) {
                $conditions_departamento['Departamento.codigo'] = $registro[0]['codigo'];
                $fields = 'Departamento.descricao';
                $departamento_desc = $this->Departamento->find('first', array('conditions' => $conditions_departamento, 'fields' => $fields));
                $registros_ura[$key][0]['descricao'] = $departamento_desc['Departamento']['descricao'];
            }
        } else {
             foreach ($registros_ura as $key => $registro) {
                $registros_ura[$key][0]['descricao'] = $registro[0]['codigo'];
             }
        }
        $this->set(compact('registros_ura', 'agrupamento'));
    }

    function sintetico_grafico() {
        $this->data['ModIvrPesquisa'] = $this->Filtros->controla_sessao($this->data, "ModIvrPesquisa");
        $filtros['ModIvrPesquisa'] = $this->Filtros->controla_sessao($this->data, "ModIvrPesquisa");
        if (empty($filtros['ModIvrPesquisa']['startq'])) {
            $filtros['ModIvrPesquisa']['startq'] = date('d/m/Y');
            $filtros['ModIvrPesquisa']['endq'] = date('d/m/Y');
            $filtros['ModIvrPesquisa']['status'] = 1;
            $filtros['ModIvrPesquisa']['agrupamento'] = 1;
        }
        if(!isset($filtros['ModIvrPesquisa']['agrupamento'])) {
            $filtros['ModIvrPesquisa']['agrupamento'] = null;
        }
        if(!isset($filtros['ModIvrPesquisa']['departamento'])) {
            $filtros['ModIvrPesquisa']['departamento'] = null;
        }
        $conditions = $this->ModIvrPesquisa->converteFiltroEmCondition($filtros);

        $this->gerar_grafico($conditions, $filtros['ModIvrPesquisa']['agrupamento'], $filtros['ModIvrPesquisa']['departamento']);
    }
    
    function gerar_grafico ($conditions, $agrupamento = null, $departamento = null) {
        $this->loadmodel('UsuarioContato');
        $this->loadmodel('Departamento');
        $this->ModIvrPesquisa->bindUsuarioComDepartamento();
        $sem_valor = $agrupamento == 1 ? 'Sem Departamento' : 'Sem Ramal';
        switch ($agrupamento) {
            case 1: //Departamento
                $campo_agrupamento = 'Departamento.codigo';
                break;
            case 2: //Ramais
                $campo_agrupamento = 'UsuarioContato.descricao';
                break;
            default:
                $campo_agrupamento = 'Departamento.codigo';
                break;
        }
        // Todos os departamentos e ramal (Gráfico Colunas)
        if(empty($departamento) || $agrupamento == 2) {
            $fields = array(
            $campo_agrupamento." AS codigo",
            'COUNT(ModIvrPesquisa.score) AS total',
            'SUM(CASE WHEN ModIvrPesquisa.score = 0 AND ModIvrPesquisa.status = 0 THEN 1 ELSE 0 END) AS qta_pt_null',
            'SUM(CASE WHEN ModIvrPesquisa.score = 0 AND ModIvrPesquisa.status = 1 THEN 1 ELSE 0 END) AS qta_pt0',
            'SUM(CASE WHEN ModIvrPesquisa.score = 1 THEN 1 ELSE 0 END) AS qta_pt1',
            'SUM(CASE WHEN ModIvrPesquisa.score = 2 THEN 1 ELSE 0 END) AS qta_pt2',
            'SUM(CASE WHEN ModIvrPesquisa.score = 3 THEN 1 ELSE 0 END) AS qta_pt3',
            'SUM(CASE WHEN ModIvrPesquisa.score = 4 THEN 1 ELSE 0 END) AS qta_pt4',
            'SUM(CASE WHEN ModIvrPesquisa.score = 5 THEN 1 ELSE 0 END) AS qta_pt5',
            );
            $group = array($campo_agrupamento);
            $order = 'codigo ASC';
        }
        // Departamento especifico (Gráfico Pizza)
        if($departamento >= 1 && $agrupamento == 1) {
            $fields = array(
                "ModIvrPesquisa.score AS codigo",
                'COUNT(ModIvrPesquisa.score) AS total',
            );
            $group = array(
                'ModIvrPesquisa.score',
            );
            $order = 'codigo ASC';
        }
        $sql_exportacao = $this->ModIvrPesquisa->find('sql',array(
            'fields' => $fields,
            'group' => $group,
            'order' => $order,
            'conditions' => $conditions,
        ));

        $registros_ura = $this->ModIvrPesquisa->query($sql_exportacao);
        if($agrupamento == 1 && $departamento < 1) {
            foreach ($registros_ura as $key => $registro) {
                $conditions_departamento['Departamento.codigo'] = $registro[0]['codigo'];
                $fields = 'Departamento.descricao';
                $departamento_desc = $this->Departamento->find('first', array('conditions' => $conditions_departamento, 'fields' => $fields));
                $registros_ura[$key][0]['descricao'] = $departamento_desc['Departamento']['descricao'];
            }
        } else {
             foreach ($registros_ura as $key => $registro) {
                $registros_ura[$key][0]['descricao'] = $registro[0]['codigo'];
             }
        }
        if(!empty($registros_ura)) {
             // Todos os departamentos e ramal (Gráfico Colunas)
            if(empty($departamento) || $agrupamento == 2) {
                foreach ($registros_ura as $key => $registro_ura) {
                    $total[]  = ($registro_ura[0]['total']);
                    $qtd_null[] = $registro_ura[0]['qta_pt_null']; 
                    $qtd_0[] = $registro_ura[0]['qta_pt0']; 
                    $qtd_1[] = $registro_ura[0]['qta_pt1'];  
                    $qtd_2[] = $registro_ura[0]['qta_pt2']; 
                    $qtd_3[] = $registro_ura[0]['qta_pt3']; 
                    $qtd_4[] = $registro_ura[0]['qta_pt4'];
                    $qtd_5[] = $registro_ura[0]['qta_pt5'];
                    $descricao[] = "'".(!empty($registro_ura[0]['descricao']) ? utf8_encode($registro_ura[0]['descricao']) : $sem_valor)."'";
                    $dadosGrafico['series'][$key]['name'] = "'".$registro_ura[0]['descricao']."'";
                    $dadosGrafico['series'][$key]['values'] = "".$registro_ura[0]['qta_pt0']."";
                }
                $dadosGrafico['series'] =  array(
                    array(
                        'name' => "'Sem nota'",
                        'values' => $qtd_null,
                    ),
                    array(
                        'name' => "'Nota 0'",
                        'values' => $qtd_0,
                    ),
                    array(
                        'name' => "'Nota 1'",
                        'values' => $qtd_1,
                    ),
                    array(
                        'name' => "'Nota 2'",
                        'values' => $qtd_2,
                    ),
                    array(
                        'name' => "'Nota 3'",
                        'values' => $qtd_3,
                    ),
                    array(
                        'name' => "'Nota 4'",
                        'values' => $qtd_4,
                    ),
                    array(
                        'name' => "'Nota 5'",
                        'values' => $qtd_5,
                    )
                );
                $dadosGrafico['tipo'] = 'column';
            }

            // Departamento especifico (Gráfico Pizza)
           if($departamento >= 1 && $agrupamento == 1) {
                foreach ($registros_ura as $key => $registro_ura) {
                    $descricao[] = "'".(!empty($registro_ura[0]['descricao']) ? utf8_encode($registro_ura[0]['descricao']) : 'Sem departamento')."'";
                    $dadosGrafico['series'][$key]['name'] = "'Nota ".$registro_ura[0]['descricao']."'";
                    $dadosGrafico['series'][$key]['values'] = "".$registro_ura[0]['total']."";
                }
                $dadosGrafico['tipo'] = 'pie' ;
                $titulo_departamento = $this->Departamento->carregar($agrupamento);
                $titulo = $titulo_departamento['Departamento']['descricao'];
            }
            $dadosGrafico['eixo_x'] = $descricao;
            $this->set(compact('dadosGrafico', 'titulo'));
        }//fim if registro_ura
    }//fim gerar_grafico

    function analitico($new_window = FALSE) {
        $this->pageTitle = 'Avaliação Ura - Analítico';
        $this->carregar_combos();
    }

    function analitico_listagem($export = false) {
        $this->pageTitle = 'Avaliação Ura - Analítico';
        $this->loadmodel('UsuarioContato');
        $this->loadmodel('Departamento');
        $filtros['ModIvrPesquisa'] = $this->Filtros->controla_sessao($this->data, 'ModIvrPesquisa');
        $conditions = $this->ModIvrPesquisa->converteFiltroEmCondition($filtros);
        $fields = array('ModIvrPesquisa.odnis as odnis',
                        'ModIvrPesquisa.logid as logid',
                        'ModIvrPesquisa.startivr as startivr',
                        'ModIvrPesquisa.endivr as endivr',
                        'ModIvrPesquisa.startq as startq',
                        'ModIvrPesquisa.endq as endq',
                        'ModIvrPesquisa.oani as oani',
                        'ModIvrPesquisa.otrkid as otrkid',
                        'ModIvrPesquisa.[queue] as [queue]',
                        'ModIvrPesquisa.agtext as agtext',
                        'ModIvrPesquisa.agtid as agtid',
                        'ModIvrPesquisa.status as status',
                        'ModIvrPesquisa.score as score',
                        'UsuarioContato.descricao as ramal',
                        'Departamento.descricao as descricao',);
        $order = 'ramal ASC';
        $limit = '50';

        $this->ModIvrPesquisa->bindUsuarioComDepartamento();

        $this->paginate['ModIvrPesquisa']  = array(
            'conditions'    => $conditions,
            'limit'         => $limit,
            'fields'        => $fields,
            'order'         => $order,
        );

        //debug($this->ModIvrPesquisa->find('sql', compact('conditions', 'fields', 'order')));
        $registros_ura = $this->paginate('ModIvrPesquisa');
        if($export) {
            $this->export_analitico_listagem($dados);
        }
        $this->set(compact('registros_ura'));
    }

    function export_analitico_listagem($query) {
        $dbo = $this->ModIvrPesquisa->getDataSource();
        $dbo->results = $dbo->_execute($query);
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('relatorio_chamadas.csv')));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', '"Ramal";"Inicio da pesquisa";"Fim da pesquisa";"Telefone Origem";"Tronco";"Status";"Pontuação";')."\n";
        while ($dado = $dbo->fetchRow()) {
            $linha  ='"'.$dado[0]['agtext'].'";';
            $linha .='"'.date('d/m/Y H:i', strtotime(str_replace('/', '-',$dado[0]['startq']))).'";';
            $linha .='"'.date('d/m/Y H:i', strtotime(str_replace('/', '-',$dado[0]['endq']))).'";';
            $linha .='"'.$dado[0]['odnis'].'";';
            $linha .='"'.$dado[0]['queue'].'";';
            $linha .='"'.(($dado[0]['status'] == 0) ? utf8_encode("Não Avaliada") : 'Avaliada').'";';
            $linha .='"'.$dado[0]['score'].'";';
            $linha .= "\n";
            echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }
        die();
    }

}

