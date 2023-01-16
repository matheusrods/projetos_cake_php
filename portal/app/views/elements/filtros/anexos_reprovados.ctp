<div class='well'>
    <?php echo $bajax->form('PedidoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PedidoExame', 'element_name' => 'anexos_reprovados', $codigo_fornecedor), 'divupdate' => '.form-procurar')) ?>
        
    
    <div class="row-fluid inline">
        <?php
        echo $this->BForm->input('codigo_fornecedor', array('type' => 'text', 'label' => 'Código', 'class' => 'input-mini', 'readonly' => 'readonly', 'value' => "{$codigo_fornecedor}"));
        echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Razão Social', 'readonly' => 'readonly', 'value' => "{$nome_fornecedor}"));
        ?>
    </div>

    <div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => $codigo_fornecedor )); ?>

<?php echo $this->BForm->input('codigo_pedido_exame', array('label' => 'Cód. do Pedido', 'class' => 'input-mini just-number', 'type' => 'text', 'style' => "width: 89px;" )); ?>

        <p><span class="label label-info">Período por:</span></p>
        <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
        <?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>        
   
    </div>

    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        setup_datepicker();
        setup_mascaras();
		
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PedidoExame/element_name:anexos_reprovados/'. $codigo_fornecedor .'/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "anexos/listagem_reprovados/'. $codigo_fornecedor .'/" + Math.random());
        }
           
    });', false);