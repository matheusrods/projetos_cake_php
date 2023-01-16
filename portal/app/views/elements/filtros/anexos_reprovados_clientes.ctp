<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'anexos_reprovados_clientes'), 'divupdate' => '.form-procurar')) ?>

            <div class="row-fluid inline">
                <?php if($_SESSION['Auth']['Usuario']['codigo_fornecedor']): ?>
                    <?php echo $this->BForm->input('codigo_fornecedor', array('class' => 'input-mini', 'label' => 'Código', 'type' => 'text','readonly' => 'readonly', 'value' => "{$_SESSION['Auth']['Usuario']['codigo_fornecedor']}")); ?>
                    <?php echo $this->BForm->input('nome_fornecedor', array('class' => 'input-xlarge', 'label' => 'Fornecedor', 'value' => $nome_fornecedor, 'type' => 'text','readonly' => true));  ?>
                <?php else: ?>
                    <div class="row-fluid inline">
                    <?= $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Fornecedor',null); ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            
            <?php if(!$_SESSION['Auth']['Usuario']['codigo_fornecedor']){
                echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); 
            }
            ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaCliente();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:anexos_reprovados_clientes/" + Math.random())
        });
            
        function atualizaListaCliente() {     
            var div = jQuery("div.lista");
            var codigo_fornecedor = $("#ClienteCodigoFornecedor").val();
            codigo_fornecedor == "" ? codigo_fornecedor = null : codigo_fornecedor;
            
            console.log(codigo_fornecedor);
            bloquearDiv(div);
            div.load(baseUrl + "anexos/listagem_reprovados/" + codigo_fornecedor + "/" +Math.random()); 
        }
    });', false);
?>
