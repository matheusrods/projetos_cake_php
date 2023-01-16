<div class = 'form-procurar'>
	<?php echo $this->element('/filtros/localizar_credenciado');?>
</div>

<?php if(Ambiente::TIPO_MAPA == 1): ?>
	<?php if ( empty($input_id)) :?>
		<script src="https://maps.googleapis.com/maps/api/js?sensor=false&key=<?php echo Ambiente::getGoogleKey(1); ?>"></script>
	<?php else:?>
		<?php echo $this->Javascript->codeBlock("
	 		$(function() {
	      		var script = document.createElement('script');
	      		script.type = 'text/javascript';
	      		script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&' + 'libraries=places&'+'callback=initialize';
	      		document.body.appendChild(script);
	    	});");
		?>	
	<?php endif;?>
<?php endif;?>

<div class='lista-credenciados'></div>