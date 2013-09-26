$(function () {
    // On document ready, call visualize on the datatable.
    $(document).ready(function() {
        /**
         * Visualize an HTML table using Highcharts. The top (horizontal) header
         * is used for series names, and the left (vertical) header is used
         * for category names. This function is based on jQuery.
         * @param {Object} table The reference to the HTML table to visualize
         * @param {Object} options Highcharts options
         */
        Highcharts.visualize = function(table, options) {
            // the categories
            options.xAxis.categories = [];
            $('tbody th', table).each( function(i) {
                options.xAxis.categories.push(this.innerHTML);
            });
    
            // the data series
            options.series = [];
            $('tr', table).each( function(i) {
                var tr = this;
                $('th, td', tr).each( function(j) {
                    if (j > 0) { // skip first column
                        if (i == 0) { // get the name and init the series
                            options.series[j - 1] = {
                                name: this.innerHTML,
                                data: []
                            };
                        } else { // add values
                            options.series[j - 1].data.push(parseFloat(this.innerHTML));
                        }
                    }
                });
            });
    
            var chart = new Highcharts.Chart(options);
        }
    
        var table = document.getElementById('datatable'),
        options = {
            chart: {
                renderTo: 'highchart2',
                plotBackgrountColor: null,
				plotBorderWidth: null,
				plotShadow: false
            },
			credits:{
				enabled: false
				},
            title: {
                text: 'Data extracted from a HTML table in the page'
            },
            
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                        this.y +' '+ this.x.toLowerCase();
                }
            },
			plotOptions: {
				pie: {
					showInLegend: true,
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels:{
						enabled: true,
						color: '#000000',
						connectorColor: '#000000',
						formatter: function(){
							return '<b>'+ this.point.name +'</b>: '+ this.point.y +' sessions';
							}
					}
				}
			}
        };
    
        Highcharts.visualize(table, options);
    });
    
});