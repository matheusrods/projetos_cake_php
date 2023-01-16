<style type="text/css">
    body{
        margin: 0 !important;
        padding: 0 !important;
    }
    #fotos{ margin-top: 25px; margin-bottom: 50px; }
    .thumbs-processo{
        border: 2px solid #DDDDDD;
        border-radius: 5px 5px 5px 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.055);            
        float: left;
        margin-right: 10px;
        margin-left: 10px;
        margin-top: 10px;
        margin-bottom: 10px;
        background-color: #EEEEEE;
        padding: 0;
    }            
    #title-photo{ min-height: 37px; 
                  padding: 5px;
                  color: #666666; }
    #thumb{ width:150px; 
            height:150px; 
            margin-right: 5px;
            margin-left: 5px;
            margin-top: 5px;
            margin-bottom: 5px;}
    #acoes-dtr{margin-top: 20;}
</style>
<div id='divFotos'>
<?php if( (!empty($codigo_checklist)) && ($fotos_checklist_entrada!==false) ): ?>   
    <?php echo $this->BForm->create('TVcenViagemChecklistEntrada', array('autocomplete' => 'off', 'url' => array('controller' => 'Viagens', 'action' => 'inserir_fotos_checklist'))) ?>
    <?php if(empty($readonly) || $readonly == false): ?>
        <div class='well'>
            <?php echo $this->element('viagens/fields_fotos_checklist_entrada') ?>        
        </div>
    <?php endif; ?>
    <?php if( !empty($fotos_checklist_entrada) ): ?>   
        <?php echo $this->element('viagens/preview_fotos_checklist_entrada') ?>        
    <?php else: ?>
        <div class="alert"><strong>Nenhuma foto encontrada para este checklist!</strong></div>
    <?php endif;?>

    <?php $this->addScript($this->Buonny->link_js('fancybox/jquery.fancybox.js')) ?>     
    <?php $this->addScript($this->Buonny->link_css('fancybox/jquery.fancybox.css')) ?>
    <?php $this->addScript($this->Buonny->link_js('anystretch/jquery.anystretch.min.js'))?>
    <?php $this->addScript($this->Buonny->link_js('anystretch/jquery.anystretch.js'))?>
    <?php
        echo $this->Javascript->codeBlock('

            var close_window = true;
            $(document).ready(function(){
                $(".uplodify").css("display","none");
                $(".btn-success").click(function(event){
                });
                $(".fancybox").fancybox();

                $( window ).unload(function() {
                    close_window = '.(empty($readonly) || $readonly == false ? 'close_window' : 'false').';
                    if (close_window) {
                        opener.location.href = opener.location.href;
                    }
                });

            });

        ');
    ?>
</div>

<?php else: ?>
    <div class="alert"><strong>Checklist não informado ou inválido</strong></div>
<?php endif; ?>