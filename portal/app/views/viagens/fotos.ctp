<div class='well'>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'Viagens', 'action' => 'inserir_fotos_checklist'))) ?>
        <?php echo $this->BForm->input('viag_codigo', array('class' => 'input-small', 'placeholder' => null, 'label' => 'Codigo viagem', 'type' => 'text')) ?>        
        <?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small', 'placeholder' => null, 'label' => 'Codigo SM', 'type' => 'text')) ?>        
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>
</div>

    <?php if(!isset($dados) && !empty($dados)): ?>
        <div class='actionbar-right'>
            <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array(''), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir fotos ao processo')); ?>
        </div>
    <?php endif;?>


        <?php if( !empty($dados) && $dados !== 'empty' ): ?>   
        <style type="text/css">
            #fotos{ margin-top: 25px; margin-bottom: 50px; }
            .thumbs-processo{
                width:158px; 
                height:195px;
                border: 2px solid #DDDDDD;
                border-radius: 4px 4px 4px 4px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.055);            
                float: left;
                margin-right: 5px;
                margin-bottom: 5px;
                background-color: #EEEEEE;
                padding: 0;
            }            
            .btn-danger{ display:block; width:170px; clear:both; }
            .form-actions{ clear: both; }
            #title-photo{ min-height: 37px; padding: 5px; color: #666666; }
            #thumb{ height:110px;}
            #acoes{ padding: 5px;}
            #acoes-dtr{ width:45px; margin: 0 auto; }
        </style>

        <div id='fotos'>
            <?php foreach ($dados as $dado): ?>       
                <div class="thumbs-processo">
                    <div id="title-photo" title="<?= $dado['ProcessoArquivo']['titulo'] ?>"><?= substr($dado['ProcessoArquivo']['titulo'], 0, 40); ?></div>
                    <a class="fancybox" href="<?php echo '/portal/app/webroot/files/imagens_viagens/'.$dado['ProcessoArquivo']['arquivo'] ?>" title="<?= $dado['ProcessoArquivo']['descricao'] ?>" data-fancybox-group="gallery">
                        <?php 
                            $type = array('jpg','jpeg','png','gif');
                            $ext  = end(explode('.', $dado['ProcessoArquivo']['arquivo']));
                            if( !in_array($ext, $type) ):
                        ?>
                            <div id="thumb" style="background: url(/portal/img/open-file.png) #FEFEFE no-repeat;"></div>
                        <?php else: ?>
                            <div id="thumb" style="background: #FEFEFE;text-align:center">
                                <img src="/portal/app/webroot/files/imagens_viagens/thumbs/<?php echo $dado['ProcessoArquivo']['arquivo'] ?>" />
                            </div>
                            
                        <?php endif; ?>
                    </a>
                    <div id="acoes">
                        <div id="acoes-dtr">
                            <a class="acoes1" title="Editar" href="/portal/viagens/editar_fotos/<?php echo $dado['ProcessoArquivo']['codigo']?>" onclick="return open_dialog(this, 'Editar Foto', 900)">
                                <i class="icon-edit icon-black"></i>
                            </a>
                            |
                            <a class="acoes2" title="Excluir" onclick="excluir_foto(<?= $dado['ProcessoArquivo']['num_processo'] ?>,<?= $dado['ProcessoArquivo']['ano_processo'] ?>,<?= $dado['ProcessoArquivo']['codigo'] ?>)" href="#">
                                <i class="icon-trash icon-black"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>              
        <div style="width:10px; height:50px; clear:both;"></div>
        <div class="form-actions">
            <a class="btn btn-danger delete" onclick="excluir_foto(<?= $dado['ProcessoArquivo']['num_processo'] ?>,<?= $dado['ProcessoArquivo']['ano_processo'] ?>)" href="#">
                Excluir todas as imagens
            </a>  
        </div>


    <?php else: ?>
        <?php if( empty($dados) ): ?>
            <div class="alert"><strong>Nenhum registro encontrado</strong></div>
        <?php else: ?>
            <div class="alert"><strong>Nenhuma foto encontrada para esse processo!</strong></div>
        <?php endif; ?>
    <?php endif;?>


    <?php $this->addScript($this->Buonny->link_js('processo')) ?>
    <?php $this->addScript($this->Buonny->link_js('fancybox/jquery.fancybox.js')) ?>     
    <?php $this->addScript($this->Buonny->link_css('fancybox/jquery.fancybox.css')) ?>
    <?php $this->addScript($this->Buonny->link_js('anystretch/jquery.anystretch.min.js'))?>
    <?php $this->addScript($this->Buonny->link_js('anystretch/jquery.anystretch.js'))?>
    <?php
        echo $this->Javascript->codeBlock('
            $(document).ready(function(){
                $(".uplodify").css("display","none");
                $(".btn-success").click(function(event){
                    event.preventDefault();
                    var new window = window.open("/portal/viagens/inserir_fotos/'.$this->data['TViagViagem']['viag_codigo_sm'].'/", "_blank", "top=0,left=0,width=600,height=600,scrollbars=yes");
                    if (window.focus){
                        newwindow.focus();
                    }
                });

                $(".fancybox").fancybox();

            });

            function excluir_foto(num_processo,ano_processo,codigo) {
                if (confirm("Deseja realmente excluir?")){
                    if( codigo == undefined  )
                        location.href = "/portal/viagens/excluir_foto" + "/" + num_processo + "/" + ano_processo ;
                    else
                        location.href = "/portal/viagens/excluir_foto" + "/" + num_processo + "/" + ano_processo + "/" + codigo ;
                }
            }
        ');
    ?>