<div class='well'>
    <?php echo $bajax->form('Funcionario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Funcionario', 'element_name' => 'funcionarios_percapita', 'codigo_cliente' => $this->data['Cliente']['codigo']), 'divupdate' => '.form-procurar')) ?>

        <h5><?= $this->Html->link((!empty($this->data['Cliente']['codigo']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>

        <div id='filtros'>

			<?php echo $this->element('funcionarios/fields_filtros_percapita') ?>

	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        </div>

        <?php echo $this->BForm->hidden('bt_filtro', array('value' => '')); ?>

    <?php echo $this->BForm->end() ?>
</div>
    <div class='well'>
        <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['codigo']); ?>
        <strong>Cliente: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['razao_social']); ?>
        <strong>Período: </strong><?php echo $this->Html->tag('span', $dt_inicio. ' - ' .$dt_fim); ?>
        
        <?php echo $this->BForm->hidden('Funcionario.codigo_cliente', array('value' => $this->data['Cliente']['codigo'])); ?>
    </div>
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
          var codigo_cliente = $("#FuncionarioCodigoCliente").val();
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:Funcionario/element_name:funcionarios_percapita/codigo_cliente:" + codigo_cliente + "/" + Math.random())
        });
        
        function atualizaLista() {

            var codigo_cliente = $("#FuncionarioCodigoCliente").val();
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "funcionarios/listagem_percapita/" + codigo_cliente + "/" + Math.random());
        }

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        
    });', false);
?>
<?php if (!empty($this->data['Cliente']['codigo'])): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>
