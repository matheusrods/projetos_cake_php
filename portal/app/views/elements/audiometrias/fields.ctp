<?php //debug($this->data); ?>

<div class='well'>
	<div class='row-fluid'>	
		<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
		<?php echo $this->BForm->hidden('redir', array('value' => $redir)); ?>
		
		<div class="span2 no-margin-left padding-left-10">
			<?php echo $this->BForm->input('codigo_funcionario', array('value' => $funcionario['Funcionario']['codigo'], 'label' => 'Cód. funcionário:',  'style' => 'width: 86%; margin-bottom: 0', 'readonly' => true)) ?>
		</div>
		<div class="span4 padding-left-10">
			<?php echo $this->BForm->input('nome_funcionario', array('value' => $funcionario['Funcionario']['nome'], 'label' => 'Funcionario:', 'style' => 'width: 95%; margin-bottom: 0', 'readonly' => true)) ?>
		</div>
		<div class="span6 padding-left-10">
			<?php echo $this->BForm->input('razao_social', array('value' => $funcionario['Cliente']['razao_social'], 'label' => 'Unidade:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
		</div>
	</div>
</div>
<div class="row-fluid">
	<h4>Criar relatório de Audiometria</h4>
	<div class="clear"></div>
	<hr>
	<?php echo $this->BForm->input('data_exame', array('type' => 'text', 'class' => 'data input-small', 'label' => 'Data do exame:', 'div' => array('class' => 'span2 control-group', 'style' => 'margin-left: 0'))); ?>

	<?php echo $this->BForm->input('tipo_exame', array('options' => $tipos_exames, 'disabled' => true, 'label' => 'Tipo de exame:', 'empty' => 'Selecione', 'div' => array('class' => 'span4 control-group', 'style' => 'margin-left: 0'))); ?>
	<div class="clear"></div>
	<hr>

	<?php echo $this->BForm->input('resultado', array('label' => 'Resultado:', 'options'  => $resultados, 'empty' => 'Selecione', 'div' => array('class' => 'span3 control-group', 'style' => 'margin-left: 0'))); ?>
	<?php echo $this->BForm->input('ref_seq', array('label' => 'Ref / Seq:', 'options'  => $refseq, 'empty' => 'Selecione', 'div' => array('class' => 'span3 control-group', 'style' => 'margin-left: 0'))); ?>
	<?php echo $this->BForm->input('aparelho', array('label' => 'Aparelho:', 'options' => $aparelhos_audiometricos, 'empty' => 'Selecione', 'div' => array('class' => 'span3 control-group clear', 'style' => 'margin-left: 0'))); ?>
	<?php echo $this->BForm->input('fabricante', array('label' => 'Fabricante:', 'div' => array('class' => 'span3 control-group', 'style' => 'margin-left: 0'))); ?>

	<?php echo $this->BForm->input('calibracao', array('type' => 'text', 'class' => 'data input-small', 'label' => 'Calibração:', 'div' => array('class' => 'span2 control-group', 'style' => 'margin-left: 0'))); ?>

	<div class="clear"></div>

	<?php echo $this->BForm->input('em_analise', array('type' => 'checkbox', 'label' => 'Em análise', 'value' => 1)) ?>
	<?php echo $this->BForm->input('ocupacional', array('type' => 'checkbox', 'label' => 'Ocupacional', 'value' => 1)) ?>
	<?php echo $this->BForm->input('agravamento', array('type' => 'checkbox', 'label' => 'Agravamento', 'value' => 1)) ?>
	<?php echo $this->BForm->input('estavel', array('type' => 'checkbox', 'label' => 'Estável', 'value' => 1)) ?>

	<div class="clear"></div>
	<hr>
	
	<div class="span3 control-group" style="margin-left: 0">
		<label>Ouve bem?</label>
		<?php echo $this->BForm->input('ouve_bem', array('type' => 'radio', 'legend' => false , 'hiddenField' => false, 'options' => array(1 => 'Sim', 0 => 'Não'))) ?>
	</div>

	<div class="span3 control-group" style="margin-left: 0">
		<label>Zumbido no ouvido?</label>
		<?php echo $this->BForm->input('zumbido_ouvido', array('type' => 'radio', 'legend' => false , 'hiddenField' => false, 'options' => array(1 => 'Sim', 0 => 'Não'))) ?>
	</div>

	<div class="span3 control-group" style="margin-left: 0">
		<label>Já sofreu trauma nos ouvidos?</label>
		<?php echo $this->BForm->input('trauma_ouvidos', array('type' => 'radio', 'legend' => false , 'hiddenField' => false, 'options' => array(1 => 'Sim', 0 => 'Não'))) ?>
	</div>

	<div class="span3 control-group" style="margin-left: 0">
		<label>Já apresentou alguma doença auditiva?</label>
		<?php echo $this->BForm->input('doenca_auditiva', array('type' => 'radio', 'legend' => false , 'hiddenField' => false, 'options' => array(1 => 'Sim', 0 => 'Não'))) ?>
	</div>

	<div class="span3 control-group" style="margin-left: 0">
		<label>Já trabalhou em local ruidoso?</label>
		<?php echo $this->BForm->input('local_ruidoso', array('type' => 'radio', 'legend' => false , 'hiddenField' => false, 'options' => array(1 => 'Sim', 0 => 'Não'))) ?>
	</div>

	<div class="span3 control-group" style="margin-left: 0">
		<label>Já realizou este exame anteriormente?</label>
		<?php echo $this->BForm->input('realizou_exame', array('type' => 'radio', 'legend' => false , 'hiddenField' => false, 'options' => array(1 => 'Sim', 0 => 'Não'))) ?>
	</div>

	<div class="clear"></div>

	<div class="span3 control-group" style="margin-left: 0">
		<label>Repouso auditivo:</label>
		<?php echo $this->BForm->input('repouso_auditivo', array('type' => 'radio', 'legend' => false , 'hiddenField' => false, 'options' => array(1 => 'Sim', 0 => 'Não'))) ?>
	</div>

	<?php echo $this->BForm->input('horas_repouso_auditivo',  array('type' => 'text', 'div' => array('class' => 'span3 control-group', 'style' => 'margin-left: 0'), 'label' => 'Quantas horas de repouso auditivo:', 'maxlength' => 5, 'class' => 'input-mini')); ?>

	<div class="clear"></div>
	<hr>

	<?php echo $this->BForm->input('observacoes', array('label' => 'Observações:', 'rows' => 2, 'div' => array('class' => 'span6 control-group clear', 'style' => 'margin-left: 0'), 'class' => 'input-xxlarge')); ?>

	<div class="clear"></div>
	<hr>

	<div class="span4" style="margin-left: 0">
		<label><strong>Meatoscopia</strong></label>

		<?php echo $this->BForm->input('meatoscopia_od', array('div' => array('class' => 'span12 control-group', 'style' => 'margin-left: 0'), 'label' => 'Meatoscopia (OD)', 'options' => $meatoscopias, 'empty' => 'Selecione')) ?>

		<?php echo $this->BForm->input('meatoscopia_oe', array('div' => array('class' => 'span12 control-group', 'style' => 'margin-left: 0'), 'label' => 'Meatoscopia (OE)', 'options' => $meatoscopias, 'empty' => 'Selecione')) ?>

	</div>

	<div class="span8">
		<label><strong>Logoaudiometria</strong></label>
		<div class="row-fluid">
			<div class="span4">
				<?php echo $this->BForm->input('str_od_dbna', array('label' => 'SRT(OD) dBNA:', 'div' => array('class' => 'control-group'))); ?>
				<?php echo $this->BForm->input('str_oe_dbna', array('label' => 'SRT(OE) dBNA:', 'div' => array('class' => 'control-group'))); ?>
			</div>
			<div class="span4">
				<?php echo $this->BForm->input('irf_od', array('label' => 'IRF(OD) %:', 'div' => array('class' => 'control-group'))); ?>
				<?php echo $this->BForm->input('irf_oe', array('label' => 'IRF(OE) %:', 'div' => array('class' => 'control-group'))); ?>
			</div>
			<div class="span4">
				<?php echo $this->BForm->input('laf_od_dbna', array('label' => 'LAF(OD) dBNA:', 'div' => array('class' => 'control-group'))); ?>
				<?php echo $this->BForm->input('laf_oe_dbna', array('label' => 'LAF(OE) dBNA:', 'div' => array('class' => 'control-group'))); ?>
			</div>
		</div>
	</div>

	<div class="clear"></div>
	<hr>
	
	<div class="span6" style="margin-left: 0">
		<div class="row-fluid">
			<h4 class="text-center">Limiares Tonais - Orelha esquerda</h4>
			<table class="table-config" border="1" bordercolor="#ccc">
				<thead>
					<tr>
						<th>KHz</th>
						<th>Via Aérea</th>
						<th>Via Óssea</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="text-center">
							<strong>0.25</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_va_025', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_va_025]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVa025" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_va_025']) ? $this->data['Audiometria']['esq_va_025'] : ""; ?>">
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_vo_025', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_vo_025]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVo025" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_vo_025']) ? $this->data['Audiometria']['esq_vo_025'] : ""; ?>">
						</td> 
					</tr>
					<tr>
						<td class="text-center">
							<strong>0.50</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_va_050', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_va_050]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVa050" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_va_050']) ? $this->data['Audiometria']['esq_va_050'] : ""; ?>">
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_vo_050', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_vo_050]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVo050" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_vo_050']) ? $this->data['Audiometria']['esq_vo_050'] : ""; ?>">
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>1</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_va_1', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_va_1]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVa1" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_va_1'])? $this->data['Audiometria']['esq_va_1'] : ""; ?>">
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_vo_1', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_vo_1]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVo1" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_vo_1'])? $this->data['Audiometria']['esq_vo_1'] : ""; ?>">
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>2</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_va_2', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_va_2]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVa2" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_va_2']) ? $this->data['Audiometria']['esq_va_2'] : ""; ?>">		
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_vo_2', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_vo_2]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVo2" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_vo_2']) ? $this->data['Audiometria']['esq_vo_2'] : ""; ?>">		
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>3</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_va_3', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_va_3]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVa3" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_va_3']) ? $this->data['Audiometria']['esq_va_3'] : ""; ?>">
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_vo_3', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_vo_3]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVo3"  min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_vo_3']) ? $this->data['Audiometria']['esq_vo_3'] : ""; ?>">			
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>4</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_va_4', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_va_4]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVa4" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_va_4']) ? $this->data['Audiometria']['esq_va_4'] : ""; ?>">
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_vo_4', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_vo_4]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVo4" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_vo_4']) ? $this->data['Audiometria']['esq_vo_4'] : ""; ?>">
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>6</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_va_6', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_va_6]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVa6" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_va_6']) ? $this->data['Audiometria']['esq_va_6'] : ""; ?>">
						</td>
						<td class="text-center">
							<?php ///echo $this->Form->input('esq_vo_6', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_vo_6]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVo6" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_vo_6']) ? $this->data['Audiometria']['esq_vo_6'] : ""; ?>">		
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>8</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_va_8', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_va_8]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVa8" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_va_8']) ? $this->data['Audiometria']['esq_va_8'] : ""; ?>">	
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('esq_vo_8', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][esq_vo_8]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaEsqVo8" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['esq_vo_8']) ? $this->data['Audiometria']['esq_vo_8'] : ""; ?>">		
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="span6">
		<div class="row-fluid">
			<h4 class="text-center">Limiares Tonais - Orelha direita</h4>
			<table class="table-config" border="1" bordercolor="#ccc">
				<thead>
					<tr>
						<th>KHz</th>
						<th>Via Aérea</th>
						<th>Via Óssea</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="text-center">
							<strong>0.25</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_va_025', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_va_025]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVa025" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_va_025']) ? $this->data['Audiometria']['dir_va_025'] : ""; ?>">
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_vo_025', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_vo_025]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVo025" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_vo_025']) ? $this->data['Audiometria']['dir_vo_025'] : ""; ?>">		
						</td> 
					</tr>
					<tr>
						<td class="text-center">
							<strong>0.50</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_va_050', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_va_050]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVa050" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_va_050']) ? $this->data['Audiometria']['dir_va_050'] : ""; ?>">		
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_vo_050', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_vo_050]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVo050" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_vo_050']) ? $this->data['Audiometria']['dir_vo_050'] : ""; ?>">		
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>1</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_va_1', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_va_1]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVa1" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_va_1']) ? $this->data['Audiometria']['dir_va_1'] : ""; ?>">		
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_vo_1', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_vo_1]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVo1" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_vo_1']) ? $this->data['Audiometria']['dir_vo_1'] : ""; ?>">		
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>2</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_va_2', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_va_2]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVa2" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_va_2']) ? $this->data['Audiometria']['dir_va_2'] : ""; ?>">	
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_vo_2', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_vo_2]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVo2" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_vo_2']) ? $this->data['Audiometria']['dir_vo_2'] : ""; ?>">		
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>3</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_va_3', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_va_3]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVa3" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_va_3']) ? $this->data['Audiometria']['dir_va_3'] : ""; ?>">		
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_vo_3', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_vo_3]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVo3" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_vo_3']) ? $this->data['Audiometria']['dir_vo_3'] : ""; ?>">		
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>4</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_va_4', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_va_4]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVa4" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_va_4']) ? $this->data['Audiometria']['dir_va_4'] : ""; ?>">		
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_vo_4', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_vo_4]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVo4" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_vo_4']) ? $this->data['Audiometria']['dir_vo_4'] : ""; ?>">		
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>6</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_va_6', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_va_6]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVa6" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_va_6']) ? $this->data['Audiometria']['dir_va_6'] : ""; ?>">	
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_vo_6', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_vo_6]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVo6" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_vo_6']) ? $this->data['Audiometria']['dir_vo_6'] : ""; ?>">		
						</td>
					</tr>
					<tr>
						<td class="text-center">
							<strong>8</strong>
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_va_8', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_va_8]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVa8" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_va_8']) ? $this->data['Audiometria']['dir_va_8'] : ""; ?>">	
						</td>
						<td class="text-center">
							<?php //echo $this->Form->input('dir_vo_8', array('class' => 'input-mini', 'label' => false)); ?>
							<input name="data[Audiometria][dir_vo_8]" type="number" class="input-mini valida-campo-inteiro" maxlength="6" id="AudiometriaDirVo8" min="-10" max="500" value="<?php echo isset($this->data['Audiometria']['dir_vo_8']) ? $this->data['Audiometria']['dir_vo_8'] : ""; ?>">	
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="clear"></div>
	<hr>

	<?php echo $this->BForm->input('observacoes2', array('label' => 'Observações:', 'rows' => 2, 'div' => array('class' => 'span6 control-group clear', 'style' => 'margin-left: 0'), 'class' => 'input-xxlarge')); ?>
	<div class="clear"></div>
	<hr>

	<div class="span12" style="margin-left: 0">
		<!-- <label><strong>Diagnóstico</strong></label> -->
		<div class="row-fluid">
			<div class="span4">
				<?php echo $this->BForm->input('diagnostico', array('div' => array('class' => 'span12 control-group', 'style' => 'margin-left: 0'), 'label' => 'Diagnóstico', 'options' => $diagnosticos, 'empty' => 'Selecione')) ?>
			</div>

			<div class="span8">
				<?php echo $this->BForm->input('diagnostico_descricao', array('label' => 'Descrição diagnóstico', 'rows' => 2, 'div' => array('class' => 'span6 control-group clear', 'style' => 'margin-left: 0'), 'class' => 'input-xxlarge')); ?>
				<div class="clear"></div>				
			</div>
		</div>
	</div>
	<hr>
</div>

<style type="text/css">
	.table-config{
		width: 100%;
	}
	.table-config thead tr{
		border: 1px solid #ccc;
	}
	.table-config input{
		margin-top: 5px;
		margin-bottom: 5px;
	}
</style>

<?php echo $this->Javascript->codeBlock("
	setup_datepicker();
	$(document).ready(function() {
		$('#AudiometriaAparelho').change(function(event) {
			if(this.value > 0) {
				$.ajax({
					url: baseUrl + '/audiometrias/obtem_aparelhos_por_ajax',
					type: 'POST',
					dataType: 'json',
					data: {codigo: this.value},
				})
				.done(function(response) {
					if(response) {
						$('#AudiometriaFabricante').val(response.fabricante);
						$('#AudiometriaCalibracao').val(response.data);
					} 
				});
			} else {
				$('#AudiometriaFabricante').val('');
				$('#AudiometriaCalibracao').val('');
			}
		});

		if($('#AudiometriaDiagnostico').val() == 2) {
			$('#AudiometriaDiagnosticoDescricao').parent().show();
		}else{
			$('#AudiometriaDiagnosticoDescricao').val('').parent().hide();
		}

		$('#AudiometriaDiagnostico').change(function(event) {
			if(this.value == 2) {
				$('#AudiometriaDiagnosticoDescricao').parent().show();
			}else{
				$('#AudiometriaDiagnosticoDescricao').val('').parent().hide();
			}
		});
	}); 
"); ?>