<div class='well'>
    <?php echo $bajax->form('ViagemFaturamento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ViagemFaturamento', 'element_name' => 'viagens_faturamento'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->Buonny->input_codigo_cliente($this, 'pagador', 'Pagador', false, 'ViagemFaturamento') ?>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'embarcador', 'Embarcador', false, 'ViagemFaturamento') ?>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'transportador', 'Transportador', false, 'ViagemFaturamento') ?>
        <?php echo $this->BForm->input('mes_faturamento', array('label' => false, 'placeholder' => 'Mês', 'class' => 'input-medium', 'options' => $meses, 'title' => 'Mês de Faturamento')) ?>
        <?php echo $this->BForm->input('ano_faturamento', array('label' => false, 'placeholder' => 'Ano','class' => 'input-mini numeric', 'options' => $anos, 'title' => 'Ano de Faturamento')) ?>
        <?php echo $this->BForm->input('monitorada', array('label' => false, 'placeholder' => 'Tipo SM','class' => 'input-medium', 'options' => array(1 => 'Monitorada', 2 => 'Telemonitorada'), 'title' => 'Tipo SM', 'empty' => 'Selecione o Tipo SM')) ?>
        <?php echo $this->BForm->input('frota', array('label' => false, 'placeholder' => 'Frota','class' => 'input-medium', 'options' => array(1 => 'Sim', 2 => 'Não'), 'title' => 'Frota', 'empty' => 'Frota?')) ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end();?>
</div>
<?php if($isPost): ?>
    <?php echo $this->Javascript->codeBlock('
        $(document).ready(function(){
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "viagens_faturamento/listar_sms_listagem/" + Math.random());
        });
    '); ?>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock('
    jQuery("#limpar-filtro").click(function(){
        bloquearDiv(jQuery(".form-procurar"));
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ViagemFaturamento/element_name:viagens_faturamento/" + Math.random())
    });
'); ?>