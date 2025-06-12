<script type="text/javascript">
    window.onload = function() {

        'use strict';
        var Dashboard = window.Dashboard || {};
        var xhr = null;
        if (window.XMLHttpRequest) {
            xhr = window.XMLHttpRequest;
        } else if (window.ActiveXObject('Microsoft.XMLHTTP')) {

            xhr = window.ActiveXObject('Microsoft.XMLHTTP');
        }
        var send = xhr.prototype.send;
        xhr.prototype.send = function(data) {
            try {
                send.call(this, data);
            } catch (e) {
                Dashboard.processExceptions(e);
            }
        };


        Dashboard.MemberTbls = '';
        Dashboard.allMemberDetails = [];

        Dashboard.filterOption = false;

        var auth_user_id = "{{ auth()->id() }}";

        Dashboard.initEvents = function() {

            //========== Member Chart =================

            $("body").on("change", ".member_block_select", function(e) {
                if ($(this).val() == 'chart') {
                    $('.member_list_block').addClass('d-none');
                    $('.member_chart_block').removeClass('d-none');
                    $('.chart_year_block').show()

                } else {
                    $('.member_list_block').removeClass('d-none');
                    $('.member_chart_block').addClass('d-none');
                    $('.chart_year_block').hide()
                }
            });

            Dashboard.getMemberChartDetails = function(year) {

                $('#dashboard-analytics-chart').html('');

                showSpinner('#dashboard-analytics-chart');

                $.ajax({

                    type: "POST",
                    url: baseUrl + 'get_analytics_chart_details',
                    data: {
                        'year' : year
                    },
                    success: function(response) {
                        if(response.status == 1){
                            $('#dashboard-analytics-chart').html(response.html);
                            $('.dashboard-analytics-chart #year-records-total').html(`<i class="fa fa-user-circle text-danger mr-25 font-medium-5 align-middle" aria-hidden="true"></i> Members (${response.total})`)
                            let query_element = ".dashboard-analytics-chart > .analytics-dash-chart";
                            Dashboard.renderChart(response.records, response.max_value,'Members',query_element)
                        }
                    },
                    error:function(response){
                        showErrorMessage(response.message);
                    }
                });

            }

            Dashboard.round = function(x) {
                return Math.ceil(x / 5) * 5;
            }

            Dashboard.manageChartOptions = function(option_json) {

                var analyticsBarChartOptions = {
                    chart: 'chart' in option_json ? {

                        ...option_json.chart,
                        height: 'height' in option_json.chart ? option_json.chart.height : 260,
                        type: 'type' in option_json.chart ? option_json.chart.type : 'bar',
                        toolbar: "toolbar" in option_json.chart ? {
                            ...option_json.chart.toolbar,
                            show: 'show' in option_json.chart.toolbar ? option_json.chart.toolbar
                                .show : false
                        } : {
                            show: false
                        }

                    } : {
                        height: 260,
                        type: 'bar',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: 'plotOptions' in option_json ? {
                        ...option_json.plotOptions,
                        bar: 'bar' in option_json.plotOptions ? {
                            ...option_json.plotOptions.bar,
                            horizontal: 'horizontal' in option_json.plotOptions.bar ? option_json
                                .plotOptions
                                .bar.horizontal : false,
                            columnWidth: 'columnWidth' in option_json.plotOptions.bar ? option_json
                                .plotOptions
                                .bar.horizontal : '20%',
                            endingShape: 'endingShape' in option_json.plotOptions.bar ? option_json
                                .plotOptions
                                .bar.horizontal : 'rounded'

                        } : {
                            horizontal: false,
                            columnWidth: '20%',
                            endingShape: 'rounded'
                        }

                    } : {
                        bar: {
                            horizontal: false,
                            columnWidth: '20%',
                            endingShape: 'rounded'
                        },
                    },
                    dataLabels: 'dataLabels' in option_json ? {
                        ...option_json.dataLabels,
                        enabled: 'enabled' in option_json.dataLabels ? option_json.dataLabels.enabled :
                            false
                    } : {
                        enabled: false
                    },
                    colors: 'colors' in option_json ? option_json.colors : ['#f10937'],
                    fill: 'fill' in option_json ? {
                        ...option_json.fill,
                        type: 'type' in option_json.fill ? option_json.fill.type : 'gradient',
                        gradient: 'gradient' in option_json.fill ? {
                            ...option_json.fill.gradient,
                            shade: 'shade' in option_json.fill.gradient ? option_json.fill.gradient
                                .shade : 'light',
                            type: 'type' in option_json.fill.gradient ? option_json.fill.gradient.type :
                                "vertical",
                            inverseColors: 'inverseColors' in option_json.fill.gradient ? option_json
                                .fill.gradient.inverseColors : true,
                            opacityFrom: 'opacityFrom' in option_json.fill.gradient ? option_json.fill
                                .gradient.opacityFrom : 1,
                            opacityTo: 'opacityTo' in option_json.fill.gradient ? option_json.fill
                                .gradient.opacityTo : 1,
                            stops: 'stops' in option_json.fill.gradient ? option_json.fill.gradient
                                .stops : [0, 70, 100]
                        } : {
                            shade: 'light',
                            type: "vertical",
                            inverseColors: true,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 70, 100]
                        }
                    } : {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: "vertical",
                            inverseColors: true,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 70, 100]
                        },
                    },
                    series: 'series' in option_json ? option_json.series : [],
                    xaxis: 'xaxis' in option_json ? {
                        ...option_json.xaxis,
                        categories: 'categories' in option_json.xaxis ? option_json.xaxis.categories : [],
                        axisBorder: 'axisBorder' in option_json.xaxis ? {
                            ...option_json.xaxis.axisBorder,
                            show: 'show' in option_json.xaxis.axisBorder ? option_json.xaxis.axisBorder
                                .show : false
                        } : {
                            show: false
                        },
                        axisTicks: 'axisTicks' in option_json.xaxis ? {
                            ...option_json.xaxis.axisTicks,
                            show: 'show' in option_json.xaxis.axisTicks ? option_json.xaxis.axisTicks
                                .show : false
                        } : {
                            show: false
                        },
                        labels: 'labels' in option_json.xaxis ? {
                            ...option_json.xaxis.labels,
                            style: 'style' in option_json.xaxis.labels ? option_json.xaxis.labels
                                .style : {
                                    colors: '#010912'
                                }
                        } : {
                            style: {
                                colors: '#010912'
                            }
                        }
                    } : {
                        categories: Object.keys(members),
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: '#e3e9ef'
                            }
                        }
                    },
                    yaxis: 'yaxis' in option_json ? {
                        ...option_json.yaxis,
                        min: 'min' in option_json.yaxis ? option_json.yaxis.min : 0,
                        max: 'max' in option_json.yaxis ? option_json.yaxis.max : 100,
                        tickAmount: 'tickAmount' in option_json.yaxis ? option_json.yaxis.tickAmount : 3,
                        labels: 'labels' in option_json.yaxis ? {
                            ...option_json.yaxis.labels,
                            style: 'style' in option_json.yaxis.labels ? option_json.yaxis.labels
                                .style : {
                                    colors: '#010912'
                                }
                        } : {
                            style: {
                                colors: '#010912'
                            }
                        }
                    } : {
                        min: 0,
                        max: parseInt(parseInt(DashboardRef.round(max_value)) + 20),
                        tickAmount: 3,
                        labels: {
                            style: {
                                color: '#010912'
                            }
                        }
                    },
                    legend: 'legend' in option_json ? {
                        ...option_json.legend,
                        show: 'show' in option_json.legend ? option_json.legend.show : false
                    } : {
                        show: false
                    },
                    tooltip: 'tooltip' in option_json ? {
                        ...option_json.tooltip,
                        y: 'y' in option_json.tooltip ? {
                            ...option_json.tooltip.y,
                            formatter: 'formatter' in option_json.tooltip.y ? option_json.tooltip.y
                                .formatter : function(val) {
                                    return val
                                }
                        } : {
                            formatter: function(val) {
                                return val
                            }
                        }
                    } : {
                        y: {
                            formatter: function(val) {
                                return val
                            }
                        }
                    }
                }
                return analyticsBarChartOptions;
            }

            $('#chart-year-select').on('change', function(e) {

                Dashboard.getMemberChartDetails($(this).val())
            });

            Dashboard.getMemberChartDetails($('#chart-year-select').val());

            Dashboard.renderChart = function(records, max_value,name,query_element) {

                let colors = ['#f10937']


                var analyticsBarChart = new ApexCharts(

                    document.querySelector(query_element),
                    Dashboard.manageChartOptions({
                        series: [{
                            name: name,
                            data: Object.values(records)
                        }],
                        colors :colors,
                        xaxis: {
                            categories: Object.keys(records),
                        },
                        yaxis: {
                            max: parseInt(parseInt(Dashboard.round(max_value)) + 20),
                        }
                    })
                );

                analyticsBarChart.render();

            }

            //========== Member Chart End ================

            // ============ Member List =================


            Dashboard.MemberTbls = $('#member_list').DataTable({

                // responsive: true,
                processing: true,
                serverSide: true,
                scrollCollapse: true,
                scrollY: '500px',
                "ajax": {
                    "type": "POST",
                    "url": baseUrl + 'members/member_json_list',
                    "data": function(d) {

                        d._token = $('meta[name="csrf-token"]').attr('content'),
                        d.filter = Dashboard.filterOption,
                        d.options = {
                        }
                        d.status = 1

                    },
                    "dataSrc": function(json) {
                        Dashboard.allMemberDetails = json.data;
                        return json.data;
                    }
                },
                "fnRowCallback" : function(nRow, aData, iDisplayIndex){
                    $("td:first", nRow).html(iDisplayIndex +1);
                return nRow;
                },
                "columns": [
                    {
                        data: "member_id",
                        "render": function(data, type, row) {
                            return row.member_id;
                        }
                    },
                    {
                        data: "member_id",
                        "render": function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: "member_name",
                        'render': function (data, type, row) {
                            var name = `<a class="primary-text-color" href="${baseUrl}members/view/${row.member_id}" >${showTextOnHoverDataTbl(data)}</a>`;
                            return (data ? name : '');
                        },
                    },
                    {
                        data: "member_id",
                        "render": function(data, type, row, meta) {
                            var country_code = row.country_code;
                            var mobile = row.phone;
                            var phone = country_code + ' ' + mobile;
                            return (row.country_code != null) && (row.phone != null) ? '+' + phone : '-';
                        }
                    },
                    {
                        data: "member_type",
                    },
                    {
                        data: "created_at",
                        'render': function (data, type, row) {
                        return (data ? convertLocalDateTimeToUtcDateTime(data,'YYYY-MM-DD') : '');
                        },
                    },

                ],
                "order": [
                    [0, 'desc']
                ],
                'columnDefs': [{
                        'targets': [0], // column index (start from 0)
                        'orderable': false, // set orderable false for selected columns
                    },
                    {
                        "visible": false,
                        "targets": [0]
                    }
                ]
            });

        }

        Dashboard.processExceptions = function(e) {
            showErrorMessage(e);
        };
        Dashboard.initEvents();

    };
</script>
