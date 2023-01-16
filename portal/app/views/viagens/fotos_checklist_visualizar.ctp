<?php $this->addScript($this->Buonny->link_js('fancybox/jquery.fancybox.js')) ?>     
<?php $this->addScript($this->Buonny->link_css('fancybox/jquery.fancybox.css')) ?>
<?php if( !empty($codigo_sm) ): ?>   

    <div class="well">
        <strong>Codigo SM: </strong><?= $codigo_sm ?>
    </div>
            
    <?php if( !empty($resultado) ): ?>   
                        <style type="text/css">
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

          <div id='fotos'>
            <?php foreach ($resultado as $imagem): ?> 
                
                <?php $tipoarquivo = strtolower(substr($imagem['TFcveFotosChecklistVeiculo']['fcve_diretorio'],-3));?> 
                <div class="thumbs-processo">
                    <a class="fancybox"  href="<?php echo '/portal/app/webroot/files/imagens_viagens/'.$imagem['TFcveFotosChecklistVeiculo']['fcve_diretorio'] ?>" data-fancybox-group="gallery">
                        <div id="thumb" style="background: #FEFEFE;text-align:center">
                               <img src="<?php echo '/portal/app/webroot/files/imagens_viagens/thumbs/'.$imagem['TFcveFotosChecklistVeiculo']['fcve_diretorio'] ?>"/>
                        </div>
                    </a> 
                    <div id="row-fluid-inline">
                        <?php echo $this->Html->link('<i class="icon-repeat icon-black"></i>', 'javascript:void(0)', array('escape' => false, 'class'=> "btn btn-small acoes-dtr", 'title' => 'Girar', 'onclick'=>"girar_foto(".$imagem['TFcveFotosChecklistVeiculo']['fcve_viag_codigo_sm'].",".$imagem['TFcveFotosChecklistVeiculo']['fcve_codigo'].",".'90'.",".'1'.")")); ?>
                    </div>               

                </div>
                
            <?php endforeach; ?>
        </div>
        <?php
            echo $this->Javascript->codeBlock('

                function girar_foto(codigo_viagem, codigo_imagem, rotacao, visualizar) {
                    location.href = "/portal/viagens/girar_foto" + "/" + codigo_viagem + "/" + codigo_imagem + "/" + rotacao + "/" + visualizar + "/";
                }

                $(document).ready(function(){
                    $(".fancybox").fancybox();
                });
            ');
        ?>
    <?php else: ?>
        <div class="alert"><strong>Nenhuma foto foi encontrada para essa SM</strong></div>
    <?php endif; ?>
<?php else: ?>
    <div class="alert"><strong>È necessario um codigo de SM</strong></div>
<?php endif; ?>



