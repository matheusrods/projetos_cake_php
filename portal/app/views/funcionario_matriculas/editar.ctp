<?php echo $this->BForm->create('Funcionario', array('url' => array('controller' => 'funcionarios', 'action' => 'editar', $codigo_cliente, $this->data['Funcionario']['codigo'], $referencia), 'type' => 'post')); ?>
<?php echo $this->element('funcionarios/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>

<!-- Formulário de cadastro de usuario -->
<div class="modal fade" tabindex="-1" role="dialog" id="novoUsuario">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Criar Novo Usuário</h4>
      </div>
      <div class="modal-body">
        <p>Informe o usuário e senha de acesso do funcionário.</p>
        <p>O campo Apelido não poderá mais ser alterado.</p>
        <div class="row-fluid inline">
	        <?php
	        echo $this->BForm->create('Usuario', array('url' => '#', 'id' => 'FormCriaUsuario'));
	        echo $this->BForm->input('apelido', array('class'));
	        echo $this->BForm->input('senha', array('type' => 'password'));
	        echo $this->BForm->end();
	        ?>
        </div>
        <div class="row-fluid inline" id="NovoUsuarioMessages">
        	
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="Usuario.save();" id="BtnSaveUsuario" data-loading-text="Aguarde...">Salvar</button>
      </div>
    </div>
  </div>
</div>

<?php
$javascript->link('funcionarios/editar.js', false);