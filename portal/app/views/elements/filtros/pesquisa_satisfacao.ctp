<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('PesquisaSatisfacao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PesquisaSatisfacao', 'element_name' => 'pesquisa_satisfacao'), 'divupdate' => '.form-procurar')) ?>
            <div class="row-fluid inline">                
                <?php echo $this->Buonny->input_codigo_cliente($this,'codigo_cliente','Cliente',true,'PesquisaSatisfacao'); ?>
                <?php echo $this->Buonny->input_periodo($this,'PesquisaSatisfacao','data_inicial', 'data_final', TRUE) ?>
                <?php echo $this->BForm->input('codigo_produto', array('class' => 'input-medium','label' => 'Produto','options' => array('Todos os produtos','1' => 'Teleconsult','82' => 'BuonnySat'))) ?>
                <?php echo $this->BForm->input('status_pesquisa', array('class' => 'input-medium','label' => 'Pesquisa','options' => array('Todas Pesquisas','Pendente','Realizada'))) ?>
             </div>
	        <div class="row-fluid inline">
                <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    	        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
    	        <?php echo $this->BForm->end() ?>
		    </div>
		<?php echo $this->BForm->end() ?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "pesquisas_satisfacao/listagem_pesquisa_satisfacao/" + Math.random());
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PesquisaSatisfacao/element_name:pesquisa_satisfacao/" + Math.random())
            jQuery(".lista").empty();
           
        });
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php else: ?>    
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").show()})');?> 
 <?php endif; ?>