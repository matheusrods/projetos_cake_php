<div class='well'>
    <?php echo $bajax->form('ViagemFaturamentoSubtotal', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ViagemFaturamentoSubtotal', 'element_name' => 'viagens_faturamento_subtotal'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->Buonny->input_codigo_cliente($this, 'pagador', 'Pagador', false, 'ViagemFaturamentoSubtotal') ?>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'embarcador', 'Embarcador', false, 'ViagemFaturamentoSubtotal') ?>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'transportador', 'Transportador', false, 'ViagemFaturamentoSubtotal') ?>
        <?php echo $this->BForm->input('mes_faturamento', array('label' => false, 'placeholder' => 'Mês', 'class' => 'input-medium', 'options' => $meses, 'title' => 'Mês de Faturamento')) ?>
        <?php echo $this->BForm->input('ano_faturamento', array('label' => false, 'placeholder' => 'Ano','class' => 'input-mini numeric', 'options' => $anos, 'title' => 'Ano de Faturamento')) ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end();?>
</div>
<?php $this->addScript($this->Buonny->link_js('search')) ?>
<?php if($isPost): ?>
    <?php echo $this->Javascript->codeBlock('
        $(document).ready(function(){
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "viagens_faturamento/embarcador_transportador_listagem/" + Math.random());
        });
    '); ?>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock('
    jQuery("#limpar-filtro").click(function(){
        bloquearDiv(jQuery(".form-procurar"));
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ViagemFaturamentoSubtotal/element_name:viagens_faturamento_subtotal/" + Math.random())
    });
'); ?>