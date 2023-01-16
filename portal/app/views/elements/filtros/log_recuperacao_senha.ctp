<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $this->Bajax->form('LogRecuperaSenha', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogRecuperaSenha', 'element_name' => 'log_recuperacao_senha'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">    
            <?php echo $this->BForm->input('ip', array('label'=>'IP','class' => 'input-medium')) ?>
            <?php echo $this->BForm->input('usuario', array('label' => 'Usuário','class' => 'input-medium')); ?>
            <?php echo $this->Buonny->input_periodo($this, 'LogRecuperaSenha', 'data_inicial', 'data_final', true) ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
            <?php echo $this->BForm->end() ?>
        </div>
    </div>
</div>
 <?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "log_recuperacao_senha/listagem/" + Math.random());':'').'

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:LogRecuperaSenha/element_name:log_recuperacao_senha/" + Math.random())
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