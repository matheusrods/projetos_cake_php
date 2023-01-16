<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
    	<?php echo $bajax->form('Usuario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Usuario', 'element_name' => 'logserasa'), 'divupdate' => '.form-procurar')) ?>
            <div class="row-fluid inline">
                 <?php echo $this->BForm->input('codigo_documento',array('label' => 'CPF','type' => 'text','class' => 'input-medium cpf', 'placeholder' => 'CPF')) ?>
                 <?php echo $this->BForm->input('novaconsulta', array('class' => 'input-small', 'options' => array(1 => 'Sim', 2 => 'Não'), 'empty' => 'Ambas', 'label'=>'Nova Consulta')) ?>
            </div>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('data_inclusao_inicio', array('label' => 'De', 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
                <?php echo $this->BForm->input('data_inclusao_fim', array('label' => 'Até', 'placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>
                <?php echo $this->BForm->input('usuario', array('class' => 'input-xxlarge','label'=>'Usuário')) ?>
            </div>
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div> 
</div> 
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){ 
        atualizaListaLogSerasa(); 
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
    setup_datepicker();
    setup_mascaras();
    jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
    jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Usuario/element_name:logserasa/" + Math.random())
        });
        
    });', false); 
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>
   
           