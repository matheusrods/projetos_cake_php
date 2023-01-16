function lista_por_processo(favcodigo,endcodigo,ano){
    form  = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="lista_por_processo" action="/portal/processos/consulta_faturamento_por_processo">';
    form += '<input type="text" id="favcodigo" value="" name="data[Processo][favcodigo]">';
    form += '<input type="text" id="endcodigo" value="" name="data[Processo][endcodigo]">';
    form += '<input type="text" id="ano" value="" name="data[Processo][CMS_AnoProcesso]">';
    form += '</form>';
    form += '</div>';
    
    jQuery('body').append(form);
    jQuery("#postlink #favcodigo").val( favcodigo );
    jQuery("#postlink #endcodigo").val( endcodigo );
    jQuery("#postlink #ano").val( ano );
    
    jQuery("#postlink form").submit();
}

function em_aberto_por_cliente(Model,processo,favcodigo,endcodigo){
    form  = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="'+Model+'PorProcesso" action="/portal/processos/em_aberto_por_cliente">'
    form += '<input type="text" id="'+Model+'CMS_NumProcesso" value="'+processo+'" name="data['+Model+'][CMS_NumProcesso]">';
    form += '<input type="text" id="'+Model+'Favcodigo" value="'+favcodigo+'" name="data['+Model+'][favcodigo]">';
    form += '<input type="text" id="'+Model+'Endcodigo" value="'+favcodigo+'" name="data['+Model+'][endcodigo]">';
    form += '</form>';
    form += '</div>';
    
    jQuery('body').append(form);
    jQuery("#postlink #"+Model+"CMS_NumProcesso").val(processo);
    jQuery("#postlink #"+Model+"Favcodigo").val(favcodigo);
    jQuery("#postlink #"+Model+"Endcodigo").val(endcodigo);

    jQuery("#postlink form").submit();
}

function em_aberto_por_cliente_faturados(Model,processo,favcodigo,endcodigo){
    form  = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="'+Model+'PorProcesso" action="/portal/processos/em_aberto_por_cliente_faturados">'
    form += '<input type="text" id="'+Model+'CMS_NumProcesso" value="'+processo+'" name="data['+Model+'][CMS_NumProcesso]">';
    form += '<input type="text" id="'+Model+'Favcodigo" value="'+favcodigo+'" name="data['+Model+'][favcodigo]">';
    form += '<input type="text" id="'+Model+'Endcodigo" value="'+endcodigo+'" name="data['+Model+'][endcodigo]">';
    form += '</form>';
    form += '</div>';
    
    jQuery('body').append(form);
    jQuery("#postlink #"+Model+"CMS_NumProcesso").val(processo);
    jQuery("#postlink #"+Model+"Favcodigo").val(favcodigo);
    jQuery("#postlink #"+Model+"Endcodigo").val(endcodigo);

    jQuery("#postlink form").submit();
}

function em_aberto_por_cliente_a_faturar(Model,processo,favcodigo,endcodigo){
    form  = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="'+Model+'PorProcesso" action="/portal/processos/em_aberto_por_cliente_a_faturar">'
    form += '<input type="text" id="'+Model+'CMS_NumProcesso" value="'+processo+'" name="data['+Model+'][CMS_NumProcesso]">';
    //form += '<input type="text" id="'+Model+'NumTitulo" value="'+titulo+'" name="data['+Model+'][num_titulo]">';
    form += '<input type="text" id="'+Model+'Favcodigo" value="'+favcodigo+'" name="data['+Model+'][favcodigo]">';
    form += '<input type="text" id="'+Model+'Endcodigo" value="'+endcodigo+'" name="data['+Model+'][endcodigo]">';
    form += '</form>';
    form += '</div>';
    
    jQuery('body').append(form);
    jQuery("#postlink #"+Model+"CMS_NumProcesso").val(processo);
    //jQuery("#postlink #"+Model+"NumTitulo").val(titulo);
    jQuery("#postlink #"+Model+"Favcodigo").val(favcodigo);
    jQuery("#postlink #"+Model+"Endcodigo").val(endcodigo);

    jQuery("#postlink form").submit();
}


function detalhes_do_processo( codigo_processo ) {   

    var form = document.createElement("form");
    var form_id = ('formresult' + Math.random()).replace('.','');
    form.setAttribute("method", "post");
    form.setAttribute("target", form_id);
    form.setAttribute("action", "/portal/processos/detalhes_do_processo/1");
    field = document.createElement("input");
    field.setAttribute("name", "data[Processo][CMS_NumProcesso]");
    field.setAttribute("value", codigo_processo);
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    document.body.appendChild(form);
    var janela = window_sizes();
    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
    form.submit();
}

function window_sizes() {
    var winW = 630, winH = 460;
    if (document.body && document.body.offsetWidth) {
     winW = document.body.offsetWidth;
     winH = document.body.offsetHeight;
    }
    if (document.compatMode=='CSS1Compat' &&
        document.documentElement &&
        document.documentElement.offsetWidth ) {
     winW = document.documentElement.offsetWidth;
     winH = document.documentElement.offsetHeight;
    }
    if (window.innerWidth && window.innerHeight) {
     winW = window.innerWidth;
     winH = window.innerHeight;
    }
    var janela = new Object;
    janela.width = winW;
    janela.height = winH;
    return janela;
}

function editar_fotos(codigo,titulo,descricao,visivel,url) {
    var check = $(visivel).is(":checked");
    $.ajax({
        type: 'POST',
        url: '/portal/processos/editar_fotos/info',
        cache: false,
        data:{  
            "data[ProcessoArquivo][codigo]" : codigo,
            "data[ProcessoArquivo][titulo]" : titulo,
            "data[ProcessoArquivo][descricao]" : descricao,
            "data[ProcessoArquivo][visivel]" : check ? 1 : 0            
        },
        beforeSend : function(){            
            $('.well').slideToggle('hide');
            $('#loader').html("<img src='/portal/img/loading.gif' title='carregando...' />");
        },
        success : function(data){ 
            $('.well').slideToggle('show');
            $('#loader').html('');
                close_dialog();
                location.href = url;
        },
        error : function(){
            $('#loader').addClass('alert alert-error');
            $('#loader').html('Erro ao atualizar as informações!');
        }
    });
}