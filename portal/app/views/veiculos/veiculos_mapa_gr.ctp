<!--<script type="text/javascript" src="http://services.maplink.com.br/maplinkapi2/api.ashx?v=4&key=ewFCaw3OSKzIGGLXeudvRJvgvwABbG3qGKdPdYF1uJzQSDKCGBHTQR=="></script>-->
<script src="https://maps.googleapis.com/maps/api/js?v=3.x"></script>
<style type="text/css">
	#canvas_mapa {
		margin: 0 !important;
		padding: 0 !important;
	}
	#canvas_mapa img,
	.google-maps img {
	  max-width: none;
	}						

</style>
<div class='form-procurar'>
    <?php echo $this->element('/filtros/veiculos_mapa_gr'); ?>
</div>
<div class='lista'></div>
<?php echo $this->Javascript->codeBlock('
	autoRefreshInteval = null;
', false);
?>