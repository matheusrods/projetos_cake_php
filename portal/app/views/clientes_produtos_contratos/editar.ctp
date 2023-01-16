<div id="editar-contrato">
<?php echo $bajax->form('ClienteProdutoContrato'); ?>
<?php echo $this->element('clientes_produtos_contratos/fields', array('edit_mode' => true)); ?>
</div>

<div id="visualizar-contrato" style="display:none"></div>
<div class="evt-voltar-contrato form-actions" style="display:none">
    <a href="#" class="btn">Voltar</a>
</div>