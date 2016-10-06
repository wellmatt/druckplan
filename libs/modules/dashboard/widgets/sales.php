<?php
$testdata = Array(
    Array("Juni",23800),
    Array("Juli",19781),
    Array("Aug.",17483),
    Array("Sept.",41421),
    Array("Nov.",59504),
    Array("Dez.",49571),
);
$testdata = json_encode($testdata);
?>

<script type="text/javascript">
    $(function() {
        var data = <?php echo $testdata;?>;
        var placeholder = $("#placeholder_sales");
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
        <h3 class="panel-title">Umsatz pro Monat</h3>
    </div>
    <div class="panel-body">
        <div id="placeholder_sales" style="width:400px;height:300px"></div>

    </div>
</div>