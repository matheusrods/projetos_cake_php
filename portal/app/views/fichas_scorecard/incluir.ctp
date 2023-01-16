<?php echo $this->Buonny->link_css('fichas_scorecard'); ?>
<?php echo $this->element('fichas_scorecard/formulario_ficha'); ?>
<?php echo $this->Javascript->codeBlock('
   jQuery(document).ready(function(){
   setup_datepicker();
   setup_codigo_cliente();
   preenche_cidade_inline();
   });', false);
?> 