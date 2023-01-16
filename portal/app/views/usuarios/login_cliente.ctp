<link rel="stylesheet" href="/portal/css/sweetalert.css" />
<?php echo $this->Buonny->link_js('sweetalert.min'); ?>
<div class="container" id="login-cliente-container">
  <div class="alert-warning">
    <?php echo $this->Buonny->flash(); ?>
  </div>

  <div class="container" style="border-style: solid; border-color: #ddd; border-radius: 25px 25px 25px 25px; border-width: 1px; background-color: #fff; position: relative; width: 330px;">
    <div class="row">
      <?php echo $html->link('Entrar no sistema IT Health', array(
        'controller' => 'azuread',
        'action' => 'sso',
        $fonteAutenticacaoArr['Cliente']['codigo']
      ), array(
        'class' => 'btn btn-lg btn-primary btn-block icon-user',
        'style' => 'border-color: ' . $fonteAutenticacaoArr['ClienteFonteAutenticacao']['cor_botao'] . ' !important; background-color: ' . $fonteAutenticacaoArr['ClienteFonteAutenticacao']['cor_botao'] . ' !important;'
      )); ?>
    </div>
  </div>
</div>