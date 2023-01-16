<!--
<div class="form-procurar-codigo-prestador">
	<?= $this->element('/filtros/prestadores_buscar_codigo') ?>
</div>
<?/*php
	 echo $this->Javascript->codeBlock("
 	$(function() {
      var script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&' + 'libraries=places&'+'callback=initialize';
      document.body.appendChild(script);
    });");
*/
?>
<div class="lista-prestadores"></div>
-->

<div class = 'form-procurar'>
	<?= $this->element('/filtros/mapa_prestadores') ?>
</div>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<div class='lista-prestadores'></div>