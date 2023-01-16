<?php
    class RelatoriosRedmineController extends AppController {
    var $name = 'RelatoriosRedmine';
    var $uses = array('RIssues', 'RUsers', 'RProjects');
    public $helpers = array('BForm', 'Buonny', 'Ajax','Highcharts');






    function index_issues() {
        $this->pageTitle = 'Demandas';
        
        $this->data['RIssues']['data_inicial'] = date('d/m/Y');
        $this->data['RIssues']['data_final'] = date('d/m/Y');
        $this->data['RIssues'] = $this->Filtros->controla_sessao($this->data, "RIssues");

        $users = $this->RUsers->comboUsers();
        $projects = $this->RProjects->comboProjects();

        $this->set(compact('users','projects'));

  
    }
    
    function grafico_coluna_analistas($countissues)
    {
        
        if(count($countissues) > 0)
        {
            foreach ($countissues as $countissue) {
                  
                    if($countissue[0]['total'] != null){ $qtd_objetivo[] = $countissue[0]['total']; }  else{ $qtd_objetivo[] = 0; }
                    $descricao[] = "'".$countissue['RUsers']['firstname']."'";
               
            } 
            $dadosGrafico['eixo_x'] = $descricao;
            $dadosGrafico['series'] =  array(
                array(
                    'name' => "'Total'",
                    'values' => $qtd_objetivo
                )
            ); 
            return $dadosGrafico;
        }
    }

    function grafico_coluna_horas_estimadas($countissues)
    {
        if(count($countissues) > 0)
        {

            foreach ($countissues as $countissue) {
                  
                   
                    if($countissue[0]['total'] != null){ $qtd_objetivo[] = $countissue[0]['total']; }  else{ $qtd_objetivo[] = 0; }

                    
                    $descricao[] = "'".$countissue['RUsers']['firstname']."'";
               
            } 
            $dadosGrafico['eixo_x'] = $descricao;
            $dadosGrafico['series'] =  array(
                array(
                    'name' => "'Total'",
                    'values' => $qtd_objetivo
                )
            ); 
            return $dadosGrafico;
        }
    }
    function grafico_coluna_horas_estimadas_realizadas($countissues)
    {
        if(count($countissues) > 0)
        {

            foreach ($countissues as $countissue) {
                  
                   
                    if($countissue[0]['total'] != null){ $qtd_objetivo[] = $countissue[0]['total']; }  else{ $qtd_objetivo[] = 0; }
                    if($countissue[0]['efetivo'] != null and $countissue[0]['efetivo'] >=0)
                        { $qtd_efetivo[] = $countissue[0]['efetivo'];}  else { $qtd_efetivo[] = 0;}
                    
                    $descricao[] = "'".$countissue['RUsers']['firstname']."'";
               
            } 
            $dadosGrafico['eixo_x'] = $descricao;
            $dadosGrafico['series'] =  array(
                array(
                    'name' => "'Estimadas'",
                    'values' => $qtd_objetivo
                ),
                array(
                    'name' => "'Realizadas'",
                    'values' => $qtd_efetivo
                )


            ); 
        
            return $dadosGrafico;
        }
    }






    function grafico_pizza_clientes($countissues)
    {
     

       if(count($countissues) > 0)
       {


            $eixo_x = array("'Solicitante'");
            
            foreach ($countissues as $countissue) {
                  
                   // $qtd_objetivo[] = $countissue[0]['total'];
                   // $descricao[] = "'".$countissue['RCustomValues']['value']."'";
                    $v = $countissue['RCustomValues']['value'];
                    $series[] = array('name' => "'$v'", 'values' => $countissue[0]['total']);
               
            } 
        }
        else
        {
            $eixo_x = array("'Solicitante'");
            $v = '';
            $series[] = array('name' => "'$v'", 'values' => 0);
        }


        return array('eixo_x' => $eixo_x, 'series' => $series);


    }




    function listagem_issues() {
    

        $this->data['RIssues'] = $this->Filtros->controla_sessao($this->data, 'RIssues');



        $conditions = $this->RIssues->converteFiltroEmCondition($this->data['RIssues']);
       
        $issues = $this->RIssues->buscaIssues($conditions);

      
        // adicional do gráfico de colunas
        $countissues = $this->RIssues->countIssues($conditions);
        $graphitems = $this->grafico_coluna_analistas($countissues); 

        // adicional do gráfico de PIZZA DE CLIENTES
        $IssuesPerClient = $this->RIssues->countIssuesPerClient($conditions);
        $graphitemspizza = $this->grafico_pizza_clientes($IssuesPerClient); 

        // adicional do gráfico de colunas de horas estimadas vs realizadas
        $HorasEstReal = $this->RIssues->totalHorasEstReal($conditions);
        $EstRealGraph = $this->grafico_coluna_horas_estimadas_realizadas($HorasEstReal); 

        // adicional do gráfico de colunas de horas estimadas
        $HorasEstimadas = $this->RIssues->totalHorasEstimadas($conditions);
        $horasgraphitems = $this->grafico_coluna_horas_estimadas($HorasEstimadas); 

        // quais os analistas que atuam em demandas em andamento no momento ?
        $Analistas = $this->RIssues->getAna($conditions);

        if(count($Analistas) > 0)
        {
            foreach($Analistas as $Analista)
            {
                
                 $descricao[] = "'".$Analista['RUsers']['usuario']."'";
                
                 $qtd_nao_ini[] = $this->RIssues->getQtdAnaPerStatus($conditions, $Analista['RUsers']['coduser'], array(1,10,11));
                 $qtd_parada[] = $this->RIssues->getQtdAnaPerStatus($conditions, $Analista['RUsers']['coduser'], array(9));
                 $qtd_desenv[] = $this->RIssues->getQtdAnaPerStatus($conditions, $Analista['RUsers']['coduser'], array(2,12));
                 $qtd_teste[] = $this->RIssues->getQtdAnaPerStatus($conditions, $Analista['RUsers']['coduser'], array(4,13,14,18));
                 $qtd_waitdeploy[] = $this->RIssues->getQtdAnaPerStatus($conditions, $Analista['RUsers']['coduser'], array(8,15));

            }
            

           //tratando os arrays multidimensionais para serem aceitos no highcharts

            foreach($qtd_nao_ini as $qni){  $qtd_nao_ini2[] = $qni[0]['total']; }
            foreach($qtd_parada as $qp){  $qtd_parada2[] = $qp[0]['total']; }
            foreach($qtd_desenv as $qd){  $qtd_desenv2[] = $qd[0]['total']; }
            foreach($qtd_teste as $qt){  $qtd_teste2[] = $qt[0]['total']; }
            foreach($qtd_waitdeploy as $qw){  $qtd_waitdeploy2[] = $qw[0]['total']; }

            $AnalistasGraph['eixo_x'] = $descricao;
            $AnalistasGraph['series'] =  array(
                array(
                    'name' => "'Não Iniciadas'",
                    'values' => $qtd_nao_ini2
                ),
                array(
                    'name' => "'Paradas'",
                    'values' => $qtd_parada2
                ),
                array(
                    'name' => "'Em Desenvolvimento'",
                    'values' => $qtd_desenv2
                ),
                array(
                    'name' => "'Em Testes'",
                    'values' => $qtd_teste2
                ),
                array(
                    'name' => "'Aguardando Deploy'",
                    'values' => $qtd_waitdeploy2
                )


            ); 

        }
        $this->set(compact('issues','graphitems','graphitemspizza', 'EstRealGraph', 'AnalistasGraph'));



    }
    

    
}


 
           
     
      