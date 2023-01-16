<style type="text/css">
            #documentos{ margin-top: 25px; margin-bottom: 50px;}
            .thumbs-documentos{
                width:180px; 
                height:120px;
                float: left;
                margin-right: 5px;
                margin-bottom: 5px;
                text-align:center;
                padding: 0;
                
            }            
            #thumb{ height:80px;width:80px;margin-right: 5px}
            #title-photo{ min-height: 37px; padding: 5px; color: #666666; }
            #text{text-align:center;}
</style>
<div id='documentos'>
	<?php $i=0?>
	<br/>
	<?php foreach ($lista_arquivos as $key => $value):?>	
		<?php  
	        $type = array('jpg','jpeg','png','gif');
    		$ext  = end(explode('.',$value));
    		if($ext == 'pdf'){
	        	$key = str_replace('MODELO','',$key);
	           	$key = str_replace('.pdf','',$key);
	        	$key = str_replace('_2014',' 2014',$key);
	    	}
        ?>
 		<div class="thumbs-documentos">
			<img src="/portal/img/logo_reader.jpg"  /><br/>
			<a href=<?php echo $this->Html->url("/files/plano_senhas/".$localiza[$i]);?> style="font-size:11px;"><?php echo $key?></a>
		</div>
		<?php $i++; ?>
	<?php  endforeach ?>	
</div>	





	              