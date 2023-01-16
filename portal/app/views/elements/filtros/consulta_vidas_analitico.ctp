<div class='well'>
	<?php echo $bajax->form('ClienteFuncionarioAnalitico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteFuncionarioAnalitico', 'element_name' => 'consulta_vidas_analitico'), 'divupdate' => '.form-procurar')) ?>

	<?= $this->element('clientes_funcionarios/fields_filtros_consulta_vidas_analitico') ?>
        


        <div class="row-fluid">	

			<div class="span4">	
        		<?php echo $this->BForm->input('ClienteFuncionarioAnalitico.ativo', array('options' => array('1'=>'ATIVO', '0' => 'INATIVO'), 'empty' => 'Selecione o Status', 'legend' => false, 'label' => false, 'div' =>'control-group input select')) ?>   				
			</div>
		    <div class="span4">	
		    	<?php echo $this->Form->input('candidato', array('required' => true, 'legend' => false, 'type' => 'select', 'multiple' => 'checkbox','label' => false, 'options' => array('candidato_entre' => 'Candidatos'))); ?>
		   	</div>
		</div>



		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id' => 'bt-analitico')) ?>	
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-consulta-vidas', 'class' => 'btn')) ;?>
		
	<?php echo $this->BForm->end() ?>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "clientes_funcionarios/consulta_vidas_analitico_listagem/" + Math.random());            
		jQuery("#limpar-filtro-consulta-vidas").click(function(){
            bloquearDiv(jQuery(".form-procurar"));            
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteFuncionarioAnalitico/element_name:consulta_vidas_analitico/" + Math.random())			
        });

        $("#ClienteFuncionarioAnaliticoCandidatoCandidatoEntre").change(function(event) {
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

		if($("#ClienteFuncionarioAnaliticoCandidatoCandidatoEntre").is(":checked")){
			 $("#ClienteFuncionarioAnaliticoCandidatoCandidatoEntre").change();
		}
		
    });', false);
?>