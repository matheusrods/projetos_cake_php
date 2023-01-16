<legend>Critérios</legend>
<div class="row-fluid">
  <?php $indice = 0; ?>
  <?php $qtd = count($criterios); ?>
  <?php $metade = ceil($qtd/2); ?>
  <?php foreach ($criterios as $codigo_criterio => $criterio): ?>
    <?php if($indice == 0): ?>
      <div class="span6">
    <?php endif; ?>           
    <div class="criterio">
      <?php echo '<h5>'.$criterio['descricao'].'</h5>'.
      (!empty($criterio['opcional']) ? '' : ' <span class="text-error obrigatorio">*</span>').
      (!empty($this->data['FichaStatusCriterio'][$codigo_criterio]['automatico']) ? '<span class="text-warning automatico">Preenchido automaticamente</span>' : ''); ?>
      <p><?php echo isset($this->data['FichaStatusCriterio'][$codigo_criterio]) ? @$criterio['StatusCriterio'][$this->data['FichaStatusCriterio'][$codigo_criterio]['codigo_status_criterio']] : ''; ?></p>
      <?php if (!empty($this->data['FichaStatusCriterio'][$codigo_criterio]['observacao'])): ?>
        <p><?php echo $this->data['FichaStatusCriterio'][$codigo_criterio]['observacao']; ?></p>        
      <?php endif; ?>
    </div>
    <hr>
    <?php $indice++; ?>     
    <?php if($metade == $indice): ?>
      </div>
      <div class="span6">
    <?php elseif($qtd == $indice): ?>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div> <!-- Fecha  row-fluid -->