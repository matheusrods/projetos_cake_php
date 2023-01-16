<div class='well'>
    <?php echo $bajax->form('ViagemFaturamentoTotal', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ViagemFaturamentoTotal', 'element_name' => 'viagens_faturamento_total'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->Buonny->input_codigo_cliente($this, 'pagador', 'Pagador', false, 'ViagemFaturamentoTotal') ?>
        <?php echo $this->BForm->input('mes_faturamento', array('label' => false, 'placeholder' => 'Mês', 'class' => 'input-medium', 'options' => $meses, 'title' => 'Mês de Faturamento')) ?>
        <?php echo $this->BForm->input('ano_faturamento', array('label' => false, 'placeholder' => 'Ano','class' => 'input-mini numeric', 'options' => $anos, 'title' => 'Ano de Faturamento')) ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end();?>
</div>
<?php if($isPost): ?>
    <?php echo $this->Javascript->codeBlock('
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "viagens_faturamento/listagem/" + Math.random());
    '); ?>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock('
    jQuery("#limpar-filtro").click(function(){
        bloquearDiv(jQuery(".form-procurar"));
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ViagemFaturamentoTotal/element_name:viagens_faturamento_total/" + Math.random())
    });
'); ?>