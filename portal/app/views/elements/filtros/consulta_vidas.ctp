<div class='well'>
	<?php echo $bajax->form('ClienteFuncionario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteFuncionario', 'element_name' => 'consulta_vidas'), 'divupdate' => '.form-procurar')) ?>
	<?= $this->element('clientes_funcionarios/fields_filtros_consulta_vidas') ?>
	
	<div class="row-fluid">	

		<div class="span4">	
			<?php echo $this->BForm->input('ClienteFuncionario.ativo', array('options' => array('1'=>'ATIVO', '0' => 'INATIVO'), 'empty' => 'Selecione o Status', 'legend' => false, 'label' => false, 'div' =>'control-group input select')) ?>    
		</div>
	    <div class="span4">	
	    	<?php echo $this->Form->input('candidato', array('required' => true, 'legend' => false, 'type' => 'select', 'multiple' => 'checkbox','label' => false, 'options' => array('candidato_entre' => 'Candidatos'))); ?>
	   	</div>
	</div>


	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id' => 'bt-sintetico')) ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-consulta-vidas', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->input('interno', array('type' => 'hidden', 'value' => $interno))?>
    <?php echo $this->BForm->end() ?>
    <?php if($interno == 1){ ?>
    <div class="well">
		   	<span class="pull-right">
		   		<strong>	
		   			<a href="/portal/clientes_funcionarios/consulta_vidas_listagem/export" title="Exportar Vidas/clientes para CSV" alt="Exportar Vidas/clientes para CSV"><i class="cus-page-white-excel"></i></a>		   		</strong>
			</span>
	</div>
    <?php } ?>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "clientes_funcionarios/consulta_vidas_listagem/" + Math.random());
		jQuery("#limpar-filtro-consulta-vidas").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteFuncionario/element_name:consulta_vidas/" + Math.random())
        });

        $("#ClienteFuncionarioCandidatoCandidatoEntre").change(function(event) {
            if($(this).is(":checked")) {
                $("#datas_entre").remove();
                $(this).next().after($("<div>", {id: "datas_entre"})
                    .append("de: ")
                    .append(
                        $("<input>", {name: "data[ClienteFuncionario][data_inicial]", class: "data", value: "'.$this->data['ClienteFuncionario']['data_inicial'].'"  ,style: "width:70px", required: "required"})
                        )
                    .append("&nbsp;&nbsp;&nbsp;até: ")
                    .append(
                        $("<input>", {name: "data[ClienteFuncionario][data_final]", class: "data", value: "'.$this->data['ClienteFuncionario']['data_final'].'", style: "width:70px;", required: "required"})
                        )
                    .append($("<div>", {class: "block margin-bottom-15"}))
                    );
                setup_datepicker();
            } else {
                $("#datas_entre").remove();
            }
        });

		if($("#ClienteFuncionarioCandidatoCandidatoEntre").is(":checked")){
			 $("#ClienteFuncionarioCandidatoCandidatoEntre").change();
		}

    });', false);
?>