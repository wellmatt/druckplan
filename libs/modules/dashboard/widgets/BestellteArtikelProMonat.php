<?php // Anzahl bestellter Artikel pro Monat // DONE

$data = new Widget(Widget::TYPE_SHOPORDERSPERMONTH);
$data = $data->getResult();


$data = json_encode($data);
?>

<script type="text/javascript">
    $(function() {
        var data = <?php echo $data;?>;
        var placeholder = $("#SHOPORDERSPERMONTH");
        $.plot(placeholder, [ data ], {
            series: {
                lines: {
                    show: true,
                    barWidth: 0.4,
                    align: "left"
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
        <h3 class="panel-title">Shop Bestellungen pro Monat</h3>
    </div>
    <div class="panel-body">
        <div id="SHOPORDERSPERMONTH" style="width:400px;height:300px"></div>

    </div>
</div>