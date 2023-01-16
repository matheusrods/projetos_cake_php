<?php
App::import('Model', 'TipoRetorno');
App::import('Model', 'Usuario');
App::import('Model', 'UsuarioContato');

class RegistroTelecom extends AppModel {
    var $name = 'RegistroTelecom';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'registros_telecom';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    //Operadoras
    CONST CLARO = 1;
    CONST VIVO = 2;
    CONST NEXTEL = 3;
    CONST TARIFADOR = 4;


    //Agrupamentos
    CONST LOGIN = 1;
    CONST DEPARTAMENTO = 2;
    CONST OPERADORA = 3;

    function listarAgrupamentos() {
        return array(
            self::OPERADORA => 'Operadora',
            self::DEPARTAMENTO => 'Departamento',
            self::LOGIN => 'Login',
        );
    }

    function retornaAgrupamento($codigo){
        switch ($codigo) {
            case self::LOGIN:
                $retorno = 'Login';
                break;
            case self::DEPARTAMENTO:
                $retorno = 'Departamento';
                break;
            case self::OPERADORA:
                $retorno = 'Operadora';
                break;
            default:
                $retorno = 'Codigo não Encontrado';
                break;
        }
        return $retorno;
    }

    var $belongsTo = array(
        'Usuario' => array(
            'className' => 'Usuario',
            'foreignKey' => 'codigo_usuario_registro',
        ),
         'TipoRetorno' => array(
            'className' => 'TipoRetorno',
            'foreignKey' => 'codigo_tipo_retorno',
        ),
         'Departamento' => array(
            'className' => 'Departamento',
            'foreignKey' => false,
            'conditions' => 'Usuario.codigo_departamento = Departamento.codigo')
    );

    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = 1, $extra = array()) {
        $joins = null;
        if (isset($extra['joins']))
            $joins = $extra['joins'];
        if (isset($extra['group']))
            $group = $extra['group'];        
        if( isset( $extra['extra']['method'] ) && $extra['extra']['method'] == 'somente_subordinados' ){
            $codigo_usuario_logado = $_SESSION['Auth']['Usuario']['codigo'];
            $query = '';
            $conditions[] = 'Usuario.codigo in (SELECT codigo FROM tblFilhos) ';
            $query = 'WITH tblFilhos AS 
                    (SELECT UsuarioPai.codigo,UsuarioPai.apelido,UsuarioPai.codigo_usuario_pai 
                    FROM dbBuonny.portal.usuario AS UsuarioPai 
                    WHERE UsuarioPai.codigo_usuario_pai = '.$codigo_usuario_logado.'
                    UNION ALL   
                    SELECT UsuarioFilho.codigo,UsuarioFilho.apelido,UsuarioFilho.codigo_usuario_pai 
                    FROM dbBuonny.portal.usuario AS UsuarioFilho 
                    JOIN tblFilhos ON ( usuarioFilho.codigo_usuario_pai = tblFilhos.codigo ) ) ';            
        }else{
           $query = '';
        }
        $query .= $this->find('sql', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
        $retorno = $this->query($query);
        return $retorno;
    }

    function paginateCount($conditions = null,$recursive = 0, $extra = array()) {         
        if( isset( $extra['extra']['method'] ) && $extra['extra']['method'] == 'somente_subordinados' ){
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
            $query .= $this->find('sql', compact('conditions'));

            $retorno = $this->query($query);
            if($retorno){
                return count($retorno);
            }else{
                return 0;
            }
        }else{    
            
            return $this->find('count', compact('conditions', 'recursive'));
        }
    }
    
    function carregarArquivoCsv($destino, $tipo = null) {
        require_once APP . 'vendors' . DS . 'excel_reader' . DS . 'excel_reader2.php';

        try {
            $reader = new Spreadsheet_Excel_Reader();
            $reader->setUTFEncoder('iconv');
            $reader->setOutputEncoding('UTF-8');
            $reader->read($destino,true);
        } catch (Exception $e) {
            $error['fatal'] = 'O arquivo está em formato inválido. Por favor salve-o como um arquivo em formato Excel 97-2003 (XLS)';
            return  $error;
        }
        
        $retorno = $this->testaCompatibilidadeArquivo($tipo,$reader->sheets);
        if ($retorno!==true) {
            return array('fatal'=>$retorno);
        }

        if($tipo == 'Claro') {
            $tipo_codigo = self::CLARO;
            return $this->processarArquivoCsvClaroVivo($reader->sheets, $tipo_codigo);
        }elseif($tipo == 'Vivo') {
            $tipo_codigo = self::VIVO;
            return $this->processarArquivoCsvClaroVivo($reader->sheets, $tipo_codigo);
        }elseif($tipo == 'Nextel') {
            $tipo_codigo = self::NEXTEL;
            return $this->processarArquivoCsvNextel($reader->sheets, $tipo_codigo);
        }elseif($tipo == 'Tarifador') {
            $tipo_codigo = self::TARIFADOR;
            return $this->processarArquivoCsvTarifador($reader->sheets, $tipo_codigo);
        }else {
            return array();
        }
    }

    function testaCompatibilidadeArquivo($tipo, $dados) {
        $capa = $dados[0];
        $operadora_arquivo = '';
        $para_pesquisa = false;
        foreach($capa['cells'] as $key_row=>$row) {
            foreach($row as $key_cell=>$cell) {
                if (trim($cell)=='') continue;
                if (strpos(strtolower($cell),'nextel')!==false) {
                    $operadora_arquivo = 'Nextel';
                    $para_pesquisa = true;
                    break;
                }
                if (strpos(strtolower($cell),'vivo')!==false) {
                    $operadora_arquivo = 'Vivo';
                    $para_pesquisa = true;
                    break;
                }
                if (strpos(strtolower($cell),'claro')!==false) {
                    $operadora_arquivo = 'Claro';
                    $para_pesquisa = true;
                    break;
                }
                if (strpos(strtolower($cell),'soma de dur./qtde')!==false) {
                    $operadora_arquivo = 'Tarifador';
                    $para_pesquisa = true;
                    break;
                }
            }

            if ($para_pesquisa) break;
        }
        if ($operadora_arquivo=='') {
           return 'Não foi possível identificar o Modelo de Importação do Arquivo XLS'; 
        }

        if ($operadora_arquivo!=$tipo) {
           return 'Modelo de Importação do Arquivo XLS diferente do Modelo selecionado';
        }
        return true;
    }

    function base_cnpj($valor) {
        return substr($valor,0,10);
    }



    function busca_mes_ano_excel ($dados) {
        $mesAno = array();
        foreach($dados as $key_data=>$data) {
            foreach($data['cells'] as $key_row=>$row) {
                if(in_array('06.326.025', array_map(array('RegistroTelecom','base_cnpj'), $row) )) {
                    if(isset($row['6'])) {
                        $mesAno['mes'] = substr($row['6'], '-7', '2');
                        $mesAno['ano'] = substr($row['6'], '-4');
                    }
                }
            }
        }
        return $mesAno;
    }

    /*Função para importação de arquivos CSV Padrão Claro e Vivo*/
    function processarArquivoCsvClaroVivo ($dados, $tipo_codigo = null) {
    
        unset($dados[0]);// Capa
        //unset($dados[1]);// Resumo
        unset($dados[2]);// Mensalidade
        unset($dados[3]);// Locais
        unset($dados[4]);// Interubanas
        unset($dados[5]);// Tarifas zero
        unset($dados[6]);// Dados nascionais
        //unset($dados[7]);// int voz
        //unset($dados[8]);// int Dados

        //Preparando os dados
        $Usuario = ClassRegistry::init('Usuario');
        $TipoRetorno = ClassRegistry::init('TipoRetorno');
        $UsuarioContato = ClassRegistry::init('UsuarioContato');
        $RegistroTelecom = ClassRegistry::init('RegistroTelecom');

        if(empty($mes) || empty($ano)) {
            $mesAno = $this->busca_mes_ano_excel($dados);
            if(!empty($mesAno)) {
                $mes = $mesAno['mes']; 
                $ano = $mesAno['ano'];
            }
            $data = array();
            $row = array();
            $keys = array();
            $key_cell = NULL;
            $key_data = NULL;
        }

        $keys = Array(
            'numero' => 0,
            'usuario'=> 0,
            'duracao_min_locais'=>0,
            'valor_min_locais'=>0,
            'duracao_min_ld' => 0,
            'valor_min_ld'=>0,
            'duracao_sms'=>0,
            'valor_sms'=>0,
            'duracao_dados'=>0,
            'valor_dados'=>0,            
            'valor_dados_outros'=>0,
            'mensalidade'=>0,
            'duracao_min_int' =>0,
            'valor_min_int' =>0,
            'duracao_sms_pacote_int'=>0,
            'duracao_sms_excedente_int'=>0,
            'valor_sms_int' =>0,
            'duracao_dados_pacote_int'=>0,
            'duracao_dados_excedente_int'=>0,
            'valor_dados_int' =>0,
        );



        $msgs = Array();
        $registros = Array();
        foreach($dados as $key_data=>$data) {
            $keys['numero'] = 0;
            foreach($data['cells'] as $key_row=>$row) {
                if ($keys['numero']==0) {
                    foreach($row as $key_cell=>$cell) {
                        if (mb_check_encoding($cell,'utf-8')!=1) $cell = utf8_encode($cell);
                        if($cell === 'Acessos' || $cell === 'Números') {
                            $colunas_nome[$key_data] = $row;
                            $keys['numero'] = $key_cell;
                        }
                        if ($key_data==1) {
                            if($cell === 'Usuário') {
                                $colunas_nome[$key_data] = $row;
                                $keys['usuario'] = $key_cell;
                            }
                            if($cell === 'Min. Locais') {
                                $inicio_coluna_minutos = $key_cell;
                                $keys['duracao_min_locais'] = $key_cell;
                                $keys['valor_min_locais'] = $key_cell+1;
                            }
                            if($cell === 'Min. LD') {
                                $inicio_coluna_minutos = $key_cell;
                                $keys['duracao_min_ld'] = $key_cell;
                                $keys['valor_min_ld'] = $key_cell+1;
                            }             
                            if($cell === 'SMS') {
                                $inicio_coluna_minutos = $key_cell;
                                $keys['duracao_sms'] = $key_cell;
                                $keys['valor_sms'] = $key_cell+1;
                            }        
                            if($cell === 'Dados') {
                                $inicio_coluna_minutos = $key_cell;
                                $keys['duracao_dados'] = $key_cell;
                                $keys['valor_dados'] = $key_cell+1;
                            }        
                            if($cell === 'Valor outros') {
                                $keys['valor_dados_outros'] = $key_cell;
                            }   
                            if($cell === 'Dados 3GB' || $cell === 'Plano Internet 4G') {
                                $usuarios_3g[$key_row] = $row;
                            }
                            if($cell === 'Mensalidades') {
                                $keys['mensalidade'] = $key_cell;
                            }
                            if($cell === 'Valor outros') {
                                $keys['valor_total_outros'] = $key_cell;
                            }
                        } elseif($key_data==7) {
                            if($cell === 'Total') {
                                $keys['duracao_min_int'] = $key_cell;
                                $keys['valor_min_int'] = $key_cell+1;
                            }                            
                        } elseif($key_data==8) {
                            if($cell === 'Trafego WAP/Web (MB)') {
                                $keys['duracao_dados_pacote_int'] = $key_cell;
                                $keys['duracao_dados_excedente_int'] = $key_cell+1;
                                $keys['valor_dados_int'] = $key_cell+2;
                            }                            
                            if($cell === 'Torpedos (Qtde)') {
                                $keys['duracao_sms_pacote_int'] = $key_cell;
                                $keys['duracao_sms_excedente_int'] = $key_cell+1;
                                $keys['valor_sms_int'] = $key_cell+2;
                            }                            
                        }                        
                    }// fim foreach celulas
                }
                    $reg = array();
                    if(isset($mes) || !empty($mes) || isset($ano) || !empty($ano) ){
                        $reg['mes'] = $mes;
                        $reg['ano'] = $ano;
                    } else {
                        $error['fatal'] = 'Arquivo em formato invalido, houve um problema na localização do mês ou ano';
                        return  $error;
                    }
               
                if (isset($row[$keys['numero']]) && (is_numeric($row[$keys['numero']]))) {
                    $conditions_novo_registro = array();
                    $conditions_usuario = array();


                    $idx_row = $key_row."_".TipoRetorno::TIPO_RETORNO_CELULAR;
                    $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_CELULAR);
                    
                    if($valor_tipo_retorno['TipoRetorno']['codigo']) {
                        $conditions_novo_registro = array('RegistroTelecom.identificador' => $row[$keys['numero']],
                                                          'RegistroTelecom.mes' =>  $reg['mes'],
                                                          'RegistroTelecom.ano' =>  $reg['ano'],
                                                          'RegistroTelecom.codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_CELULAR);
                        $retonos = $this->find('first', array('conditions' => $conditions_novo_registro));

                        if(is_array($retonos) && count($retonos) > 0) {
                            $msgs[$idx_row]['linha']        = $key_row;
                            $msgs[$idx_row]['telefone']     = $row[$keys['numero']];
                            $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                            $msgs[$idx_row]['mensagem']     = 'O registro foi ignorado pois já se encontra no cadastro';
                        } else {
                            $numero = $row[$keys['numero']];
                            if (strlen($numero)>11) $numero = substr($numero, 4);
                            elseif (strlen($numero)>9) $numero = substr($numero, 2);

                            $conditions_usuario_contato = array('UsuarioContato.descricao LIKE ' => '%'. $numero. '%',
                                                                'UsuarioContato.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                            $valor_usuario = $UsuarioContato->find('first', array('conditions' => $conditions_usuario_contato));
                            $codigo_usuario = $valor_usuario['UsuarioContato']['codigo_usuario'];
                            //if($tipo_codigo = self::VIVO) {
                                if (!((is_array($valor_usuario)) && (count($valor_usuario)>0)) )  {
                                    $msgs[$idx_row]['linha'] = $key_row;
                                    $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                    $msgs[$idx_row]['mensagem'] = 'O tipo não foi encontrado:'.$valor_tipo_retorno['TipoRetorno']['descricao'].". Favor realizar cadastro no usuário";
                                    $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                    $msgs[$idx_row]['tipo'] = 'T';
                                } else {
                                    // Salvar Celular
                                    $reg['codigo_usuario_registro'] = $codigo_usuario;
                                    $identificador = (string)$row[$keys['numero']];
                                    $reg['identificador'] = $identificador;
                                    $codigo_tipo_retorno = $valor_tipo_retorno['TipoRetorno']['codigo'];
                                    $reg['codigo_tipo_retorno'] = $codigo_tipo_retorno;
                                    $reg['linha'] = $key_row;
                                    
                                    $qta=0;
                                    $valor=0;
                                    
                                    if ($key_data==1) {
                                        $qta = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['duracao_min_ld']]));
                                        $qta += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['duracao_min_locais']]));

                                        $valor = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['valor_min_ld']]));
                                        $valor += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['valor_min_locais']]));
                                    } elseif($key_data==7)  {
                                        $qta = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['duracao_min_int']]));
                                        $valor = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['valor_min_int']]));

                                    }

                                    if (!isset($registros[$identificador][$codigo_tipo_retorno])) {
                                        $reg['qta'] = $qta;
                                        $reg['valor'] = $valor;
                                        $registros[$identificador][$codigo_tipo_retorno] = $reg;
                                    } else {
                                        $registros[$identificador][$codigo_tipo_retorno]['qta'] += $qta;
                                        $registros[$identificador][$codigo_tipo_retorno]['valor'] += $valor;
                                    }
                                    
                                }
                            //}
                        }
                    } else {
                        $msgs[$idx_row]['linha'] = $key_row;
                        $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                        $msgs[$idx_row]['tipo_retorno'] = 'CELULAR';
                        $msgs[$idx_row]['mensagem'] = 'O tipo CELULAR, não foi encontrado';
                    }

                    $idx_row = $key_row."_".TipoRetorno::TIPO_RETORNO_SMS;
                    $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_SMS);

                    if($valor_tipo_retorno['TipoRetorno']['codigo']) {
                        $conditions_novo_registro = array('RegistroTelecom.identificador' => $row[$keys['numero']],
                                                          'RegistroTelecom.mes' =>  $reg['mes'],
                                                          'RegistroTelecom.ano' =>  $reg['ano'],
                                                          'RegistroTelecom.codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_SMS);
                        $retonos = $this->find('first', array('conditions' => $conditions_novo_registro));

                        if(is_array($retonos) && count($retonos) > 0) {
                            $msgs[$idx_row]['linha'] = $key_row;
                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                            $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                            $msgs[$idx_row]['mensagem'] = 'O registro foi ignorado pois já se encontra no cadastro';
                        } else {
                            $numero = $row[$keys['numero']];
                            if (strlen($numero)>11) $numero = substr($numero, 4);
                            elseif (strlen($numero)>9) $numero = substr($numero, 2);                   
                            // Salvar SMS
                            $conditions_novo_registro = array();
                            $reg = array();
                            $conditions_usuario_contato = array();
                            $valor_usuario =array();
                            $reg['mes'] = $mes;
                            $reg['ano'] = $ano;
                            $reg['identificador'] = $row[$keys['numero']];
                            $conditions_usuario_contato = array('UsuarioContato.descricao LIKE ' => '%'. $numero . '%',
                                                                'UsuarioContato.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                            $valor_usuario = $UsuarioContato->find('first', array('conditions' => $conditions_usuario_contato));
                            $codigo_usuario = $valor_usuario['UsuarioContato']['codigo_usuario'];
                            $reg['codigo_usuario_registro'] = $codigo_usuario;
                            if (!((is_array($valor_usuario)) && (count($valor_usuario)>0))) {
                                $msgs[$idx_row]['linha'] = $key_row;
                                $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                $msgs[$idx_row]['mensagem'] = 'O tipo não foi encontrado:'.$valor_tipo_retorno['TipoRetorno']['descricao'].'. Favor realizar cadastro no usuário.';
                                $msgs[$idx_row]['tipo'] = 'T';
                            } else {
                                // Salvar Celular
                                $reg['codigo_usuario_registro'] = $codigo_usuario;
                                $identificador = (string)$row[$keys['numero']];
                                $reg['identificador'] = $identificador;
                                $codigo_tipo_retorno = $valor_tipo_retorno['TipoRetorno']['codigo'];
                                $reg['codigo_tipo_retorno'] = $codigo_tipo_retorno;
                                $reg['linha'] = $key_row;
                                
                                $qta=0;
                                $valor=0;
                                
                                if ($key_data==1) {
                                    $qta = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['duracao_sms']]));
                                    $valor = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['valor_sms']]));
                                } elseif($key_data==8)  {
                                    if (!isset($row[$keys['duracao_sms_pacote_int']]))      $row[$keys['duracao_sms_pacote_int']] = 0;
                                    if (!isset($row[$keys['duracao_sms_excedente_int']]))   $row[$keys['duracao_sms_excedente_int']] = 0;
                                    $qta = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['duracao_sms_pacote_int']]));
                                    $qta += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['duracao_sms_excedente_int']]));
                                    $valor = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['valor_sms_int']]));
                                }
                                if (!isset($registros[$identificador][$codigo_tipo_retorno])) {
                                    $reg['qta'] = $qta;
                                    $reg['valor'] = $valor;
                                    $registros[$identificador][$codigo_tipo_retorno] = $reg;
                                } else {
                                    $registros[$identificador][$codigo_tipo_retorno]['qta'] += $qta;
                                    $registros[$identificador][$codigo_tipo_retorno]['valor'] += $valor;
                                }

                            }
                        }
                    } else {
                        $msgs[$idx_row]['linha'] = $key_row;
                        $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                        $msgs[$idx_row]['tipo_retorno'] = 'SMS';
                        $msgs[$idx_row]['mensagem'] = 'O tipo SMS, não foi encontrado';
                    }

                    $idx_row = $key_row."_".TipoRetorno::TIPO_RETORNO_3G;
                    $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_3G);

                    if($valor_tipo_retorno['TipoRetorno']['codigo']) {
                        $conditions_novo_registro = array('RegistroTelecom.identificador' => $row[$keys['numero']],
                                                          'RegistroTelecom.mes' =>  $reg['mes'],
                                                          'RegistroTelecom.ano' =>  $reg['ano'],
                                                          'RegistroTelecom.codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_3G);
                        $retonos = $this->find('first', array('conditions' => $conditions_novo_registro));

                        if(is_array($retonos) && count($retonos) > 0) {
                            $msgs[$idx_row]['linha'] = $key_row;
                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                            $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                            $msgs[$idx_row]['mensagem'] = 'O registro foi ignorado pois já se encontra no cadastro';
                        } else {
                            $numero = $row[$keys['numero']];
                            if (strlen($numero)>11) $numero = substr($numero, 4);
                            elseif (strlen($numero)>9) $numero = substr($numero, 2);

                            // Salvar CELULAR 3G
                            $conditions_novo_registro = array();
                            $reg = array();
                            $conditions_usuario_contato = array();
                            $valor_usuario = array();
                            $reg['mes'] = $mes;
                            $reg['ano'] = $ano;
                            $reg['codigo_usuario_registro'] = $codigo_usuario;
                            $reg['identificador'] = $row[$keys['numero']];
                            $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_3G);
                            $conditions_usuario_contato = array('UsuarioContato.descricao LIKE ' => '%'. $numero. '%',
                                                                'UsuarioContato.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                            $valor_usuario = $UsuarioContato->find('first', array('conditions' => $conditions_usuario_contato));
                            $codigo_usuario = $valor_usuario['UsuarioContato']['codigo_usuario'];
                            $reg['codigo_usuario_registro'] = $codigo_usuario;
                             if (!((is_array($valor_usuario)) && (count($valor_usuario)>0))) {
                                $msgs[$idx_row]['linha'] = $key_row;
                                $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];

                                $msgs[$idx_row]['mensagem'] = 'O tipo não foi encontrado:'.$valor_tipo_retorno['TipoRetorno']['descricao'].'. Favor realizar cadastro no usuário.';

                                $msgs[$idx_row]['tipo'] = 'T';
                            } else {

                                // Salvar Celular
                                $reg['codigo_usuario_registro'] = $codigo_usuario;
                                $identificador = (string)$row[$keys['numero']];
                                $reg['identificador'] = $identificador;
                                $codigo_tipo_retorno = $valor_tipo_retorno['TipoRetorno']['codigo'];
                                $reg['codigo_tipo_retorno'] = $codigo_tipo_retorno;
                                $reg['linha'] = $key_row;
                                
                                $qta=0;
                                $valor=0;
                                
                                if ($key_data==1) {
                                    $qta = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['duracao_dados']]));
                                    $valor = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['valor_dados']]));
                                    $valor += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['valor_dados_outros']]));
                                } elseif($key_data==8)  {
                                    $qta = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['duracao_dados_pacote_int']]));
                                    $qta += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['duracao_dados_excedente_int']]));
                                    $valor = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['valor_dados_int']]));

                                }

                                if (!isset($registros[$identificador][$codigo_tipo_retorno])) {
                                    $reg['qta'] = $qta;
                                    $reg['valor'] = $valor;
                                    $registros[$identificador][$codigo_tipo_retorno] = $reg;
                                } else {
                                    $registros[$identificador][$codigo_tipo_retorno]['qta'] += $qta;
                                    $registros[$identificador][$codigo_tipo_retorno]['valor'] += $valor;
                                }

                            }
                        }
                    } else {
                        $msgs[$idx_row]['linha'] = $key_row;
                        $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                        $msgs[$idx_row]['tipo_retorno'] = '3G';
                        $msgs[$idx_row]['mensagem'] = 'O tipo DADOS(3G), não foi encontrado';
                    }

                    //Registro Mensalidade
                    $idx_row = $key_row."_".TipoRetorno::TIPO_RETORNO_MENSALIDADE;
                    $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_MENSALIDADE);

                    if($valor_tipo_retorno['TipoRetorno']['codigo']) {
                        $conditions_novo_registro = array('RegistroTelecom.identificador' => $row[$keys['numero']],
                                                          'RegistroTelecom.mes' =>  $reg['mes'],
                                                          'RegistroTelecom.ano' =>  $reg['ano'],
                                                          'RegistroTelecom.codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_MENSALIDADE);
                        $retonos = $this->find('first', array('conditions' => $conditions_novo_registro));
                        if(is_array($retonos) && count($retonos) > 0) {
                            $msgs[$idx_row]['linha'] = $key_row;
                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                            $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                            $msgs[$idx_row]['mensagem'] = 'O registro foi ignorado pois já se encontra no cadastro';
                        } else {
                            $numero = $row[$keys['numero']];
                            if (strlen($numero)>11) $numero = substr($numero, 4);
                            elseif (strlen($numero)>9) $numero = substr($numero, 2);
                            $conditions_novo_registro = array();
                            $reg = array();
                            $conditions_usuario_contato = array();
                            $valor_usuario = array();
                            $conditions_usuario_contato = array('UsuarioContato.descricao LIKE ' => '%'. $numero. '%',
                                                                'UsuarioContato.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                            $valor_usuario = $UsuarioContato->find('first', array('conditions' => $conditions_usuario_contato));
                            $codigo_usuario = $valor_usuario['UsuarioContato']['codigo_usuario'];
                            $reg['mes'] = $mes;
                            $reg['ano'] = $ano;
                            $reg['codigo_usuario_registro'] = $codigo_usuario;
                            $reg['identificador'] = $row[$keys['numero']];
                            $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_MENSALIDADE);
                            $reg['codigo_usuario_registro'] = $codigo_usuario;
                             if (!((is_array($valor_usuario)) && (count($valor_usuario)>0))) {
                                $msgs[$idx_row]['linha'] = $key_row;
                                $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                $msgs[$idx_row]['mensagem'] = 'O tipo não foi encontrado:'.$valor_tipo_retorno['TipoRetorno']['descricao'].'. Favor realizar cadastro no usuário.';    
                                $msgs[$idx_row]['tipo'] = 'T';
                            } else {
                                // Salvar Celular
                                $reg['codigo_usuario_registro'] = $codigo_usuario;
                                $identificador = (string)$row[$keys['numero']];
                                $reg['identificador'] = $identificador;
                                $codigo_tipo_retorno = $valor_tipo_retorno['TipoRetorno']['codigo'];
                                $reg['codigo_tipo_retorno'] = $codigo_tipo_retorno;
                                $reg['linha'] = $key_row;
                                
                                $qta=0;
                                $valor=0;
                                
                                if ($key_data==1) {
                                    $qta = 1;
                                    $valor = preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$keys['mensalidade']]));
                                } 

                                if (!isset($registros[$identificador][$codigo_tipo_retorno])) {
                                    $reg['qta'] = $qta;
                                    $reg['valor'] = $valor;
                                    $registros[$identificador][$codigo_tipo_retorno] = $reg;
                                } else {
                                    $registros[$identificador][$codigo_tipo_retorno]['qta'] += $qta;
                                    $registros[$identificador][$codigo_tipo_retorno]['valor'] += $valor;
                                }

                            }
                        }
                    } else {
                        $msgs[$idx_row]['linha'] = $key_row;
                        $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                        $msgs[$idx_row]['tipo_retorno'] = 'MENSALIDADE';
                        $msgs[$idx_row]['mensagem'] = 'O tipo MENSALIDADE não foi encontrado';
                    }
                } elseif(!empty($row[$keys['numero']])) {
                    if (strtolower($row[$keys['numero']]) == 'outros valores') {
                        $idx_row = $key_row."_".TipoRetorno::TIPO_RETORNO_MENSALIDADE;
                        $reg = array();
                        $usuario_contato = array();
                        $conditions_busca_usuario_contato = array();
                        $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_MENSALIDADE);
                        if(isset($mes) || !empty($mes) || isset($ano) || !empty($ano) ){
                            $reg['mes'] = $mes;
                            $reg['ano'] = $ano;
                        } else {
                            $error['fatal'] = 'Arquivo em formato invalido, houve um problema na localização do mês ou ano';
                            return  $error;
                        }
                        $qta=1;
                        $valor=0;
                        
                        if ($key_data==1) {
                            $valor_com_parentese = preg_replace("/[^0-9.()]/", "", str_replace(',','',$row[$keys['valor_total_outros']]));
                            $valor = preg_replace("/[^0-9.-]/", "", str_replace(',','',$row[$keys['valor_total_outros']]));
                            if(strpos($valor_com_parentese,'(') === true) {
                                $valor = $valor * -1;
                            }
                            if(!(strpos($valor,'-') === false)) {
                                $valor = null;
                            }
                        }
                        if($tipo_codigo == self::CLARO)
                        $reg['identificador'] = '000000001';
                        if($tipo_codigo == self::VIVO) 
                        $reg['identificador'] = '000000002';
                        $reg['codigo_tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['codigo'];
                        $reg['codigo_operadora'] = $tipo_codigo;
                        if(!empty($valor)) {
                            $conditions_busca_usuario_contato = array('UsuarioContato.descricao LIKE' => '%'.$reg['identificador'].'%');
                            $usuario_contato = $UsuarioContato->find('first', array('conditions' => $conditions_busca_usuario_contato));
                            $reg['quantidade'] = $qta;
                            $reg['valor'] = $valor;
                            $reg['codigo_usuario_registro'] = $usuario_contato['UsuarioContato']['codigo_usuario'];

                            $conditions_novo_registro = array(
                                                          'RegistroTelecom.identificador' => '00'.$reg['identificador'],
                                                          'RegistroTelecom.mes' =>  $reg['mes'],
                                                          'RegistroTelecom.ano' =>  $reg['ano'],
                                                          'RegistroTelecom.codigo_operadora' =>  $reg['codigo_operadora'],
                                                          'RegistroTelecom.codigo_tipo_retorno' => $reg['codigo_tipo_retorno'],
                                                          'RegistroTelecom.valor' => $reg['valor'],
                                                          'RegistroTelecom.quantidade' => $reg['quantidade']);
                            $retonos = $this->find('first', array('conditions' => $conditions_novo_registro));
                            if(is_array($retonos) && count($retonos) > 0) {
                                $msgs[$idx_row]['linha'] = $key_row;
                                $msgs[$idx_row]['telefone'] = 'Outros Valores';
                                $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                $msgs[$idx_row]['mensagem'] = 'O registro foi ignorado pois já se encontra no cadastro';
                            } else {
                                $salvar = Array('RegistroTelecom'=>$reg);
                                $this->incluir($salvar);
                            }
                        }
                        continue;
                    }
                    if ($row[$keys['numero']] == 'Acessos' || empty($row[$keys['numero']]) ||  $row[$keys['numero']] != '') continue;
                    if (strtolower($row[$keys['numero']]) == 'total geral' || $row[$keys['numero']] == 'Total') break;
                    if (isset($row['1']) && $row['1'] == 'Total Geral' || isset($row['1']) && $row['1'] == 'Total') break;
                        $msgs[$key_row]['linha'] = $key_row;
                        $msgs[$key_row]['telefone'] = $row[$keys['numero']];
                        $msgs[$key_row]['mensagem'] = 'Não é um telefone valido';
                    continue;
                }
            }//fim segundo foreach celulas
        }//fim primeiro foreach data

        //debug($registros);
        
        foreach ($registros as $identificador => $registros_identificador) {
            foreach ($registros_identificador as $codigo_tipo_retorno => $reg) {
                $qta = $reg['qta'];
                $valor = $reg['valor'];

                if ($codigo_tipo_retorno==TipoRetorno::TIPO_RETORNO_MENSALIDADE) {
                    $grava_registro = (!empty($valor) && $valor != 0);
                } else {
                    $grava_registro = (!empty($qta) && $qta != 0) && (!empty($valor) && $valor != 0);
                }

                if($grava_registro) {     
                    $reg['quantidade'] = $reg['qta'];
                    $reg['codigo_operadora'] = $tipo_codigo;
                    $salvar = Array('RegistroTelecom'=>$reg);
                    $this->incluir($salvar);
                } else {

                    // $valor_tipo_retorno =  $TipoRetorno->findByCodigo($codigo_tipo_retorno);
                    // $idx_row = $reg['linha']."_".$codigo_tipo_retorno;

                    // $msgs[$idx_row]['linha'] = $reg['linha'];
                    // $msgs[$idx_row]['telefone'] = $identificador;
                    // $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];

                    // if ($codigo_tipo_retorno==TipoRetorno::TIPO_RETORNO_MENSALIDADE) {
                    //     $msgs[$idx_row]['mensagem'] = 'Registro ignorado pois o valor está vazio';
                    // } else {
                    //     $msgs[$idx_row]['mensagem'] = 'Registro ignorado pois a quantiadade e o valor estão vazios';
                    // }
                }                
            }
        }


        return $msgs;
    }
    /*Função para importação de arquivos Csv padrão Nextel*/
    function processarArquivoCsvNextel($dados, $tipo_codigo = null) {

        unset($dados[0]);// Capa
        unset($dados[2]);// Mensalidades
        unset($dados[3]);// Voz
        unset($dados[5]);// Tarifas zero
        unset($dados[6]);// Ofensores
        unset($dados[7]);// GPS

        //Preparando os dados
        $Usuario = ClassRegistry::init('Usuario');
        $TipoRetorno = ClassRegistry::init('TipoRetorno');
        $UsuarioContato = ClassRegistry::init('UsuarioContato');
        $RegistroTelecom = ClassRegistry::init('RegistroTelecom');

        $keys = Array(
            'numero' => 0,
        );
        $keys_valor_torpedos =array();
        $keys_quantia_torpedos =array();
        $keys_quantia_tel =array();
        $keys_valor_tel =array();
        $keys_quantia_dados =array();
        $keys_valor_dados =array();
        $keys_mensalidades = array();
        $keys_outros_valores = array();

        $msgs = Array();
        foreach($dados as $key_data=>$data) {
            $keys['numero'] = 0;
            foreach($data['cells'] as $key_row=>$row) {
                if(in_array('06.326.025', array_map(array('RegistroTelecom','base_cnpj'), $row) )) {
                    if(isset($row['14']) && !empty($row['14'])) {
                        $meses = Comum::listMeses(true);
                        $mes = array_search(substr($row['14'], '-8', '3'), $meses);
                        $ano = substr($row['14'], '-4', '4');
                    }elseif (isset($row['18']) && !empty($row['18'])){
                        $meses = Comum::listMeses(true);
                        $mes = array_search(substr($row['18'], '-8', '3'), $meses);
                        $ano = substr($row['18'], '-4', '4');

                    }
                }

                if ($keys['numero']==0) {
                    foreach($row as $key_cell=>$cell) {
                        if (mb_check_encoding($cell,'utf-8')!=1) $cell = utf8_encode($cell);

                        if($cell === 'Acessos' || $cell === 'Números') {
                            $colunas_nome[$key_data] = $row;
                            $keys['numero'] = $key_cell;
                        }
                        if($cell === 'Min. Locais Inclusos Pacote') {
                            $keys_quantia_tel[] = $key_cell;
                            $keys_quantia_tel[] = $key_cell+1;
                            $keys_quantia_tel[] = $key_cell+3;
                            $keys_quantia_tel[] = $key_cell+4;
                        }
                        if($cell === 'Valor Min. Locais NAO Inclusos Pacote') {
                            $keys_valor_tel[] = $key_cell;
                           // $keys_valor_tel[] = $key_cell+3; 
                           //Removido pois segundo analise foi verificado que a coluna estava sendo somada junto a outra coluna de outros
                            $keys_valor_tel[] = $key_cell+4;
                        }             
                        if($cell === 'Torpedos Inclusos') {
                            $keys_quantia_torpedos[] = $key_cell;
                            $keys_quantia_torpedos[] = $key_cell+1;
                            $keys_valor_torpedos[] = $key_cell+2;
                        }        
                        if($cell === 'Trafego WAP Incluso (MB)') {
                            $keys_quantia_dados[] = $key_cell;
                            $keys_quantia_dados[] = $key_cell+1;
                            $keys_quantia_dados[] = $key_cell+3;
                            $keys_quantia_dados[] = $key_cell+4;
                            $keys_valor_dados[] = $key_cell+2;
                            $keys_valor_dados[] = $key_cell+5;
                            $keys_valor_dados[] = $key_cell+9;
                        }
                        if($cell === 'Mensalidades') {
                            $keys_mensalidades[] = $key_cell;
                            $keys_mensalidades[] = $key_cell+2;
                        } 
                        if($cell === 'Valor Outros Servicos de Dados') {
                            $keys_outros_valores[] = $key_cell;
                        }
                         if($cell === 'Valor de Uso') {
                            $keys_mensalidades[] = $key_cell;
                        }         
                    }// fim foreach celulas
                }


                if(isset($row[$keys['numero']]) && $row[$keys['numero']] == 'BUONNY PROJ E SERV DE RISCOS SECURIT LTD') {
                    $row[$keys['numero']] = '00000000003';
                }


                $row[$keys['numero']] = (isset($row[$keys['numero']]) ? preg_replace("/[^0-9.]/", "",$row[$keys['numero']]) : null); 
                if (isset($row[$keys['numero']]) && is_numeric($row[$keys['numero']])) {
                    $reg = Array();
                    $retonos = Array();
                    $conditions_novo_registro = Array();
                    $valor = 0;
                    $qta = 0;
                      if(!isset($mes) || !empty($mes) || !isset($ano) || !empty($ano) ){
                        $reg['mes'] = $mes;
                        $reg['ano'] = $ano;
                    } else {
                        $error['fatal'] = 'Arquivo em formato invalido, houve um problema na localização do mês ou ano';
                        return  $error;
                    }

                    $numero = preg_replace("/[^0-9.]/", "",$row[$keys['numero']]);
                    if (strlen($numero)>10) $numero = substr($numero, 4);
                    elseif (strlen($numero)>8) $numero = substr($numero, 2);

                    $arrPlan = Array(1=>'Resumo', 4=>'Dados');
                    $base_idx_row = "Plan. ".$arrPlan[$key_data]." - ".$key_row;

                    //Salvar Celular
                    if ($key_data==1) {
                        // Salvar Celular - Voz

                        $idx_row = $base_idx_row."_".TipoRetorno::TIPO_RETORNO_CELULAR;
                        $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_CELULAR);

                        if($valor_tipo_retorno['TipoRetorno']['codigo']) {
                                $conditions_novo_registro = array('RegistroTelecom.identificador' => $row[$keys['numero']],
                                                                  'RegistroTelecom.mes' =>  $reg['mes'],
                                                                  'RegistroTelecom.ano' =>  $reg['ano'],
                                                                  'RegistroTelecom.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                                $retonos = $this->find('first', array('conditions' => $conditions_novo_registro));
                                if(is_array($retonos) && count($retonos) > 1) {
                                    $msgs[$idx_row]['linha'] = $base_idx_row;
                                    $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                    $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                    $msgs[$idx_row]['mensagem'] = 'O registro foi ignorado pois já se encontra no cadastro';
                                } else {
                                    $reg['identificador'] = $row[$keys['numero']];
                                    $reg['codigo_tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['codigo'];
                                    foreach ( $keys_quantia_tel as $value) {
                                       $qta += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$value]));
                                    }
                                    $reg['quantidade'] =  (trim($qta)==""?'0':$qta);
                                    foreach ($keys_valor_tel as $value) {
                                        $valor += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$value]));
                                    }
                                    if(!empty($qta) && !empty($valor)) {    
                                        $reg['valor'] =  (trim($valor)==""?'0':$valor) ;
                                        $reg['codigo_operadora'] =  $tipo_codigo;
                                        $conditions_usuario_cadastrado = array('UsuarioContato.descricao LIKE ' => '%'. $numero. '%',
                                                                    'UsuarioContato.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                                        $valor_usuario = $UsuarioContato->find('first', array('conditions' => $conditions_usuario_cadastrado));
                                        $codigo_usuario = $valor_usuario['UsuarioContato']['codigo_usuario'];
                                        if (!((is_array($valor_usuario)) && (count($valor_usuario)>1))) {
                                            $msgs[$idx_row]['linha'] = $base_idx_row;
                                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                            $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                            $msgs[$idx_row]['mensagem'] = 'O tipo não foi encontrado:'.$valor_tipo_retorno['TipoRetorno']['descricao'].'. Favor realizar cadastro no usuário.';
                                            $msgs[$idx_row]['tipo'] = 'T';
                                        } else {
                                            $reg['codigo_usuario_registro'] = $codigo_usuario;
                                            $salvar = Array('RegistroTelecom'=>$reg);
                                            $this->incluir($salvar); 
                                        }
                                    }
                                }
                        } else {
                            $msgs[$idx_row]['linha'] = $base_idx_row;
                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                            $msgs[$idx_row]['tipo_retorno'] = 'CELULAR';
                            $msgs[$idx_row]['mensagem'] = 'O tipo CELULAR, não foi encontrado';
                        }

                        // Salvar Celular - Mensalidade
                        $idx_row = $base_idx_row."_".TipoRetorno::TIPO_RETORNO_MENSALIDADE;
                        $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_MENSALIDADE);
                        if($valor_tipo_retorno['TipoRetorno']['codigo']) {
                                $conditions_novo_registro = array('RegistroTelecom.identificador' => $row[$keys['numero']],
                                                                  'RegistroTelecom.mes' =>  $reg['mes'],
                                                                  'RegistroTelecom.ano' =>  $reg['ano'],
                                                                  'RegistroTelecom.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                                $retonos = $this->find('first', array('conditions' => $conditions_novo_registro));
                                if(is_array($retonos) && count($retonos) > 1) {
                                    $msgs[$idx_row]['linha'] = $base_idx_row;
                                    $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                    $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                    $msgs[$idx_row]['mensagem'] = 'O registro foi ignorado pois já se encontra no cadastro';
                                } else {
                                    $reg['identificador'] = $row[$keys['numero']];
                                    $reg['codigo_tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['codigo'];
                                    $reg['quantidade'] =  1;
                                    $valor = 0;
                                    foreach ($keys_mensalidades as $value) {
                                        $val = preg_replace("/[^0-9.()-]/", "", str_replace(',','.',$row[$value]));
                                        $this->log($val, 'valores');
                                        if (strpos($val,'(')!==false || strpos($val,'-')!==false) {
                                            $valor -= preg_replace("/[^0-9.]/", "", $val);
                                        } else {
                                            $valor += preg_replace("/[^0-9.]/", "", $val);
                                        }
                                    }
                                    if(!empty($valor)) {
                                        $reg['valor'] = (trim($valor)==""?'0':$valor) ;
                                        $reg['codigo_operadora'] =  $tipo_codigo;
                                        $conditions_usuario_cadastrado = array('UsuarioContato.descricao LIKE ' => '%'. $numero. '%',
                                                                    'UsuarioContato.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                                        $valor_usuario = $UsuarioContato->find('first', array('conditions' => $conditions_usuario_cadastrado));
                                        $codigo_usuario = $valor_usuario['UsuarioContato']['codigo_usuario'];
                                        if (!((is_array($valor_usuario)) && (count($valor_usuario)>1))) {
                                            $msgs[$idx_row]['linha'] = $base_idx_row;
                                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                            $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                            $msgs[$idx_row]['mensagem'] = 'O tipo não foi encontrado:'.$valor_tipo_retorno['TipoRetorno']['descricao'].'. Favor realizar cadastro no usuário.';
                                            $msgs[$idx_row]['tipo'] = 'T';
                                        } else {
                                            $reg['codigo_usuario_registro'] = $codigo_usuario;
                                            $salvar = Array('RegistroTelecom'=>$reg);
                                            $this->incluir($salvar);
                                        }
                                    } else {
                                        $msgs[$idx_row]['linha'] = $base_idx_row;
                                        $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                        $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                        $msgs[$idx_row]['mensagem'] = 'Registro ignorado pois o valor não está preenchido ou está zerado';
                                    }
                                }
                        } else {
                            $msgs[$idx_row]['linha'] = $base_idx_row;
                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                            $msgs[$idx_row]['tipo_retorno'] = 'MENSALIDADE';
                            $msgs[$idx_row]['mensagem'] = 'O tipo MENSALIDADE não foi encontrado';
                        }
                    }
                    // Salvar SMS
                    if ($key_data==4) {
                        $reg = array();
                        $reg['mes'] = $mes;
                        $reg['ano'] = $ano;
                        $valor_tipo_retorno = array();

                        $idx_row = $base_idx_row."_".TipoRetorno::TIPO_RETORNO_SMS;
                        $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_SMS);

                        if($valor_tipo_retorno['TipoRetorno']['codigo']) {
                            $retonos = Array();
                            $conditions_novo_registro = Array();
                            $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_SMS);
                                $conditions_novo_registro = array('RegistroTelecom.identificador' => $row[$keys['numero']],
                                                              'RegistroTelecom.mes' =>  $reg['mes'],
                                                              'RegistroTelecom.ano' =>  $reg['ano'],
                                                              'RegistroTelecom.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                                $retonos = $this->find('first', array('conditions' => $conditions_novo_registro));
                                if(is_array($retonos) && count($retonos) > 0) {
                                    $msgs[$idx_row]['linha'] = $base_idx_row;
                                    $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                    $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                    $msgs[$idx_row]['mensagem'] = 'O registro foi ignorado pois já se encontra no cadastro';
                                } else {
                                    $valor = 0;
                                    $qta = 0;
                                    $outros_valores =0;
                                    
                                    $reg['identificador'] = $row[$keys['numero']];
                                    $reg['codigo_tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['codigo'];
                                    foreach ( $keys_quantia_torpedos as $value) {
                                        $qta += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$value]));
                                    }
                                    foreach ($keys_valor_torpedos as $value) {
                                        $valor += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$value]));
                                    }
                                    foreach ($keys_outros_valores as $value) {
                                        $outros_valores += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$value]));
                                        if(!empty($outros_valores) && $outros_valores > 0) {
                                            $qta += 1;
                                            $valor += $outros_valores;
                                        }
                                    }
                                    $numero = preg_replace("/[^0-9.]/", "",$row[$keys['numero']]);
                                    if (strlen($numero)>10) $numero = substr($numero, 4);
                                    elseif (strlen($numero)>8) $numero = substr($numero, 2);
                                    $reg['quantidade'] =  (trim($qta)==""?'0':$qta);
                                    if(!empty($qta) && !empty($valor)) { 
                                        $reg['valor'] =  (trim($valor)==""?'0':$valor) ;
                                        $reg['codigo_operadora'] =  $tipo_codigo;
                                        $conditions_usuario_cadastrado = array('UsuarioContato.descricao LIKE ' => '%'. $numero. '%',
                                                                        'UsuarioContato.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                                        $valor_usuario = $UsuarioContato->find('first', array('conditions' => $conditions_usuario_cadastrado));
                                        $codigo_usuario = $valor_usuario['UsuarioContato']['codigo_usuario'];
                                        if (!((is_array($valor_usuario)) && (count($valor_usuario)>1)) && $tipo_codigo != self::NEXTEL) {
                                            $reg['codigo_usuario_registro'] = $codigo_usuario;
                                            $salvar = Array('RegistroTelecom'=>$reg);
                                            $this->incluir($salvar);
                                        } else {
                                            $msgs[$idx_row]['linha'] = $base_idx_row;
                                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                            $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                            $msgs[$idx_row]['mensagem'] = 'O tipo não foi encontrado:'.$valor_tipo_retorno['TipoRetorno']['descricao'].'. Favor realizar cadastro no usuário.';
                                            $msgs[$idx_row]['tipo'] = 'T';
                                        }
                                    } 
                                    // else {
                                    //     $msgs[$idx_row]['linha'] = $base_idx_row;
                                    //     $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                    //     $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                    //     $msgs[$idx_row]['mensagem'] = 'Registro ignorado pois a quantiadade está vazia';
                                    // }
                                }
                        } else {
                            $msgs[$idx_row]['linha'] = $base_idx_row;
                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                            $msgs[$idx_row]['tipo_retorno'] = 'SMS';
                            $msgs[$idx_row]['mensagem'] = 'O tipo SMS, não foi encontrado';
                        }
                        

                        // Salvar CELULAR 3G
                        $idx_row = $base_idx_row."_".TipoRetorno::TIPO_RETORNO_3G;
                        $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_3G);

                        if($valor_tipo_retorno['TipoRetorno']['codigo']) {
                            $reg = Array();
                            $retonos = Array();
                            $reg['mes'] = $mes;
                            $reg['ano'] = $ano;
                            $conditions_novo_registro = Array();

                            $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_3G);
                            $conditions_novo_registro = array('RegistroTelecom.identificador' => $row[$keys['numero']],
                                                                  'RegistroTelecom.mes' =>  $reg['mes'],
                                                                  'RegistroTelecom.ano' =>  $reg['ano'],
                                                                  'RegistroTelecom.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                                $retonos = $this->find('first', array('conditions' => $conditions_novo_registro));

                                if(is_array($retonos) && count($retonos) > 0) {
                                    $msgs[$idx_row]['linha'] = $base_idx_row;
                                    $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                    $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                    $msgs[$idx_row]['mensagem'] = 'O registro foi ignorado pois já se encontra no cadastro';
                                } else {
                                    $valor = 0;
                                    $qta = 0;
                                    $reg['identificador'] = $row[$keys['numero']];
                                    $reg['codigo_tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['codigo'];
                                    foreach ( $keys_quantia_dados as $value) {
                                       $qta += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$value]));
                                    }
                                    $reg['quantidade'] =  (trim($qta)==""?'0':$qta);
                                    foreach ($keys_valor_dados as $value) {
                                        $valor += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$value]));
                                    }
                                    if(!empty($qta) && !empty($valor)) {
                                        $reg['valor'] =  (trim($valor)==""?'0':$valor) ;
                                        $reg['codigo_operadora'] =  $tipo_codigo;
                                        $conditions_usuario_cadastrado = array('UsuarioContato.descricao LIKE ' => '%'. $numero. '%',
                                                                    'UsuarioContato.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                                        $valor_usuario = $UsuarioContato->find('first', array('conditions' => $conditions_usuario_cadastrado));
                                        $codigo_usuario = $valor_usuario['UsuarioContato']['codigo_usuario'];
                                        if (!((is_array($valor_usuario)) && (count($valor_usuario)>1)) && $tipo_codigo != self::NEXTEL) {
                                            $msgs[$idx_row]['linha'] = $base_idx_row;
                                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                            $msgs[$idx_row]['mensagem'] = 'O tipo não foi encontrado:'.$valor_tipo_retorno['TipoRetorno']['descricao'].'. Favor realizar cadastro no usuário.';
                                            $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                            $msgs[$idx_row]['tipo'] = 'T';
                                        } else {
                                            $reg['codigo_usuario_registro'] = $codigo_usuario;
                                            $salvar = Array('RegistroTelecom'=>$reg);
                                            $this->incluir($salvar);
                                        }
                                    } 
                                    // else {
                                    //     $msgs[$idx_row]['linha'] = $base_idx_row;
                                    //     $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                                    //     $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                                    //     $msgs[$idx_row]['mensagem'] = 'Registro ignorado pois a quantiadade está vazia';
                                    // }
                                }
                        }else {
                            $msgs[$idx_row]['linha'] = $base_idx_row;
                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                            $msgs[$idx_row]['tipo_retorno'] = '3G';
                            $msgs[$idx_row]['mensagem'] = 'O tipo Dados(3G), não foi encontrado';
                        }                           
                    } 

                } elseif(!empty($row[$keys['numero']])) {
                    if ($row[$keys['numero']] == 'Acessos') continue;
                    if (strtolower($row[$keys['numero']]) == 'total geral') break;
                    if(strtolower(($row[$keys['numero']])) == 'buonny proj e serv de riscos securit ltd') {
                        $retonos = Array();
                        $conditions_novo_registro = Array();
                        $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_MENSALIDADE);

                        $reg['numero'] = '00000000003';
                        $reg['mes'] = $mes;
                        $reg['ano'] = $ano;

                        foreach ( $keys_quantia_dados as $value) {
                            $qta += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$value]));
                        }
                        $reg['quantidade'] =  (trim($qta)==""?'0':$qta);
                        foreach ($keys_valor_dados as $value) {
                            $valor += preg_replace("/[^0-9.]/", "", str_replace(',','.',$row[$value]));
                        }
                        $reg['valor'] = $valor;
                        $reg['quantidade'] = $qta;

                        $conditions_novo_registro = array('RegistroTelecom.identificador' => $row[$keys['numero']],
                                                          'RegistroTelecom.mes' =>  $reg['mes'],
                                                          'RegistroTelecom.ano' =>  $reg['ano'],
                                                          'RegistroTelecom.valor' =>  $reg['valor'],
                                                          'RegistroTelecom.quantidade' =>  $reg['quantidade'],
                                                          'RegistroTelecom.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']);
                        $retonos = $this->find('first', array('conditions' => $conditions_novo_registro));
                        if(is_array($retonos) && count($retonos) > 0) {
                            $msgs[$idx_row]['linha'] = $base_idx_row;
                            $msgs[$idx_row]['telefone'] = $row[$keys['numero']];
                            $msgs[$idx_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                            $msgs[$idx_row]['mensagem'] = 'O registro foi ignorado pois já se encontra no cadastro';
                        }
                        continue;
                    }
                    
                    $msgs[$base_idx_row]['linha'] = $base_idx_row;
                    $msgs[$base_idx_row]['telefone'] = $row[$keys['numero']];
                    $msgs[$base_idx_row]['mensagem'] = 'Não é um telefone valido';
                    continue;
                }
                if ($row[1] == 'Total Geral') continue;
            }//fim segundo foreach celulas
        }//fim primeiro foreach data
        return $msgs;
    }

    /*Função para importação de arquivos CSV Padrão Tarifador*/
    function processarArquivoCsvTarifador ($dados, $tipo_codigo = null) {
 
        //Preparando os dados

        $Usuario = ClassRegistry::init('Usuario');
        $TipoRetorno = ClassRegistry::init('TipoRetorno');
        $UsuarioContato = ClassRegistry::init('UsuarioContato');
        $RegistroTelecom = ClassRegistry::init('RegistroTelecom');

        if (isset($dados[1])) unset($dados[1]);
        if (isset($dados[2])) unset($dados[2]);

        $keys = Array(
            'numero' => 0,
            'duracao'=>0,
            'valor'=>0,
        );

        $msgs = Array();
        $total_usuario = Array();

        foreach($dados as $key_data=>$data) {
            $variavel_controle = 0;
            $anomes = $data['cells'][1][2];
            $mes = substr($anomes, '-7', '2');
            $ano = substr($anomes, '-4', '4');

            $valor_total = preg_replace("/[^0-9.]/", "", str_replace(',','.',$data['cells'][1][4]));

            if(empty($ano) || empty($mes)){
                $error['fatal'] = 'Arquivo em formato invalido, houve um problema na localização do mês ou ano';
                return  $error;
            }

            if(empty($valor_total)){
                $error['fatal'] = 'Arquivo em formato invalido, houve um problema na localização do valor total';
                return  $error;
            }

            foreach($data['cells'] as $key_row=>$row) {
                $row = array_map('utf8_encode',$row);
                $row = array_map('trim',$row);

                if ($keys['numero']==0) {
                    foreach($row as $key_cell=>$cell) {
                        if (mb_check_encoding($cell,'utf-8')!=1) $cell = utf8_encode($cell);

                        if($cell === 'Ramal') {
                            $keys['numero'] = $key_cell;
                            $keys['duracao'] = $key_cell+1;
                        }

                    }// fim foreach celulas
                }
                if ( ($keys['numero']!=0) && (isset($row[$keys['numero']])) && ($row[$keys['numero']]!='Ramal') && ($row[$keys['numero']]!='Total Resultado') && ($row[$keys['numero']]!='Total Geral') && ($row[$keys['numero']]!='') ) {
                    $ramal = $row[$keys['numero']];

                    $arrRamal = explode('-',$ramal);
                    $ramal = trim($arrRamal[0]);

                    $param_pesquisa = Array();

                    $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_RAMAL);

                    $param_pesquisa['conditions'] = Array(
                        'UsuarioContato.descricao' => $ramal,
                        'UsuarioContato.codigo_tipo_retorno' => $valor_tipo_retorno['TipoRetorno']['codigo']
                    );

                    $valor_usuario = $UsuarioContato->find('first', $param_pesquisa);
                    if (!((is_array($valor_usuario)) && (count($valor_usuario)>1))) {
                        $msgs[$key_row]['linha'] = $key_row;
                        $msgs[$key_row]['telefone'] = $ramal;
                        $msgs[$key_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                        $msgs[$key_row]['mensagem'] = 'Ramal não encontrado, realizar o registro no cliente.';
                        continue;
                    }
                    
                    $codigo_usuario = $valor_usuario['UsuarioContato']['codigo_usuario'];
                    $codigo_tipo_retorno = $valor_tipo_retorno['TipoRetorno']['codigo'];
                    if(isset($mes) || isset($ano) ) {
                        $conditions_novo_registro = array('RegistroTelecom.identificador' => $ramal,
                                                          'RegistroTelecom.mes' =>  $mes,
                                                          'RegistroTelecom.ano' =>  $ano,
                                                          'RegistroTelecom.codigo_tipo_retorno' => $codigo_tipo_retorno
                                                          );
                        $retornos = $this->find('first', array('conditions' => $conditions_novo_registro));
                    } else {
                            $error['fatal'] = 'Arquivo em formato invalido, houve um problema na localização do mês ou ano';
                            return  $error;
                      }

                    if(is_array($retornos) && count($retornos) > 0) {
                        $msgs[$key_row]['linha'] = $key_row;
                        $msgs[$key_row]['telefone'] = $ramal;
                        $msgs[$key_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                        $msgs[$key_row]['mensagem'] = 'O registro foi ignorado pois já se encontra no cadastro';
                        continue;
                    }

                    if (preg_match('/[^0-9.]/', $row[$keys['duracao']])) {
                        $msgs[$key_row]['linha'] = $key_row;
                        $msgs[$key_row]['telefone'] = $ramal;
                        $msgs[$key_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                        $msgs[$key_row]['mensagem'] = 'Duração das Ligações não está em um formato válido. Por favor, altere a coluna para trabalhar com formato texto.';
                        continue;
                    }

                    if (!isset($total_usuario[$variavel_controle][$codigo_usuario])) {
                        $total_usuario[$variavel_controle][$codigo_usuario] = Array(
                            'codigo_usuario' => $codigo_usuario,
                            'ramal' => $ramal,
                            'qta' =>0,
                            'valor' =>0
                        );
                    } else {
                        $this->log($valor_usuario,'registro_telecom_problema_usuario');
                    }

                    $duracao = $row[$keys['duracao']];
                    //$qta = Comum::time_to_decimal($duracao);
                    $qta = $duracao * (24*60);
                    $total_usuario[$variavel_controle][$codigo_usuario]['qta'] = $qta;
                    $variavel_controle++;

                } elseif (($keys['numero']!=0) && (trim($row[$keys['numero']])=='Total Resultado' || trim($row[$keys['numero']])=='Total Geral')) {
                    $duracao = $row[$keys['duracao']];
                    //if (strpos($duracao,''))
                    //$qta = Comum::time_to_decimal($duracao);
                    $qta = $duracao * (24*60);
                    $qta_total = $qta;
                    $variavel_controle++;
                }
            }
        }

        if (trim($valor_total)=='') {
            $msgs[1]['linha'] = 1;
            $msgs[1]['telefone'] = '';
            $msgs[1]['mensagem'] = 'Valor Total não definido no arquivo';
            break;
        }

        if (!isset($qta_total)) $qta_total = 1;
        foreach ($total_usuario as $key => $usuarios) {
            foreach ($usuarios as $codigo_usuario => $dados_usuario) {
                $reg = Array();
                $reg['mes'] = $mes;
                $reg['ano'] = $ano;
                $numero = $dados_usuario['ramal'];
                // Salvar Celular
                $reg['codigo_usuario_registro'] = $codigo_usuario;
                $reg['identificador'] = $numero;
                $valor_tipo_retorno =  $TipoRetorno->findByCodigo(TipoRetorno::TIPO_RETORNO_RAMAL);
                if($valor_tipo_retorno['TipoRetorno']['codigo']) {
                    if(!empty($dados_usuario['qta'])) {    
                        $reg['codigo_tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['codigo'];
                        $reg['quantidade'] = (trim($dados_usuario['qta'])==""?'0':$dados_usuario['qta']) ;
                        $reg['valor'] = ($valor_total / $qta_total) * $reg['quantidade'] ;
                        $reg['codigo_operadora'] =  $tipo_codigo;
                        $salvar = Array('RegistroTelecom'=>$reg);
                        $this->incluir($salvar);
                    } else {
                        $msgs[$key_row]['linha'] = $key_row;
                        $msgs[$key_row]['telefone'] = $row[$keys['numero']];
                        $msgs[$key_row]['tipo_retorno'] = $valor_tipo_retorno['TipoRetorno']['descricao'];
                        $msgs[$key_row]['mensagem'] = 'Registro ignorado pois a quantiadade está vazia';
                    }
                }else {
                    $msgs[$key_row]['linha'] = $key_row;
                    $msgs[$key_row]['telefone'] = $row[$keys['numero']];
                    $msgs[$key_row]['tipo_retorno'] = 'RAMAL';
                    $msgs[$key_row]['mensagem'] = 'O tipo RAMAL não foi encontrado';
                }
            }
        }
        return $msgs;
    }

    function converteFiltroEmCondition($data) {
        $conditions = array();
        // if(!empty($data['codigo_tipo_cobranca'])) {
        //     if($data['codigo_tipo_cobranca'] == 1) {
        //         $conditions['RegistroTelecom.codigo_tipo_retorno'] = TipoRetorno::TIPO_RETORNO_SMS;
        //     }elseif($data['codigo_tipo_cobranca'] == 2){
        //         if($data['codigo_operadora'] == 4) {
        //             $conditions['RegistroTelecom.codigo_tipo_retorno'] = TipoRetorno::TIPO_RETORNO_RAMAL;
        //         } else {
        //             $conditions['RegistroTelecom.codigo_tipo_retorno'] = TipoRetorno::TIPO_RETORNO_CELULAR;
        //         }
        //     }elseif($data['codigo_tipo_cobranca'] == 3){
        //         $conditions['RegistroTelecom.codigo_tipo_retorno'] = TipoRetorno::TIPO_RETORNO_3G;
        //     }elseif($data['codigo_tipo_cobranca'] == 4){
        //         $conditions['RegistroTelecom.codigo_tipo_retorno'] = TipoRetorno::TIPO_RETORNO_MENSALIDADE;
        //     }
        // }
        if(!empty($data['codigo_tipo_cobranca'])) {
            $conditions['RegistroTelecom.codigo_tipo_retorno'] = $data['codigo_tipo_cobranca'];
        }
        if (!empty($data['codigo_operadora']))
            $conditions['RegistroTelecom.codigo_operadora'] = $data['codigo_operadora'];
        if (!empty($data['mes']))
            $conditions['RegistroTelecom.mes'] = $data['mes'];
        if (!empty($data['ano']))
            $conditions['RegistroTelecom.ano'] = $data['ano'];
        if (!empty($data['apelido']))
            $conditions['Usuario.apelido like'] = '%'.$data['apelido'].'%';
        if (!empty($data['nome']))
            $conditions['Usuario.nome like'] = '%'.$data['nome'].'%';
        if (!empty($data['codigo_departamento']))
            $conditions['Departamento.codigo'] = $data['codigo_departamento'];
        if (!empty($data['identificador'])) {
            if (in_array($data['codigo_tipo_cobranca'],Array(1,3,5,7,8,9,10,11))) {
                $conditions['RegistroTelecom.identificador like'] = '%'.(Comum::soNumero($data['identificador'])).'%';
            } else {
                $conditions['RegistroTelecom.identificador like'] = '%'.$data['identificador'].'%';
            }
        }
        return $conditions;
    }

    function sintetico ($conditions, $agrupamento) {
        $this->Uperfil = ClassRegistry::init('Uperfil');
        if ($agrupamento == self::LOGIN) {
            $fields = array(
                'Usuario.apelido AS descricao',
                'Usuario.apelido AS codigo',
                'COUNT(Usuario.codigo) AS qtd',
                'SUM(RegistroTelecom.valor) AS valor',
                'SUM(RegistroTelecom.quantidade) AS quantidade',
            );
            $group = array(
                'Usuario.apelido',
            );
            $order = 'Usuario.apelido';
        }
   
        if ($agrupamento == self::DEPARTAMENTO) {
            $fields = array(
                'Departamento.descricao AS descricao',  
                'Departamento.codigo AS codigo',  
                'COUNT(Departamento.codigo) AS qtd',
                'SUM(RegistroTelecom.valor) AS valor',
                'SUM(RegistroTelecom.quantidade) AS quantidade',
            );
            $group = array(
                'Departamento.descricao',
                'Departamento.codigo'
            );
            $order = array('Departamento.descricao');
        }

        
        if ($agrupamento == self::OPERADORA) {
            $fields = array(
                "(CASE
                   WHEN RegistroTelecom.codigo_operadora = ".self::CLARO." THEN 'Claro'
                   WHEN RegistroTelecom.codigo_operadora = ".self::VIVO." THEN 'Vivo'
                   WHEN RegistroTelecom.codigo_operadora = ".self::NEXTEL." THEN 'Nextel'
                   WHEN RegistroTelecom.codigo_operadora = ".self::TARIFADOR." THEN 'Tarifador'
                   END)  AS descricao",
                'RegistroTelecom.codigo_operadora AS codigo',
                'COUNT(RegistroTelecom.codigo_operadora) AS qtd',
                'SUM(RegistroTelecom.valor) AS valor',
                'SUM(RegistroTelecom.quantidade) AS quantidade',
            );
            $group = array(
                'RegistroTelecom.codigo_operadora',
            );
            $order = 'RegistroTelecom.codigo_operadora';
        }
        if(($_SESSION['Auth']['Usuario']['codigo_uperfil'] != Uperfil::ADMIN
                && !$_SESSION['Auth']['Usuario']['admin'])  &&  $_SESSION['Auth']['Usuario']['codigo_uperfil'] != Uperfil::OPERADOR_TELECOM
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
        }else{
            $query = '';
        }
        $query .= $this->find('sql',array(
            'fields' => $fields,
            'order' => $order,
            'group' => $group,
            'conditions' => $conditions,
        ));        

        return $this->query($query);
    }

}
?>