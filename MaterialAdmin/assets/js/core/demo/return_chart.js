(function (namespace, $) {
    "use strict";
    var DemoCharts = function () {
        // Create reference to this instance
        var o = this;
        // Initialize app when document is ready
        $(document).ready(function () {
            o.initialize();
        });

    };
    var p = DemoCharts.prototype;
    p.initialize = function () {
        this._initMorris();
    };
    p._initMorris = function () {
        if (typeof Morris !== 'object') {
            return;
        }
        // Morris line demo
        if ($('#morris-line-graph').length > 0) {
            var decimal_data = [];
            for (var x = 20; x <= 360; x += 10) {
                decimal_data.push({
                    x: x,
                    y: 1.5 + 1.5 * Math.sin(Math.PI * x / 180).toFixed(4)
                });
            }
            console.log(decimal_data);
            window.m = Morris.Line({
                element: 'morris-line-graph',
                data: [
                    {x: 'may Q1', y: 0.12 },
                    {x: '2011 Q2', y: 2 },
                    {x: '2011 Q3', y: 0},
                    {x: '2011 Q4', y: 2}
                ],
                xkey: 'x',
                ykeys: ['y'],
                labels: ['sin(x)'],
                parseTime: false,
                resize: true,
                lineColors: $('#morris-line-graph').data('colors').split(','),
                hoverCallback: function (index, options, default_content) {
                    var row = options.data[index];
                    return default_content.replace("sin(x)", "1.5 + 1.5 sin(" + row.x + ")");
                },
                xLabelMargin: 10,
                integerYLabels: true
            });
        }
    };

    // =========================================================================
    namespace.DemoCharts = new DemoCharts;
}(this.materialadmin, jQuery)); // pass in (namespace, jQuery):
