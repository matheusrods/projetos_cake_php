<div class="usuarios_fields">
    <div class="row-fluid inline">
        <?php echo $this->BForm->hidden('codigo'); ?>
        <?php echo $this->BForm->hidden('codigo_cliente'); ?>
        <?php echo $this->BForm->hidden('codigo_corretora'); ?>
        <?php echo $this->BForm->hidden('codigo_seguradora'); ?>
        <?php echo $this->BForm->input('apelido', array('class' => 'input-small', 'label' => 'Login','readonly' => true)); ?>
        <?php echo $this->BForm->input('nome', array('class' => 'input-large', 'label' => 'Nome','readonly' => true)); ?>       
        <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'label' => 'CNPJ / CPF','readonly' => true)); ?>
        <?php echo $this->BForm->input('codigo_departamento', array('class' => 'input-medium', 'label' => 'Departamento', 'options' => $departamentos, 'empty' => 'Selecione')); ?>
        <?php echo $this->BForm->input('codigo_usuario_pai', array('class' => 'input-medium', 'label' => 'Superior imediato', 'options' => $usuario_pai, 'empty' => 'Selecione')); ?>        
     </div>
    <div class="row-fluid inline">
            <?php echo $this->BForm->input('email', array('class' => 'input-large', 'label' => 'E-mail','readonly' => true)); ?>
            <?php echo $this->BForm->input('email_alternativo', array('class' => 'input-large', 'label' => 'E-mails Alternativos','readonly' => true)); ?>
            <?php echo $this->BForm->input('celular', array('class' => 'input-large telefone', 'label' => 'Celular','readonly' => true)); ?>
    </div>   
    <h4>Ponto eletrônico</h4>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('cracha', array('class' => 'input-large', 'label' => 'Crachá', 'maxlength'=>'5')); ?>
	    <div class="control-group input">
			<label>&nbsp;</label>
            <?php echo $this->BForm->label('apagar_digital', $this->BForm->checkbox('apagar_digital').'Apagar digital', array('class' => 'checkbox inline input-medium', 'escape'=>false)); ?>
            <?php echo $this->BForm->label('gestor', $this->BForm->checkbox('gestor').'Gestor da área', array('class' => 'checkbox inline input-medium', 'escape'=>false)); ?>
    	</div>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('fuso_horario', array('class' => 'input-medium', 'label' => 'Fuso horário', 'options' => array('-2'=>'UTC -2', '-3'=>'UTC -3', '-4'=>'UTC -4'), 'empty' => 'Selecione')); ?>
    	<div class="control-group input">
    		<label>&nbsp;</label>
    		<?php echo $this->BForm->label('horario_verao', $this->BForm->checkbox('horario_verao').'Adota horário de verão', array('class' => 'checkbox inline input-xlarge', 'escape'=>false)); ?>
        </div>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('escala', array('type' => 'checkbox', 'label' => 'Possui escala', 'class' => 'checkbox-escala')); ?>
        <div class="row-fluid inline usuario-escala-expediente">
            <div class="row-fluid inline usuario-escala" style="display:<?php echo (empty($this->data['Usuario']['escala']) ? 'none':'')?>;">
                <?php echo $this->element('usuarios/carrega_usuario_escala'); ?>
            </div>
            <div class="row-fluid inline usuario-expediente" style="display:<?php echo (!empty($this->data['Usuario']['escala']) ? 'none':'')?>;">            
                <?php echo $this->element('usuarios/carrega_usuario_expediente'); ?>
            </div>
        </div>    
    </div>
    <div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
      <?= $html->link('Voltar', array('action' => 'configuracao'), array('class' => 'btn')); ?>
    </div>
    <?php echo $this->BForm->end(); ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        $(".checkbox-escala").change(function(){
            if( $(this).is(":checked")){
                $(".usuario-escala").show();
                $(".usuario-expediente").hide();                
            }else{
                $(".usuario-escala").hide();
                $(".usuario-expediente").show(); 
            }
        });
    });', false);?>