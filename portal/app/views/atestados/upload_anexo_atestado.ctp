<?php echo $this->BForm->create('Atestado',array('url' => array('controller' => 'atestados', 'action' => 'upload_anexo_atestado', $codigo_atestado), 'enctype' => 'multipart/form-data')); ?>
 <div class="modal-header" style="text-align: center;">
            <h3>Upload Arquivo Atestado</h3>
        </div>
<?php echo $this->element('atestados/anexo', array('edit_mode' => true)); ?>

<div class="modal-footer">
    <div class="right">
        <a href="javascript:void(0);" onclick="anexo_atestado(<?php echo $codigo_atestado; ?>, 0);" class="btn btn-danger">FECHAR</a>
        <a href="javascript:void(0);" onclick="salvar_anexo(<?php echo  $codigo_atestado; ?>);" class="btn btn-success">SALVAR</a>          
    </div>
</div>
<?php echo $this->BForm->end(); ?>
