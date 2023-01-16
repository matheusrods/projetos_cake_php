<div class='well'>
	<h5><?= $this->Html->link((!empty($this->data['Exame']['codigo_cliente']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
		<?php echo $bajax->form('Exame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Exame', 'element_name' => 'posicao_exames_analitico'), 'divupdate' => '.form-procurar')) ?>
		<?= $this->element('exames/posicao_fields_filtros') ?>
		<div class="row-fluid">
    		<?php echo $this->Form->input('tipo_exame', array('type' => 'hidden', 'value' => $tipos_exames)); ?>
		    
		    <div class="span4 " style="margin-left: 0">
		        <span class="label label-info">Situação:</span>
		        <?php echo $this->Form->input('situacao', array('required' => true, 'legend' => false, 'type' => 'select', 'multiple' => 'checkbox','label' => false, 'options' => $tipos_situacoes)); ?>
		    </div>		    
		</div>		


		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-posicao', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end() ?>
	</div>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "exames/posicao_exames_analitico_listagem/" + Math.random());

		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

		jQuery("#limpar-filtro-posicao").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Exame/element_name:posicao_exames_analitico/" + Math.random())
        });
		

	    $("#ExameSituacaoVencerEntre").change(function(event) {
	            if($(this).is(":checked")) {
	                $("#datas_entre").remove();
	                $(this).next().after($("<div>", {id: "datas_entre"})
	                    .append("de: ")
	                    .append(
	                        $("<input>", {name: "data[Exame][data_inicial]", class: "data", value: "'.$this->data['Exame']['data_inicial'].'"  ,style: "width:70px", required: "required"})
	                        )
	                    .append("&nbsp;&nbsp;&nbsp;até: ")
	                    .append(
	                        $("<input>", {name: "data[Exame][data_final]", class: "data", value: "'.$this->data['Exame']['data_final'].'", style: "width:70px;", required: "required"})
	                        )
	                    .append($("<div>", {class: "block margin-bottom-15"}))
	                    );
	                setup_datepicker();
	            } else {
	                $("#datas_entre").remove();
	            }
	        });

	     if($("#ExameSituacaoVencerEntre").is(":checked")){
	     	 $("#ExameSituacaoVencerEntre").change();
		 }


    });', false);
?>
<?php if (!empty($this->data['Exame']['codigo_cliente'])): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>

 