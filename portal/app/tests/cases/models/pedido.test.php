<?php
App::import('Model', 'ItemPedidoAlocacao');
class PedidoTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.pedido',
        'app.cliente',
        'app.cliente_funcionario',
        'app.funcionario',
        'app.setor',
        'app.cargo',
        //'app.pedido_exame',
        //'app.multi_empresa',
        //'app.ficha_clinica',
        //'app.medico',
        'app.conselho_profissional',
        'app.fornecedor',
        //'app.item_pedido_exame',
        //'app.fornecedor_medico',
        //'app.ficha_clinica_resposta',
        'app.funcionario_setor_cargo',
        // 'app.ficha_clinica_questao',
        // 'app.ficha_clinica_grupo_questao',
        // 'app.ficha_clinica_farmaco',
         'app.pedido',
         'app.item_pedido',
         'app.item_pedido_alocacao',
         'app.cliente_produto',
         'app.produto',
         'app.cliente_produto_desconto',
         'app.cliente_produto_servico_2',
         'app.servico',
        // 'app.motivo_bloqueio',
        'app.detalhe_item_pedido_manual',
        'app.ficha_assistencial',
        'app.ficha_assistencial_resposta',
        'app.ficha_assistencial_questao',
        'app.ficha_assistencial_gq',
        'app.ficha_assistencial_farmaco',
        'app.atestado',
        'app.motivo_bloqueio',
        'app.grupo_economico',
        'app.grupo_economico_cliente',
        'app.last_id',
        'app.pedido_exame',
        'app.multi_empresa',
        'app.ficha_clinica',
        'app.medico',
        'app.item_pedido_exame',
        'app.fornecedor_medico',
        'app.ficha_clinica_resposta',
        'app.ficha_clinica_questao',
        'app.ficha_clinica_grupo_questao',
        'app.ficha_clinica_farmaco'
    );
    
    function startTest() {
        $this->Pedido =& ClassRegistry::init('Pedido');
        $this->ItemPedidoAlocacao =& ClassRegistry::init('ItemPedidoAlocacao');
    	//$this->ClienteFuncionario  =& ClassRegistry::init('ClienteFuncionario');
        $_SESSION['Auth']['Usuario']['codigo'] = 1;
        $_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
    }
    

    function testcalculoProRata(){

        $dados['codigo_cliente_pagador'] = '2042';
        $dados['mes'] = '04';
        $dados['ano'] = '2018';

        //seta a data de inicio
        $dados['data_inicial'] = '20180401';
        $dados['data_final'] = '20180430';

    	 $this->assertTrue($this->Pedido->faturamento_percapita($dados));

         //Verifica se gerou cobranca pro rata
        $dias_cobrados = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador'],'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] ,'dias_cobrado' => 28, 'data_demissao' => '2018-04-27'  ), 'recursive' => -1));
        $this->assertEqual($dias_cobrados, 1);

        //Verifica a quantidade de cobranças sem pro rata
        $sem_pro_rata = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador'],'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] ,'dias_cobrado' => NULL), 'recursive' => -1));
        $this->assertEqual($sem_pro_rata,3);
        
        //Verifica se gerou sem pro rata pois foi demitido no mes seguinte
        $demissao_apos_periodo = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador'],'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] ,'dias_cobrado' => NULL, 'data_demissao' => '2018-05-01'  ), 'recursive' => -1));
        $this->assertEqual($demissao_apos_periodo, 1);
    
         //Verifica se gerou faturamento de funcionario demitido antes do faturamento
        $demissao_antes_faturamento = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador'],'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] ,'data_demissao' => '2018-03-31' ), 'recursive' => -1));
        $this->assertEqual($demissao_antes_faturamento, 0);


         //Verifica se gerou faturamento de funcionario com inclusão de matrícula após o faturamento
        $inclusao_apos_faturamento = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador'],'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] ,'data_inclusao_cliente_funcionario >' => '2018-04-30 23:59:59' ), 'recursive' => -1));
        $this->assertEqual($inclusao_apos_faturamento, 0);


        //Verifica se gerou faturamento pro rata de funcionario com inclusão de matrícula no período do faturamento
        $inclusao_durante_faturamento = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador'],'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] ,'data_inclusao_cliente_funcionario BETWEEN ? AND ?' => array('2018-04-01 00:00:00','2018-04-30 23:59:59'),'dias_cobrado' => 10 ), 'recursive' => -1));
        $this->assertEqual($inclusao_durante_faturamento, 1);


        //Verifica se cobrança pro rata foi baseada na data de ativacao pacote (que ocorreu após a inclusão da matricula)
        $inclusao_anterior_ativacao = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']95,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] , 'dias_cobrado' => '17'), 'recursive' => -1));
        $this->assertEqual($inclusao_anterior_ativacao, 1);

        //Verifica se gerou faturamento de funcionario com demissao anterior a ativação do pacote per capita
        $demissao_anterior_ativacao = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']95,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] , 'data_demissao <' => '2018-04-14'), 'recursive' => -1));
        $this->assertEqual($demissao_anterior_ativacao, 0);

        //Verifica se gerou faturamento de funcionario com demissao no dia da ativação do pacote per capita
        $demissao_dia_ativacao = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']95,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] , 'data_demissao' => '2018-04-14','dias_cobrado' => 1), 'recursive' => -1));
        $this->assertEqual($demissao_dia_ativacao, 1); 

        //Verifica se gerou faturamento de funcionario com inclusao após inativação do pacote per capita
        $inclusao_apos_inativacao = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'], 'codigo_cliente_alocacao' => 2097, 'dias_cobrado' => NULL, 'codigo_cliente_funcionario' => 7560 ), 'recursive' => -1));
        $this->assertEqual($inclusao_apos_inativacao, 0);

        //Verifica se gerou pro rata até a data de inativação do pacote per capita no periodo do faturamento
        $prorata_ate_inativacao = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'], 'codigo_cliente_alocacao' => 2097,'codigo_cliente_funcionario' => 7562, 'data_demissao' => NULL,'data_inativacao_produto' => '2018-04-19', 'dias_cobrado' => 20, 'data_demissao' => NULL ), 'recursive' => -1));
        $this->assertEqual($prorata_ate_inativacao, 1);

        //Verifica se gerou faturamento de funcionario com assinatura na alocação ativa e na matriz inativa e sem pro rata
        $aloca_ativa_matriz_inativa = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'], 'codigo_cliente_alocacao' => 2100,'dias_cobrado' => NULL ), 'recursive' => -1));
        $this->assertEqual($aloca_ativa_matriz_inativa , 1);

        //Cobrar pro rata de 1 dia de funcionário com data de demissão no mês porém com data de inclusão maior que demissao
        $demissao_menor_inclusao = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador'],'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'], 'codigo_cliente_alocacao' => 20,'codigo_cliente_funcionario' => 7563,'dias_cobrado' => 1 ), 'recursive' => -1));
        $this->assertEqual($demissao_menor_inclusao , 1);

        //Não cobrar funcionário com data de demissão anterior ao faturamento porém com data de inclusão no período
        $demissao_menor_inclusao_mes_anterior = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador'],'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'], 'codigo_cliente_alocacao' => 20,'codigo_cliente_funcionario' => 7564), 'recursive' => -1));
        $this->assertEqual($demissao_menor_inclusao_mes_anterior , 0);    

        //Cobrar pro rata baseado na data de inativação que é anterior a data de demissão
        $inativacao_menor_demissao = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'], 'codigo_cliente_alocacao' => 2097,'codigo_cliente_funcionario' => 7565,'dias_cobrado' => 20), 'recursive' => -1));
        $this->assertEqual($inativacao_menor_demissao , 1);

        //Cobrar pro rata baseado na data de demissao que é anterior a data de inativacao
        $inativacao_maior_demissao = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'], 'codigo_cliente_alocacao' => 2097,'codigo_cliente_funcionario' => 7566,'dias_cobrado' => 19, 'data_demissao' => '2018-04-18'), 'recursive' => -1));
        $this->assertEqual($inativacao_maior_demissao , 1);

        //Não cobra devido a data de inativacao que é anterior ao início do faturamento
        $inativacao_anterior_faturamento = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'], 'codigo_cliente_alocacao' => 2101,'codigo_cliente_funcionario' => 7567, 'data_demissao' => NULL), 'recursive' => -1));
        $this->assertEqual($inativacao_anterior_faturamento , 0);
             
       //Não cobra porque a data de ativacao é maior que a data final do faturamento
        $ativacao_maior_faturamento = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'], 'codigo_cliente_alocacao' => 2102,'codigo_cliente_funcionario' => 7568), 'recursive' => -1));
        $this->assertEqual($ativacao_maior_faturamento , 0);      

        //Cobra completo porque a data de inativacao é maior que a data final do faturamento
        $inativacao_maior_faturamento = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'], 'codigo_cliente_alocacao' => 2207,'codigo_cliente_funcionario' => 7569, 'dias_cobrado' => NULL), 'recursive' => -1));
        $this->assertEqual($inativacao_maior_faturamento, 1);

        //Verifica se cobrança pro rata foi baseada na data de inclusao que é maior que ativação
        $inclusao_maior_ativacao = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']95,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] , 'dias_cobrado' => '16', 'codigo_cliente_funcionario' => 7570), 'recursive' => -1));
        $this->assertEqual($inclusao_maior_ativacao, 1);        

        //Verifica pro rata com data de ativacao, inativacao, inclusao e demissao no periodo do faturamento
        //Inclusao menor que ativacao e demissao menor que inativacao
        $inclusao_demissao_menor = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'codigo_cliente_alocacao' => 2208,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] , 'dias_cobrado' => '9', 'codigo_cliente_funcionario' => 7571), 'recursive' => -1));
        $this->assertEqual($inclusao_demissao_menor, 1);

        //Verifica pro rata com data de ativacao, inativacao, inclusao e demissao no periodo do faturamento
        //Inclusao maior que ativacao e demissao maior que inativacao
        $inclusao_demissao_maior = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'codigo_cliente_alocacao' => 2208,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] , 'dias_cobrado' => '13', 'codigo_cliente_funcionario' => 7572), 'recursive' => -1));
        $this->assertEqual($inclusao_demissao_maior, 1);        

        //Verifica pro rata com data de ativacao, inativacao, inclusao e demissao no periodo do faturamento
        //Inclusao menor que ativacao e demissao maior que inativacao
        $ativacao_inativacao_maior = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'codigo_cliente_alocacao' => 2208,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] , 'dias_cobrado' => '14', 'codigo_cliente_funcionario' => 7573), 'recursive' => -1));
        $this->assertEqual($ativacao_inativacao_maior, 1);

        //Verifica pro rata com data de ativacao, inativacao, inclusao e demissao no periodo do faturamento
        //Inclusao maior que demissao entre o periodo de ativacao e inativacao
        //Deve cobrar 1 dia
        $demissao_menor_inclusao_entre_inativa = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'codigo_cliente_alocacao' => 2208,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] , 'dias_cobrado' => '1', 'codigo_cliente_funcionario' => 7575), 'recursive' => -1));
        $this->assertEqual($demissao_menor_inclusao_entre_inativa, 1);

        //Verifica pro rata com data de ativacao, inativacao, inclusao e demissao no periodo do faturamento
        //Inclusao maior que demissao porem demissao menor que ativacao do pacote
        //Não deve cobrar
        $demissao_menor_inclusao_antes_ativa = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'codigo_cliente_alocacao' => 2208,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] , 'codigo_cliente_funcionario' => 7576), 'recursive' => -1));
        $this->assertEqual($demissao_menor_inclusao_antes_ativa, 0); 

         //Verifica pro rata com data de ativacao, inativacao, inclusao e demissao no periodo do faturamento
        //Inclusao maior que demissao, demissao entre ativacao e inativacao, porém inclusão após inativacao
        //Não deve cobrar
        $inclusao_maior_inativacao_demissao_menor = $this->ItemPedidoAlocacao->find('count', array('conditions' => array('codigo_cliente_pagador' => $dados['codigo_cliente_pagador']97,'codigo_cliente_alocacao' => 2208,'ano_referencia' => $dados['ano'], 'mes_referencia' => $dados['mes'] , 'codigo_cliente_funcionario' => 7577), 'recursive' => -1));
        $this->assertEqual($inclusao_maior_inativacao_demissao_menor, 0);
    }

    function endTest() {
        unset($this->Pedido);
    	unset($this->ItemPedidoAlocacao);
        ClassRegistry::flush();
    }
}
?>