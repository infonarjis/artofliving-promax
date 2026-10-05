startDate = '';
endDate = '';
$(document).ready((function () {
    loadMemberGraph(startDate, endDate);

    // franchise Id:
    $('#franchise_id').change(function () {
        var franchise_id = $(this).val();
        if (franchise_id) {
            let franchise_id_url = btoa(franchise_id);
            let franchiseLoginLink = $("#base_url").val() + '/franchise-login-history/' + franchise_id_url;
            $('#franchiseLoginLink').attr('href', franchiseLoginLink);
        } else {
            $('#franchiseLoginLink').attr('href', $("#base_url").val() + '/franchise-login-history');
        }
        loadMemberGraph(startDate, endDate);
    });
    // Date Picker:
    $('input[name="daterange"]').daterangepicker({
        opens: 'right',
        maxDate: moment(),
        autoApply: false,
        autoUpdateInput: false
    }, function (start, end, label) {
        $('input[name="daterange"]').val(
            start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY')
        );
    });

    var picker = $('input[name="daterange"]').data('daterangepicker');

    // Add Clear button
    picker.container.find('.drp-buttons').prepend(
        '<button type="button" class="btn btn-sm btn-clear" style="float:left;">Clear</button>'
    );

    // Clear button click
    picker.container.on('click', '.btn-clear', function () {
        $('input[name="daterange"]').val('');

        startDate = '';
        endDate = '';

        picker.hide();

        loadMemberGraph(startDate, endDate);
    });
    $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
        startDate = picker.startDate.format('YYYY-MM-DD');
        endDate = picker.endDate.format('YYYY-MM-DD');
        loadMemberGraph(startDate, endDate);
    });
    $('input[name="daterange"]').on('cancel.daterangepicker', function (ev, picker) {
        startDate = '';
        endDate = '';
        loadMemberGraph(startDate, endDate);
    });
}));

// Member Daily Income Graph Get Data : Date : 25-02-2022
function loadMemberGraph(startDate, endDate) {
    var formData = new FormData();
    formData.append('franchise_id', $('#franchise_id').val());
    formData.append('start_date', startDate)
    formData.append('end_date', endDate)
    var url = $("#base_url").val() + '/franchise/get-graph-data';
    ajaxRequest('#resultData', formData, url, 'ajaxMemberGraphResponse');
}

function getDashboardEvent(key = '') {
    var formData = new FormData();
    formData.append('activity_type', key);
    formData.append('franchise_id', $('#franchise_id').val());
    formData.append('start_date', endDate);
    formData.append('end_date', startDate);
    formData.append('checkCount', $('.' + key).text());
    var action = $("#base_url").val() + '/franchise/dashboard-data';
    ajaxRequest($(this), formData, action, 'ajaxChangeStatusResponse');
}

function ajaxChangeStatusResponse(_this, response) {
    if (response.status == 'success') {
        window.location.href = response.redirectUrl;
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", 'No Data Found', "Failed");
    }
}

function ajaxMemberGraphResponse(_this, response) {
    if (response.status == "success") {
        // Append Data:
        if (response.data.franchise_name != '') {
            $('#showfranchiseDetails').removeClass('d-none');
            $('#franchiseName').html(response.data.franchise_name);
            $('#franchiseLastLogin').html(response.data.franchise_last_login);
            // Franchise Total Working Times:
            $('.totalTimeSpend').removeClass('d-none');
            $('#totalTimeSpend').html(response.data.totalTimeSpend);
        } else {
            $('.totalTimeSpend').addClass('d-none');
            $('#showfranchiseDetails').addClass('d-none');
        }
        $('#viewProfileCount').html(response.data.viewProfileCount);
        $('#memberPaymentCount').html(response.data.memberPaymentCount);
        $('#editprofileCount').html(response.data.editprofileCount);
        $('#uploadPhotoCount').html(response.data.uploadPhotoCount);
        $('#assignedMemberCount').html(response.data.assignedMemberCount);
        $('#pendingAssignMemberCount').html(response.data.pendingAssignMemberCount);
        $('#addCommentCount').html(response.data.addCommentCount);
        $('#totalFranchiseCommAmount').html(response.data.totalFranchiseCommAmount);

        // Earning Reports Chart:
        var monthlyPayment = response.data.monthlyPayment;
        var monthlyPaymentTotal = [];
        $.each(monthlyPayment, function (index, value) {
            monthlyPaymentTotal.push(value);
        });
        var recentMonthsName = response.data.recentMonthsName;
        var myArray = [];
        $.each(recentMonthsName, function (index, value) {
            myArray.push(value);
        });

        var options = {
            series: [{
                name: 'Commission Amount',
                data: monthlyPaymentTotal
            }],
            chart: {
                height: 350,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    borderRadius: 20,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },

            xaxis: {
                categories: recentMonthsName,
                position: 'top',
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                crosshairs: {
                    fill: {
                        type: 'gradient',
                        gradient: {
                            colorFrom: '#D8E3F0',
                            colorTo: '#BED1E6',
                            stops: [0, 100],
                            opacityFrom: 0.4,
                            opacityTo: 0.5,
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                }
            },
            yaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false,
                },
                labels: {
                    show: false,
                    formatter: function (val) {
                        return val;
                    }
                }
            },
            title: {
                text: 'Last 12 Months Franchise Total Commission Earn',
                floating: true,
                offsetY: 330,
                align: 'center',
                style: {
                    color: '#444'
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#franchiseCommissionReports"), options);
        chart.render();
    } else {
        ajaxMemberGraphResponse
    }
}
