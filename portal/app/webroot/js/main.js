
$(function () {

    $("#linksUteis").change(function () {
        window.location = $(this).find("option:selected").val();
    });
    $(".solutionNav").click(function () {
        var action = $(this).attr("rel");
        $(".solutionHome").hide();
        $(".solutionHide").hide();
        $("#" + action).show();
    });
    $(".solutionSelect").change(function () {
        var action = $(this).find("option:selected").val();
        $(".solutionHome").hide();
        $(".solutionHide").hide();
        $("#" + action).show();
    });
    function toggleChevron(e) {
        $(e.target)
                .prev('.panel-accordion')
                .find("i.indicator")
                .toggleClass('btnSwitchShow btnSwitchHide');
    }

    $('#accordion').on('hidden.bs.collapse', toggleChevron);
    $('#accordion').on('shown.bs.collapse', toggleChevron);
    $('.dropdown-toggle').dropdown();
    $('.dropdown-menu').find('form').click(function (e) {
        e.stopPropagation();
    });

    $(".carousel-inner").swipe({
        swipeLeft: function (event, direction, distance, duration, fingerCount) {
            $(this).parent().carousel('next');
        },
        swipeRight: function () {
            $(this).parent().carousel('prev');
        },
        threshold: 0
    });


    if ($('.popup-gallery').length) {
        $('.popup-gallery').magnificPopup({
            delegate: 'a',
            type: 'image',
            tLoading: 'Carregando imagem #%curr%...',
            mainClass: 'mfp-img-mobile',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
            },
            image: {
                tError: '<a href="%url%">A imagem #%curr%</a> nÃƒÂ£o foi carregada =/',
                titleSrc: function (item) {
                    return item.el.attr('title') + ' ';
                }
            }
        });
    }


    var action = "/contato";
    var form = $("form#formFooter");
    var btnContact = "#btnFormSubmit";
    if (form.length) {
        $(btnContact).on('click', function () {
            var result = $("#resultContato");
            $(result).html('<img src="/img/loader.gif" alt="Processando..."  id="loading_contato" /> Processando...');
            var loading = $("#loading_contato");
            var data = $(form).serialize();
            var result = "formNome=&formEmail=&formTelefone=&formMsg=";
            $.ajax({
                type: "POST",
                url: action,
                data: data,
                cache: false,
                async: false,
                success: function (response) {
                    $("#resultContato").fadeIn(500, function () {

                        $(this).html(response);
                        if (loading !== "") {
                            $(loading).hide("slow");
                        }

                        return false;
                    });
                }
            });
            return false;
        });
    }
});
function go(url_src) {
    return window.location = url_src;
}

function get(elemento) {
    return document.getElementById(elemento);
}
function confirma(string) {
    return confirm(string);
}

function postAjax(url, data, result, loading) {
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        cache: false,
        async: false,
        success: function (response) {
            $(result).fadeIn(500, function () {

                $(this).html(response);
                if (loading !== "") {
                    $(loading).hide("slow");
                }
            });
        }
    });
}