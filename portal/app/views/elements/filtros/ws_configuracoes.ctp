<div class='well'>
	<?php echo $this->Bajax->form('WsConfiguracao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'WsConfiguracao', 'element_name' => 'ws_configuracoes'), 'divupdate' => '.form-procurar')) ?>
    	<div class="row-fluid inline">
    		<?php echo $this->Buonny->input_codigo_cliente($this,'codigo_cliente','Cliente',false,'WsConfiguracao') ?>
    	</div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php if($isPost): ?>
    <?php echo $this->Javascript->codeBlock('
        $(document).ready(function(){
            atualizaListaWsConfiguracoes();            
        });
    '); ?>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:WsConfiguracao/element_name:ws_configuracoes/" + Math.random())
        });
    });', false);

?>
    		