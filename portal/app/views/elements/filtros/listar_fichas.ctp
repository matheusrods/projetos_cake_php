<?php $exibirFiltroCliente = !isset($_SESSION['Auth']['Usuario']['codigo_cliente']) || empty($_SESSION['Auth']['Usuario']['codigo_cliente']); ?>

<div class="well">
    <?php echo $bajax->form('Ficha', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Ficha', 'element_name' => 'listar_fichas'), 'divupdate' => '.form-procurar')); ?>
    <?php //echo $this->BForm->create('Ficha'); ?>
        <div class="row-fluid inline">
            
            <?php //echo $this->Buonny->input_cliente_tipo($this, 0, $clientes_tipos); ?>
            
            <?php if ($exibirFiltroCliente): ?>
                <?php echo $this->BForm->input('codigo_cliente', array('label' => false, 'placeholder' => 'Código cliente','class' => 'input-small', 'type' => (empty($authUsuario['Usuario']['codigo_cliente'])) ? 'text' : 'hidden')); ?>
            <?php endif; ?>


            <?php /* echo $this->BForm->input('produto_de', array('label' => false, 'empty' => 'Produto de:', 'class' => 'input-large', 'options' => $produtos_cliente)); ?>
            <?php echo $this->BForm->input('produto_para', array('label' => false, 'empty' => 'Produto para:', 'class' => 'input-large', 'options' => $produtos_cliente)); ?>
            <?php
            
            Filtros de data
            <?php echo $this->BForm->input('data_validade_inicio', array('label' => false, 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
            <?php echo $this->BForm->input('data_validade_fim', array('label' => false, 'placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>
            */
            ?>
        </div>
            
        <div class="control-group">
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        </div>
    <?php echo $this->BForm->end(); ?>
</div>

<?php
$blocoScript = "
    $(document).ready(function() {
        atualizaListaFichasAlterarProduto();

        $('#FichaCodigoCliente').change(function(e) {
            if (e.keyCode == 13) {
                $(this).blur()
                return false
            }

            var codigo = $(this).val();
            
        }).change()
        
        $('#limpar-filtro').click(function(){
            bloquearDiv(jQuery('.form-procurar'));
            jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:Ficha/element_name:alterar_produto/' + Math.random())
        });

        
    });
";
echo $this->Javascript->codeBlock($blocoScript, false);
?>