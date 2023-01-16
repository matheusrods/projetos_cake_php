<div class='well'>
	<?php echo $bajax->form('OrdemServico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'OrdemServico', 'element_name' => 'consulta_vigencia_ppra_pcmso'), 'divupdate' => '.form-procurar')) ?>
	<h5><?= $this->Html->link((!empty($this->data['OrdemServico']['codigo_cliente']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_consulta_versao($this, 'OrdemServico', $unidades); ?>
			<?php echo $this->BForm->input('produto',  array('options' => $produtos, 'empty' => 'Selecione um Produto', 'label' => "Produtos", 'type' => 'select', 'class' => 'input-large')); ?>
		</div>

		<div class="row-fluid inline">
			<span class="label label-info">Status:</span>
			<div id='agrupamento'>
		        <?php echo $this->BForm->input('status', array('type' => 'select', 'multiple' => 'checkbox', 'options' => (array)$status, 'label' => false, 'id' => false, 'hiddenField' => false, 'class' => 'checkbox inline input-xsmall')) ?>
		    </div>
		    
		    <div id="data_periodo" >
			    <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple', 'default' => date('d/m/Y'), 'oldvalue' => date('d/m/Y'))); ?>
				<?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple', 'default' => date('d/m/Y', strtotime("+ 30 days")), 'oldvalue' => date('d/m/Y', strtotime("+ 30 days")))); ?>
			</div>
		</div>
		<div class="row-fluid inline">
			<span class="label label-info">Ordenação:</span>
	        <div id='agrupamento'>
            	<?php echo $this->BForm->input('ordenacao', array('type' => 'radio', 'options' => $ordenacao, 'default' => 6, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
        	</div>
		</div>

		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-consulta-ordem-servico', 'class' => 'btn')) ;?>
	
	</div>
	<?php echo $this->BForm->end() ?>
</div>
<?php $this->addScript($this->Buonny->link_js('moment.min')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "aplicacao_exames/vigencia_ppra_pcmso_listagem/" + Math.random());

		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

		jQuery("#limpar-filtro-consulta-ordem-servico").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:OrdemServico/element_name:consulta_vigencia_ppra_pcmso/" + Math.random());
            $("#OrdemServicoCodigoCliente").val("");
        });
        
        jQuery("#OrdemServicoStatusAV").on(\'click\', function(){
            if(jQuery(this).is(":checked")){
                jQuery("div#data_periodo").show();
            }else{
                jQuery("div#data_periodo").hide();
            }
        });
        
        jQuery("#OrdemServicoDataInicio").on(\'change\', function(){
            if(jQuery("#OrdemServicoStatusAV").is(":checked")){
                if(moment().diff(moment(this.value, ["DD/MM/YYYY", "YYYY-MM-DD"], true), \'days\') > 0){
                    swal("ATENÇÃO!", "A data de inicio do à vencer, não pode ser menor que a data atual!", "warning");
                    this.value = jQuery(this).attr("oldvalue");
                }else{
                    jQuery(this).attr("oldvalue", this.value);
                }
            }
        })
        
        jQuery("#OrdemServicoDataInicio").val(jQuery("#OrdemServicoDataInicio").attr("oldvalue"));
        jQuery("#OrdemServicoDataFim").val(jQuery("#OrdemServicoDataFim").attr("oldvalue"));
                
        jQuery("div#data_periodo").hide();
    });', false);
?>
<?php if (!empty($this->data['OrdemServico']['codigo_cliente'])): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})'); ?>
<?php endif; ?>
<?php if(!empty($this->data['OrdemServico']['status']) && is_array($this->data['OrdemServico']['status']) && in_array('AV', $this->data['OrdemServico']['status'])): ?>
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#data_periodo").show()})'); ?>
<?php endif; ?>