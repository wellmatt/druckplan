<?php // Anzahl aufträge pro Monat // DONE

$data = new Widget(Widget::TYPE_COLINVPERMONTH);
$data = $data->getResult();


$data = json_encode($data);
?>

<script type="text/javascript">
    $(function() {
        var data = <?php echo $data;?>;
        var placeholder = $("#COLINVPERMONTH");
        $.plot(placeholder, [ data ], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.4,
                    align: "center"
                }
            },
            xaxis: {
                mode: "categories",
                tickLength: 0
            }
        });
    });
</script>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Aufträge pro Monat</h3>
    </div>
    <div class="panel-body">
        <div id="COLINVPERMONTH" style="width:400px;height:300px"></div>

    </div>
</div>