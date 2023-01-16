<div class = 'form-procurar'>   
  <?= $this->element('/filtros/permissao') ?> 
</div> 

  <?php if(!empty($msg_erro)) {  
    echo "<div class='alert alert-error'>".$msg_erro."</div>"; 
  } ?> 

<div class='lista'></div>

