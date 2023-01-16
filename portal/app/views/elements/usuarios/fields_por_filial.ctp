<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->hidden('codigo_filial'); ?>
    <?php echo $this->BForm->input('apelido', array('class' => 'input-small', 'label' => 'Login')); ?>
    <?php echo $this->BForm->input('nome', array('class' => 'input-large', 'label' => 'Nome')); ?>
    <?php if ($this->action == 'incluir_por_filial'): ?>
        <?php echo $this->BForm->input('senha', array('class' => 'input-small', 'label' => 'Senha', 'readonly' => true)); ?>
    <?php else: ?>
        <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => 'Status', 'options' => array('inativo', 'ativo'))); ?>
        <?php echo $this->BForm->input('senha', array('class' => 'input-small', 'readonly' => true, 'value'=>'', 'label' => 'Gerar Senha', 'style'=>'cursor:pointer;position:relative;')); ?>
        <div class='control-group input text'>
            <label>&nbsp;</label>
            <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-refresh novaSenha', 'title' => 'Alterar Senha', 'style' => 'margin-top:7px;')); ?>
        </div>
    <?php endif ?> 
    <?php echo $this->BForm->input('codigo_uperfil', array('empty' => 'Selecione ' , 'class' => 'input-large', 'label' => 'Perfil', 'options' => $perfis, 'default' => 21)) ?>   
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('email', array('class' => 'input-large', 'label' => 'Email')); ?>
    <?php echo $this->BForm->input('celular', array('class' => 'input-large telefone', 'maxlength'=>'14','label' => 'Celular')); ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
    });', false);
?>