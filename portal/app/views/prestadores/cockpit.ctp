<div class='well'>
    <?php echo $this->BForm->create('Seguradora', array('autocomplete' => 'off', 'url' => array('controller' => 'seguradoras', 'action' => 'cockpit'))) ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('ano', array('options' => $anos, 'class' => 'input-small', 'label' => false)); ?>
		<?php echo $this->BForm->input('codigo_seguradora', array('type' => 'select', 'options' => $seguradoras, 'class' => 'input-large', 'label' => false, 'empty' => 'Todas Seguradoras')); ?>
		<?php echo $this->Buonny->input_codigo_corretora($this,'codigo_corretora','Corretora',false,'Seguradora') ?>
    </div>

    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end();?>
</div>
<?php if ($gadgets): ?>
    <div class='well'>
        <?= (isset($seguradora['Seguradora']['codigo']) ? '<strong>Código: </strong>'.$seguradora['Seguradora']['codigo'] : '') ?>
        <?= (isset($seguradora['Seguradora']['nome'])  ? '<strong>Seguradora: </strong>'.$seguradora['Seguradora']['nome'] : '') ?>
        <?= (isset($corretora['Corretora']['codigo']) ? '<strong>Código: </strong>'.$corretora['Corretora']['codigo'] : '') ?>
        <?= (isset($corretora['Corretora']['nome'])  ? '<strong>Corretora: </strong>'.$corretora['Corretora']['nome'] : '') ?>
    </div>
    <?php $qtd_gg = 2 ?>
    <?php foreach($gadgets as $gadget): ?>
        <?php if ($qtd_gg == 2): ?>
            <div class='row-fluid'>
            <?php $qtd_gg = 0 ?>
        <?php endif ?>
        <?php $qtd_gg++ ?>
            <?php $div_id = rand() ?>
            <?php if (isset($gadget['titulo'])): ?>
                <div class='span6 window-gadget'>
                    <div class='alert alert-info'><?php echo $gadget['titulo'] ?></div>
                    <div id='<?= $div_id ?>'>
                    </div>
                </div>
                <?php $this->addScript($this->Javascript->codeBlock("carrega_gadget('{$gadget['titulo']}', '{$gadget['url']}', '{$hash}', '{$div_id}')")) ?>
            <?php endif ?>
        <?php if ($qtd_gg == 2): ?>
            </div>
        <?php endif ?>
    <?php endforeach ?>
    <?php if ($qtd_gg != 2): ?>
        </div>
    <?php endif ?>
<?php endif ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php echo $this->Javascript->codeBlock(
    "function carrega_gadget(titulo, url, hash, div_id) {
        var div = jQuery('#'+div_id);
        bloquearDiv(div);
        jQuery.ajax({
            type: 'POST',
            url: baseUrl + url + '/' + Math.random()
            ,data: \"data[Seguradora][hash]=\" + hash
            ,success: function(data) {
                div.html(data);
            }
            ,error: function (jqXHR, textStatus, errorThrow) {
                div.html(errorThrow);
            }
        });
    }")


?>