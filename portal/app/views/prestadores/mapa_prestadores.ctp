<div class = 'form-procurar'>
	<?= $this->element('/filtros/mapa_prestadores') ?>
</div>
<?php if ( empty($input_id)) :?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<?php else:?>
<?php
	echo $this->Javascript->codeBlock("
 	$(function() {
      var script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&' + 'libraries=places&'+'callback=initialize';
      document.body.appendChild(script);
    });");
?>	
<?php endif;?>
<div class='lista-prestadores'></div>