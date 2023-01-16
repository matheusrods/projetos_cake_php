<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("
            close_dialog();
            var div_apontamento = jQuery('#historicoapontamentos');
            bloquearDiv(div_apontamento);
            div_apontamento.load(baseUrl + '/fichas_scorecard_art_criminais/listar_por_profissional/{$this->passedArgs['0']}/{$this->passedArgs['1']}');");
        exit;
    }
?>
<?php echo $this->Bajax->form('FichaScorecardArtCriminal', array('url' => array('controller' => 'fichas_scorecard_art_criminais', 'action' => 'editar', $this->passedArgs[0], $this->passedArgs[1], $this->passedArgs[2]))); ?>
  <?php echo $this->element('fichas_scorecard_art_criminais/fields'); ?>