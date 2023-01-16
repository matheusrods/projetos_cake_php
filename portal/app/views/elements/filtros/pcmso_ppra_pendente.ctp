<div class="well">
	<div id='filtros'>
        <?php echo $bajax->form('Consulta', 
                                array(  'autocomplete' => 'off', 
                                        'url' => array( 'controller' => 'filtros', 
                                                        'action' => 'filtrar', 
                                                        'model' => 'Consulta', 
                                                        'element_name' => 'pcmso_ppra_pendente'), 
                                        'divupdate' => '.form-procurar')) ?>
            
        <div class="row-fluid inline">
        <?php echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'Consulta'); ?>
    		<?php echo $this->BForm->input('pendencia', array('label' => 'Pendências', 'class' => 'input-large', 'options' => $status, 'empty' => 'Todos')); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>       
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
	</div>

</div>

<?php echo $this->Javascript->codeBlock('
		
	$(function(){

        atualizaLista();

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:Consulta/element_name:pcmso_ppra_pendente/" + Math.random())
        });	
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "consultas/listagem_ppra_pcmso_pendente/" + Math.random());
        }
        
    });', false);
?>
