<div class='well' id="dados_matriz_unidade">
  <div class='row-fluid inline'>
    <h5>Empresa</h5>
      <?php echo $this->Html->tag('span', '<strong>Código: </strong>'.$this->data['Matriz']['codigo']); ?>
      <?php echo $this->Html->tag('span', '<strong>Razão Social: </strong>'.$this->data['Matriz']['razao_social']); ?>
      <?php echo $this->Html->tag('span', '<strong>Nome Fantasia: </strong>'.$this->data['Matriz']['nome_fantasia']); ?>
  </div>
  <?php if(!empty($this->data['Unidade'])): ?>
  <hr style="border:1px solid #ccc; margin:10px 0 0;"/>
    <div class='row-fluid inline'>
      <h5>Unidade</h5>
      <?php echo $this->Html->tag('span', '<strong>Código: </strong>'.$this->data['Unidade']['codigo']); ?>
      <?php echo $this->Html->tag('span', '<strong>Razão Social: </strong>'.$this->data['Unidade']['razao_social']); ?>
      <?php echo $this->Html->tag('span', '<strong>Nome Fantasia: </strong>'.$this->data['Unidade']['nome_fantasia']); ?>
    </div>
  <?php endif;?>
</div>