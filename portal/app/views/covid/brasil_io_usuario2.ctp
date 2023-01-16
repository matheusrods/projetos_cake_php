
<style>

    .embedd_pw {
        width:100%;
        min-height:100%; 
        height:665px;
    }

    @media screen and (max-width:999px)
    {
        .embedd_pw {
            width:100%;
            min-height:100%; 
            height:665px;
        }
    }

    @media screen and (max-width:480px)
    {
        .embedd_pw {
            width:100%;
            min-height:100%; 
            height:665px;
        }
    }
</style>

<?php 

    // TODO - colocar aqui, valores vindos na url
    $grafico = 'lyn'; 
    $accessToken = $access_token;
    
    // $reportId = "7b746182-bf24-41b3-8227-f3e45b331a34";
    // $groupId = "efb5dda9-9728-4ea8-8173-ff726233258b";
    
    echo $this->Buonny->link_js('jquery');
    echo $this->Buonny->link_js('powerbi/powerbi'); 
?>

<div>


    <div class="embedd_pw" id="embedContainer"></div>

    <?php echo $this->Javascript->codeBlock('
        
        jQuery(document).ready(function() {


            $(".header").hide();
            
            var accessToken = "'.$accessToken.'";

            var embedUrl = "https://app.powerbi.com/reportEmbed?reportId='.$reportId.'&groupId='.$groupId.'&w=2&config=eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly9XQUJJLUJSQVpJTC1TT1VUSC1CLVBSSU1BUlktcmVkaXJlY3QuYW5hbHlzaXMud2luZG93cy5uZXQiLCJlbWJlZEZlYXR1cmVzIjp7Im1vZGVybkVtYmVkIjp0cnVlfX0%3d";

            var embedReportId = "'.$reportId.'";

            var models = window["powerbi-client"].models;
            
            // const arrFilters = [
            //    {$schema: "http://powerbi.com/product/schema#basic",
            //    target: {
            //        hierarchy: "Localizacao",
            //        hierarchyLevel: "Estado",
            //        // hierarchyLevel: "Cidade",
            //        table: "caso_full_resumo"
            //     },
            //     operator: "In",
            //     values: ["'.$estado.'"]}
            // ];

            var config = {
                type: "visual",
                // type: "report",
                tokenType: models.TokenType.Embed,
                accessToken: accessToken,
                embedUrl: embedUrl,
                id: embedReportId,
                permissions: models.Permissions.All,
                pageName: "ReportSection8c38bb95e9c1be3ec1da",
                visualName: "b7dec7f5b98f817dd5f4",
                // filters: arrFilters,
                settings: {
                    filterPaneEnabled: true,
                    navContentPaneEnabled: true,
                    layoutType: models.LayoutType.MobilePortrait
                }
            };

            // Get a reference to the embedded report HTML element
            // var reportContainer = $(\'#embedContainer\')[0];
            var reportContainer = document.getElementById("embedContainer");

            // Embed the report and display it within the div container.
            var report = powerbi.embed(reportContainer, config);
                
        });
    '); ?>


</div>