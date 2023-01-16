<div class='well'>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_unidade', 'Cliente', null, 'ClienteImplantacao', isset($codigo_unidade) ? $codigo_unidade : ''); ?>
	</div>
	<div id="sub_fitro" class="row-fluid inline">
		<?php echo $bajax->form('Atestado', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Atestado', 'element_name' => 'absenteismo'), 'divupdate' => '.form-procurar')) ?>
		
			<?php if(isset($codigo_grupo_economico)) : ?>
				<?php echo $this->BForm->input('GrupoEconomico.codigo', array('type' => 'hidden', 'id' => 'codigo_grupo_economico', 'value' => $codigo_grupo_economico)); ?>
			<?php endif; ?>
			
			<?php echo $this->BForm->input('codigo_cliente', array('value' => (isset($codigo_cliente) ? $codigo_cliente : ''), 'label' => 'Unidade', 'class' => 'input-small','options' => $lista_unidades, 'id' => 'unidades', 'style' => 'width:270px', 'empty' => 'Selecione a Unidade')); ?>
			<?php echo $this->BForm->input('codigo_setor', array('value' => (isset($codigo_setor) ? $codigo_setor : ''), 'class' => 'input-small', 'label' => 'Setores', 'options' => $lista_setores,'style' => 'width:270px', 'id' => 'setores', 'empty' => 'Selecione o Setor')); ?>
			<?php echo $this->BForm->input('codigo_cargo', array('value' => (isset($codigo_cargo) ? $codigo_cargo : ''), 'class' => 'input-small', 'label' => 'Cargos', 'options' => $lista_cargos,'style' => 'width:270px', 'id' => 'cargos', 'empty' => 'Selecione o Cargo')); ?>
			<?php echo $this->BForm->input('codigo_funcionario', array('value' => (isset($codigo_funcionario) ? $codigo_funcionario : ''), 'class' => 'input-small', 'label' => 'Funcionarios', 'options' => $lista_funcionarios, 'id' => 'funcionarios','style' => 'width:270px', 'empty' => 'Selecione o Funcionário')); ?>
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
				<input type="radio" name="data[Atestado][tipo_data]" class="pull-left" value="I" required="required" <?php if($this->data['Atestado']['tipo_data'] == 'I'):?> checked="checked" <? endif?> id="tipo_i"><label class="pull-left margin-right-10 margin-left-5" for="tipo_i">Inclusão</label>

				<input type="radio" name="data[Atestado][tipo_data]" class="pull-left margin-left-10" required="required" value="A" <?php if($this->data['Atestado']['tipo_data'] == 'A'):?> checked="checked" <? endif?> id="tipo_a"> <label class="pull-left margin-right-10 margin-left-5" for="tipo_a">Afastamento</label>

				<input type="radio" name="data[Atestado][tipo_data]" class="pull-left margin-left-10" required="required" value="R" <?php if($this->data['Atestado']['tipo_data'] == 'R'):?> checked="checked" <? endif?> id="tipo_r"><label class="pull-left margin-right-10 margin-left-5" for="tipo_r">Retorno</label>
			</div>
		</div>	
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $this->BForm->end() ?>
			
		<div class="carregando" style="display: none;">
			<img src="/portal/img/loading.gif" style="padding: 10px;">
		</div>			
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_mascaras(); setup_time(); setup_datepicker();

		var div = jQuery(".lista");
		
		if(!$("#codigo_grupo_economico").val()) {
			bloquearDiv(div);
			div.html("");
		} else {
	        bloquearDiv(div);
			$("#sub_fitro").load(baseUrl + "absenteismo/sub_filtro_cliente_funcionario/" + $("#codigo_grupo_economico").val() + "/" + $("#ClienteImplantacaoCodigoUnidade").val()  + "/" + Math.random());
		}
		
		$("#ClienteImplantacaoCodigoUnidade").change(function() {
			$.get( baseUrl + "filtros/limpar/model:Atestado/element_name:absenteismo/" + Math.random(), atualizaLista($(this).val()) );
		});
		
		if($("input[name=\"data[ClienteImplantacao][codigo_unidade]\"]").val() != ""){
			atualizaLista( $("input[name=\"data[ClienteImplantacao][codigo_unidade]\"]").val() );
		}
		
		$("#ClienteImplantacaoCodigoUnidade-search").click(function(){
			$(".carregando").show();
		});
    });
		
	function atualizaLista(codigo_unidade) {
		$.ajax({
	        type: "POST",
	        url: "/portal/absenteismo/retorna_codigo_grupo_economico",
	        dataType: "json",
	        data: "codigo_unidade=" + codigo_unidade,
	        beforeSend: function() {
				$("#unidades").html("<option value=\"\">Carregando...</option>");
				$("#setores").html("<option value=\"\">Carregando...</option>");
				$("#cargos").html("<option value=\"\">Carregando...</option>");
				$("#funcionarios").html("<option value=\"\">Carregando...</option>");
				$("#ativo").html("<option>Carregando...</option>");
			},
	        success: function(json) {
				if(json.codigo_grupo_economico) {
					$("#sub_fitro").load(baseUrl + "absenteismo/sub_filtro_cliente_funcionario/" + json.codigo_grupo_economico + "/" + codigo_unidade + "/" + Math.random());
		
				} else {
					$("#unidades").html("<option>Selecione a Unidade</option>");			
					$("#setores").html("<option>Selecione o Setor</option>");
					$("#cargos").html("<option>Selecione o Cargo</option>");
					$("#funcionarios").html("<option>Selecione o Funcionário</option>");
					$("#ativo").html("<option>Status</option>");
		
					alert("Cliente não encontrado!");
				}
	        },
	        complete: function() {
				$(".lista").html("");
			}
		});		
	}', false);
?>