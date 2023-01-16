jQuery(document).ready(function() {
	jQuery('[id^="NotaiteGrupoEmpresa"]').change(function() {
		jQuery.ajax({
            'url': baseUrl + 'lojas_naveg/listar/' + jQuery(this).val() + '/' + Math.random(),
            'success': function(data) {
                jQuery('#NotaiteEmpresa').html(data);
            }
        });
	});
})