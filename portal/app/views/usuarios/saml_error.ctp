<link rel="stylesheet" href="/portal/css/sweetalert.css" />
<?php echo $this->Buonny->link_js('sweetalert.min'); ?>
<div class="container" id="login-cliente-container">
  <div class="row">
    <h2>Falha ao autenticar</h2>
    <div class="alert-warning">
      <?php echo $this->Buonny->flash(); ?>
    </div>
    <?php echo $html->link('Voltar para login', '/', array('class' => 'btn btn-lg btn-primary btn-block icon-user', 'style' => 'width: 160px;')); ?>
  </div>
</div>