<?php
App::import('Controller', 'Filtros');
class RegistrosTelecomController extends AppController {
	public $name = 'RegistrosTelecom';
    public $layout = 'cliente';
    public $components = array('RequestHandler');
    public $helpers = array('Html', 'Ajax', 'BForm', 'Buonny', 'Ajax','Highcharts');
	public $uses = array('RegistroTelecom');


    function importar_arquivo() {
        $this->pageTitle = 'Importar arquivo';
        $this->loadModel('RegistroTelecom');
        $this->loadModel('Usuario');
        $operadoras = array(
            '1'  => 'Vivo', 
            '2'  => 'Claro',   
            '3'  => 'Nextel',
            '4'  => 'Tarifador');
        if(!empty($this->data)) {
            if(!is_null($this->data['RegistroTelecom']['arquivo']['name'])) {
                if (strpos($this->data['RegistroTelecom']['arquivo']['name'], ".xls") > 0) {
                    if ($this->data['RegistroTelecom']['arquivo']['size']>3000000) {
                        $this->BSession->setFlash(array(MSGT_ERROR, 'Tamanho do arquivo não pode sem maior que 3MB'));    
                        $this->set(compact('operadoras'));
                        return;
                    }

                    $tipo_arquivo = (isset($operadoras[$this->data['RegistroTelecom']['tipo_arquivo']]) ? $operadoras[$this->data['RegistroTelecom']['tipo_arquivo']] : '');

                    $destino = APP . "tmp" . DS .$tipo_arquivo.time().".tmp";
                    if (file_exists($destino))
                        unlink($destino);
                    move_uploaded_file($this->data['RegistroTelecom']['arquivo']['tmp_name'], $destino);
                    $nome_arquivo = $this->data['RegistroTelecom']['arquivo']['name'];
                    $resultados = array();                    
                    $resultados = $this->RegistroTelecom->carregarArquivoCsv($destino, $tipo_arquivo);
                    if(isset($resultados['fatal'])) {
                        $this->BSession->setFlash(array(MSGT_ERROR, $resultados['fatal'])); 
                        $resultados = Array();
                    } else {
                        $this->BSession->setFlash(array(MSGT_SUCCESS, 'Importação realizada com sucesso')); 
                    }

                    if ($this->data['RegistroTelecom']['tipo_arquivo']!=3) $this->sortRetorno($resultados);

                    $this->set(compact('resultados'));
                } else {
                    $this->BSession->setFlash('invalid_file');
                }
            } else {
                $this->BSession->setFlash('no_file');
            }
        }
       
        if (file_exists($destino))
                unlink($destino);
        $this->set(compact('operadoras'));
    }

    private function sortRetorno(&$arrayRetorno) {
        $linha = Array();
        $tipo_retorno = Array();
        foreach ($arrayRetorno as $key => $row) {
            $linha[$key] = $row['linha'];
            $tipo_retorno[$key] = (isset($row['tipo_retorno']) ? $row['tipo_retorno'] : '');
        }
        array_multisort($linha,SORT_ASC,$tipo_retorno,SORT_ASC,$arrayRetorno);
    }

    function carrega_combos() {
        $anos = Comum::listAnos(date('Y')-2);
        $meses = Comum::listMeses(true);
        $this->loadModel('TipoRetorno');

        $operadoras = array(
            Registrotelecom::VIVO  => 'Vivo (Celular)', 
            Registrotelecom::CLARO  => 'Claro (Celular)',   
            Registrotelecom::NEXTEL  => 'Nextel (Radio)',
            Registrotelecom::TARIFADOR  => 'Tarifador (Ramal)');

        $this->loadModel('Departamento');
        $departamentos = $this->Departamento->find('list');

        $conditions['TipoRetorno.usuario_interno'] = true;
        $tipo_cobranca = $this->RegistroTelecom->TipoRetorno->find('list', array('conditions' => $conditions));

        $isPost = ($this->RequestHandler->isAjax() || $this->RequestHandler->isPost());

        $this->set(compact('meses','anos','isPost','tipos_retorno', 'operadoras', 'tipo_contato', 'tipo_cobranca', 'departamentos'));
    }

    function analitico() {
        $this->pageTitle = 'Registros Telecom';
        $this->data['RegistroTelecom'] = $this->Filtros->controla_sessao($this->data, "RegistroTelecom");      
        $this->carrega_combos();
        $this->data['RegistroTelecom']['mes'] = date('m');
        $this->data['RegistroTelecom']['ano'] = date('Y');
    }

    function analitico_listagem($export = false) {
        $this->layout = 'ajax';
        $this->loadModel('Uperfil');
        $filtros['RegistroTelecom'] = $this->Filtros->controla_sessao($this->data, "RegistroTelecom"); 
        if (!isset($filtros['RegistroTelecom']['ano'])) $filtros['RegistroTelecom']['ano'] = date('Y');
        if (!isset($filtros['RegistroTelecom']['mes'])) $filtros['RegistroTelecom']['mes'] = date('m');

        $order = Array('identificador','codigo_tipo_retorno');
        $conditions = $this->RegistroTelecom->converteFiltroEmCondition($filtros['RegistroTelecom']);
        $registros_telecom = array();
        $fields = array("(CASE
                            WHEN RegistroTelecom.codigo_operadora = ".RegistroTelecom::CLARO." THEN 'Claro'
                            WHEN RegistroTelecom.codigo_operadora = ".RegistroTelecom::VIVO." THEN 'Vivo'
                            WHEN RegistroTelecom.codigo_operadora = ".RegistroTelecom::NEXTEL." THEN 'Nextel'
                            WHEN RegistroTelecom.codigo_operadora = ".RegistroTelecom::TARIFADOR." THEN 'Tarifador'
                        END)                                    AS operadora_descricao",
                        'RegistroTelecom.identificador          AS identificador',
                        'RegistroTelecom.valor                  AS valor',
                        'RegistroTelecom.quantidade             AS quantidade',
                        'RegistroTelecom.codigo_tipo_retorno    AS codigo_tipo_retorno',
                        'Usuario.apelido                        AS apelido',
                        'Usuario.nome                           AS nome',
                        'TipoRetorno.descricao                  AS tipo_retorno_descricao',
                        'Departamento.descricao                 AS departamento_descricao',
            );
        if($_SESSION['Auth']['Usuario']['codigo_uperfil'] == Uperfil::ADMIN
            || $_SESSION['Auth']['Usuario']['admin'] 
        ){
            $extra = null;
        }else{
            $extra = array('method' => 'somente_subordinados');
        }
        $this->paginate['RegistroTelecom']  = array(
            'conditions'    => $conditions,
            'limit'         => 50,
            'fields'        => $fields,
            'order'         => $order,
            'extra'         => $extra
        );
        $query = '';
        if($export) {
            if($_SESSION['Auth']['Usuario']['codigo_uperfil'] != Uperfil::ADMIN
                &&  !$_SESSION['Auth']['Usuario']['admin'] 
            ){
                $codigo_usuario_logado = $_SESSION['Auth']['Usuario']['codigo'];
                $conditions[] = 'Usuario.codigo in (SELECT codigo FROM tblFilhos) ';
                $query = 'WITH tblFilhos AS 
                        (SELECT UsuarioPai.codigo,UsuarioPai.apelido,UsuarioPai.codigo_usuario_pai 
                        FROM dbBuonny.portal.usuario AS UsuarioPai 
                        WHERE UsuarioPai.codigo_usuario_pai = '.$codigo_usuario_logado.'
                        UNION ALL   
                        SELECT UsuarioFilho.codigo,UsuarioFilho.apelido,UsuarioFilho.codigo_usuario_pai 
                        FROM dbBuonny.portal.usuario AS UsuarioFilho 
                        JOIN tblFilhos ON ( usuarioFilho.codigo_usuario_pai = tblFilhos.codigo ) ) ';
            }           
            $this->gerarExcel($query . $this->RegistroTelecom->find('sql', compact('conditions', 'fields', 'order', 'joins', 'group')));
        }

        $fields = array('COUNT(distinct RegistroTelecom.codigo_usuario_registro) as registros',
            'COUNT(distinct RegistroTelecom.identificador) as identificadores',
            'SUM(RegistroTelecom.valor) as valor',
            'SUM(RegistroTelecom.quantidade) as quantidade',
            );

        $query .= $this->RegistroTelecom->find('sql', array('conditions' => $conditions, 'fields' => $fields));
        $resultado = $this->RegistroTelecom->query($query);        
        $resultado = current($resultado);

        $registros_telecom = $this->paginate('RegistroTelecom'); 
        $this->set(compact('registros_telecom','resultado'));
    }

    public function sintetico() {
        $this->pageTitle = 'Registro Telecom';
        $this->data['RegistrosTelecom'] = $this->Filtros->controla_sessao($this->data, "RegistroTelecom");
        $this->data['RegistroTelecom']['mes'] = (isset($filtros['RegistroTelecom']['mes']) ? $filtros['RegistroTelecom']['mes'] : date('m'));
        $this->data['RegistroTelecom']['ano'] = (isset($filtros['RegistroTelecom']['ano']) ? $filtros['RegistroTelecom']['ano'] : date('Y'));

        $filtrado = FALSE;
        $agrupamento = $this->RegistroTelecom->listarAgrupamentos();
        $this->carrega_combos();
        $this->set(compact('agrupamento', 'filtrado'));
    }

    public function sintetico_listagem() {
        $this->data['RegistroTelecom'] = $this->Filtros->controla_sessao($this->data, "RegistroTelecom");
        $conditions = $this->RegistroTelecom->converteFiltroEmCondition($this->data['RegistroTelecom']);

        $agrupamento = $this->data['RegistroTelecom']['agrupamento'];
        $registros_telecom = $this->RegistroTelecom->sintetico($conditions,$agrupamento);

        if(!empty($registros_telecom)){
            $this->sintetico_grafico($registros_telecom,$agrupamento);
        }
        $quantia_registros = count($registros_telecom);
        $this->set(compact('registros_telecom','agrupamento', 'quantia_registros'));
    }

    function sintetico_grafico($registros_telecom,$agrupamento){
        foreach ($registros_telecom as $registros) {
            $valor_formatado = number_format($registros[0]['valor'], 2, '.', '');
            $valor[] = $valor_formatado;
            if(empty($registros[0]['descricao'])) {
                $descricao[] = "'Sem departamentos'";
            }elseif(utf8_encode(($registros[0]['descricao'])) == 'TECNOLOGIA DA INFORMAÇÃO') {
                $descricao[] = "'TI'";
            }else{
                $descricao[] = "'".utf8_encode(($registros[0]['descricao']))."'";
            }
        }


        $descricao_agrupamento = $this->RegistroTelecom->retornaAgrupamento($agrupamento);
        $dadosGrafico['eixo_x'] = $descricao;
        $dadosGrafico['series'] =  array(
            array(
                'name' => "'$descricao_agrupamento'",
                'values' => $valor
            )
        );            
        $this->set(compact('dadosGrafico'));
    }


    private function gerarExcel($query) {
        $dbo = $this->RegistroTelecom->getDataSource();
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename("registros_telecom_".date('d-m-Y_His').".csv")));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', '"Nome";"Login";"Operadora";"Tipo Cobrança";"Contato";"Departamento";"Quantia";"Valor";')."\n";
        $dados = $dbo->fetchAll($query);
        foreach ($dados as $dado) {
            $linha = '"'.$dado[0]['nome'].'";';
            $linha .= '"'.$dado[0]['apelido'].'";';
            $linha .= '"'.$dado[0]['operadora_descricao'].'";';
            $linha .= '"'.$dado[0]['tipo_retorno_descricao'].'";';
            $linha .= '"'.$dado[0]['identificador'].'";';
            $linha .= '"'.$dado[0]['departamento_descricao'].'";';
            $linha .= '"'.number_format($dado[0]['quantidade'], 2).'";';
            $linha .= '"'.number_format($dado[0]['valor'], 2).'";';
            $linha .= "\n";
            echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }
        die();
    }

}
?>