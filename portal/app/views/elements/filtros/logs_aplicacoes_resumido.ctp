<div class='well'>
	<?php echo $this->Bajax->form('LogAplicacao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogAplicacao', 'element_name' => 'logs_aplicacoes_resumido'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<?= $this->Buonny->input_codigo_cliente($this); ?>
			<?= $this->Buonny->input_periodo($this, 'LogAplicacao') ?>
			<?= $this->BForm->input('hora_inicial', array('label' => false, 'class' => 'hora input-mini')) ?>
			<?= $this->BForm->input('hora_final', array('label' => false, 'class' => 'hora input-mini')) ?>
			
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('sistema', array('class' => 'input-large', 'label'=>false, 'options'=>$sistemas,'empty'=>'Selecione o sistema')); ?>
			<?= $this->BForm->input('descricao', array('class' => 'input-large', 'placeholder' => 'Descrição', 'label' => false)) ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>
<?php 
echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){ 
    	$(".hora").mask("99:99");
    	atualizaListaLogsAplicacoesResumido();    	
    	setup_mascaras();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:LogAplicacao/element_name:logs_aplicacoes_resumido/" + Math.random())
        });
		function atualizaListaLogsAplicacoesResumido() {
			var div = jQuery("div.lista");
			bloquearDiv(div);
			div.load(baseUrl + "LogsAplicacoes/listagem_resumido/" + Math.random());
		}
    });', false);

?>