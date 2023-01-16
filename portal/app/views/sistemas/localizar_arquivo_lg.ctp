<div class='well'>
	<p>Este programa irá localizar arquivos dos diretorios de EDI LG baseados no conteúdo informado</p>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Sistema', array('url' => array('controller' => 'sistemas', 'action' => 'localizar_arquivo_lg'))) ?>
            <?php echo $this->BForm->input('content', array('class' => 'input-xxlarge', 'placeholder' => 'Conteúdo', 'label' => false)) ?>
            <?php echo $this->BForm->submit('Localizar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $this->BForm->end() ?>
    </div>  
    <div class="row-fluid">
    	<h5>Pasta enviada</h5>
    	<pre><?php print_r($enviada) ?></pre>
    	<h5>Pasta processado</h5>
    	<pre><?php print_r($processado) ?></pre>
    </div>
</div>