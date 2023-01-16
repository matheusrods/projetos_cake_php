<div class='well'>
	<h5><?= $this->Html->link((!empty($this->data['RiscoExameAplicados']['codigo_cliente']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	
    <div id='filtros'>
		
	<?php echo $bajax->form('RiscoExameAplicados', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RiscoExameAplicados', 'element_name' => 'riscos_exames_aplicados'), 'divupdate' => '.form-procurar')) ?>
		
    <?php echo $this->Buonny->input_grupo_economico($this, 'RiscoExameAplicados', $unidades, $setores, $cargos,null); ?>

    <div class="row-fluid inline">
        <div id='tipo'>
            <?php echo $this->BForm->input('tipos', array('options' => $tipos, 'class' => 'input-medium', 'label' => 'Tipo')); ?>
            <?php echo $this->BForm->input('tomadores', array('options' => $tomadores, 'empty' => 'Selecione o tomador', 'class' => 'input-medium', 'label' => 'Tomador')); ?>
        </div>
    </div>

    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-aplicados', 'class' => 'btn')) ;?>
    </div> 
 <?php echo $this->BForm->end() ?>
</div> 
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListagem("riscos_exames/listagem_aplicados/");
        jQuery("#limpar-filtro-aplicados").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RiscoExameAplicados/element_name:riscos_exames_aplicados/" + Math.random())
        });
    });', false);
?>