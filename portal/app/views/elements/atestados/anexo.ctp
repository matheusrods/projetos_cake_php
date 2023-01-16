<div class='row-fluid inline'>
    <div class="modal-dialog modal-sm" style="position: static;">
    	<div class="modal-content">
           
            <div class="modal-body" id="modal-body" style="padding-top: 20px">
                <b>Escolha o arquivo para upload</b>
                <div class='row-fluid inline' style="min-height: 60px;">
                
        			<?php echo $this->BForm->input('anexo_atestado', array('type'=>'file', 'label' => false)); ?>
        			<?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoExame', 'class' => 'btn btn-anexos')); ?>

        			<?php $arquivo = end(glob(DIR_ANEXOS_ATESTADOS.$codigo_atestado.DS.'atestado_'.$codigo_atestado.'*')); ?>


    			</div>
    		</div>
             <?php if(!empty($this->data['Atestado']['anexo'])): ?>
                <div style="padding-left: 1%">
                    <?php 

                    if(strstr($this->data['Atestado']['anexo'],'https://api.rhhealth.com.br')) {

                        echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')).' Ver Arquivo', $this->data['Atestado']['anexo'], array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item'));

                    }
                    else {

                        echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')).' Ver Arquivo', '/files/anexos_atestados/'.$this->data['Atestado']['codigo'].'/'.basename($this->data['Atestado']['anexo']), array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item'));
                        
                    }
                    echo " | "; 
                    echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-remove')).' Remover Arquivo', array('controller' => 'atestados', 'action' => 'excluir_anexo', $this->data['Atestado']['codigo']), array('escape' => false, 'title' => 'Excluir Anexo'),'Confirma exclusão do anexo?');
                    ?>
                </div>
            <?php endif; ?> 
    	</div>
    </div>
</div>
<?php
    echo $this->Javascript->codeBlock('
        $("#LimparArquivoExame").click(function(){
            $("#AtestadoAnexoAtestado").val("");                
        });

        function salvar_anexo(codigo_atestado) {
            if( $("#AtestadoAnexoAtestado").val() == "" || $("#AtestadoAnexoAtestado").val() == undefined ){
                swal({
                    type: "warning",
                    title: "Atenção",
                    text: "Favor selecione um arquivo para salvar",
                });
            } else {
                var div = jQuery("#modal-body");
                bloquearDiv(div);
                $("#AtestadoUploadAnexoAtestadoForm").submit();
            }
        }

       
      
    ');
?>