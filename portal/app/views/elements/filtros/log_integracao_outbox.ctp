<div class='well'>
	<?php echo $bajax->form('LogIntegracaoOutbox', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogIntegracaoOutbox', 'element_name' => 'log_integracao_outbox'), 'divupdate' => '.form-procurar')) ?>
    	<div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this); ?>
			<?php echo $this->Buonny->input_periodo($this, 'LogIntegracaoOutbox') ?>
			<?php echo $this->BForm->input('hora_inicial', array('label' => false, 'class' => 'hora input-mini')) ?>
			<?php echo $this->BForm->input('hora_final', array('label' => false, 'class' => 'hora input-mini')) ?>
			<?php echo $this->BForm->input('sistema', array('class' => 'input-large', 'label'=>false, 'options'=>$sistemas,'empty'=>'Sistema')); ?>
        </div>
        <div class="row-fluid inline">
			<?php echo $this->BForm->input('codigo_sm', array('class' => 'input-medium', 'placeholder' => 'SM', 'label' => false, 'maxlength' => 10)) ?>
			<?php echo $this->BForm->input('loadplan', array('class' => 'input-medium', 'placeholder' => 'Loadplan', 'label' => false, 'maxlength' => 10)) ?>
            <?php echo $this->BForm->input('sucesso', array('class' => 'input-small', 'label'=>false, 'options'=>Array('S'=>'Sim','N'=>'Não'),'empty'=>'Sucesso')); ?>
		</div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaLogIntegracaoOutbox("div#lista");
        setup_datepicker();
        setup_time();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:LogIntegracaoOutbox/element_name:log_integracao_outbox/" + Math.random())
        });
    });', false);

?>
