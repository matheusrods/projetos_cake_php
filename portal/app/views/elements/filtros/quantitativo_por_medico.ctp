<div class='well'>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_unidade', 'Cliente', null, 'ClienteImplantacao', isset($codigo_unidade) ? $codigo_unidade : ''); ?>
	</div>
	<div id="sub_fitro" class="row-fluid inline">
		<?php echo $bajax->form('Atestado', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Atestado', 'element_name' => 'quantitativo_por_medico'), 'divupdate' => '.form-procurar')) ?>
		
			<?php if(isset($codigo_grupo_economico)) : ?>
				<?php echo $this->BForm->input('GrupoEconomico.codigo', array('type' => 'hidden', 'id' => 'codigo_grupo_economico', 'value' => $codigo_grupo_economico)); ?>
			<?php endif; ?>
			
			<?php echo $this->BForm->input('Atestado.codigo_cliente', array('value' => (isset($codigo_cliente) ? $codigo_cliente : ''), 'label' => 'Unidade', 'class' => 'input-small','options' => $lista_unidades, 'id' => 'unidades', 'style' => 'width:270px', 'empty' => 'Selecione a Unidade')); ?>
			<?php echo $this->BForm->input('Atestado.codigo_setor', array('value' => (isset($codigo_setor) ? $codigo_setor : ''), 'class' => 'input-small', 'label' => 'Setores', 'options' => $lista_setores,'style' => 'width:270px', 'id' => 'setores', 'empty' => 'Selecione o Setor')); ?>
			<?php echo $this->BForm->input('Atestado.codigo_cargo', array('value' => (isset($codigo_cargo) ? $codigo_cargo : ''), 'class' => 'input-small', 'label' => 'Cargos', 'options' => $lista_cargos,'style' => 'width:270px', 'id' => 'cargos', 'empty' => 'Selecione o Cargo')); ?>
			<?php echo $this->BForm->input('Atestado.codigo_funcionario', array('value' => (isset($codigo_funcionario) ? $codigo_funcionario : ''), 'class' => 'input-small', 'label' => 'Funcionarios', 'options' => $lista_funcionarios, 'id' => 'funcionarios','style' => 'width:270px', 'empty' => 'Selecione o Funcionário')); ?>
		
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $this->BForm->end() ?>
			
		<div class="carregando" style="display: none;">
			<img src="/portal/img/loading.gif" style="padding: 10px;">
		</div>			
	</div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		var div = jQuery(".lista");
		
		if(!$("#codigo_grupo_economico").val()) {
			bloquearDiv(div);
			div.html("");
		} else {
	        bloquearDiv(div);
			$("#sub_fitro").load(baseUrl + "quantitativo_por_medico/sub_filtro_cliente_funcionario/" + $("#codigo_grupo_economico").val() + "/" + $("#ClienteImplantacaoCodigoUnidade").val()  + "/" + Math.random());
		}
		
		$("#ClienteImplantacaoCodigoUnidade").change(function() {
			$.get( baseUrl + "filtros/limpar/model:Atestado/element_name:quantitativo_por_medico/" + Math.random(), atualizaLista($(this).val()) );
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
	        url: "/portal/quantitativo_por_medico/retorna_codigo_grupo_economico",
	        dataType: "json",
	        data: "codigo_unidade=" + codigo_unidade,
	        beforeSend: function() {
				$("#unidades").html("<option>Carregando...</option>");
				$("#setores").html("<option>Carregando...</option>");
				$("#cargos").html("<option>Carregando...</option>");
				$("#funcionarios").html("<option>Carregando...</option>");
				$("#ativo").html("<option>Carregando...</option>");
			},
	        success: function(json) {
				if(json.codigo_grupo_economico) {
					$("#sub_fitro").load(baseUrl + "quantitativo_por_medico/sub_filtro_cliente_funcionario/" + json.codigo_grupo_economico + "/" + codigo_unidade + "/" + Math.random());
		
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