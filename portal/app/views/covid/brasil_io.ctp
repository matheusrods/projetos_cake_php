
<?php 

    echo $this->Buonny->link_js('jquery');
    echo $this->Buonny->link_js('powerbi/powerbi'); 
?>

<div>


<div style="width:100%;min-height:100%; height:630px;" id="embedContainer"></div>

<?php echo $this->Javascript->codeBlock('
    
    jQuery(document).ready(function() {
        
        var accessToken = "'.$accessToken.'";

        var embedUrl = "https://app.powerbi.com/reportEmbed?reportId='.$reportId.'&groupId='.$groupId.'&w=2&config=eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly9XQUJJLUJSQVpJTC1TT1VUSC1CLVBSSU1BUlktcmVkaXJlY3QuYW5hbHlzaXMud2luZG93cy5uZXQiLCJlbWJlZEZlYXR1cmVzIjp7Im1vZGVybkVtYmVkIjp0cnVlfX0%3d";

        var embedReportId = "'.$reportId.'";

        var models = window["powerbi-client"].models;
        
        var config = {
            type: "report",
            tokenType: models.TokenType.Embed,
            accessToken: accessToken,
            embedUrl: embedUrl,
            id: embedReportId,
            permissions: models.Permissions.All,
            settings: {
                filterPaneEnabled: true,
                navContentPaneEnabled: true
            }
        };

        // Get a reference to the embedded report HTML element
        // var reportContainer = $(\'#embedContainer\')[0];
        var reportContainer = document.getElementById("embedContainer");

        // Embed the report and display it within the div container.
        var report = powerbi.embed(reportContainer, config);
            
    });
'); ?>
 
</script>

</div>