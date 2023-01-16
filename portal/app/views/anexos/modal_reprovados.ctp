
<?php echo $this->BForm->create('AnexoExame',array('url' => array('controller' => 'anexos', 'action' => 'upload_exame', $codigo_item_pedido_exame, $codigo_exame, $codigo_status_auditoria_imagem, $codigo_cliente, $codigo_pedido_exame), 'enctype' => 'multipart/form-data', "target"=>"cancel2")); ?>
    <iframe style="display:none;" name="cancel2"></iframe>

    <b>Upload do Exame</b>

    <?php            
        $arquivo_app = '';
        if(strstr($caminho_arquivo,'https://api.rhhealth.com.br')) {
            $arquivo_app = $caminho_arquivo;
        }
        else if(strstr($caminho_arquivo,'http://api.rhhealth.com.br')) {
            $arquivo_app = $caminho_arquivo;
        }
    ?>

    <div>
        <?php if(!empty($arquivo_app)): ?>

            <div style="display: inline-flex;">
               
                <?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $arquivo_app, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) ?>
                
            </div>
        
        <?php endif; ?>
    </div>

    <?php echo $this->BForm->input('anexo_exame', array('type'=>'file', 'label' => false)); ?>

    <?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'type'=>'reset', 'label' => 'Limpar', 'id' => 'LimparArquivoExame', 'class' => 'btn btn-anexos')); ?>
   
<?php echo $this->BForm->end(); //fim do form exame 
?>

<?php 

$Configuracao = &ClassRegistry::init('Configuracao');
if ($codigo_exame == $Configuracao->getChave('INSERE_EXAME_CLINICO')) : 
    echo $this->BForm->create('AnexoFichaClinica',array('url' => array('controller' => 'anexos', 'action' => 'upload_ficha_clinica', $codigo_item_pedido_exame, $codigo_ficha_clinica), 'enctype' => 'multipart/form-data', "target"=>"cancel3")); ?>
    <iframe style="display:none;" name="cancel3"></iframe>

    <b>Upload do Ficha Clínica</b>
    
    <?php            
        $arquivo_app = $caminho_ficha_clinica;    
    ?>

    <div>
        <?php if(!empty($arquivo_app)): ?>

            <div style="display: inline-flex;">
               
                <?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), '/files/anexos_exames/'.$arquivo_app, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) ?>
                
            </div>
        
        <?php endif; ?>
    </div>

    <?php echo $this->BForm->input('ficha_clinica', array('type'=>'file', 'label' => false)); ?>

    <?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'type'=>'reset', 'label' => 'Limpar', 'id' => 'LimparArquivoExame', 'class' => 'btn btn-anexos')); ?>
   
<?php echo $this->BForm->end(); 
 endif; //Fim do form ficha clinica
 ?>