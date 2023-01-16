<?php echo $bajax->form('Atestado', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Atestado', 'element_name' => 'absenteismo'), 'divupdate' => '.form-procurar')) ?>
	<?php echo $this->BForm->input('GrupoEconomico.codigo', array('type' => 'hidden', 'id' => 'codigo_grupo_economico', 'value' => $codigo_grupo_economico)); ?>
	<?php echo $this->BForm->input('codigo_unidade', array('type' => 'hidden', 'id' => 'codigo_unidade', 'value' => $codigo_unidade)); ?>
	
	<?php echo $this->BForm->input('codigo_cliente', array('value' => (isset($codigo_cliente) ? $codigo_cliente : ''), 'label' => 'Unidade', 'class' => 'input-small','options' => $lista_unidades, 'id' => 'unidades', 'style' => 'width:270px', 'empty' => 'Selecione a Unidade')); ?>
	<?php echo $this->BForm->input('codigo_setor', array('value' => (isset($codigo_setor) ? $codigo_setor : ''), 'class' => 'input-small', 'label' => 'Setores', 'options' => $lista_setores,'style' => 'width:270px', 'id' => 'setores', 'empty' => 'Selecione o Setor')); ?>
	<?php echo $this->BForm->input('codigo_cargo', array('value' => (isset($codigo_cargo) ? $codigo_cargo : ''), 'class' => 'input-small', 'label' => 'Cargos', 'options' => $lista_cargos,'style' => 'width:270px', 'id' => 'cargos', 'empty' => 'Selecione o Cargo')); ?>
	<?php echo $this->BForm->input('codigo_funcionario', array('value' => (isset($codigo_funcionario) ? $codigo_funcionario : ''), 'class' => 'input-small', 'label' => 'Funcionarios', 'options' => $lista_funcionarios, 'id' => 'funcionarios','style' => 'width:270px', 'empty' => 'Selecione o Funcionário')); ?>
	<div id="sub_fitro" class="row-fluid inline">
		<div id="sub_fitro" class="row-fluid inline">
			<div class="span1" style="padding-top: 5px">
				Data:
			</div>
			<div class="span2" style="margin-left: -1%" >
				<?php echo $this->BForm->input('data_inicio', array('label' => false, 'place-holder' => 'Afastamento', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
			</div>
			<div class="span1" style="padding-top: 6px;margin-left: -3%">
				até
			</div>
			<div class="span2" style="margin-left: -3%" >
				<?php echo $this->BForm->input('data_fim', array('label' => false, 'place-holder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
			</div>
			<div class="span6" >
			<?php if(empty($this->data['Atestado']['tipo_data'])): 
					$this->data['Atestado']['tipo_data'] = "";  
					endif;
			?>
				<div class="pull-left margin-right-10">Data de:</div>
				<input type="radio" name="data[Atestado][tipo_data]" class="pull-left" required="required" value="I" <?php if($this->data['Atestado']['tipo_data'] == 'I'):?> checked="checked" <? endif?> id="tipo_i"><label class="pull-left margin-right-10 margin-left-5" for="tipo_i">Inclusão</label>

				<input type="radio" name="data[Atestado][tipo_data]" class="pull-left margin-left-10" required="required" value="A" <?php if($this->data['Atestado']['tipo_data'] == 'A'):?> checked="checked" <? endif?> id="tipo_a"> <label class="pull-left margin-right-10 margin-left-5" for="tipo_a">Afastamento</label>

				<input type="radio" name="data[Atestado][tipo_data]" class="pull-left margin-left-10" required="required" value="R" <?php if($this->data['Atestado']['tipo_data'] == 'R'):?> checked="checked" <? endif?> id="tipo_r"><label class="pull-left margin-right-10 margin-left-5" for="tipo_r">Retorno</label>
			</div>
		</div>	

	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $this->BForm->end() ?>
	    
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function() {
		setup_mascaras(); setup_time(); setup_datepicker();

		if($("#codigo_grupo_economico").val()) {
			$(".lista").load(baseUrl + "absenteismo/listagem/" + $("#codigo_grupo_economico").val() + "/" + Math.random());
		}
    });
'); ?>