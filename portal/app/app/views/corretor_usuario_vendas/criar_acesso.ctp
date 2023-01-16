<?php echo $bajax->form('CorretorUsuarioVendas', array('url' => array('action' => 'criar_acesso', $dados['Corretora']['codigo']))); ?>
<?php echo $this->BForm->input('codigo', array('type' => 'hidden', 'value' => $dados['Corretora']['codigo']))?>

<?php echo $this->BForm->input('nome', array('label' => 'Nome Corretora', 'value' => $dados['Corretora']['nome']))?>
<?php echo $this->BForm->input('user', array('label' => 'Nome Usuário', 'value' => $dados['Corretora']['nome']))?>
<?php echo $this->BForm->input('email', array('label' => 'E-mail (login)', 'value' => ''))?>

<div class="fullwide submit_box">
   <input type="submit" value="Enviar"></input>
</div>

<div class="clear"></div>

<?php echo $this->BForm->end() ?>