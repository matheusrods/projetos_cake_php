<div class="well">
	<?php echo $this->Bajax->form('Tranrec', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Tranrec', 'element_name' => 'transacoes_recebimento_sintetico'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->hidden('listar', array('value' =>1)); ?>
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', False,'Tranrec') ?>
			<?php echo $this->Buonny->input_periodo($this,'Tranrec') ?>
			<?php echo $this->Buonny->input_codigo_endereco_regiao($this, $filiais, 'Filiais','codigo_endereco_regiao', false, 'Tranrec') ?>
		</div>
		<div class="row-fluid inline">
	      	<?php echo $this->Buonny->input_codigo_corretora($this, 'codigo_corretora', 'Corretora', false, 'Tranrec'); ?>
			<?php echo $this->BForm->input('codigo_seguradora', array('label' => false, 'class' => 'input-medium', 'options' => $seguradoras, 'empty' => ' Seguradoras')); ?>
		    <?php echo $this->BForm->input('status', array('label' => false, 'class' => 'input-small', 'options' => $tranrec_status, 'empty' => 'Status')); ?>
	    </div>        
	    <div class="row-fluid inline">
            <span class="label label-info">Agrupar por:</span>
            <div id='agrupamento'>
                <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
            </div>
        </div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn','id'=>'filtro')); ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function() {
	setup_datepicker();
	jQuery("#limpar-filtro").click(function(){
		bloquearDiv(jQuery(".form-procurar"));
		jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Tranrec/element_name:transacoes_recebimento_sintetico/" + Math.random())
		
	});
	var div = jQuery(".lista");
	bloquearDiv(div);
	div.load(baseUrl + "transacoes_de_recebimento/sintetico_listagem/" + Math.random());
})') ?>