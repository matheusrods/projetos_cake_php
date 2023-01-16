<?php $filtrado = $this->data['TIcheItemChecklist']['iche_pjur_pess_oras_codigo'];?>
<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('TIcheItemChecklist', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TIcheItemChecklist', 'element_name' => 'itens_checklist'), 'divupdate' => '.form-procurar')) ?>
        <div class='row-fluid inline'>    
            <?php echo $this->Buonny->input_codigo_cliente($this, 'iche_pjur_pess_oras_codigo', 'Cliente', 'Cliente', 'TIcheItemChecklist'); ?>
            <?php echo $this->BForm->input('iche_descricao', array('class' => 'input-xlarge', 'label' => 'Descrição',)); ?>
        </div>
        <div class='row-fluid inline'>
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        </div>
        <?php echo $this->BForm->end() ?>
    </div>    
</div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){
         '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "itens_checklist/listagem/" + Math.random());':'').'

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TIcheItemChecklist/element_name:itens_checklist/" + Math.random())
           
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