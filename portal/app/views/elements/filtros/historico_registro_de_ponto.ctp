<?php $filtrado = $this->data['PontoEletronico']['filtrado']; ?>
<?php if(!empty($this->data['PontoEletronico']['codigo_usuario'])):
        $codigo_usuario = $this->data['PontoEletronico']['codigo_usuario'];
      else:
        $codigo_usuario = null;
      endif;  
?>
<div class='well'>
      <h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
      <div id='filtros'>
        <?php echo $this->BForm->create('PontoEletronico', array('url'=>array_merge(array('controller'=>$this->params['controller']), $this->passedArgs))) ?>
        <div class="row-fluid inline">
          <?php echo $this->BForm->input('codigo_usuario', array('label' => false, 'class' => 'input-medium', 'options' => $usuarios, 'empty' => 'Selecione o usuário')) ?>
          <?php echo $this->Buonny->input_periodo($this) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        
        <?php echo $this->BForm->end();?>
    </div>
  </div>
  <div class="well">
    <span class="pull-right">
      <?php if($filtrado):?>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => 'historico_exportar',base64_encode($this->data['PontoEletronico']['data_inicial']), base64_encode($this->data['PontoEletronico']['data_final']),$codigo_usuario), array('escape' => false, 'title' =>'Exportar para Excel'));?>
      <?php endif;?>
    </span>
</div>
<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){
        $.placeholder.shim();
        setup_mascaras();  
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "ponto_eletronico/historico_listagem/" + Math.random());':'').'
        
    });', false);
?>