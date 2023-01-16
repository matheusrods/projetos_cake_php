<?php echo $this->BForm->create('Usuario', array('action' => 'minhas_configuracoes')) ?>
    <div class="row-fluid">
    	<?php echo $this->BForm->hidden('codigo'); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('email', array('class' => 'input-large', 'label' => 'Email')); ?>
        <?php echo $this->BForm->input('celular', array('class' => 'input-large telefone', 'maxlength'=>'14','label' => 'Celular')); ?>
    </div>
    
    <?php echo $this->element('usuarios/fields_configurar_tipos_alerta'); ?>

    <?php if(isset( $usuario['Usuario']['codigo_tipo_perfil']) &&  $usuario['Usuario']['codigo_tipo_perfil'] === TipoPerfil::INTERNO ): ?>
    	<h4>Ponto eletrônico</h4>
        <div class="row-fluid">
            <?php echo $this->BForm->input('cracha', array('class' => 'input-large', 'label' => 'Crachá', 'readonly'=>(!empty($this->data['Usuario']['cracha']) ? 'readonly' : ''))); ?>
            <?php if(!empty($this->data['Usuario']['digital'])): ?>
            	<span>Digital já cadastrada</span>
            <?php else: ?>
          		<?php echo $this->Html->link('Cadastrar digital', array('controller'=>'usuarios', 'action'=>'cadastrar_digital'), array('onclick'=>'return open_popup(this, 270, 300);'))?>
          	<?php endif; ?>            
        </div>
    <?php endif; ?>
    <div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    </div>    
<?php echo $this->BForm->end() ?>
<?php $this->addScript($this->Javascript->codeBlock("
    jQuery(document).ready(function() {
        setup_mascaras();
    }); 
")) ;
?>