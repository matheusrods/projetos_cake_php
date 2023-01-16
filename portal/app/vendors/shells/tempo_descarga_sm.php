<?php

class TempoDescargaSmShell extends Shell {
    var $uses = array(
    	'TViagViagem',
    	'TVlocViagemLocal',
        'TVlevViagemLocalEvento',
        'TRperRecebimentoPeriferico',
        'TRmacRecebimentoMacro',
        'TRmliRecebimentoMensagLivre',
        'TRefeReferencia',
        'TRposRecebimentoPosicao',
        'TVterViagemTerminal',
        'TTermTerminal',
    	'TMpadMacroPadrao',
        'TVestViagemEstatus'
    );
    private $inAberturaBau = array(
        'CHEGADA NA LOJA',
        'PARADA PREVISTA ENTREGA',
        'PARADA PARA ENTREGA PERCURSO',
        'CHEGADA NO CLIENTE CROSSDOCKING',
        'CHEGADA NO CLIENTE AGUARD DESCARGA',
        'ABRIR BAU',
        'CHEGADA AO CLIENTE',
        'CHEGADA CLIENTE',
        'CHEGADA CLIENTE INOCIO DE DESCARG',
        'LIBERAR BAU',
        'PARADA ENTREGA',
        'PARADA MOTIVO ENTREGAS',
        'PARADA PARA ENTREGA/CLIENTE',
    );
    private $inFechamentoBau = array(
        'INICIO DE VIAGEM CARREGADO',
        'INICIO DE VIAGEM: CARREGADO',
        'INCIO DE VIAGEM CARREGADO',
    );
    
    function main() {
        echo "Funcoes: \n";
        echo "=> preencher_descarga \n";
    }

    function is_alive(){
        $retorno = shell_exec("ps -ef | grep \"tempo_descarga_sm \" | wc -l");
        return ($retorno > 3);
    }

    function run(){
        if($this->is_alive())
            return false;
        
        $this->preencher_descarga();
    }

    function preencher_descarga(){
        echo "CARREGANDO VLOCS ";
		$inicio = null;
		$fim = null;
		if(isset($this->args[0]) and !is_null($this->args[0])){
			$inicio = $this->args[0];
		}
		if(isset($this->args[1]) and !is_null($this->args[1])){
			$fim = $this->args[1];
		}
		$vlocs = $this->carrega_vloc($inicio, $fim);

        $qtd = count($vlocs);
        $atual = 0;
        echo "- OK\n\n";
        if($vlocs){
            foreach($vlocs as $vloc){
                $atual++;
                echo "- SM {$vloc['TViagViagem']['viag_codigo_sm']} ----------------------------------- [".$atual."/".$qtd."] ".(number_format($atual*100/$qtd,2))."%\n";

                echo "| {$vloc['TVlocViagemLocal']['vloc_codigo']} - {$vloc['TRefeReferencia']['refe_codigo']}\n";
                if($vloc){
                    echo "|   Carregando RPERs ";
                    $rpers = $this->carrega_rper($vloc);

                    if($rpers){
                        echo "- OK\n";
                        $data_abertura_bau = NULL;
                        $data_fechamento_bau = NULL;
                        $data_fechamento_bau_indice = NULL;

                        usort($rpers,function($a,$b){
                            return $a['TRperRecebimentoPeriferico']['rper_data_computador_bordo'] == $b['TRperRecebimentoPeriferico']['rper_data_computador_bordo'] ? 0 : $a['TRperRecebimentoPeriferico']['rper_data_computador_bordo'] < $b['TRperRecebimentoPeriferico']['rper_data_computador_bordo'] ? -1 : 1;
                        });

                        foreach($rpers as $key => $rper){
                            if($data_abertura_bau == NULL && $rper['TRperRecebimentoPeriferico']['rper_valor'] == 1){
                                $data_abertura_bau = $rper['TRperRecebimentoPeriferico']['rper_data_computador_bordo'];
                            }
                            if($rper['TRperRecebimentoPeriferico']['rper_valor'] == 0){
                                $data_fechamento_bau = $rper['TRperRecebimentoPeriferico']['rper_data_computador_bordo'];
                                $data_fechamento_bau_indice = $key;
                            }
                        }
                        if($vloc['TVlocViagemLocal']['vloc_tpar_codigo'] == 4){
                            foreach($rpers as $key => $rper){
                                if($key >= $data_fechamento_bau_indice)
                                    break;

                                if($rper['TRperRecebimentoPeriferico']['rper_valor'] == 1)
                                    $data_abertura_bau = $rper['TRperRecebimentoPeriferico']['rper_data_computador_bordo'];
                            }
                        }
                        if($data_abertura_bau || $data_fechamento_bau){
                            $vloc['TVlocViagemLocal']['vloc_data_abertura_bau'] = $data_abertura_bau;
                            $vloc['TVlocViagemLocal']['vloc_data_fechamento_bau'] = $data_fechamento_bau;
                            $vloc['TVlocViagemLocal']['vloc_usuario_alterou'] = 'TEMPO DESCARGA RPER';
                            if($this->TVlocViagemLocal->atualizar($vloc)){
                                echo "| VIAGEM LOCAL {$vloc['TVlocViagemLocal']['vloc_codigo']} ATUALIZADO\n";continue;
                            }else{
                                echo "| ERRO - {$vloc['TVlocViagemLocal']['vloc_codigo']}\n";die;
                            }
                        }
                    }else{
                        echo "- RPER nao encontrado\n";
                    }

                    echo "|   Carregando RMACs ";
                    $rmacs = $this->carrega_rmac($vloc);
                    
                    if($rmacs){
                        echo "- OK\n";
                        $data_abertura_bau = NULL;
                        $data_fechamento_bau = NULL;
                        $data_fechamento_bau_indice = NULL;

                        usort($rmacs,function($a,$b){
                            return $a['TRmacRecebimentoMacro']['rmac_data_computador_bordo'] == $b['TRmacRecebimentoMacro']['rmac_data_computador_bordo'] ? 0 : $a['TRmacRecebimentoMacro']['rmac_data_computador_bordo'] < $b['TRmacRecebimentoMacro']['rmac_data_computador_bordo'] ? -1 : 1;
                        });

                        foreach($rmacs as $key => $rmac){
                            if($data_abertura_bau == NULL && in_array($rmac['TMpadMacroPadrao']['mpad_tmac_codigo'], $this->TRmacRecebimentoMacro->retorna_codigos_bau('A'))) {
                                $data_abertura_bau = $rmac['TRmacRecebimentoMacro']['rmac_data_computador_bordo'];
                            }

                            if (in_array($rmac['TMpadMacroPadrao']['mpad_tmac_codigo'], $this->TRmacRecebimentoMacro->retorna_codigos_bau('F'))) {
                                $data_fechamento_bau = $rmac['TRmacRecebimentoMacro']['rmac_data_computador_bordo'];
                                $data_fechamento_bau_indice = $key;
                            }
                        }
                        if($vloc['TVlocViagemLocal']['vloc_tpar_codigo'] == 4){
                            foreach($rmacs as $key => $rmac){
                                if($key >= $data_fechamento_bau_indice)
                                    break;
                                
                                if (in_array($rmac['TRmacRecebimentoMacro']['mpad_tmac_codigo'], $this->TRmacRecebimentoMacro->retorna_codigos_bau('A')))
                                    $data_abertura_bau = $rmac['TRmacRecebimentoMacro']['rmac_data_computador_bordo'];
                            }
                        }
                        if($data_abertura_bau || $data_fechamento_bau){
                            $vloc['TVlocViagemLocal']['vloc_data_abertura_bau'] = $data_abertura_bau;
                            $vloc['TVlocViagemLocal']['vloc_data_fechamento_bau'] = $data_fechamento_bau;
                            $vloc['TVlocViagemLocal']['vloc_usuario_alterou'] = 'TEMPO DESCARGA RMAC';
                            if($this->TVlocViagemLocal->atualizar($vloc)){
                                echo "| VIAGEM LOCAL {$vloc['TVlocViagemLocal']['vloc_codigo']} ATUALIZADO\n";continue;
                            }else{
                                echo "| ERRO - {$vloc['TVlocViagemLocal']['vloc_codigo']}\n";die;
                            }
                        }
                    }else{
                        echo "- RMAC nao encontrado\n";
                    }

                    echo "|   Carregando RMLIs ";
                    $rmlis = $this->carrega_rmli($vloc);
                    
                    if($rmlis){
                        echo "- OK\n";
                        $data_abertura_bau = NULL;
                        $data_fechamento_bau = NULL;
                        $data_fechamento_bau_indice = NULL;

                        usort($rmlis,function($a,$b){
                            return $a['TRmliRecebimentoMensagLivre']['rmli_data_computador_bordo'] == $b['TRmliRecebimentoMensagLivre']['rmli_data_computador_bordo'] ? 0 : $a['TRmliRecebimentoMensagLivre']['rmli_data_computador_bordo'] < $b['TRmliRecebimentoMensagLivre']['rmli_data_computador_bordo'] ? -1 : 1;
                        });

                        foreach($rmlis as $key => $rmli){
                            if($data_abertura_bau == NULL && in_array($rmli['TRmliRecebimentoMensagLivre']['rmli_texto'],$this->inAberturaBau)){
                                $data_abertura_bau = $rmli['TRmliRecebimentoMensagLivre']['rmli_data_computador_bordo'];
                            }
                            if(in_array($rmli['TRmliRecebimentoMensagLivre']['rmli_texto'],$this->inFechamentoBau)){
                                $data_fechamento_bau = $rmli['TRmliRecebimentoMensagLivre']['rmli_data_computador_bordo'];
                                $data_fechamento_bau_indice = $key;
                            }
                        }
                        if($vloc['TVlocViagemLocal']['vloc_tpar_codigo'] == 4){
                            foreach($rmlis as $key => $rmli){
                                if($key >= $data_fechamento_bau_indice)
                                    break;

                                if(in_array($rmli['TRmliRecebimentoMensagLivre']['rmli_texto'],$this->inAberturaBau))
                                    $data_abertura_bau = $rmli['TRmliRecebimentoMensagLivre']['rmli_data_computador_bordo'];
                            }
                        }
                        if($data_abertura_bau || $data_fechamento_bau){
                            $vloc['TVlocViagemLocal']['vloc_data_abertura_bau'] = $data_abertura_bau;
                            $vloc['TVlocViagemLocal']['vloc_data_fechamento_bau'] = $data_fechamento_bau;
                            $vloc['TVlocViagemLocal']['vloc_usuario_alterou'] = 'TEMPO DESCARGA RMLI';
                            if($this->TVlocViagemLocal->atualizar($vloc)){
                                echo "| VIAGEM LOCAL {$vloc['TVlocViagemLocal']['vloc_codigo']} ATUALIZADO\n";
                            }else{
                                echo "| ERRO - {$vloc['TVlocViagemLocal']['vloc_codigo']}\n";die;
                            }
                        }
                    }else{
                        echo "- RMLI nao encontrado\n";
                    }
                }else{
                    echo "|   VLOC vazia\n";
                }
                echo "-------------------------------------------------\n\n";
        	}
        }else{
			if(is_null($inicio))
				$inicio = date('Y-m-d H:i', strtotime('-2 days'));
			if(is_null($fim))
				$fim = date('Y-m-d H:i');
            echo "- Nenhuma VLOC encontrada no periodo de {$inicio} a {$fim}";
        }
    }

    private function carrega_vter($viag_codigo){
        return $this->TVterViagemTerminal->find('first',array(
            'conditions' => array(
                'TVterViagemTerminal.vter_viag_codigo' => $viag_codigo,
                'TVterViagemTerminal.vter_precedencia' => 1,
            ),
            'joins' => array(
                array(
                    'table' => "{$this->TTermTerminal->databaseTable}.{$this->TTermTerminal->tableSchema}.{$this->TTermTerminal->useTable}",
                    'alias' => 'TTermTerminal',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TTermTerminal.term_codigo = TVterViagemTerminal.vter_term_codigo',
                    )
                ),
            ),
            'fields' => array(
                '"TVterViagemTerminal"."vter_codigo"',
                '"TVterViagemTerminal"."vter_term_codigo"',
                '"TTermTerminal"."term_numero_terminal"',
                '"TTermTerminal"."term_vtec_codigo"',
                '"TTermTerminal"."term_gmac_veiculo_central"',
            ),
        ));
    }

    private function carrega_vloc($inicio = null, $fim = null){
		$conditions = array(
                'TVlocViagemLocal.vloc_tpar_codigo' => array(3,4),
                'TVlocViagemLocal.vloc_data_abertura_bau IS NULL',
                'TVlocViagemLocal.vloc_data_fechamento_bau IS NULL'                 
            );
		if(is_null($inicio) && is_null($fim)){
			$conditions['TViagViagem.viag_data_fim >= ?'] = array(date('Y-m-d H:i', strtotime('-30 minutes')));
		}else if(is_null($fim)){
			$conditions['TViagViagem.viag_data_fim >= ?'] = array($inicio);
		}else{
			$conditions['TViagViagem.viag_data_fim BETWEEN ? AND ?'] = array($inicio, $fim);
		}
        return $this->TVlocViagemLocal->find('all',array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => "{$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable}",
                    'alias' => 'TRefeReferencia',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TRefeReferencia.refe_codigo = TVlocViagemLocal.vloc_refe_codigo',
                    )
                ), 
                array(
                    'table' => "{$this->TVlevViagemLocalEvento->databaseTable}.{$this->TVlevViagemLocalEvento->tableSchema}.{$this->TVlevViagemLocalEvento->useTable}",
                    'alias' => 'TVlevViagemLocalEventoEntrada',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TVlevViagemLocalEventoEntrada.vlev_vloc_codigo = TVlocViagemLocal.vloc_codigo',
                        'TVlevViagemLocalEventoEntrada.vlev_tlev_codigo' => 1,
                        'OR' => array('TVlevViagemLocalEventoEntrada.vlev_data IS NOT NULL','TVlocViagemLocal.vloc_tpar_codigo' => 4),
                    )
                ), 
                array(
                    'table' => "{$this->TVlevViagemLocalEvento->databaseTable}.{$this->TVlevViagemLocalEvento->tableSchema}.{$this->TVlevViagemLocalEvento->useTable}",
                    'alias' => 'TVlevViagemLocalEventoSaida',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TVlevViagemLocalEventoSaida.vlev_vloc_codigo = TVlocViagemLocal.vloc_codigo',
                        'TVlevViagemLocalEventoSaida.vlev_tlev_codigo' => 8,
                        'TVlevViagemLocalEventoSaida.vlev_data IS NOT NULL',
                    )
                ), 
                array(
                    'table' => "{$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable}",
                    'alias' => 'TViagViagem',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TViagViagem.viag_codigo = TVlocViagemLocal.vloc_viag_codigo',
                    )
                ), 
                array(
                    'table' => "{$this->TVterViagemTerminal->databaseTable}.{$this->TVterViagemTerminal->tableSchema}.{$this->TVterViagemTerminal->useTable}",
                    'alias' => 'TVterViagemTerminal',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TVterViagemTerminal.vter_viag_codigo = TViagViagem.viag_codigo',
                    )
                ), 
                array(
                    'table' => "{$this->TTermTerminal->databaseTable}.{$this->TTermTerminal->tableSchema}.{$this->TTermTerminal->useTable}",
                    'alias' => 'TTermTerminal',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TTermTerminal.term_codigo = TVterViagemTerminal.vter_term_codigo',
                    )
                ),
                array(
                    'table' => "{$this->TVestViagemEstatus->databaseTable}.{$this->TVestViagemEstatus->tableSchema}.{$this->TVestViagemEstatus->useTable}",
                    'alias' => 'TVestViagemEstatus',
                    'type' => 'LEFT',
                    'conditions' => array(
                        "TVestViagemEstatus.vest_viag_codigo = TViagViagem.viag_codigo",
                        "TVestViagemEstatus.vest_estatus <> '2'"
                    )
                ),

            ),
            'fields' => array(
                '"TVlocViagemLocal"."vloc_codigo"',
                '"TVlocViagemLocal"."vloc_tpar_codigo"',
                '"TVlevViagemLocalEventoEntrada"."vlev_data" AS data_entrada_alvo',
                '"TVlevViagemLocalEventoSaida"."vlev_data" AS data_saida_alvo',
                '"TRefeReferencia"."refe_codigo"',
                '"TRefeReferencia"."refe_latitude_min"',
                '"TRefeReferencia"."refe_latitude_max"',
                '"TRefeReferencia"."refe_longitude_min"',
                '"TRefeReferencia"."refe_longitude_max"',
                '"TViagViagem"."viag_codigo"',
                '"TViagViagem"."viag_codigo_sm"',
                '"TViagViagem"."viag_data_cadastro"',
                '"TViagViagem"."viag_data_fim"',
                '"TVterViagemTerminal"."vter_codigo"',
                '"TVterViagemTerminal"."vter_term_codigo"',
                '"TTermTerminal"."term_numero_terminal"',
                '"TTermTerminal"."term_vtec_codigo"',
                '"TTermTerminal"."term_gmac_veiculo_central"',
            ),
        ));
    }

    private function carrega_rper($vloc){
        App::import('Model', 'TEppaEventoPerifericoPadrao');
        $conditions = array();
        $conditions['TRperRecebimentoPeriferico.rper_data_computador_bordo >= ?'] = array($vloc['TViagViagem']['viag_data_cadastro']);
        if($vloc['TVlocViagemLocal']['vloc_tpar_codigo'] == 3 && $vloc[0]['data_entrada_alvo'] && $vloc[0]['data_saida_alvo']){
            $conditions['TRperRecebimentoPeriferico.rper_data_computador_bordo >= ?'] = array($vloc[0]['data_entrada_alvo']);
        }

        return $this->TRperRecebimentoPeriferico->find('all',array(
            'conditions' => array_merge($conditions,array(
                'TRperRecebimentoPeriferico.rper_eppa_codigo' => explode(",", TEppaEventoPerifericoPadrao::STATUS_SENSORES_BAU),
                'TRperRecebimentoPeriferico.rper_term_numero_terminal' => $vloc['TTermTerminal']['term_numero_terminal'],
                'TRperRecebimentoPeriferico.rper_vtec_codigo' => $vloc['TTermTerminal']['term_vtec_codigo'],
                'TRperRecebimentoPeriferico.rper_data_computador_bordo <= ?' => array($vloc[0]['data_saida_alvo']),
            )),
            'joins' => array(
                array(
                    'table' => "{$this->TRposRecebimentoPosicao->databaseTable}.{$this->TRposRecebimentoPosicao->tableSchema}.{$this->TRposRecebimentoPosicao->useTable}",
                    'alias' => 'TRposRecebimentoPosicao',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TRposRecebimentoPosicao.rpos_rece_codigo = TRperRecebimentoPeriferico.rper_rece_codigo',
                        'TRposRecebimentoPosicao.rpos_latitude >= ?' => array($vloc['TRefeReferencia']['refe_latitude_min']),
                        'TRposRecebimentoPosicao.rpos_latitude <= ?' => array($vloc['TRefeReferencia']['refe_latitude_max']),
                        'TRposRecebimentoPosicao.rpos_longitude >= ?' => array($vloc['TRefeReferencia']['refe_longitude_min']),
                        'TRposRecebimentoPosicao.rpos_longitude <= ?' => array($vloc['TRefeReferencia']['refe_longitude_max']),
                    )
                ),
            ),
            'fields' => array(
                '"TRperRecebimentoPeriferico"."rper_eppa_codigo"',
                '"TRperRecebimentoPeriferico"."rper_data_computador_bordo"',
                '"TRperRecebimentoPeriferico"."rper_rece_codigo"',
                '"TRperRecebimentoPeriferico"."rper_valor"',
            ),
        ));
    }

    private function carrega_rmac($vloc){
        $conditions = array();
        if($vloc['TVlocViagemLocal']['vloc_tpar_codigo'] == 4 && $vloc[0]['data_saida_alvo']){
            $conditions['TRmacRecebimentoMacro.rmac_data_computador_bordo >= ?'] = array($vloc['TViagViagem']['viag_data_cadastro']);
        }elseif($vloc['TVlocViagemLocal']['vloc_tpar_codigo'] == 3 && $vloc[0]['data_entrada_alvo'] && $vloc[0]['data_saida_alvo']){
            $conditions['TRmacRecebimentoMacro.rmac_data_computador_bordo >= ?'] = array($vloc[0]['data_entrada_alvo']);
        }

        $rmac = $this->TRmacRecebimentoMacro->find('all',array(
            'joins' => array(
                array(
                    'table' => "{$this->TRposRecebimentoPosicao->databaseTable}.{$this->TRposRecebimentoPosicao->tableSchema}.{$this->TRposRecebimentoPosicao->useTable}",
                    'alias' => 'TRposRecebimentoPosicao',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TRposRecebimentoPosicao.rpos_rece_codigo = TRmacRecebimentoMacro.rmac_rece_codigo',
                        'TRposRecebimentoPosicao.rpos_latitude >= ?' => array($vloc['TRefeReferencia']['refe_latitude_min']),
                        'TRposRecebimentoPosicao.rpos_latitude <= ?' => array($vloc['TRefeReferencia']['refe_latitude_max']),
                        'TRposRecebimentoPosicao.rpos_longitude >= ?' => array($vloc['TRefeReferencia']['refe_longitude_min']),
                        'TRposRecebimentoPosicao.rpos_longitude <= ?' => array($vloc['TRefeReferencia']['refe_longitude_max']),
                    )
                ),
                array(
                    'table' => "{$this->TMpadMacroPadrao->databaseTable}.{$this->TMpadMacroPadrao->tableSchema}.{$this->TMpadMacroPadrao->useTable}",
                    'alias' => 'TMpadMacroPadrao',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TMpadMacroPadrao.mpad_gmac_codigo' => $vloc['TTermTerminal']['term_gmac_veiculo_central'],
                        'TMpadMacroPadrao.mpad_numero = TRmacRecebimentoMacro.rmac_numero',
                        'TMpadMacroPadrao.mpad_tmac_codigo' => array(17,36,31,32),
                    )
                ),
            ),
            'conditions' => array_merge($conditions,array(
                'TRmacRecebimentoMacro.rmac_term_numero_terminal' => $vloc['TTermTerminal']['term_numero_terminal'],
                'TRmacRecebimentoMacro.rmac_vtec_codigo' => $vloc['TTermTerminal']['term_vtec_codigo'],
                'TRmacRecebimentoMacro.rmac_data_computador_bordo <= ?' => array($vloc[0]['data_saida_alvo']),
            )),
            'fields' => array(
                'TRmacRecebimentoMacro.rmac_rece_codigo',
                'TRmacRecebimentoMacro.rmac_data_computador_bordo',
                'TMpadMacroPadrao.mpad_tmac_codigo',
                'TMpadMacroPadrao.mpad_descricao',
            ),
        ));
        return $rmac;
    }

    private function carrega_rmli($vloc){
        $conditions = array();
        if($vloc['TVlocViagemLocal']['vloc_tpar_codigo'] == 4 && $vloc[0]['data_saida_alvo']){
            $conditions['TRmliRecebimentoMensagLivre.rmli_data_computador_bordo >= ?'] = array($vloc['TViagViagem']['viag_data_cadastro']);
        }elseif($vloc['TVlocViagemLocal']['vloc_tpar_codigo'] == 3 && $vloc[0]['data_entrada_alvo'] && $vloc[0]['data_saida_alvo']){
            $conditions['TRmliRecebimentoMensagLivre.rmli_data_computador_bordo >= ?'] = array($vloc[0]['data_entrada_alvo']);
        }

        $rmli = $this->TRmliRecebimentoMensagLivre->find('all',array(
            'joins' => array(
                array(
                    'table' => "{$this->TRposRecebimentoPosicao->databaseTable}.{$this->TRposRecebimentoPosicao->tableSchema}.{$this->TRposRecebimentoPosicao->useTable}",
                    'alias' => 'TRposRecebimentoPosicao',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TRposRecebimentoPosicao.rpos_rece_codigo = TRmliRecebimentoMensagLivre.rmli_rece_codigo',
                        'TRposRecebimentoPosicao.rpos_latitude >= ?' => array($vloc['TRefeReferencia']['refe_latitude_min']),
                        'TRposRecebimentoPosicao.rpos_latitude <= ?' => array($vloc['TRefeReferencia']['refe_latitude_max']),
                        'TRposRecebimentoPosicao.rpos_longitude >= ?' => array($vloc['TRefeReferencia']['refe_longitude_min']),
                        'TRposRecebimentoPosicao.rpos_longitude <= ?' => array($vloc['TRefeReferencia']['refe_longitude_max']),
                    )
                ),
            ),
            'conditions' => array_merge($conditions,array(
                'TRmliRecebimentoMensagLivre.rmli_term_numero_terminal' => $vloc['TTermTerminal']['term_numero_terminal'],
                'TRmliRecebimentoMensagLivre.rmli_vtec_codigo' => $vloc['TTermTerminal']['term_vtec_codigo'],
                'TRmliRecebimentoMensagLivre.rmli_data_computador_bordo <= ?' => array($vloc[0]['data_saida_alvo']),
                'TRmliRecebimentoMensagLivre.rmli_texto' => array_merge($this->inAberturaBau,$this->inFechamentoBau),
            )),
            'fields' => array(
                'TRmliRecebimentoMensagLivre.rmli_rece_codigo',
                'TRmliRecebimentoMensagLivre.rmli_data_computador_bordo',
                'TRmliRecebimentoMensagLivre.rmli_texto',
            ),
        ));
        return $rmli;
    }
}