<?php if (!$dados): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php else: ?>
	
	<?php if(!empty($dados_funcionarios_empresa)):?>
		<div class="row-fluid inline">
				<span class="margin-left-5">
				 <strong>Empresa: </strong><?=$this->Buonny->leiaMais($dados_funcionarios_empresa['nome_empresa'],50);?>
				</span>
				<span class="margin-left-100">
					<strong>Funcionários ativos: </strong><?=$dados_funcionarios_empresa['ativos'];?>
					<span class="margin-left-10">
						<strong>Inativos: </strong><?=$dados_funcionarios_empresa['inativos'];?>
					</span>
					<span class="margin-left-10">
						<strong>Total: </strong><?=$dados_funcionarios_empresa['total'];?>
					</span>
				</span>
			
		</div>
	<?php endif?>
	<div class="row">
		<div id="grafico_posicao_exames" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
	</div>
	<table class="table table-striped">
	    <thead>
	        <tr>
				<td class='input-small'>Código</td>
				<td>Descricao</td>
				<td class='numeric input-small'>
					Pendentes
					<i class="adjust-icon icon-question-sign " data-toggle="tooltip" title="QUANTIDADE EXAMES PENDENTES / QUANTIDADE DE FUNCIONÁRIOS"></i>
				</td>
				<td class='numeric input-small'>
					Vencidos
					<i class="adjust-icon icon-question-sign " data-toggle="tooltip" title="QUANTIDADE EXAMES VENCIDOS / QUANTIDADE DE FUNCIONÁRIOS"></i>
				</td>
				<td class='numeric input-small'>
					À Vencer
					<i class="adjust-icon icon-question-sign " data-toggle="tooltip" title="QUANTIDADE EXAMES À VENCER / QUANTIDADE DE FUNCIONÁRIOS"></i>
				</td>
				<td class='numeric input-small' >
					Quantidade
					<i class="adjust-icon icon-question-sign " data-toggle="tooltip" title="QUANTIDADE EXAMES / QUANTIDADE DE FUNCIONÁRIOS"></i>
				</td>
			</tr>
		</thead>
		<tbody>
			<?php 
				$total = 0 ;
				$total_pendentes = 0;
				$total_vencidos = 0;
				$total_vencer = 0;
				//funcionarios
				$total_func 			= 0 ;
				$total_func_pendente 	= 0;
				$total_func_vencido 	= 0;
				$total_func_vencer 		= 0;
			?>
			<?php $dados_sintetico = array() ?>
			<?php foreach($dados as $key => $value) : ?>
				<?php 
				//Totalizadores
				$total += $value[0]['quantidade'];
				$total_pendentes += $value[0]['pendente'];
				$total_vencidos += $value[0]['vencido'];
				$total_vencer += $value[0]['vencer'];
				//funcionarios
				$total_func += $value[0]['total_func'];
				$total_func_pendente += $value[0]['total_func_pendente'];;
				$total_func_vencido += $value[0]['total_func_vencido'];;
				$total_func_vencer += $value[0]['total_func_vencer'];;
				?>
				<?php $dados_sintetico[] = array('name' => '"'.str_replace('"', "'", $this->Buonny->leiaMais($value['0']['descricao'],35)).'"', 'values' => $value[0]['quantidade']) ?>
				<tr>
					<td class='input-small status_exame'><?php echo $value['0']['codigo']; ?></td>
					<td><?php echo $this->Buonny->leiaMais($value['0']['descricao'],50); ?></td>
					<?php $codigo = empty($value['0']['codigo']) ? -1 : $value['0']['codigo'] ?>
					<td class='numeric input-small status_exame'>
						<?=($value[0]['pendente'] > 0) ?  $this->Html->link($value[0]['pendente'], "javascript:analitico('{$codigo}','pendentes')") : $value[0]['pendente']?> / <?php echo $value[0]['total_func_pendente']; ?>
					</td>

					<td class='numeric input-small status_exame'>
						<?=($value[0]['vencido'] > 0)? $this->Html->link($value[0]['vencido'], "javascript:analitico('{$codigo}','vencidos')") : $value[0]['vencido'] ?> / <?php echo $value[0]['total_func_vencido']; ?>
					</td>

					<td class='numeric input-small status_exame'>
						<?=($value[0]['vencer'] > 0) ? $this->Html->link($value[0]['vencer'], "javascript:analitico('{$codigo}','vencer_entre')") : $value[0]['vencer'] ?> / <?php echo $value[0]['total_func_vencer']; ?>
					</td>

					<td class='numeric input-small status_exame'>
						<?=($value[0]['quantidade'] > 0) ? $this->Html->link($value[0]['quantidade'], "javascript:analitico('{$codigo}','')") : $value[0]['quantidade'] ?> / <?php echo $value[0]['total_func']; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>

				<td>Total</td>
				<td class='numeric status_exame' colspan="2">
					<?=($total_pendentes > 0) ? $this->Html->link($total_pendentes, "javascript:analitico('','pendentes')") : $total_pendentes ?> / <?php echo $total_func_pendente; ?>
				</td>
				<td class='numeric status_exame'>
					<?=($total_vencidos > 0) ? $this->Html->link($total_vencidos, "javascript:analitico('','vencidos')") : $total_vencidos ?> / <?php echo $total_func_vencido; ?>
				</td>
				<td class='numeric status_exame'>
					<?=($total_vencer > 0) ? $this->Html->link($total_vencer, "javascript:analitico('','vencer_entre')") : $total_vencer ?> / <?php echo $total_func_vencer; ?>
				</td>
				<td class='numeric status_exame'>
					<?=($total > 0) ? $this->Html->link($total, "javascript:analitico('','')") : $total ?> / <?php echo $total_func; ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<?php 

		//Verifica cada item do array para passar para o formulário analítico
		$situacao_0 = !empty($this->data['Exame']['situacao'][0]) ? $this->data['Exame']['situacao'][0] : NULL; 
		$situacao_1 = !empty($this->data['Exame']['situacao'][1]) ? $this->data['Exame']['situacao'][1] : NULL; 
		$situacao_2 = !empty($this->data['Exame']['situacao'][2]) ? $this->data['Exame']['situacao'][2] : NULL; 

		$tipo_exame_0 = !empty($this->data['Exame']['tipo_exame'][0]) ? $this->data['Exame']['tipo_exame'][0] : NULL; 
		$tipo_exame_1 = !empty($this->data['Exame']['tipo_exame'][1]) ? $this->data['Exame']['tipo_exame'][1] : NULL; 
		$tipo_exame_2 = !empty($this->data['Exame']['tipo_exame'][2]) ? $this->data['Exame']['tipo_exame'][2] : NULL; 
		$tipo_exame_3 = !empty($this->data['Exame']['tipo_exame'][3]) ? $this->data['Exame']['tipo_exame'][3] : NULL; 
		$tipo_exame_4 = !empty($this->data['Exame']['tipo_exame'][4]) ? $this->data['Exame']['tipo_exame'][4] : NULL; 
		
		$data_ini = empty($this->data['Exame']['data_inicial']) ? null : $this->data['Exame']['data_inicial'];
		$data_fim = empty($this->data['Exame']['data_final']) ? null : $this->data['Exame']['data_final'];


	echo $this->Javascript->codeBlock("
	    function analitico(codigo_selecionado, status) {
	        var agrupamento = {$agrupamento}; 
	        //codigos criados para selecionar os exames ocupacionais no agrupamento
	        var tipo_exame = ['admissional','demissional','mudanca','periodico','retorno'];

	    
	        var form = document.createElement('form');
	        var form_id = ('formresult' + Math.random()).replace('.','');
	        form.setAttribute('method', 'post');
	        form.setAttribute('target', form_id);
	        form.setAttribute('action', '/portal/exames/posicao_exames_analitico/1/' + Math.random());
   			
   			field = document.createElement('input');
	        field.setAttribute('name', 'data[Exame][codigo_cliente]');
	        field.setAttribute('value', '{$this->data['Exame']['codigo_cliente']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        if(status != ''){
		        field = document.createElement('input');
		        field.setAttribute('name', 'data[Exame][situacao][]');
		        field.setAttribute('value', status);
		        field.setAttribute('type', 'hidden');
		        form.appendChild(field);
			} else {
		        field = document.createElement('input');
		        field.setAttribute('name', 'data[Exame][situacao][]');
		        field.setAttribute('value', '{$situacao_0}');
		        field.setAttribute('type', 'hidden');
		        form.appendChild(field);

		        field = document.createElement('input');
		        field.setAttribute('name', 'data[Exame][situacao][]');
		        field.setAttribute('value','{$situacao_1}');
		        field.setAttribute('type', 'hidden');
		        form.appendChild(field);

		        field = document.createElement('input');
		        field.setAttribute('name', 'data[Exame][situacao][]');
		        field.setAttribute('value','{$situacao_2}');
		        field.setAttribute('type', 'hidden');
		        form.appendChild(field);

			}

	        if(agrupamento != 4 || (agrupamento == 4  && codigo_selecionado == '')){

		        field = document.createElement('input');
		        field.setAttribute('name', 'data[Exame][tipo_exame][]');
		        field.setAttribute('value','{$tipo_exame_0}');
		        field.setAttribute('type', 'hidden');
		        form.appendChild(field);

	            field = document.createElement('input');
		        field.setAttribute('name', 'data[Exame][tipo_exame][]');
		        field.setAttribute('value', '{$tipo_exame_1}');
		        field.setAttribute('type', 'hidden');
		        form.appendChild(field);

		        field = document.createElement('input');
		        field.setAttribute('name', 'data[Exame][tipo_exame][]');
		        field.setAttribute('value', '{$tipo_exame_2}');
		        field.setAttribute('type', 'hidden');
		        form.appendChild(field);

		        field = document.createElement('input');
		        field.setAttribute('name', 'data[Exame][tipo_exame][]');
		        field.setAttribute('value', '{$tipo_exame_3}');
		        field.setAttribute('type', 'hidden');
		        form.appendChild(field);

		        field = document.createElement('input');
		        field.setAttribute('name', 'data[Exame][tipo_exame][]');
		        field.setAttribute('value', '{$tipo_exame_4}');
		        field.setAttribute('type', 'hidden');
		        form.appendChild(field);
			} else {
		        field = document.createElement('input');
		        field.setAttribute('name', 'data[Exame][tipo_exame][]');
		        field.setAttribute('value', tipo_exame[codigo_selecionado-1]);
		        field.setAttribute('type', 'hidden');
		        form.appendChild(field);

			}

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Exame][data_inicial]');
	        field.setAttribute('value', '{$data_ini}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Exame][data_final]');
	        field.setAttribute('value', '{$data_fim}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
            field.setAttribute('name', 'data[Exame][codigo_unidade]');     
            field.setAttribute('value', codigo_selecionado);
            field.setAttribute('value', agrupamento == 1 ? codigo_selecionado : '');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[Exame][codigo_setor]');     
            field.setAttribute('value', codigo_selecionado);
            field.setAttribute('value', agrupamento == 2 ? codigo_selecionado : '');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);
	               
            field = document.createElement('input');
            field.setAttribute('name', 'data[Exame][codigo_exame]');     
            field.setAttribute('value', codigo_selecionado);
            field.setAttribute('value', agrupamento == 3 ? codigo_selecionado : '');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[Exame][codigo_tipo_exame]');     
            field.setAttribute('value', codigo_selecionado);
            field.setAttribute('value', agrupamento == 4 ? codigo_selecionado : '');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);
	               
	        var janela = window_sizes();
	        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	        document.body.appendChild(form);
	        form.submit();
	    }");?>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render(array(), $dados_sintetico, array(
	    'title' => '',
	    'renderTo' => 'grafico_posicao_exames',
	    'chart' => array('type' => 'pie'),
	    'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
	    'plotOptions' => array('pie' => array('showInLegend'=>true)),
	    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false')))
	))); ?>
<?php endif ?>
<style type="text/css">
	.status_exame{
		color: #08c;
	}
</style>
