var Usuario = {
	cpf : '',
	data : {},
	_generateData: function(){
		this.data = $('#FormCriaUsuario').serialize();
		this.cpf = $('#FuncionarioCpf').val();
	},
	_saveData : function(){
		this._button('loading');
		$.ajax({
			url: baseUrl + 'funcionarios/gera_usuario/' + this.cpf + '/' + Math.random(),
			type: 'POST',
			data: Usuario.data,
			dataType: 'json',
			success: function(data){
				Usuario._button('reset');
				Usuario._clearMessages();
				if (data.status === 'ok') {
					Usuario._returnSucess(data);
				}else{
					Usuario._returnFail(data);
				}
			},
			error : function(){
				Usuario._addMessage('Ocorreu um erro, durante a requisição, tente novamente.', 'warning');
				Usuario._button('reset');
			}
 		});
	},
	_returnSucess: function(data){
		$('.btn_cria_usuario').remove();
		$('.formUsuario').removeClass('hidden');
		$('#UsuarioSenha').removeAttr('disabled');
		$('#UsuarioCodigo').val(data.result.Usuario.codigo);
		$('#UsuarioApelido').val(data.result.Usuario.apelido);
		this._closeModal();
	},
	_button : function(type){
		$('#BtnSaveUsuario').button(type);
	},
	_returnFail: function(data){
		var obj;
		try {
		    obj = $.parseJSON(data.message);
		    jQuery.each(obj, function(i, val){
		    	Usuario._addMessage(val, 'info');
		    });
		} catch(ex) {
		    Usuario._addMessage(data.message, 'info');
		}
	},
	_clearMessages : function(){
		$('#NovoUsuarioMessages').html('');
	},
	_addMessage : function(message, messageclass){
		html = '<div class="alert alert-' + messageclass + '"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + message + '</div>';
		$('#NovoUsuarioMessages').append(html);
	},
	_closeModal : function(){
		$('#novoUsuario').modal('toggle');
	},
	abreModal : function(){
		$('#novoUsuario').modal('show');
	},
	save : function(){
		this._generateData();
		this._saveData();
	}
};