<?php
$testdata = Array(
    Array("Jan",10),
    Array("Feb",30),
    Array("Maerz",90),
    Array("September",70),
    Array("Dezember",80),
    Array("August",110),
);
$testdata = json_encode($testdata);
?>

<script type="text/javascript">
    $(function() {
        var data = <?php echo $testdata;?>;
        var placeholder = $("#placeholder_test2");
        $.plot(placeholder, [ data ], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.6,
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
        <h3 class="panel-title">Test Beispiel 2</h3>
    </div>
    <div class="panel-body">
        <div id="placeholder_test2" style="width:400px;height:300px"></div>
        test
    </div>
</div>