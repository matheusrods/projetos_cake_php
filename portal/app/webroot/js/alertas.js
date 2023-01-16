function init_alertas() {
	//var alertas = self.setInterval(function(){exibe_quantidade_alertas()},120000);
	//exibe_quantidade_alertas();
	jQuery("#menu_alerta > a").click(function(e){
		e.preventDefault();
		exibe_lista_alertas();
	});
	
	$('body').on('click', function (e) {
		if(jQuery('#menu_alerta').has(jQuery(e.target)).length == 0 && !jQuery(e.target).hasClass('carregar-mais'))
			fechar_lista();
	});
}

function exibe_quantidade_alertas() {
	jQuery.ajax({
		'url': baseUrl + 'alertas/contar_alertas_pendentes',
		'dataType': 'json',
		'success': function(data) {
			jQuery('.quantidade-alertas').remove();
			jQuery("#menu_alerta > a").append('<span class="label label-important quantidade-alertas">' + data + '</span>');
		}
	});
}

function exibe_lista_alertas() {
	if(jQuery("#menu_alerta div.popover").is(":visible")) {
		fechar_lista();
	} else {
		jQuery("#menu_alerta a").popover({placement:'bottom', title:'Alertas pendentes', content:'', html:true, trigger:'manual'});
		jQuery("#menu_alerta a").popover('show');
		carregar_lista_alertas(1);
	}
}

function fechar_lista() {
	jQuery("#menu_alerta a").popover('hide');
	jQuery("#menu_alerta a").popover('destroy');
}

function carregar_lista_alertas(pagina) {
	adicionar_loading();
	exibe_quantidade_alertas();
	jQuery.ajax({
		'url': baseUrl + 'alertas/listar_alertas_pendentes/' + pagina,
		'dataType': 'json',
		'error': function(data) {
			jQuery('.popover-content').html('<p class="mensagem">' + data.responseText + '</p>');
		},
		'success': function(data) {
			var lista = '<ul>';
			var ul = jQuery('#menu_alerta .popover-content ul');
			if(ul.length != 0)
				lista += ul.html();
			jQuery(data).each(function(){
				lista += '<li class="' + (this.Alerta.atribuido ? 'atribuido' : 'nao_atribuido') + '">';
				lista += '<input type="hidden" id="codigo_alerta" value="' + this.Alerta.codigo + '" />';
				lista += '<span class="codigo">#' + this.Alerta.codigo;
				if(this.Alerta.nome_usuario_tratamento != null)
					lista += ' - ' + this.Alerta.nome_usuario_tratamento;
				lista += '</span>';
				lista += '<span class="data">' + this.Alerta.data_inclusao + '</span>';
				lista += '<p>' + this.Alerta.descricao + '</p>';
				lista += '</li>';
			});
			lista += '</ul>';
			var qtd_total = jQuery('.quantidade-alertas').html();
			var qtd_lista = jQuery(lista).find('li').length;
			if(qtd_total > qtd_lista){
				lista += '<a class="carregar-mais ">Carregar mais...</a>';
			}

			jQuery('.popover-content').html(lista);
			jQuery('.carregar-mais').click(function(){
				carregar_lista_alertas(pagina + 1);
			});
			bind_tratar();
		}
	});
}

function adicionar_loading(){
	jQuery('.carregar-mais').remove();
	jQuery('.popover-content').append("<img src='/portal/img/loading.gif' title='carregando...' />");
}

function bind_tratar(){
	 $(".popover-content ul li.nao_atribuido, .popover-content ul li.atribuido").click(function(event){
		 var codigo_alerta = jQuery(this).find('#codigo_alerta').val();
		 open_dialog(baseUrl + 'alertas/tratar/' + codigo_alerta, 'Tratar Alerta', 572)
		 fechar_lista();
		 return false; 
	});
}

function parar_de_tratar(codigo_alerta) {
	bloquearDiv(jQuery('.ui-dialog'));
	jQuery.ajax({
		'url': baseUrl + 'alertas/parar_de_tratar/'+codigo_alerta,
		'success': function(data) {
			close_dialog();
		}
	});
}

function close_dialog_alerta(){
	if(jQuery('#modal_dialog .alert-success').length > 0){
		close_dialog();
		exibe_quantidade_alertas();
	}
}