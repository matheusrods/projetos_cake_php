<legend>Critérios</legend>
<div class="row-fluid" style="width: 95%; height:95%; align: center; overflow-x:hidden; overflow-y:auto">
  <?php $indice = 0; ?>
  <?php $qtd    = count($criterios); ?>
  <?php $metade = ceil($qtd/2); ?>
  <?php $bloqueia_campo = empty($disabled);?>
  <?php $forca_bloqueio_combo = FALSE;?>
  
  <?php foreach ($criterios as $codigo_criterio => $criterio): ?>
    <?php 
      if( $bloqueia_campo ){
        if( isset($criterio) && $criterio['criterio_bloqueado'] == 'S') {
          $disabled = TRUE;
          $criterio['opcional'] = TRUE;
        } else {
          $disabled = FALSE;  
        }
      }
    ?>
    <?php if($indice == 0): ?>
      <div class="span6">
    <?php endif; ?>   
    <?php 
       if (!isset($criterio['codigo_profissional_tipo2'])){
          $criterio['codigo_profissional_tipo2']='';
       }
    ?>
    <div class="criterio" id="<?=$codigo_criterio?>" >
      <?php echo $this->BForm->hidden('FichaStatusCriterio.'.$codigo_criterio.'.codigo_criterio', array('value'=>$codigo_criterio)); ?>
      <?php echo $this->BForm->input('FichaStatusCriterio.'.$codigo_criterio.'.opcional', array('value'=>$criterio['opcional'], 'class'=>'opcional', 'type'=>'hidden')); ?>
      <?php echo $this->BForm->input('FichaStatusCriterio.'.$codigo_criterio.'.automatico', array('type'=>'hidden')); ?>
      <?php 
        if($codigo_criterio==3 && $disabled == FALSE ){
           $label = $criterio['descricao']."<div id='serasa'><iframe id='myIframe' src='' width='100%' height='100%' scrolling='no'></iframe></div><a href='#' id='dialogBtn'>(Consulta Serasa)</a>";
        }elseif($codigo_criterio==23  && $disabled == FALSE ) { 
            $label = $criterio['descricao']."<div id='serasa0'><iframe id='myIframe0' src='' width='100%' height='100%' scrolling='no'></iframe></div><a href='#' id='dialogBtn0'>(Consulta Serasa)</a>";
        }elseif($codigo_criterio==26  && $disabled == FALSE){
            $label = $criterio['descricao']."<div id='serasa1'><iframe id='myIframe1' src='' width='100%' height='100%' scrolling='no'></iframe></div><a href='#' id='dialogBtn1'>(Consulta Serasa)</a>";
        }elseif($codigo_criterio==27){
          $label = $criterio['descricao']."<div id='serasa2'><iframe id='myIframe2' src='' width='100%' height='100%' scrolling='no'></iframe></div><a href='#' id='dialogBtn2'>(Consulta Serasa)</a>";
        } else {
           $label = $criterio['descricao'] ;
        }
        $label .= (!empty($criterio['opcional']) ? '' : ' <span class="text-error">*</span>');
        if( isset($this->data['FichaStatusCriterio'][$codigo_criterio]['automatico']) && $this->data['FichaStatusCriterio'][$codigo_criterio]['automatico'] ):
          $label .= ' <span class="text-warning">Preenchido automaticamente</span>';
          $dialog_extracao = null;
          if ( isset($this->data['FichaStatusCriterio'][$codigo_criterio]['observacao']) && trim($this->data['FichaStatusCriterio'][$codigo_criterio]['observacao']) != ''):
            $dialog_extracao = 'extracao-'.$codigo_criterio;                         
          endif;
        endif;
        if ( isset($this->data['FichaStatusCriterio'][$codigo_criterio]['observacao']) && trim($this->data['FichaStatusCriterio'][$codigo_criterio]['observacao']) != ''):
         @$label .= $this->Html->link(' <i class="icon-info-sign"></i>', 'javascript:void(0);', array('onclick'=>'jQuery(\'#'.$dialog_extracao.'\').dialog({width: 600});','escape'=>false));
        endif;  
        $status_criterios = $criterio['StatusCriterio'];      
        if( $codigo_criterio == Criterio::IDADE_PROFISSIONAL && $disabled == FALSE && !empty($this->data['FichaStatusCriterio'][$codigo_criterio]['automatico']) ){
          $disabled = TRUE;
          $forca_bloqueio_combo = TRUE;
          echo $this->BForm->input('FichaStatusCriterio.'.$codigo_criterio.'.codigo_status_criterio', array('type' => 'hidden', 'options' => $status_criterios));
          echo $this->BForm->input('FichaStatusCriterio.'.$codigo_criterio.'.observacao', array( 'type' => 'hidden')) ;           
        }
        if( $codigo_criterio == Criterio::IDADE_PROFISSIONAL && $disabled ){
          echo $this->BForm->input('FichaStatusCriterio.'.$codigo_criterio.'.codigo_status_criterio', array('type' => 'hidden', 'options' => $status_criterios));
        }
        echo $this->BForm->input('FichaStatusCriterio.'.$codigo_criterio.'.codigo_status_criterio', array('label' => $label, 'class' => 'input-xlarge status', 'type' => 'select', 'options' => $status_criterios,'for' => $codigo_criterio, 'empty' => 'SELECIONE', 'disabled'=> $disabled));
        if( $forca_bloqueio_combo )
          $disabled = FALSE;
        if ($criterio['aceita_texto'] && $disabled == FALSE) {
          echo $this->Html->link('Exibir observação', 'javascript:void(0)', array('class'=>'exibe-observacao')); 
          echo $this->BForm->input('FichaStatusCriterio.'.$codigo_criterio.'.observacao', array('maxlength' => 2048, 'rows'=>2,'class' => 'input-xxlarge observacao', 'placeholder' => 'Observação', 'label' =>false, 'type' => 'textarea' , 'div'=> 'control-group input textarea observacao-criterio' )) ; 
        }?>     
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <hr> 
    </div>
    <?php $indice++; ?>     
    <?php if($metade == $indice): ?>
      </div>
      <div class="span6">
    <?php elseif($qtd == $indice): ?>    
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>
<?php echo $this->Javascript->codeBlock('
  $(document).ready(function() {
    $("#serasa").dialog({
        autoOpen: false,
        modal: true,
        width:  900,
        height: 800,
        maxHeight:1020,
        open: function(ev, ui){
                 $(\'#myIframe\').attr(\'src\',\'http://tstportal.buonny.com.br/bcb/index/visualizar-relatorio/tipoPessoa/fisica\');
              }
    });

    $(\'#dialogBtn\').click(function(){
        $(\'#serasa\').dialog(\'open\');
    });
  });', false);?>


  <?php echo $this->Javascript->codeBlock('
  $(document).ready(function() {
    $("#serasa0").dialog({
        autoOpen: false,
        modal: true,
        width:  900,
        height: 800,
        maxHeight:1020,
        open: function(ev, ui){
                 $(\'#myIframe0\').attr(\'src\',\'http://tstportal.buonny.com.br/bcb/index/visualizar-relatorio/tipoPessoa/fisica\');
              }
    });

    $(\'#dialogBtn0\').click(function(){
        $(\'#serasa0\').dialog(\'open\');
    });
  });', false);?>

    <?php echo $this->Javascript->codeBlock('
  $(document).ready(function() {
    $("#serasa1").dialog({
        autoOpen: false,
        modal: true,
        width:  900,
        height: 800,
        maxHeight:1020,
        open: function(ev, ui){
                 $(\'#myIframe1\').attr(\'src\',\'http://tstportal.buonny.com.br/bcb/index/visualizar-relatorio/tipoPessoa/fisica\');
              }
    });

    $(\'#dialogBtn1\').click(function(){
        $(\'#serasa1\').dialog(\'open\');
    });
  });', false);?>

  <?php echo $this->Javascript->codeBlock('
  $(document).ready(function() {
    $("#serasa2").dialog({
        autoOpen: false,
        modal: true,
        width:  900,
        height: 800,
        maxHeight:1020,
        open: function(ev, ui){
                 $(\'#myIframe2\').attr(\'src\',\'http://tstportal.buonny.com.br/bcb/index/visualizar-relatorio/tipoPessoa/fisica\');
              }
    });

    $(\'#dialogBtn2\').click(function(){
        $(\'#serasa2\').dialog(\'open\');
    });
  });', false);?>