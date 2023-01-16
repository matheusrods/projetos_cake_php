<?php if( !empty($codigo_sm) ): ?>   
    <div class='well'>
            <?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'Viagens', 'action' => 'inserir_fotos_checklist'))) ?>
            <?php echo $this->BForm->hidden('viag_codigo') ?>
            <?php echo $this->BForm->hidden('viag_codigo_sm') ?>
        <?php if(empty($readonly) || $readonly == false): ?>
             <?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small', 'label' => 'SM', 'readonly' => true, 'value' => $codigo_sm)) ?>
            <div class='actionbar-left'>
                <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir novas Fotos', '', array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir fotos ao processo', 'onclick'=>'javascript: inclui_fotos(event)')); ?>
            </div>
        <?php endif; ?>
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
                <?php foreach ($resultado as $dado): ?>       
                <?php $ext  = end(explode('.', $dado['TFcveFotosChecklistVeiculo']['fcve_diretorio'])); ?>
                    <div class="thumbs-processo">
                        <a class="fancybox" href="<?php echo '/portal/app/webroot/files/imagens_viagens/'.$dado['TFcveFotosChecklistVeiculo']['fcve_diretorio'] ?>" title="<?= $dado['TFcveFotosChecklistVeiculo']['fcve_diretorio'] ?>" data-fancybox-group="gallery">
                            <div id="thumb" style="background: #FEFEFE;text-align:center">
                                <img src="/portal/app/webroot/files/imagens_viagens/thumbs/<?php echo $dado['TFcveFotosChecklistVeiculo']['fcve_diretorio'] ?>" />
                            </div>
                        </a>
                        <div id="row-fluid-inline">
                            <?php echo $this->Html->link('<i class="icon-trash icon-black"></i>', 'javascript:void(0)', array('escape' => false, 'class'=> "btn btn-small acoes-dtr", 'title' => 'Excluir', 'onclick' => "excluir(".$dado['TFcveFotosChecklistVeiculo']['fcve_viag_codigo_sm'].",".$dado['TFcveFotosChecklistVeiculo']['fcve_codigo'].")")); ?>
                            <?php echo $this->Html->link('<i class="icon-repeat icon-black"></i>', 'javascript:void(0)', array('escape' => false, 'class'=> "btn btn-small acoes-dtr", 'title' => 'Girar', 'onclick'=>"girar_foto(".$dado['TFcveFotosChecklistVeiculo']['fcve_viag_codigo_sm'].",".$dado['TFcveFotosChecklistVeiculo']['fcve_codigo'].",".'90'.")")); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="width:10px; height:50px; clear:both;"></div>
            <div class="form-actions">
                <a class="btn btn-danger delete" onclick="excluir(<?= $dado['TFcveFotosChecklistVeiculo']['fcve_viag_codigo_sm'] ?>, <?= -1 ?>)" href="#">
                    Excluir todas as imagens
                </a>  
            </div>


        <?php else: ?>
            <?php if( empty($resultado) ): ?>
                <div class="alert"><strong>Nenhum registro encontrado</strong></div>
            <?php else: ?>
                <div class="alert"><strong>Nenhuma foto encontrada para essa SM!</strong></div>
            <?php endif; ?>
        <?php endif;?>


        <?php $this->addScript($this->Buonny->link_js('fancybox/jquery.fancybox.js')) ?>     
        <?php $this->addScript($this->Buonny->link_css('fancybox/jquery.fancybox.css')) ?>
        <?php $this->addScript($this->Buonny->link_js('anystretch/jquery.anystretch.min.js'))?>
        <?php $this->addScript($this->Buonny->link_js('anystretch/jquery.anystretch.js'))?>
        <?php
            echo $this->Javascript->codeBlock('
                function inclui_fotos(event) {
                   event.preventDefault();
                    var newwindow = window.open("/portal/viagens/inserir_fotos/'.$codigo_sm.'", "_blank", "top=0,left=0,width=600,height=600,scrollbars=yes");
                    if (window.focus){
                        newwindow.focus();
                    }            
                }

                 function girar_foto(codigo_viagem, codigo_imagem, rotacao) {
                    location.href = "/portal/viagens/girar_foto" + "/" + codigo_viagem + "/" + codigo_imagem + "/" + rotacao + "/";
                }

                $(document).ready(function(){
                    $(".uplodify").css("display","none");
                    $(".btn-success").click(function(event){
                    });
                    $(".fancybox").fancybox();

                });
                function excluir(codigo_viagem, codigo_imagem) {
                    if (confirm("Deseja realmente excluir?")){
                            location.href = "/portal/viagens/excluir_foto" + "/" + codigo_viagem + "/" + codigo_imagem + "/";
                    }
                }
            ');
        ?>

<?php else: ?>
    <div class="alert"><strong>È necessario um codigo de SM</strong></div>
<?php endif; ?>