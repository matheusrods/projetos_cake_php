function alvo_sintetico_analitico2(filtros, codigo_selecionado, status_alvo, status_janela, status_permanencia) {
    //console.log(filtros);
    var form = document.createElement("form");
    var form_id = ("formresult" + Math.random()).replace(".","");
    form.setAttribute("method", "post");
    form.setAttribute("target", form_id);
    form.setAttribute("action", "/portal/viagens/tempo_maximo_analitico");

    if(filtros.agrupamento == "1"){
        field = document.createElement("input");
        field.setAttribute("name", "data[TViagViagem][cd_id]");
        field.setAttribute("value", codigo_selecionado);
        field.setAttribute("type", "hidden");
        form.appendChild(field);
    } else {
        if(typeof filtros.cd_id == "object"){
            $.each(filtros.cd_id, function(i, codigo){
                field = document.createElement("input");
                field.setAttribute("name", "data[TViagViagem][cd_id][]");
                field.setAttribute("value", codigo);
                field.setAttribute("type", "hidden");
                form.appendChild(field);
            });
        }else{
            field = document.createElement("input");
            field.setAttribute("name", "data[TViagViagem][cd_id]");
            field.setAttribute("value", filtros.cd_id);
            field.setAttribute("type", "hidden");
            form.appendChild(field);
        }
    }
    
    if(filtros.agrupamento == "2"){
        field = document.createElement("input");
        field.setAttribute("name", "data[TViagViagem][bandeira_id]");
        field.setAttribute("value", codigo_selecionado);
        field.setAttribute("type", "hidden");
        form.appendChild(field);
    } else {
        if(typeof filtros.bandeira_id == "object"){
            $.each(filtros.bandeira_id, function(i, codigo){
                field = document.createElement("input");
                field.setAttribute("name", "data[TViagViagem][bandeira_id][]");
                field.setAttribute("value", codigo);
                field.setAttribute("type", "hidden");
                form.appendChild(field);
            });
        }else{
            field = document.createElement("input");
            field.setAttribute("name", "data[TViagViagem][bandeira_id]");
            field.setAttribute("value", filtros.bandeira_id);
            field.setAttribute("type", "hidden");
            form.appendChild(field);
        }
    }

    if(filtros.agrupamento == "3"){
        field = document.createElement("input");
        field.setAttribute("name", "data[TViagViagem][regiao_id]");
        field.setAttribute("value", codigo_selecionado);
        field.setAttribute("type", "hidden");
        form.appendChild(field);
    } else {
        if(typeof filtros.regiao_id == "object"){
            $.each(filtros.regiao_id, function(i, codigo){
                field = document.createElement("input");
                field.setAttribute("name", "data[TViagViagem][regiao_id][]");
                field.setAttribute("value", codigo);
                field.setAttribute("type", "hidden");
                form.appendChild(field);
            });
        }else{
            field = document.createElement("input");
            field.setAttribute("name", "data[TViagViagem][regiao_id]");
            field.setAttribute("value", filtros.regiao_id);
            field.setAttribute("type", "hidden");
            form.appendChild(field);
        }
    }

    if(filtros.agrupamento == "4"){
        field = document.createElement("input");
        field.setAttribute("name", "data[TViagViagem][loja_id]");
        field.setAttribute("value", codigo_selecionado);
        field.setAttribute("type", "hidden");
        form.appendChild(field);
    } else {
        if(typeof filtros.loja_id == "object"){
            $.each(filtros.loja_id, function(i, codigo){
                field = document.createElement("input");
                field.setAttribute("name", "data[TViagViagem][loja_id][]");
                field.setAttribute("value", codigo);
                field.setAttribute("type", "hidden");
                form.appendChild(field);
            });
        }else{
            field = document.createElement("input");
            field.setAttribute("name", "data[TViagViagem][loja_id]");
            field.setAttribute("value", filtros.loja_id);
            field.setAttribute("type", "hidden");
            form.appendChild(field);
        }
    }

    if(filtros.agrupamento == "5"){
        field = document.createElement("input");
        field.setAttribute("name", "data[TViagViagem][transportador_id]");
        field.setAttribute("value", codigo_selecionado);
        field.setAttribute("type", "hidden");
        form.appendChild(field);
    } else {
        if(typeof filtros.transportador_id == "object"){
            $.each(filtros.transportador_id, function(i, codigo){
                field = document.createElement("input");
                field.setAttribute("name", "data[TViagViagem][transportador_id][]");
                field.setAttribute("value", codigo);
                field.setAttribute("type", "hidden");
                form.appendChild(field);
            });
        }else{
            field = document.createElement("input");
            field.setAttribute("name", "data[TViagViagem][transportador_id]");
            if (filtros.transportador_id) {
                field.setAttribute("value", filtros.transportador_id);
            } else {
                field.setAttribute("value", '');
            }
            field.setAttribute("type", "hidden");
            form.appendChild(field);
        }
    }

    if(typeof filtros.cref_codigo == "object"){
        $.each(filtros.cref_codigo, function(i, codigo){
            field = document.createElement("input");
            field.setAttribute("name", "data[TViagViagem][cref_codigo][]");
            field.setAttribute("value", codigo);
            field.setAttribute("type", "hidden");
            form.appendChild(field);
        });
    }else{
        field = document.createElement("input");
        field.setAttribute("name", "data[TViagViagem][cref_codigo]");
        field.setAttribute("value", typeof(filtros.cref_codigo) != "undefined" ? filtros.cref_codigo : "");
        field.setAttribute("type", "hidden");
        form.appendChild(field);
    }

    if(typeof filtros.tvei_codigo == "object"){
        $.each(filtros.tvei_codigo, function(i, codigo){
            field = document.createElement("input");
            field.setAttribute("name", "data[TViagViagem][tvei_codigo][]");
            field.setAttribute("value", codigo);
            field.setAttribute("type", "hidden");
            form.appendChild(field);
        });
    }else{
        field = document.createElement("input");
        field.setAttribute("name", "data[TViagViagem][tvei_codigo]");
        field.setAttribute("value", typeof(filtros.tvei_codigo) != "undefined" ? filtros.tvei_codigo : "");
        field.setAttribute("type", "hidden");
        form.appendChild(field);
    }

    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][data_inicial]");
    field.setAttribute("value", filtros.data_inicial.substr(0,10));
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][data_final]");
    field.setAttribute("value", filtros.data_final.substr(0,10));
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][maximo_minutos]");
    field.setAttribute("value", typeof(filtros.maximo_minutos) != "undefined" ? filtros.maximo_minutos : 1);
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][codigo_cliente]");
    field.setAttribute("value", filtros.codigo_cliente);
    field.setAttribute("type", "hidden");
    form.appendChild(field);

    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][base_cnpj]");
    field.setAttribute("value", filtros.base_cnpj);
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][status_permanencia]");
    field.setAttribute("value", typeof(status_permanencia) != "undefined" ? status_permanencia : "");
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][status_viagem]");
    field.setAttribute("value", typeof(filtros.status_viagem) != "undefined" ? filtros.status_viagem : "");
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    if(typeof status_alvo == "object"){
        $.each(status_alvo, function(i, codigo){
            field = document.createElement("input");
            field.setAttribute("name", "data[TViagViagem][status_alvo][]");
            field.setAttribute("value", codigo);
            field.setAttribute("type", "hidden");
            form.appendChild(field);
        });
    }else{
        field = document.createElement("input");
        field.setAttribute("name", "data[TViagViagem][status_alvo]");
        field.setAttribute("value", typeof(status_alvo) != "undefined" ? status_alvo : "");
        field.setAttribute("type", "hidden");
        form.appendChild(field);
    }
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][status_janela]");
    field.setAttribute("value", typeof(status_janela) != "undefined" ? status_janela : "");
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][alvo_critico]");
    field.setAttribute("value", "");
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][proximo_alvo]");
    field.setAttribute("value", "");
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][mesclar_prazo_adiantado]");
    field.setAttribute("value", typeof(filtros.mesclar_prazo_adiantado) != "undefined" ? filtros.mesclar_prazo_adiantado : "");
    field.setAttribute("type", "hidden");
    form.appendChild(field);


    document.body.appendChild(form);
    var janela = window_sizes();
    window.open("", form_id, "scrollbars=yes,menubar=no,height="+(janela.height-200)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");
    form.submit();
}