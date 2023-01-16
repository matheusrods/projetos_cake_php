<div class="well">

    <h5><?= $this->Html->link((!empty($this->data['Consulta']['codigo_cliente']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	
    <div id='filtros'>
        
        <?php echo $bajax->form('Consulta', array('autocomplete' => 'off', 'url' => array( 'controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Consulta', 'element_name' => 'pcmso_ppra_pendente_terceiros'), 'divupdate' => '.form-procurar')) ?>            
        
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_grupo_economico($this, 'Consulta', $unidades, $setores, $cargos); ?>
                <?php echo $this->BForm->input('pendencia', array('label' => 'Pendências', 'class' => 'input-large', 'options' => $status, 'empty' => 'Todos')); ?>
            </div>

            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>       
            
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>

        <?php echo $this->BForm->end() ?>
	</div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
		
	$(function(){

        atualizaLista();

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:Consulta/element_name:pcmso_ppra_pendente_terceiros/" + Math.random())
        });	
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "consultas/listagem_ppra_pcmso_pendente_terceiros/" + Math.random());
        }
        
    });', false);
?>
