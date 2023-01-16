<?php
    class RelatoriosOcomonController extends AppController {
    var $name = 'RelatoriosOcomon';
    var $uses = array('OOcorrencias','OProblemas');
    public $helpers = array('BForm', 'Buonny', 'Ajax','Highcharts');





    function index_ocorrencias() {
        $this->pageTitle = 'Chamados';
        
        $this->data['OOcorrencias']['data_inicial'] = date('d/m/Y');
        $this->data['OOcorrencias']['data_final'] = date('d/m/Y');
        $this->data['OOcorrencias'] = $this->Filtros->controla_sessao($this->data, "OOcorrencias");

        $problemas = $this->OProblemas->comboProblemas();
        $this->set(compact('problemas'));

  
    }

   function buscaDias() 
   {
        $DataInicioArray    = explode("/", $this->data['OOcorrencias']['data_inicial']);
        $DataInicioUSA      = "$DataInicioArray[2]"."-"."$DataInicioArray[1]"."-"."$DataInicioArray[0]";
        $DataFimArray       = explode("/", $this->data['OOcorrencias']['data_final']);
        $DataFimUSA         = "$DataFimArray[2]"."-"."$DataFimArray[1]"."-"."$DataFimArray[0]";
        
        $PrimeiroDiaAbs     = strtotime($DataInicioUSA);
        $UltimoDiaAbs       = strtotime($DataFimUSA);
        
        $finish = $PrimeiroDiaAbs;
        $datas = array();
        while($finish <= $UltimoDiaAbs)
        {
            $datas[] = date('Y-m-d',($finish));
            $finish = $finish + (3600*24);

        }

        return $datas;

   }


    function buscaSemanas() {

        $DataInicioArray    = explode("/", $this->data['OOcorrencias']['data_inicial']);
        $DataInicioUSA      = "$DataInicioArray[2]"."-"."$DataInicioArray[1]"."-"."$DataInicioArray[0]";
        $DataFimArray       = explode("/", $this->data['OOcorrencias']['data_final']);
        $DataFimUSA         = "$DataFimArray[2]"."-"."$DataFimArray[1]"."-"."$DataFimArray[0]";

        $SemanaInicio       = date('W', strtotime($DataInicioUSA));
        $AnoInicio          = date('Y', strtotime($DataInicioUSA));
        $PrimeiroDia        = date('Y-m-d', strtotime($AnoInicio."W".$SemanaInicio.'1'));

        $SemanaFim          = date('W', strtotime($DataFimUSA));
        $AnoFim             = date('Y', strtotime($DataFimUSA));
        $UltimoDia          = date('Y-m-d', strtotime($AnoFim."W".$SemanaFim.'5'));

        $PrimeiroDiaAbs     = strtotime($PrimeiroDia);
        $UltimoDiaAbs       = strtotime($UltimoDia);

        $finish = $PrimeiroDiaAbs;
        $datas = array();
        while($finish <= $UltimoDiaAbs)
        {
            $p = date('Y-m-d',($finish));
            $finish = $finish + (3600*96);
            $u = date('Y-m-d',($finish));

            $datas[] = array($p, $u);

            $finish = $finish + (3600*72);
        }
        return $datas;
    }
    



    function semanaPorAnalista($semanasAtendidas, $conditions) {
        
        // Obter backlog antes da primeira semana.
        $PrimeData          = $semanasAtendidas[0][0];
        $TotalAbertasAntes  = $this->OOcorrencias->buscaBacklogPrimeAbertas($conditions, $PrimeData);
        $TotalFechadasAntes = $this->OOcorrencias->buscaBacklogPrimeFechadas($conditions, $PrimeData);
        $BacklogInicial     = $TotalAbertasAntes[0][0]['backlog_abertas'] - $TotalFechadasAntes[0][0]['backlog_fechadas'];
    

         $i = 0;

        foreach($semanasAtendidas as $semanasAtendida)
        {

            $conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?'] = array($semanasAtendida[0],$semanasAtendida[1]);
            

            //ENTREGAS
            // Id do Analista, Nome do Analista e Total de Ocorrencias fechadas por ele.
            $Analistas      = $this->OOcorrencias->buscaAnalistaSemana($conditions);
           if(isset($Analistas[0]))
           {
            $OAnalistaID    = $Analistas[0]['OUsuarios']['user_id'];
            $OAnalistaNome  = $Analistas[0]['OUsuarios']['nome'];
           }


            $TotalFechadas = 0;
            foreach($Analistas as $Analista)
            {
                $TotalFechadas += $Analista[0]['total_fechadas'];
            }
            //ABERTAS NA SEMANA
            $abertas = $this->OOcorrencias->buscaAbertasSemana($conditions);
           
            // BACKLOG DA SEMANA
            if($i == 0)
            {
                $BacklogSemana = ($BacklogInicial + $abertas[0][0]['total_abertas']) - $TotalFechadas;
            }
            else
            {
                $BacklogSemana = ($BacklogSemana + $abertas[0][0]['total_abertas']) - $TotalFechadas;
            }
        

            //MONTAGEM DE ARRAY DE RESULTADOS
            $RayNomes[]         = "'".$OAnalistaNome."'";
            $RayAbertas[]       = $abertas[0][0]['total_abertas'];
            $RayFechadas[]      = $TotalFechadas;
            $RayBacklog[]       = $BacklogSemana;

        
            $i++;
        }


        return array($RayNomes, $RayAbertas, $RayFechadas, $RayBacklog);
    }



    function listagem_ocorrencias() {

        $this->data['OOcorrencias'] = $this->Filtros->controla_sessao($this->data, 'OOcorrencias');
        $conditions = $this->OOcorrencias->converteFiltroEmCondition($this->data['OOcorrencias']);
        $ocorrencias = $this->OOcorrencias->buscaOcorrencias($conditions);
       
        if(isset($conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?']))
        {

        

            $semanasAtendidas = $this->buscaSemanas();

            // GRÁFICO DE EVOLUÇÃO DE CHAMADOS POR SEMANA
            $Evolucao = $this->semanaPorAnalista($semanasAtendidas, $conditions);
            $EvolucaoGraph['eixo_x'] = $Evolucao[0];
            $EvolucaoGraph['series'] =  array(
                    array(
                        'name' => "'Abertos'",
                        'values' => $Evolucao[1]
                    ),
                    array(
                        'name' => "'Fechados'",
                        'values' => $Evolucao[2]
                    ),
                    array(
                        'name' => "'Backlog'",
                        'values' => $Evolucao[3]
                    )); 
            /////////////////////////////////////////////////////////////////////

            // GRÁFICO DE TIPO DE CHAMADOS ENCERRADOS
            $FechadasPorTipo = $this->OOcorrencias->buscaFechadasPorTipo($conditions);
            
            if(!isset($FechadasPorTipo[0]))
            {
                $FechadasPorTipo[0]['OProblemas']['problema'] = '';
                $FechadasPorTipo[0][0]['total'] = 0;
            }   
            if(!isset($FechadasPorTipo[1]))
            {
                $FechadasPorTipo[1]['OProblemas']['problema'] = '';
                $FechadasPorTipo[1][0]['total'] = 0;
            }   
            if(!isset($FechadasPorTipo[2]))
            {
                $FechadasPorTipo[2]['OProblemas']['problema'] = '';
                $FechadasPorTipo[2][0]['total'] = 0;
            }   

                $graphitemspizza['eixo_x'][0] = "'Chamados Fechados'";
                $graphitemspizza['series'] =  array(
                        array(
                            'name' => "'".$FechadasPorTipo[0]['OProblemas']['problema']."'",
                            'values' => $FechadasPorTipo[0][0]['total']
                        ),
                        array(
                            'name' => "'".$FechadasPorTipo[1]['OProblemas']['problema']."'",
                            'values' => $FechadasPorTipo[1][0]['total']
                        ),
                        array(
                            'name' => "'".$FechadasPorTipo[2]['OProblemas']['problema']."'",
                            'values' => $FechadasPorTipo[2][0]['total']
                        )); 
            
            /////////////////////////////////////////////////////////////////////

            // GRÁFICO DE ENCERRADAS POR DIA, NO PERÍODO
            $DiasAtendidos =$this->buscaDias();
            

            foreach($DiasAtendidos as $DiaAtendido)
            {
                $FechadosNoDia  = $this->OOcorrencias->buscaFechadasPorDia($conditions, $DiaAtendido);
               
                if(isset($FechadosNoDia[0]))
                {
                    $GraphDias[] = "'$DiaAtendido'";
                    // fix dos faltantes
                    $TBug = 0;
                    $TDuv = 0;
                    $TAut = 0;
                    foreach($FechadosNoDia as $FechadoNoDia)
                    {
                        switch($FechadoNoDia['OProblemas']['prob_id'])
                        {
                            case '68':
                                $TDuv  = 1;
                                break;
                            case '176':
                                $TBug  = 1;
                                break;
                            case '181':
                                $TAut  = 1;
                                break;
                        }

                    }
                    $t = count($FechadosNoDia);
                    if($TBug == 0){ $FechadosNoDia[$t][0]['fechadas_dia'] = 0;  $FechadosNoDia[$t]['OProblemas']['problema'] = 'Erro / Bug';  $FechadosNoDia[$t]['OProblemas']['prob_id'] = 176;}
                    $t = count($FechadosNoDia);
                    if($TDuv == 0){ $FechadosNoDia[$t][0]['fechadas_dia'] = 0;  $FechadosNoDia[$t]['OProblemas']['problema'] = 'Dúvida';  $FechadosNoDia[$t]['OProblemas']['prob_id'] = 68;}
                    $t = count($FechadosNoDia);
                    if($TAut == 0){ $FechadosNoDia[$t][0]['fechadas_dia'] = 0;  $FechadosNoDia[$t]['OProblemas']['problema'] = 'Autorização';  $FechadosNoDia[$t]['OProblemas']['prob_id'] = 181;}


                    foreach($FechadosNoDia as $FDia)
                    {
                      


                        switch ($FDia['OProblemas']['prob_id'])
                        {
                            case '68':
                                $RayDuvidas[]   = $FDia[0]['fechadas_dia'];
                                break;
                            case '176':
                                $RayBugs[] = $FDia[0]['fechadas_dia'];
                                break;
                            case '181':
                                $RayAuths[] = $FDia[0]['fechadas_dia'];
                                break;
       

                        }

                    }


                }


            }
                    if(isset($GraphDias))
                    {
                        
                        
                        for($i=0; $i < count($RayBugs); $i++)
                        {
                            $TotDia[] = $RayBugs[$i] + $RayDuvidas[$i] + $RayAuths[$i];
                        }



                        $TipoPorDiaGraph['eixo_x'] = $GraphDias;
                        $TipoPorDiaGraph['series'] =  array(
                        array(
                            'type' => 'column',
                            'color' => 'gray',
                            'name' => "'Total'",
                            'values' => $TotDia
                        ),
                        array(
                            'name' => "'Erro / Bug'",
                            'values' => $RayBugs
                        ),
                        array(
                            'name' => "'Dúvidas'",
                            'values' => $RayDuvidas
                        ),
                        array(
                            'name' => "'Autorização'",
                            'values' => $RayAuths
                        ));
                    }

                /////////////////////////////////////////////////////////////////////




    }
        $this->set(compact('ocorrencias', 'EvolucaoGraph', 'graphitemspizza', 'TipoPorDiaGraph'));

    }
    
    

    
}


 
           
     
      