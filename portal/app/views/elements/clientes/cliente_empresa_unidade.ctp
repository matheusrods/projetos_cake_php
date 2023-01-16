<div class='well'>
  <div class='row-fluid inline'>
    <h5>Empresa</h5>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Matriz']['codigo']); ?>
    <strong>Razão Social: </strong><?php echo $this->Html->tag('span', $this->data['Matriz']['razao_social']); ?>
  </div>
  <hr style="border:1px solid #ccc; margin:10px 0 0;"/>
  <div class='row-fluid inline'>
    <h5>Unidade</h5>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Unidade']['codigo']); ?>
    <strong>Razão Social: </strong><?php echo $this->Html->tag('span', $this->data['Unidade']['razao_social']); ?>
  </div>
</div>