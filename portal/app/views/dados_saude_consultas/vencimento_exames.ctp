<span class="background-greenblue color-white font-size-22 padding-5">Vencimento de exames</span>
<span class="pull-right color-gray margin-top-3"><?php echo  $this->Html->link('Ver mais', "javascript:posicao_exames('')") ?></span>
<div class="background-greenblue margin-top-10" style="height:3px"></div>
<div class="padding-top-30">
<div class="row-fluid">
	<div class="span3">
		<div class="text-center font-size-36 color-red">
        <strong><?php echo $dados['vencidos'] ?></strong>
    	</div>
    	<br>
		<div class="text-center"><strong>estão<br>vencidos</strong></div>
	</div>
	<div class="span3">
		<div class="text-center font-size-36 color-orangered">
        <strong><?php echo $dados['vence_em_30_dias'] ?></strong>
    	</div>
    	<br>
		<div class="text-center"><strong>vence em<br>30 dias</strong></div>
	</div>
	<div class="span3">
		<div class="text-center font-size-36 color-orange">
        <strong><?php echo $dados['vence_em_60_dias'] ?></strong>
    	</div>
    	<br>
		<div class="text-center"><strong>vence em<br>60 dias</strong></div>
	</div>
	<div class="span3">
		<div class="text-center font-size-36 color-green">
        <strong><?php echo $dados['vence_em_90_dias'] ?></strong>
    	</div>
    	<br>
		<div class="text-center"><strong>vence em<br>90 dias</strong></div>
	</div>
</div>
</div>
<?php

$unidade = !empty($conditions['GrupoEconomicoCliente.codigo_cliente']) ? $conditions['GrupoEconomicoCliente.codigo_cliente'] : '';

$setor = !empty($conditions['FuncionarioSetorCargo.codigo_setor']) ? $conditions['FuncionarioSetorCargo.codigo_setor'] : '';
$data_inicial = date("d/m/Y");
$data_final = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")+90, date("Y")));
	echo $this->Javascript->codeBlock("
	    function posicao_exames() {
	        var agrupamento = 1; 
	        //codigos criados para selecionar os exames ocupacionais no agrupamento
	        var tipo_exame = ['admissional','demissional','mudanca','periodico','retorno'];

	    
	        var form = document.createElement('form');
	        var form_id = ('formresult' + Math.random()).replace('.','');
	        form.setAttribute('method', 'post');
	        form.setAttribute('target', form_id);
	        form.setAttribute('action', '/portal/exames/posicao_exames_sintetico/' + Math.random());
   			
   			field = document.createElement('input');
	        field.setAttribute('name', 'data[Exame][codigo_cliente]');
	        field.setAttribute('value', '{$conditions['GrupoEconomico.codigo_cliente']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);
		        
    		field = document.createElement('input');
	        field.setAttribute('name', 'data[Exame][situacao][]');
	        field.setAttribute('value', 'vencidos');
	        field.setAttribute('type', 'hidden');
		        field.setAttribute('defaultChecked','defaultChecked');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Exame][situacao][]');
	        field.setAttribute('value','vencer_entre');
	        field.setAttribute('type', 'hidden');
	        field.setAttribute('defaultChecked','defaultChecked');
	        form.appendChild(field);
		
	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Exame][tipo_exame][]');
	        field.setAttribute('value', 'periodico');
	        field.setAttribute('type', 'hidden');
	        field.setAttribute('defaultChecked','defaultChecked');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Exame][data_inicial]');
	        field.setAttribute('value', '{$data_inicial}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Exame][data_final]');
	        field.setAttribute('value', '{$data_final}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
            field.setAttribute('name', 'data[Exame][codigo_unidade]');     
            field.setAttribute('value', '{$unidade}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[Exame][codigo_setor]');     
            field.setAttribute('value', '{$setor}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);
	               
            field = document.createElement('input');
            field.setAttribute('name', 'data[Exame][codigo_exame]');     
            field.setAttribute('value', '');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[Exame][codigo_tipo_exame]');     
            field.setAttribute('value', '');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[Exame][agrupamento]');     
            field.setAttribute('value', '1');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);
	               
	        var janela = window_sizes();
	        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	        document.body.appendChild(form);
	        form.submit();
	    }");?>