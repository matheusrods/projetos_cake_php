<?php
echo $ithealth->loadHelperJs(); // TODO: REMOVER - Quando estiver carregando no AppController


echo $ithealth->create('NotaFiscalServico', array(
        'url' => array(
            'controller' => 'notas_fiscais_servico', 
            'action' => 'visualizar', 
            $codigo
        ),
        'enctype'=>'multipart/form-data',
        // 'ajax_submit' => true,
        'callbackBeforeSend' => 'callbackBeforeSend',
        'callbackSuccess' => 'callbackSuccess',
        'callbackError' => 'callbackError',
        'callbackComplete' => 'callbackComplete'
    ));
?>
<?php echo $this->Form->hidden('codigo'); ?>
<?php echo $this->element('nota_fiscal_servico/fields', array('edit_mode' => false, 'readonly_mode' => true)); ?>
<?php echo $ithealth->formEnd(); ?>