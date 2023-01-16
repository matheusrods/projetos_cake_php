<style>
    .error-input-fonte-auth {
        color: red;
    }
</style>
<div id="form_cliente_fonte_autenticacao" class="row-fluid">
    <?php echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'id' => 'codigo_cliente', 'class' => 'input-xxlarge', 'value' => $codigo_cliente)) ?>
    <?php echo $this->BForm->input('codigo', array('type' => 'hidden', 'id' => 'codigo', 'class' => 'input-xxlarge', 'value' => isset($this->data['ClienteFonteAutenticacao']['codigo']) ? $this->data['ClienteFonteAutenticacao']['codigo'] : '')) ?>
    <span class="span9">
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('id_cliente', array('id' => 'id_cliente', 'class' => 'input-xxlarge input-fonte-auth', 'label' => 'Identificador do Cliente', 'value' => isset($this->data['ClienteFonteAutenticacao']['id_cliente']) ? $this->data['ClienteFonteAutenticacao']['id_cliente'] : '')) ?>
            <span class="error-input-fonte-auth"></span>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('id_entity', array('id' => 'id_entity', 'class' => 'input-xxlarge input-fonte-auth', 'label' => 'Identificador da aplicação', 'value' => isset($this->data['ClienteFonteAutenticacao']['id_entity']) ? $this->data['ClienteFonteAutenticacao']['id_entity'] : '')) ?>
            <span class="error-input-fonte-auth"></span>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('id_azure_ad', array('id' => 'id_azure_ad', 'class' => 'input-xxlarge input-fonte-auth', 'label' => 'Identificador Azure AD', 'value' => isset($this->data['ClienteFonteAutenticacao']['id_azure_ad']) ? $this->data['ClienteFonteAutenticacao']['id_azure_ad'] : '')) ?>
            <span class="error-input-fonte-auth"></span>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('url_login', array('id' => 'url_login', 'class' => 'input-xxlarge input-fonte-auth', 'label' => 'URL Login', 'value' => isset($this->data['ClienteFonteAutenticacao']['url_login']) ? $this->data['ClienteFonteAutenticacao']['url_login'] : '')) ?>
            <span class="error-input-fonte-auth"></span>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('url_logout', array('id' => 'url_logout', 'class' => 'input-xxlarge input-fonte-auth', 'label' => 'URL Logout', 'value' => isset($this->data['ClienteFonteAutenticacao']['url_logout']) ? $this->data['ClienteFonteAutenticacao']['url_logout'] : '')) ?>
            <span class="error-input-fonte-auth"></span>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('url_reply', array('id' => 'url_reply', 'class' => 'input-xxlarge input-fonte-auth', 'label' => 'URL Resposta', 'value' => isset($this->data['ClienteFonteAutenticacao']['url_reply']) ? $this->data['ClienteFonteAutenticacao']['url_reply'] : '')) ?>
            <span class="error-input-fonte-auth"></span>
        </div>
        <div class="row-fluid inline">
            <label for="certificado">Certificado</label>
            <?php echo $this->BForm->textarea('certificado', array('id' => 'certificado', 'style' => 'width: 620px; height: 345px;', 'class' => 'input-fonte-auth', 'label' => 'Certificado', 'value' => isset($this->data['ClienteFonteAutenticacao']['certificado']) ? $this->data['ClienteFonteAutenticacao']['certificado'] : '')) ?>
            <span class="error-input-fonte-auth"></span>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('cor_botao', array('id' => 'cor_botao', 'class' => 'input-xxlarge input-fonte-auth', 'label' => 'Cor botão (e.g. #ffffff)', 'style' => 'width: 125px;', 'value' => isset($this->data['ClienteFonteAutenticacao']['cor_botao']) ? $this->data['ClienteFonteAutenticacao']['cor_botao'] : '')) ?>
        </div>
        <div class="row-fluid inline">
            <label for="auto_redirect">Auto redirect?</label>
            <?php echo $this->BForm->select('auto_redirect', array('1' => 'Sim', '0' => 'Não'), null, array('id' => 'auto_redirect', 'label' => 'Auto redirect?', 'value' => isset($this->data['ClienteFonteAutenticacao']['auto_redirect']) ? $this->data['ClienteFonteAutenticacao']['auto_redirect'] : '0')) ?>
        </div>        
        <div class="row-fluid inline">
            <button type="button" id="btn_submit_cliente_fonte_autenticacao" class="btn btn-primary">Salvar Fonte de Autenticação</button>
        </div>
    </span>
</div>
<?php echo $this->Html->css('hex-color-picker'); ?>
<?php echo $this->Javascript->link('hex-color-picker.min'); ?>
<script type="text/javascript">
    var ControleTelaClienteFonteAutenticacao = (function() {
        return {
            init: function() {

                this.divFormClienteFonteAutenticacao = $('div[id="form_cliente_fonte_autenticacao"]');

                this.inputHiddenCodigo = this.divFormClienteFonteAutenticacao.find('input[id="codigo"]');

                this.inputHiddenCodigoCliente = this.divFormClienteFonteAutenticacao.find('input[id="codigo_cliente"]');

                this.inputTextIdCliente = this.divFormClienteFonteAutenticacao.find('input[id="id_cliente"]');
                this.inputTextIdEntity = this.divFormClienteFonteAutenticacao.find('input[id="id_entity"]');
                this.inputTextIdAzureAd = this.divFormClienteFonteAutenticacao.find('input[id="id_azure_ad"]');
                this.inputTextUrlLogin = this.divFormClienteFonteAutenticacao.find('input[id="url_login"]');
                this.inputTextUrlLogout = this.divFormClienteFonteAutenticacao.find('input[id="url_logout"]');
                this.inputTextUrlReply = this.divFormClienteFonteAutenticacao.find('input[id="url_reply"]');
                this.textareaCertificado = this.divFormClienteFonteAutenticacao.find('textarea[id="certificado"]');
                this.inputTextCorBotao = this.divFormClienteFonteAutenticacao.find('input[id="cor_botao"]');

                this.selectAutoRedirect = this.divFormClienteFonteAutenticacao.find('select[id="auto_redirect"]');

                this.inputTextCorBotao.hexColorPicker();

                this.buttonSubmitFormClienteFonteAutenticacao = this.divFormClienteFonteAutenticacao.find('button[id="btn_submit_cliente_fonte_autenticacao"]');

                this.formClienteFonteAutenticacaoValido = true;

                this.camposInvalidados = [];

                this.spansInputError = this.divFormClienteFonteAutenticacao.find('span[class="error-input-fonte-auth"]');

                this.events();
            },
            events: function() {

                this.buttonSubmitFormClienteFonteAutenticacao.on('click', this.handleClickButtonSubmitFormClienteFonteAutenticacao.bind(this));
            },
            handleClickButtonSubmitFormClienteFonteAutenticacao: function(event) {

                event.preventDefault();

                this.submit();
            },
            validar: function() {

                var camposInvalidados = [];

                if (this.inputTextIdCliente.val().trim() === '')
                    camposInvalidados.push(this.inputTextIdCliente);

                if (this.inputTextIdEntity.val().trim() === '')
                    camposInvalidados.push(this.inputTextIdEntity);

                if (this.inputTextIdAzureAd.val().trim() === '')
                    camposInvalidados.push(this.inputTextIdAzureAd);

                if (this.inputTextUrlLogin.val().trim() === '')
                    camposInvalidados.push(this.inputTextUrlLogin);

                if (this.inputTextUrlLogout.val().trim() === '')
                    camposInvalidados.push(this.inputTextUrlLogout);

                if (this.inputTextUrlReply.val().trim() === '')
                    camposInvalidados.push(this.inputTextUrlReply);

                if (this.textareaCertificado.val().trim() === '')
                    camposInvalidados.push(this.textareaCertificado);

                this.camposInvalidados = camposInvalidados;

                return camposInvalidados;
            },
            submit: function() {

                var camposInvalidados = this.validar();

                if (camposInvalidados.length > 0) {

                    this.handleErrors(camposInvalidados);
                } else {

                    this.saveAjax();
                }
            },
            handleErrors: function(camposInvalidados) {

                camposInvalidados.forEach(function(campoInvalidado, index) {

                    var spanError = campoInvalidado.parent().parent().find('span[class="error-input-fonte-auth"]');

                    spanError.html('Campo obrigatório. Favor preencher.');

                });

                setTimeout(function() {
                    this.spansInputError.each(function() {

                        $(this).show();

                        $(this).fadeOut(1000, function() {
                            $(this).html('');
                        }.bind(this));
                    });
                }.bind(this), 2000);
            },
            saveAjax: function() {

                this.buttonSubmitFormClienteFonteAutenticacao.html('Gravando...');

                var codigo = this.inputHiddenCodigo.val();

                var codigo_cliente = this.inputHiddenCodigoCliente.val();

                var id_entity = this.inputTextIdEntity.val();
                var id_cliente = this.inputTextIdCliente.val();
                var id_azure_ad = this.inputTextIdAzureAd.val();
                var url_login = this.inputTextUrlLogin.val();
                var url_logout = this.inputTextUrlLogout.val();
                var url_reply = this.inputTextUrlReply.val();
                var certificado = this.textareaCertificado.val();
                var cor_botao = this.inputTextCorBotao.val();
                var auto_redirect = this.selectAutoRedirect.val();

                $.ajax({
                        url: baseUrl + "clientes_fontes_autenticacao/salvar_ajax",
                        type: 'POST',
                        data: {
                            codigo,
                            codigo_cliente,
                            id_cliente,
                            id_entity,
                            id_azure_ad,
                            url_login,
                            url_logout,
                            url_reply,
                            certificado,
                            cor_botao,
                            auto_redirect
                        }
                    })
                    .done(function(data) {

                        if (data.codigo)
                            this.inputHiddenCodigo.val(data.codigo);

                        if (data.id_entity)
                            this.inputTextIdEntity.val(data.id_entity);

                        if (data.id_azure_ad)
                            this.inputTextIdAzureAd.val(data.id_azure_ad);

                        if (data.url_login)
                            this.inputTextUrlLogin.val(data.url_login);

                        if (data.url_logout)
                            this.inputTextUrlLogout.val(data.url_logout);

                        if (data.url_reply)
                            this.inputTextUrlReply.val(data.url_reply);

                        if (data.url_certificado)
                            this.textareaCertificado.val(data.url_certificado);

                        swal({
                            type: 'success',
                            title: 'Dados de fonte de autenticação salvos com sucesso',
                        }, function() {});
                    }.bind(this))
                    .fail(function(jqXHR, textStatus, errorThrown) {

                        console.log(errorThrown);
                    })
                    .always(function() {

                        this.buttonSubmitFormClienteFonteAutenticacao.html('Salvar Fonte de Autenticação');
                    }.bind(this));
            }
        };
    })();

    $(function() {

        ControleTelaClienteFonteAutenticacao.init();
    });
</script>