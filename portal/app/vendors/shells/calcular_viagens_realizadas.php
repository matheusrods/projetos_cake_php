<?php
class CalcularViagensRealizadasShell extends Shell {
    
    public function main() {
        if (!$this->im_running('calcular_viagens_realizadas'))
            $this->run();
    }
    
    
    public function run($limit = 300) {
        $this->TViagViagem = ClassRegistry::init('TViagViagem');
        $this->TTermTerminal = ClassRegistry::init('TTermTerminal');

        $viagens_calcular = $this->TViagViagem->retornaViagensPendentesCalculo('23/08/2015 12:05:00',null,$limit);
        //debug($viagens_calcular);
        //die;
        //$viagens_calcular = Array(Array('TViagViagem'=>Array('viag_codigo'=>3297793)));
        foreach ($viagens_calcular as $viagem) {
            $resultado = $this->recupera_dados_viagem($viagem['TViagViagem']['viag_codigo']);  
            //debug($resultado);
            if ($resultado['calculado']) {
                $this->TViagViagem->id = $viagem['TViagViagem']['viag_codigo'];
                $resultado['dados_retorno']['viag_codigo'] = $viagem['TViagViagem']['viag_codigo'];
                if (!$this->TViagViagem->save(Array('TViagViagem'=>$resultado['dados_retorno']))) { 
                    $resultado['processado'] = 0;
                }
            }
            
        }
    }

    public function runBetweenDates() {
        $this->TViagViagem = ClassRegistry::init('TViagViagem');
        $this->TTermTerminal = ClassRegistry::init('TTermTerminal');
        $this->TRotaRota = ClassRegistry::init('TRotaRota');
        $this->TVrotViagemRota = ClassRegistry::init('TVrotViagemRota');
        $viagens_calcular = $this->TViagViagem->retornaViagensPendentesCalculoPrevisao('01/06/2015 00:00:00','24/08/2015 23:59:59',null);
        foreach ($viagens_calcular as $seq=> $viagem) {
            $resultado = $this->recupera_dados_previsao_viagem($viagem['TViagViagem']['viag_codigo']);
            //debug($resultado);
            if ($resultado['calculado']) {
                $this->TViagViagem->query('BEGIN TRANSACTION');
                try {

                    $this->TViagViagem->id = $viagem['TViagViagem']['viag_codigo'];
                    $dados_viagem = Array(
                        'viag_codigo' => $viagem['TViagViagem']['viag_codigo'],
                        'viag_previsao_litros' => $resultado['dados_retorno']['viag_previsao_litros'],
                        'viag_previsao_pedagio' => $resultado['dados_retorno']['viag_previsao_pedagio'],
                        'viag_distancia' => $resultado['dados_retorno']['viag_distancia']
                    );
                    $ret = $this->TViagViagem->save(Array('TViagViagem'=>$dados_viagem),false);
                    //debug($ret);
                    if (!$ret) { 
                        $resultado['processado'] = 0;
                        $this->TViagViagem->rollback();
                        continue;
                    }
                    if (!empty($resultado['dados_retorno']['vrot_codigo'])) {
                        $dados_vrot = Array(
                            'vrot_codigo' => $resultado['dados_retorno']['vrot_codigo'],
                            'vrot_previsao_valor_combustivel' => $resultado['dados_retorno']['vrot_previsao_valor_combustivel'],
                            'vrot_previsao_quantia_combustivel' => $resultado['dados_retorno']['viag_previsao_litros'],
                            'vrot_previsao_valor_pedagio' => $resultado['dados_retorno']['viag_previsao_pedagio'],
                            'vrot_previsao_distancia' => $resultado['dados_retorno']['viag_distancia']
                        );
                        if (!$this->TVrotViagemRota->save(Array('TVrotViagemRota'=>$dados_vrot))) { 
                            $resultado['processado'] = 0;
                            $this->TViagViagem->rollback();
                            continue;
                        }
                    }
                    if (!empty($resultado['dados_retorno']['rota_codigo'])) {
                        if(!$this->TRotaRota->atualiza_informacoes_de_custo($resultado['dados_retorno']['rota_codigo'], $resultado['dados_retorno']['vrot_previsao_valor_combustivel'], $resultado['dados_retorno']['viag_previsao_pedagio'], $resultado['dados_retorno']['viag_previsao_litros'], $resultado['dados_retorno']['viag_distancia'])) {
                            $resultado['processado'] = 0;
                            $this->TViagViagem->rollback();
                            continue;
                        }
                    }
                    $this->TViagViagem->commit();
                } catch(Exception $e) {
                    $resultado['processado'] = 0;
                    $this->TViagViagem->rollback();
                    continue;                    
                }
            }
            echo "Processada Viagem $seq:".$viagem['TViagViagem']['viag_codigo']."\n";
        }
        $viagens_calcular = $this->TViagViagem->retornaViagensPendentesCalculo('01/06/2015 00:00:00','24/08/2015 23:59:59',null,true);
        foreach ($viagens_calcular as $seq=> $viagem) {
            $resultado = $this->recupera_dados_viagem($viagem['TViagViagem']['viag_codigo']);
            if ($resultado['calculado']) {
                $this->TViagViagem->id = $viagem['TViagViagem']['viag_codigo'];
                $resultado['dados_retorno']['viag_codigo'] = $viagem['TViagViagem']['viag_codigo'];
                if (!$this->TViagViagem->save(Array('TViagViagem'=>$resultado['dados_retorno']))) { 
                    $resultado['processado'] = 0;
                }
            }
            echo "Processada Viagem $seq: ".$viagem['TViagViagem']['viag_codigo']."\n";
        }
    }

    private function recupera_dados_previsao_viagem($viag_codigo) {
        $this->TViagViagem->bindTRotaRota();
        $dados_viagem = $this->TViagViagem->carregar($viag_codigo);
        App::import('Component','Maplink');
        $this->Maplink = new MaplinkComponent();

        try {
            $TVlocViagemLocal = ClassRegistry::init('TVlocViagemLocal');
            $TPcomPrecoCombustivel = ClassRegistry::init('TPcomPrecoCombustivel');
            $locais = $TVlocViagemLocal->buscaItinerarioSemNotas($dados_viagem['TViagViagem']['viag_codigo_sm']);
            $sigla_estado = $locais[0]['TEstaEstado']['esta_sigla'];
            $valor_gasolina = $TPcomPrecoCombustivel->buscarPorSiglaEstado($sigla_estado);
            $valor_gasolina['TPcomPrecoCombustivel']['pcom_valor'] = !empty($valor_gasolina['TPcomPrecoCombustivel']['pcom_valor']) ? $valor_gasolina['TPcomPrecoCombustivel']['pcom_valor'] : 1;

            if (!empty($dados_viagem['TRotaRota']['rota_codigo'])) {
                $TRotaRota = ClassRegistry::init('TRotaRota');
                $pontos = $TRotaRota->retornaPontosRota($dados_viagem['TRotaRota']['rota_codigo']);
            } else {
                //$locais = $TVlocViagemLocal->buscaItinerarioSemNotas($dados_viagem['TViagViagem']['viag_codigo_sm']);
                $pontos = array_map(function($item) {
                    return Array(
                        'latitude' => $item['TRefeReferencia']['refe_latitude'],
                        'longitude' =>$item['TRefeReferencia']['refe_longitude'],
                    );
                },$locais);
            }
            $array_viagens = Array();
            foreach ($pontos as $key => $ponto) {
                if (isset($pontos[$key-1])) {
                    $array_viagens[] = Array(
                        'latitude_origem' => $pontos[$key-1]['latitude'],
                        'longitude_origem' => $pontos[$key-1]['longitude'],
                        'latitude_destino' => $pontos[$key]['latitude'],
                        'longitude_destino' => $pontos[$key]['longitude'],
                    );
                }
            }
            $dados_veiculos = $this->retorna_dados_veiculos($dados_viagem['TViagViagem']['viag_codigo_sm']);

            $distancia_percorrida = 0;
            $litros_combustivel = 0;
            $valor_pedagio = 0;
            $total_combustivel = 0;

            foreach ($array_viagens as $key => $viagem) {
                extract($viagem);
                extract($dados_veiculos);
                $valores_viagens = $this->Maplink->calcula_valores_viagens($latitude_origem, $longitude_origem, $latitude_destino, $longitude_destino, $consumo_medio, $qtd_eixos, $dados_viagem['TViagViagem']['viag_codigo_sm'], MaplinkComponent::TIPO_CALCULO_PREVISTO);

                $total_combustivel += isset($valores_viagens['valor_combustivel']) ? $valores_viagens['valor_combustivel']*$valor_gasolina['TPcomPrecoCombustivel']['pcom_valor'] : 0;
                $distancia_percorrida += (isset($valores_viagens['distancia']) ? $valores_viagens['distancia'] : 0);
                $litros_combustivel += (isset($valores_viagens['quantia_combustivel']) ? $valores_viagens['quantia_combustivel'] : 0);
                $valor_pedagio += (isset($valores_viagens['valor_pedagio']) ? $valores_viagens['valor_pedagio'] : 0);

            }

            $dados_retorno = Array(
                'viag_previsao_litros' => $litros_combustivel,
                'viag_previsao_pedagio' => $valor_pedagio,
                'viag_distancia' => $distancia_percorrida,
                'vrot_previsao_valor_combustivel' => $total_combustivel,
                'rota_codigo' => $dados_viagem['TRotaRota']['rota_codigo'],
                'vrot_codigo' => $dados_viagem['TVrotViagemRota']['vrot_codigo'],
            );

            return Array(
                'processado' => 1,
                'calculado' => 1,
                'dados_retorno' => $dados_retorno,
                'observacao' => 'Cálculo realizado'
            );    

        } catch(Exception $e) {
            return Array(
                'processado' => 0,
                'calculado' => 0,
                'observacao' => 'Erro ao calcular: '.$e->getMessage()
            );
        }
    }


    private function recupera_dados_viagem($viag_codigo) {
        $this->TViagViagem->bindTTermPrincipal();
        $dados_viagem = $this->TViagViagem->carregar($viag_codigo);
        App::import('Component','Maplink');
        $this->Maplink = new MaplinkComponent();

        try {

            $term_codigo = (!empty($dados_viagem['TTermTerminal']) ? $dados_viagem['TTermTerminal']['term_codigo'] : '');
            if ($term_codigo == '') {
                return Array(
                    'processado' => 1,
                    'calculado' => 0,
                    'observacao' => 'Veículo da Viagem não possui terminal. Não foi realizado o cálculo'
                );
            }


            $array_viagem = $this->montaArrayViagemComWaypoints($term_codigo, $dados_viagem['TViagViagem']['viag_data_inicio'], $dados_viagem['TViagViagem']['viag_data_fim']);
            //$array_viagens = $this->montaArrayViagens($term_codigo, $dados_viagem['TViagViagem']['viag_data_inicio'], $dados_viagem['TViagViagem']['viag_data_fim']);
            $dados_veiculos = $this->retorna_dados_veiculos($dados_viagem['TViagViagem']['viag_codigo_sm']);

            $distancia_percorrida = 0;
            $litros_combustivel = 0;
            $valor_pedagio = 0;

            //foreach ($array_viagens as $key => $viagem) {
            extract($array_viagem);
            //extract($viagem);
            extract($dados_veiculos);

            //$distancia = (Comum::distancia_entre_dois_pontos($latitude_origem,$longitude_origem, $latitude_destino, $longitude_destino));

            //if ($distancia<=0.5) continue;

            //$valores_viagens = $this->Maplink->calcula_valores_viagens($latitude_origem,$longitude_origem, $latitude_destino, $longitude_destino, $consumo_medio, $qtd_eixos, $dados_viagem['TViagViagem']['viag_codigo_sm'], MaplinkComponent::TIPO_CALCULO_REALIZADO);
            if (isset($origem) && isset($destino)) {
                $valores_viagens = $this->Maplink->calcula_valores_viagens_com_waypoints($origem, $destino,$waypoints, $consumo_medio, $qtd_eixos, $dados_viagem['TViagViagem']['viag_codigo_sm'], MaplinkComponent::TIPO_CALCULO_REALIZADO);

                $distancia_percorrida += (isset($valores_viagens['distancia']) ? $valores_viagens['distancia'] : 0);
                $litros_combustivel += (isset($valores_viagens['quantia_combustivel']) ? $valores_viagens['quantia_combustivel'] : 0);
                $valor_pedagio += (isset($valores_viagens['valor_pedagio']) ? $valores_viagens['valor_pedagio'] : 0);
            }
            //}

            $dados_retorno = Array(
                'viag_litros_combustivel' => $litros_combustivel,
                'viag_valor_pedagio' => $valor_pedagio,
                'viag_distancia_percorrida' => $distancia_percorrida
            );

            return Array(
                'processado' => 1,
                'calculado' => 1,
                'dados_retorno' => $dados_retorno,
                'observacao' => 'Cálculo realizado'
            );            

        } catch(Exception $e) {
            return Array(
                'processado' => 0,
                'calculado' => 0,
                'observacao' => 'Erro ao calcular: '.$e->getMessage()
            );
        }
    }

    private function retorna_dados_veiculos($viag_codigo_sm) {
        $this->TVveiViagemVeiculo = ClassRegistry::init('TVveiViagemVeiculo');


        $veiculos = $this->TVveiViagemVeiculo->porViagem($viag_codigo_sm);
        $qtd_eixos = 0;
        $consumo_medio = 0;
        foreach ($veiculos as $veiculo) {
            $qtd_eixos += $veiculo[0]['qtd_eixos'];
            if ($veiculo['TVveiViagemVeiculo']['vvei_precedencia']) $consumo_medio = $veiculo[0]['consumo_medio'];
        }

        return compact('qtd_eixos','consumo_medio');
    }

    private function montaArrayViagens($term_codigo, $data_inicial, $data_final) {
        
        $data_inicial = $this->TViagViagem->dateToDbDate($data_inicial);
        $data_final = $this->TViagViagem->dateToDbDate($data_final);

        $posicoes = $this->TTermTerminal->historico_posicoes_terminal($term_codigo, $data_inicial, $data_final, 2);

        if (!(is_array($posicoes)) && count($posicoes)>0) return Array();
        if (!isset($posicoes[0])) return Array();

        if (!(is_array($posicoes[0])) && count($posicoes[0])>0) return Array();
        if (!isset($posicoes[0][0])) return Array();


        $posicao_ini = Array(
            'latitude'=>$posicoes[0][0]['latitude'],
            'longitude'=>$posicoes[0][0]['longitude'],
        );
        $meia_hora = 30*60;
        $proxima_data = date('Y-m-d H:i:s', strtotime($data_inicial)+$meia_hora);
        $ultima_chave = 0;

        $viagens = Array();
        $key = 0;
        foreach ($posicoes as $key => $posicao) {
            if (strtotime($posicao[0]['data_inicial']) > strtotime($proxima_data)) {
                $viagens[] = Array(
                    'latitude_origem' => $posicao_ini['latitude'],
                    'longitude_origem' => $posicao_ini['longitude'],
                    'latitude_destino' => $posicao[0]['latitude'],
                    'longitude_destino' => $posicao[0]['longitude'],
                );
                $posicao_ini = $posicao[0];
                $proxima_data = date('Y-m-d H:i:s', strtotime($proxima_data)+$meia_hora);
                $ultima_chave = $key;
            }
        }        
        if ($key>$ultima_chave) {
            if (is_array($posicao)) {
                $viagens[] = Array(
                    'latitude_origem' => $posicao_ini['latitude'],
                    'longitude_origem' => $posicao_ini['longitude'],
                    'latitude_destino' => $posicao[0]['latitude'],
                    'longitude_destino' => $posicao[0]['longitude'],
                );
            }
        }

        return $viagens;

    }

    private function montaArrayViagemComWaypoints($term_codigo, $data_inicial, $data_final) {
        
        $data_inicial = $this->TViagViagem->dateToDbDate($data_inicial);
        $data_final = $this->TViagViagem->dateToDbDate($data_final);

        $posicoes = $this->TTermTerminal->historico_posicoes_terminal($term_codigo, $data_inicial, $data_final, 2);

        if (!(is_array($posicoes)) && count($posicoes)>0) return Array();
        if (!isset($posicoes[0])) return Array();

        if (!(is_array($posicoes[0])) && count($posicoes[0])>0) return Array();
        if (!isset($posicoes[0][0])) return Array();


        $origem = Array(
            'latitude'=>$posicoes[0][0]['latitude'],
            'longitude'=>$posicoes[0][0]['longitude'],
        );
        $posicao_ini = $origem;
        $destino = null;
        $meia_hora = 30*60;
        $proxima_data = date('Y-m-d H:i:s', strtotime($data_inicial)+$meia_hora);
        $ultima_chave = 0;

        $waypoints = Array();
        $key = 0;
        foreach ($posicoes as $key => $posicao) {
            if (strtotime($posicao[0]['data_inicial']) > strtotime($proxima_data)) {

                $distancia = (Comum::distancia_entre_dois_pontos($posicao_ini['latitude'],$posicao_ini['longitude'], $posicao[0]['latitude'], $posicao[0]['longitude']));

                if ($distancia>0.5) {
                    $waypoints[] = Array(
                        'latitude' => $posicao[0]['latitude'],
                        'longitude' => $posicao[0]['longitude'],
                    );
                    $posicao_ini = $posicao[0];
                    $ultima_chave = $key;
                }
                $proxima_data = date('Y-m-d H:i:s', strtotime($proxima_data)+$meia_hora);
            }
        }
        if ($key>$ultima_chave) {
            if (is_array($posicao)) {
                $destino = Array(
                    'latitude' => $posicao[0]['latitude'],
                    'longitude' => $posicao[0]['longitude'],
                );
            }
        }
        if (empty($destino)) {
            $destino = array_pop($waypoints);
        }
        $viagem = compact('origem','destino','waypoints');

        return $viagem;

    }    


    private function im_running($tipo) {
        if (PHP_OS!='WINNT') {
            $cmd = shell_exec("ps aux | grep '{$tipo}'");
            // 1 execução é a execução atual
            return substr_count($cmd, 'cake.php -working') > 1;
        } else {
            $cmd = `tasklist /v | findstr /R /C:"{$tipo}"`;
            $ret = substr_count($cmd, 'cake\console\cake') > 1;         
        }
    }
    
}
?>