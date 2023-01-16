<?php if($session->read('Message.flash.params.type') == MSGT_SUCCESS):
        echo "<label style='font-size: 14px;padding-top: 15px;text-align: center;'>Cadastro efetuado com sucesso!<br />Obrigado.</label>";
    exit;
endif; ?>

<?php echo $bajax->form('ClienteOpFat',   array('url' => array('controller' => 'clientes', 'action' => 'opcao_fatura_email'))) ?>
<?php echo $this->BForm->hidden('link') ?>

<label style="float: left;font-size: 14px;padding: 15px;text-align: center;">Com o recebimento das informações de faturamento somente por email, você estará contribuindo para um mundo melhor!</label>
<img src='../img/pense_verde.jpg' border="0" alt="Pense Verde" />

<div class="form-actions">
    <?php echo $this->BForm->submit('Confirmar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php echo $html->link('Voltar', '#', array('class' => 'btn closeDialog', 'onclick' => 'close_dialog();')); ?>
</div>
<?php echo $this->BForm->end(); ?>