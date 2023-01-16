<?php if( !empty($ready) ): ?> 
    <html lang="en">
        <head>
        <meta charset="utf-8">
        <title>jQuery File Upload Demo</title>    
        <?php $this->addScript($this->Buonny->link_js('uploadify/jquery.uploadify')) ?>
        <?php $this->addScript($this->Buonny->link_css('uploadify/uploadify')) ?> 
        
        <body>
            
        <?php if( !empty($codigo_sm) ): ?>   
            <div class="well">
                <b>Codigo Sm: </b><?=$codigo_sm?>
                <?php $codigo = $codigo_sm; ?>
            </div>


            <div class="uplodify" id='uplodify'>
                <form>
                    <div id="queue"></div>
                    <input id="file_upload" name="file_upload" type="file" multiple="true">
                    <a class="btn btn-primary start" href="javascript:$('#file_upload').uploadify('upload', '*')">
                        <i class="icon-upload icon-white"></i>  Iniciar upload
                    </a>
                    <a class="btn btn-warning cancel" href="javascript:$('#file_upload').uploadify('cancel', '*')">
                        <i class="icon-ban-circle icon-white"></i>  Cancelar upload
                    </a>
                </form>
            </div> 
            <script type="text/javascript">
                    $(function() {
                        $("#file_upload").uploadify({
                             'swf'             : '/portal/img/uploadify.swf'
                            ,'uploader'        : '/portal/js/uploadify/uploadify.php?codigo_sm=<?=$codigo_sm;?>'
                            ,'auto'            : false
                            ,'buttonImage'     : '/portal/img/select-file-uploadify.jpg'
                            ,'width'           : 172
                            ,'height'          : 34
                            ,'fileTypeExts'    : '*.gif; *.jpg; *.jpeg; *.png; *.pdf'
                            ,'fileSizeLimit'   : '2MB'
                            ,'onQueueComplete' : function(queueData){
                                alert(queueData.uploadsSuccessful + ' Arquivos(s) adicionado(s)');
                                opener.location.href = "http://<?=$_SERVER['SERVER_NAME']?>/portal/viagens/fotos_checklist/<?=$codigo_sm;?>";
                                self.close();
                            }
                    
                        });
                    });
            </script>

        <?php else: ?>
            <div class="alert"><strong>SM não encontrada</strong></div>
        <?php endif; ?>
        </body>
    </html>
<?php else: ?>
    <?php if( !empty($codigo_sm) ): ?>   
        <div class="well">
            <b>Codigo Sm: </b><?=$codigo_sm?>
            <?php $codigo = $codigo_sm; ?>
        </div>
        <?php echo $this->BForm->create('FotosViagem', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'inserir_fotos/'.$codigo_sm))); ?>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('arquivo', array('name' => 'data[arquivo][]','type'=>'file', 'label' => false, 'multiple'=>true)); ?>
            </div>
            <?php echo $this->BForm->submit('Importar', array('div' => false, 'class' => 'btn btn-primary', 'id' => 'importar')); ?>
        <?php echo $this->BForm->end(); ?>
     <?php else: ?>
            <div class="alert"><strong>SM não encontrada</strong></div>
        <?php endif; ?>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        ".($processado ? "opener.location.reload(); window.close();" : "")."
    });
   
");
?>
<?php endif; ?>
