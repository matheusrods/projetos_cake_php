<?php $this->addScript($this->Buonny->link_js('fancybox/jquery.fancybox.js')) ?> 
<?php $this->addScript($this->Buonny->link_css('fancybox/jquery.fancybox.css')) ?>
<?php
   $path = 'img'.DS.'mensagens';
   $diretorio = dir($path);
?>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'upload_imagem'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Imagem'));?>
</div>
</br>
<table class="table table-striped">
	<thead>
		<th>Titulo</th>
		<th>Link</th>
		<th class="input-mini numeric">&nbsp;</th>
	</thead>	
	<?php while($arquivo = $diretorio -> read()):?>
		<?php
		    $extencao = explode('.', $arquivo);
			$tiposPermitidos = array('gif', 'jpeg', 'pjpeg', 'png','jpg');
		?>
		<?php if(in_array($extencao[1], $tiposPermitidos)):?>
			<tbody>
				<td>	
					<!-- Nome do Arquivo-->
					<?= str_replace('_', ' ', $extencao[0]) ?>
				</td>
				<td>
					<?php $portal = $_SERVER['SERVER_NAME'];?>
					<?php $protocolo = isset($_SERVER['HTTPS']) ? 'https' : 'http';?>
					<div class="input-append">
					  <input readonly="true" value="<?=$protocolo?>://<?=$portal?>/portal/img/mensagens/<?= $arquivo?>" class="input-xxlarge" id="appendedInput" type="text">
					  <span class="add-on"><?php echo $html->image("paperclip.png");?></span>
					</div>
				</td>
				<td>
					<a class="icon-eye-open fancybox" href="/portal/img/mensagens/<?= $arquivo?>"  title="<?= $arquivo?>"></a> 
					<?= $this->Html->link('', array('action' => 'excluir_imagem', $arquivo, rand()), array('title' => 'Excluir', 'class' => 'icon-trash')) ?>
				</td>		
			</tbody>
		<?php endif;?>	
	<?php endwhile;
		$diretorio->close();
	?>
</table>
<?= $this->Javascript->codeBlock('
    $(document).ready(function(){
        $(".fancybox").fancybox();
    });
');?>