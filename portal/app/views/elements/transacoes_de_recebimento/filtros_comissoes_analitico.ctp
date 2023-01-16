<div class='well'>
	<?php echo $this->Bajax->form('Tranrec', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Tranrec', 'element_name' => 'comissoes_analitico'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('mes_faturamento', array('class' => 'input-medium', 'label' => 'Mês', 'options' => $meses)); ?>
		<?php echo $this->BForm->input('ano_faturamento', array('class' => 'input-small', 'label' => 'Ano', 'options' => $anos)); ?>
		<?php echo $this->Buonny->input_codigo_endereco_regiao($this, $regioes, 'Selecione', 'codigo_endereco_regiao', 'Filial', 'Tranrec') ?>
		<?php echo $this->BForm->input('tipo_faturamento', array('class' => 'input-small', 'label' => 'Tipo Faturamento', 'options' => array(1 => 'Parcial', 2 => 'Total'), 'empty' => 'Selecione')); ?>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'Tranrec'); ?>
		<?php if ($visualiza_por_configuracao): ?>
			<?php echo $this->BForm->input('configuracao_comissao', array('class' => 'input-small', 'label' => 'Configuração', 'options' => array('1' => 'Configurado', '2' => 'Sem Configuração'), 'empty' => 'Todos')); ?>
		<?php endif ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('codigo_gestor', array('class' => 'input-xlarge', 'label' => 'Gestor', 'options' => $gestores, 'empty' => 'Selecione')); ?>
		<?php echo $this->BForm->input('codigo_seguradora', array('class' => 'input-xlarge', 'label' => 'Seguradora', 'options' => $seguradoras, 'empty' => 'Selecione')); ?>
		<?php echo $this->BForm->input('codigo_corretora', array('class' => 'input-xlarge', 'label' => 'Corretora', 'options' => $corretoras, 'empty' => 'Selecione')); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>