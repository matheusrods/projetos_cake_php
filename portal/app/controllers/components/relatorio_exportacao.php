<?php
class RelatorioExportacaoComponent extends Object {
    var $name = 'RelatorioExportacao';    

    function __construct(){
        parent::__construct();
    }
    
    public function acompanhamentoViagensAnalitico( $conditions, $download = true, $nome_arquivo = false ){
        $this->TEstaEstado = ClassRegistry::init('TEstaEstado');
        $this->RelatorioSm = ClassRegistry::init('RelatorioSm');
        $query = $this->RelatorioSm->listagem_analitico($conditions, null, null, true);
        $query = str_replace('"', '', $query);
        $dbo   = $this->TEstaEstado->getDataSource();
        $cabecalho = 'SM;"Pedido do Cliente";"Tecnologia";"Transportadora";"Placa/Chassi";"Início Previsto";"Início Real";"Último Alvo";"Previsão Último Alvo";"Entrada Último Alvo";"Saída Último Alvo";"Status Último Alvo";"Posição Atual";"Data Última Posição";"Próximo Alvo";"Previsão Próximo Alvo";"Status Próximo Alvo";"Tempo Restante";"Km Restante";"Status";"Posicionando";"Fim real";"Região 1º Entrega";"Mínima";"Máxima";"Atual";"% Dentro Temp.";"% Fora Temp.";"Solicitante";"LoadPlan";"Alvo Origem";"Alvo Destino";';
        if( $download === true ){
            header('Content-type: application/vnd.ms-excel');
            header(sprintf('Content-Disposition: attachment; filename="%s"', basename('acompanhamento_de_viagens.csv')));
            header('Pragma: no-cache');
            echo iconv('UTF-8', 'ISO-8859-1', $cabecalho );
        }else{
            $this->geraArquivo( $nome_arquivo, comum::trata_nome($cabecalho));
        }
        $data_upos = date('Y-m-d H:i:s',strtotime('-2 hour'));
        $registros = $dbo->fetchAll($query);
        foreach($registros as $registro){
            $registro = $registro[0];
            $data_ultima_posicao_estimada = date('Y-m-d H:i', strtotime("+".(!empty($registro['tempominutosrestante']) ? $registro['tempominutosrestante'] : 0)." minutes", strtotime($registro['dataultimaposicao'].":00")));
            $data_previsao_proximo_alvo = date('Y-m-d H:i', strtotime("+60 minutes", strtotime($registro['previsaoproximoalvo'].":00")));
            $registro['statusproximoalvo'] = ($data_previsao_proximo_alvo < $data_ultima_posicao_estimada ? 'Atrasado' : 'Normal');
            $inicioReal = AppModel::dbDateToDate($registro['inicioreal']);
            $fimReal = AppModel::dbDateToDate($registro['fimreal']);
            $linha = "";
            $linha .= '"'. $registro['sm'] . '";';
            $linha .= '"'. $registro['pedidocliente'] . '";';
            $linha .= '"'. $registro['tecnologia'] . '";';
            $linha .= '"'. iconv('ISO-8859-1', 'UTF-8', $registro['transportadora']) . '";';
            $linha .= '"'. (isset($registro['placa'][0]) && ctype_alpha($registro['placa'][0]) ? preg_replace('/(\w{3})(\d+)/i', "$1-$2", $registro['placa']) : $registro['chassi']) . '";';
            $linha .= '"'. AppModel::dbDateToDate($registro['inicioprevisto']) . '";';
            $linha .= '"'. $inicioReal . '";';
            $linha .= '"'. iconv('ISO-8859-1', 'UTF-8', $registro['ultimoalvo']) . '";';
            $linha .= '"'. AppModel::dbDateToDate($registro['previsaoultimoalvo']) . '";';
            $linha .= '"'. AppModel::dbDateToDate($registro['entradaultimoalvo']) . '";';
            $linha .= '"'. AppModel::dbDateToDate($registro['saidaultimoalvo']) . '";';
            $linha .= '"'. $registro['statusultimoalvo'] . '";';
            $linha .= '"'. iconv('ISO-8859-1', 'UTF-8', $registro['ultimaposicaodescricao']) . '";';
            $linha .= '"'. AppModel::dbDateToDate($registro['dataultimaposicao']) . '";';
            $linha .= '"'. iconv('ISO-8859-1', 'UTF-8', $registro['proximoalvo']) . '";';
            $linha .= '"'. AppModel::dbDateToDate($registro['previsaoproximoalvo']) . '";';
            $linha .= '"'. $registro['statusproximoalvo'] . '";';
            $linha .= '"'. (!empty($registro['temporestante']) ? $registro['temporestante'] : "") . '";';
            $linha .= '"'. (!empty($registro['kmrestante']) ? $registro['kmrestante'] : "") . '";';
            $linha .= '"'. $registro['status'] . '";';
            if(date('Y-m-d H:i:s',Comum::dateToTimestamp($registro['dataultimaposicao'])) >= $data_upos){
                $linha .= '"S";';
            }else{
                $linha .= '"N";';
            }
            $linha .= '"'. $fimReal .'";';
            $linha .= '"'. $registro['regiao_primeiro_alvo'].'";';            
            $linha .= '"'. $registro['temperaturaminima'].'";';
            $linha .= '"'. $registro['temperaturamaxima'].'";';
            $linha .= '"'. $registro['ultimatemperatura'].'";';
            $linha .= '"'. number_format($registro['vtem_percentual_dentro'],2,',','.').'";';
            $linha .= '"'. number_format($registro['vtem_percentual_fora'],2,',','.').'";';
            $linha .= '"'. $registro['solicitante'].'";';
            $linha .= '"'. $registro['loadplans'].'";';            
            $linha .= '"'. $registro['alvoorigem'].'";';
            $linha .= '"'. $registro['alvodestino'].'";';
            if( $download === true ){            
                echo "\n".iconv('UTF-8', 'ISO-8859-1', $linha);
            } else {
                $this->geraArquivo( $nome_arquivo, $linha );
            }
        }
    }

    function consultaGeralSm( $conditions, $download = TRUE, $nome_arquivo = false ){
        $this->RelatorioSm = ClassRegistry::init('RelatorioSm');
        $this->TEstaEstado = ClassRegistry::init('TEstaEstado');
        $query = $this->RelatorioSm->listagem_analitico($conditions, null, null, true, true);
        $query = str_replace('"', '', $query);
        $dbo = $this->TEstaEstado->getDataSource();
        $cabecalho = 'SM;"Placa";"Início";"Fim";"Transportador";"Embarcador";"Gerenciadora";"Estação";"Tecnologia";"Número Terminal";"Previsão Inicio";"Previsão Fim";"Cidade Origem";"Estado Origem";"Cidade Destino";"Estado Destino";"Nome Motorista";"CPF Motorista";"Valor SM"';
        if( $download ===TRUE  ){
            header('Content-type: application/vnd.ms-excel');
            header(sprintf('Content-Disposition: attachment; filename="%s"', basename('consulta_geral_sm.csv')));
            header('Pragma: no-cache');
            echo iconv('UTF-8', 'ISO-8859-1', $cabecalho);
        }else{
            $this->geraArquivo( $nome_arquivo, comum::trata_nome($cabecalho));
        }
        $registros = $dbo->fetchAll($query);

        foreach($registros as $registro){
            $registro   = $registro[0];
            $sm         = $registro['sm'];
            $placa      = isset($registro['placa'][0])         ? preg_replace('/(\w{3})(\d+)/i', "$1-$2", $registro['placa']) : NULL;
            $inicio_real= AppModel::dbDateToDate($registro['inicioreal']);
            $fim_real   = isset($registro['fimreal'])          ? AppModel::dbDateToDate($registro['fimreal']) : NULL ;
            $transportadora  = isset($registro['transportadora'])   ? iconv('ISO-8859-1', 'UTF-8', $registro['transportadora']) : NULL ;
            $embarcador      = isset($registro['embarcador'])       ? iconv('ISO-8859-1', 'UTF-8', $registro['embarcador']) : NULL;
            $gerenciadora    = isset($registro['gerenciadora'])     ? iconv('ISO-8859-1', 'UTF-8', $registro['gerenciadora']) : 'Não Possui Gerenciadora';
            $estacao         = isset($registro['estacao'])          ? $registro['estacao'] : NULL;
            $tecnologia      = isset($registro['tecnologia'])       ? $registro['tecnologia'] : NULL;
            $numero_terminal = isset($registro['numero_terminal'])  ? $registro['numero_terminal'] : NULL;
            $inicioprevisto  = isset($registro['inicioprevisto'])   ? AppModel::dbDateToDate($registro['inicioprevisto']) : NULL;
            $fimprevisto     = isset($registro['fimprevisto'])      ? AppModel::dbDateToDate($registro['fimprevisto']) : NULL;
            $cidade_origem   = isset($registro['cidade_origem'])    ? iconv('ISO-8859-1', 'UTF-8', $registro['cidade_origem']) : NULL;
            $estado_origem   = isset($registro['estado_origem'])    ? iconv('ISO-8859-1', 'UTF-8', $registro['estado_origem']) : NULL;
            $cidade_destino  = isset($registro['cidade_destino'])   ? iconv('ISO-8859-1', 'UTF-8', $registro['cidade_destino']) : NULL;
            $estado_destino  = isset($registro['estado_destino'])   ? iconv('ISO-8859-1', 'UTF-8', $registro['estado_destino']) : NULL;
            $pess_nome       = isset($registro['pess_nome'])        ? iconv('ISO-8859-1', 'UTF-8', $registro['pess_nome']) : NULL;
            $pfis_cpf        = isset($registro['pfis_cpf'])         ? comum::formatarDocumento($registro['pfis_cpf']) : NULL;
            $valor_carga     = isset($registro['valor_carga'])      ? number_format($registro['valor_carga'], 2, ',', '.') : '0,00' ;
            $linha  = "";
            $linha .= '"'. $sm . '";';
            $linha .= '"'. $placa . '";';
            $linha .= '"'. $inicio_real . '";';
            $linha .= '"'. $fim_real. '";';
            $linha .= '"'. $transportadora . '";';
            $linha .= '"'. $embarcador. '";';
            $linha .= '"'. $gerenciadora . '";';
            $linha .= '"'. $estacao . '";';
            $linha .= '"'. $tecnologia . '";';
            $linha .= '"'. $numero_terminal . '";';
            $linha .= '"'. $inicioprevisto. '";';
            $linha .= '"'. $fimprevisto . '";';
            $linha .= '"'. $cidade_origem . '";';
            $linha .= '"'. $estado_origem. '";';
            $linha .= '"'. $cidade_destino. '";';
            $linha .= '"'. $estado_destino. '";';
            $linha .= '"'. $pess_nome. '";';
            $linha .= '"'. $pfis_cpf. '";';
            $linha .= '"'. $valor_carga. '";';
            if( $download ===TRUE  ){
                echo "\n".iconv('UTF-8', 'ISO-8859-1', $linha);
            } else {
                $this->geraArquivo( $nome_arquivo, $linha );            
            }
        }
    }

    public function custosViagensAnalitico( $conditions, $download = true, $nome_arquivo = false ){
        $this->TEstaEstado = ClassRegistry::init('TEstaEstado');
        $this->RelatorioSm = ClassRegistry::init('RelatorioSm');
        $query = $this->RelatorioSm->listagem_custos_da_viagem($conditions, null, null, true);
        $query = str_replace('"', '', $query);
        $dbo   = $this->TEstaEstado->getDataSource();
        $cabecalho = 'SM;"Pedido do Cliente";"Previsao Distancia";"Previsao Litros Combustivel";"Previsao Valor Pedagio";"Distancia Percorrida";"Litros Combustivel";"Valor Pedagio";"Placa";"Alvo Origem";"Alvo Destino";';
        if( $download === true ){
            header('Content-type: application/vnd.ms-excel');
            header(sprintf('Content-Disposition: attachment; filename="%s"', basename('custos_de_viagens.csv')));
            header('Pragma: no-cache');
            echo iconv('UTF-8', 'ISO-8859-1', $cabecalho );
        }else{
            $this->geraArquivo( $nome_arquivo, comum::trata_nome($cabecalho));
        }
        $data_upos = date('Y-m-d H:i:s',strtotime('-2 hour'));
        $viagens = $dbo->fetchAll($query);
        foreach($viagens as $viagem){
            $linha = "";
            $linha .= '"'.$viagem[0]['sm'].'";';
            $linha .= '"'.$viagem[0]['pedidocliente'].'";';
            $linha .= '"'.number_format($viagem[0]['previsaodistancia'],2,',','.').'";';
            $linha .= '"'.number_format($viagem[0]['previsaolitroscombustivel'],2,',','.').'";';
            $linha .= '"'.number_format($viagem[0]['previsaovalorpedagio'],2,',','.').'";';
            $linha .= '"'.number_format($viagem[0]['distanciapercorrida'],2,',','.').'";';
            $linha .= '"'.number_format($viagem[0]['litroscombustivel'],2,',','.').'";';
            $linha .= '"'.number_format($viagem[0]['valorpedagio'],2,',','.').'";';
            $linha .= '"'.(!empty($viagem[0]['placa']) ? preg_replace('/(\w{3})(\d+)/i', "$1-$2", $viagem[0]['placa']) : $viagem[0]['chassi']).'";';
            $linha .= '"'.$viagem[0]['alvoorigem'].'";';
            $linha .= '"'.$viagem[0]['alvodestino'].'";';
            
            if( $download === true ){            
                echo "\n".iconv('UTF-8', 'ISO-8859-1', $linha);
            } else {
                $this->geraArquivo( $nome_arquivo, $linha );
            }
        }
    }

    private function geraArquivo($nome_arquivo, $linha ){
        $fp = fopen("$nome_arquivo", "a+");
        fwrite($fp, $linha."\r\n");
        fclose($fp);
    }
}
?>