<?php
class ImportarSinistroTask extends Shell {	
	var $uses =  array('Sinistro');
    public function importar() {
        $data = $this->retornaDadosImportacao();
        $gravaLogErro = $this->escreveArquivoLogImportacao( TRUE, TRUE );
        foreach($data as $key => $value) {
            try{
                $this->Sinistro->query('begin transaction');
                $this->Sinistro->incluir($value);
                $this->Sinistro->commit();
            } catch (Exception $ex){
                $this->Sinistro->rollback();
                $msg = $value['sm'].' - '.$ex->getMessage();
                file_put_contents( DS.'tmp'.DS.'log_importacao_sinistro.txt' , $msg, FILE_APPEND );
            }    
            if( !$this->Sinistro->id ){
                $errors = NULL;
                foreach( $this->Sinistro->validationErrors as $erro ){
                    $errors .= $erro.' ,';
                }
                $errors .= substr($errors,0,-1);
                $gravaLogErro = $this->escreveArquivoLogImportacao( $value, FALSE,  $errors);
            }
        }
    }

    public function retornaDadosImportacao(){

        $row = 1;
        $handle = fopen (APP.'tmp'.DS.'planilha.csv',"r");
        $dados  = array();
        $i = 0;

        while(($data = fgetcsv($handle, 1000, ",")) !== FALSE) {            
            if( $i != 0 ){
                if( is_numeric($data[1]) ){
                    $hora_evento = ($this->validateDate($data[4], 'H:s') == TRUE ? $data[4]. ':00' : '00:00:00');
                    $latitude    = $this->converteLatitudeLongitude(utf8_encode($data[15]));
                    $longitude   = $this->converteLatitudeLongitude(utf8_encode($data[16]));
                    $msg_erro    = NULL;
                    
                    if( $latitude==0 || $longitude==0 ){                        
                        $this->TRefeReferencia =& ClassRegistry::init('TRefeReferencia');
                        $dados_cidade= explode('-', utf8_encode($data[12]));
                        $cidade = isset($dados_cidade[0]) ? $dados_cidade[0] : NULL;
                        $estado = isset($dados_cidade[1]) ? $dados_cidade[1] : NULL;
                        $local = array(
                            'endereco' => utf8_encode($data[10]),
                            'cidade'   => array(
                                'nome'   => utf8_encode($cidade),
                                'estado' => utf8_encode($estado)
                            )
                        );
                        $xy         = $this->TRefeReferencia->maplinkLocaliza(1);
                        $latitude   =  $xy->getXYResult->y;
                        $longitude  =  $xy->getXYResult->x;
                        if( $latitude == -12.924042 ){
                            $xy         = $this->TRefeReferencia->maplinkLocaliza($local, TRUE );
                            $latitude   =  $xy->getXYResult->y;
                            $longitude  =  $xy->getXYResult->x;
                            $msg_erro   = 'latitude  e latitude não foram encontradas na busca por endereço.';
                        }
                    }
                     $dados[] = array(
                         'sm'               => $data[1],
                         'data_evento'      => $data[2].' '.$hora_evento,
                         'natureza'         => $this->retornaCodigoNatureza(utf8_encode($data[9])),
                         'status_veiculo'   => $this->retornaCodigoStatusVeiculo(utf8_encode($data[30])),
                         'valor_carga'      => str_replace(',','.',str_replace('.','',substr($data[20],3))),
                         'valor_sinistrado' => str_replace(',','.',str_replace('.','',substr($data[21],3))),
                         'valor_recuperado' => str_replace(',','.',str_replace('.','',substr($data[22],3))),
                         'latitude'         => $latitude,
                         'longitude'        => $longitude,
                         'modo_de_operacao' => utf8_encode($data[32]),
                         'observacao'       => utf8_encode($data[33]),
                         'atuacao_central'  => utf8_encode($data[34]),
                         'avalicao_geral'   => $this->retornaCodigoAvaliacao(utf8_encode($data[35])),
                         'endereco'         => utf8_encode($data[10]),
                         'cidade'           => utf8_encode($data[12]),
                         'estado'           => utf8_encode($data[13]),
                     );      

                    // if( $msg_erro != NULL ){
                    //     $erro .=  $msg_erro. ' - '.utf8_encode($data[12]);
                    //     $gravaLogErro = $this->escreveArquivoLogImportacao( $dados, FALSE,  $erro );
                    // }
                 }
            }
            $i++;             
        }

        return $dados;                
    }

    public function converteLatitudeLongitude($cordenada){
        if( !is_numeric($cordenada) ){
            $co = trim($cordenada);            
            $co = str_replace(' ', '', $cordenada);
            $co = str_replace('w', '', $cordenada);
            $co = str_replace('s', '', $cordenada);
            $co = str_replace('"', "''", $cordenada);
            $grau = array_shift(explode('º', $co));
            $minu = array_shift(explode("'", end(explode('º', $co))));
            $seco = explode("'",end(explode('º', $co)));
            $seco = isset($seco[1]) ? $seco[1] : 0;
            return $grau + ($minu/60) + ($seco/3600);
        }else{
            return $cordenada;
        }
    }

    public function retornaCodigoNatureza($natureza){
        switch ($natureza) {
            case 'Roubo Parcial': return 1;              
                break;
            case 'Tentativa': return 3;                 
                break;
            case 'Recuperado': return 0;                 
                break;
            case 'Roubo Total': return 2;                
                break;            
            default: return 1;
                break;
        }
    }

    public function retornaCodigoStatusVeiculo($status){
        switch ($status) {
            case 'Parado': return 2;              
                break;
            case 'Em movimento': return 0;                 
                break;
            case 'Não monitorado': return 1;                 
                break;
            case 'Pernoite': return 3;                
                break;  
            case 'Telemonitorado': return 4;
                break;            
            default: return 2;
                break;
        }
    }

    public function retornaCodigoAvaliacao($avaliacao){
        switch ($avaliacao) {
            case 'Ruim': return 3;              
                break;
            case 'Regular': return 2;                 
                break;
            case 'Ótima': return 0;                 
                break;
            case 'Boa': return 1;                
                break;  
            case 'Péssima': return 4;
                break;            
            default: return 2;
                break;
        }
    }

    public function escreveArquivoLogImportacao( $data, $cabecalho=FALSE, $erro=NULL ){
        $arq_erros_importacao = APP.DS.'tmp'.DS.'erros_importacao_sinistro_'.date('Ymd').'.csv';
        $fp  = fopen($arq_erros_importacao, "a+");
        $array_avaliacao = array(0=>'Ótima', 1=>'Boa', 2=>'Regular', 3=>'Ruim', 4=>'Péssima');
        $array_status_veiculo = array(0=>'Em movimento', 1=>'Não monitorado', 2=>'Parado', 3=>'Pernoite', 4=>'Telemonitorado');
        $array_natureza = array(0=>'Recuperado', 1=>'Roubo Parcial', 2=>'Roubo Total', 3=>'Tentativa');

        if( $cabecalho ==TRUE ){
            fwrite($fp,"SM;data_evento;natureza;status_veiculo;valor_carga;valor_sinistrado;valor_recuperado;latitude;longitude;modo_de_operacao;observacao;atuacao_central;avalicao_geral;endereco;cidade;estado;erro;\r\n");
        } else{
            $natureza        = $array_natureza[$data['natureza']];
            $status_veiculo  = $array_status_veiculo[$data['status_veiculo']];
            $avalicao_geral  = $array_status_veiculo[$data['avalicao_geral']];
            fwrite($fp,"{$data['sm']};{$data['data_evento']};{$natureza};{$status_veiculo};{$data['valor_carga']};{$data['valor_sinistrado']};{$data['valor_recuperado']};{$data['latitude']};{$data['longitude']};{$data['modo_de_operacao']};{$data['observacao']};{$data['atuacao_central']};{$avalicao_geral};{$data['endereco']};{$data['cidade']};{$data['estado']};{$erro}\r\n");
        }
        if(!$fp)
            return false;
        fclose($fp);
        return true;
    }

    public function validateDate($date, $format = 'd/m/Y'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}
?>
 