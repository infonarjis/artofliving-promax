
$(document).ready((function () {
    if ($("#chart").length) {
        loadMemberGraph();
    }
}));


// ================= APEX GLOBAL MANAGER =================
var apexCharts = {};

function renderApexChart(id, options) {

    const element = document.querySelector(id);

    // Element does not exist on this page
    if (!element) {
        console.warn('ApexCharts element not found:', id);
        return;
    }

    // Destroy existing chart
    if (apexCharts[id]) {
        apexCharts[id].destroy();
        delete apexCharts[id];
    }

    apexCharts[id] = new ApexCharts(element, options);

    apexCharts[id].render();
}

function destroyAllApexCharts() {
    Object.keys(apexCharts).forEach(id => {
        apexCharts[id].destroy();
    });
    apexCharts = {};
}

// Reusable tooltip override for donut charts whose labels
// already contain the count (e.g. "Approved (118)")
var noDuplicateTooltip = {
    y: {
        title: {
            formatter: function (seriesName) {
                return seriesName; // label already has the count baked in
            }
        },
        formatter: function () {
            return ''; // hide the raw value so it doesn't repeat
        }
    }
};

// Member Daily Income Graph Get Data : Date : 25-02-2022
function loadMemberGraph() {
    var formData = new FormData();
    var url = $("#base_url").val() + '/get-graph-data';
    ajaxRequest('#resultData', formData, url, 'ajaxMemberGraphResponse');
}

function ajaxMemberGraphResponse(_this, response) {
    if (response.status !== "success") return;

    const d = response.data;

    // ================= COUNTS =================
    $('#allMemberCount').html(d.allMemberCount);
    $('#approvedMemberCount').html(d.approvedMemberCount);
    $('#unapprovedMemberCount').html(d.unapprovedMemberCount);
    $('#suspendedMemberCount').html(d.suspendedMemberCount);
    $('#maleMemberCount').html(d.maleMemberCount);
    $('#femaleMemberCount').html(d.femaleMemberCount);
    $('#paidMemberCount').html(d.paidMemberCount);
    $('#expiredMemberCount').html(d.expiredMemberCount);
    $('#notPaidMemberCount').html(d.notPaidMemberCount);

    // ================= CHART 1 : MEMBER STATUS =================
    renderApexChart("#chart", {
        series: [d.approvedMemberCount, d.unapprovedMemberCount, d.suspendedMemberCount],
        chart: { width: 350, type: 'donut' },
        labels: [
            `Approved (${d.approvedMemberCount})`,
            `Unapproved (${d.unapprovedMemberCount})`,
            `Suspended (${d.suspendedMemberCount})`
        ],
        colors: ['#71dd37', '#233446', '#E91E63'],
        dataLabels: { enabled: false },
        legend: { position: 'right' },
        tooltip: noDuplicateTooltip
    });

    // ================= CHART 2 : PLAN STATUS =================
    renderApexChart("#chart2", {
        series: [d.paidMemberCount, d.notPaidMemberCount, d.expiredMemberCount],
        chart: { width: 350, type: 'donut' },
        labels: [
            `Paid (${d.paidMemberCount})`,
            `Not Paid (${d.notPaidMemberCount})`,
            `Expired (${d.expiredMemberCount})`
        ],
        colors: ['#71dd37', '#03c3ec', '#ff3e1d'],
        dataLabels: { enabled: false },
        legend: { position: 'right' },
        tooltip: noDuplicateTooltip
    });

    // ================= CHART 3 : EARNING LINE =================
    renderApexChart("#chart3", {
        series: [{
            name: "Earning Reports",
            data: d.monthlyPayment
        }],
        chart: { height: 350, type: 'line', zoom: { enabled: false } },
        stroke: { curve: 'straight' },
        xaxis: { categories: d.recentMonthsName }
    });

    // ================= CHART 4 : GENDER =================
    renderApexChart("#chartGender", {
        series: [d.maleMemberCount, d.femaleMemberCount],
        chart: { width: 350, type: 'donut' },
        labels: [
            `Male (${d.maleMemberCount})`,
            `Female (${d.femaleMemberCount})`
        ],
        colors: ['#71dd37', '#03c3ec'],
        dataLabels: { enabled: false },
        legend: { position: 'right' },
        tooltip: noDuplicateTooltip
    });

    // ================= CHART 5 : PHOTO RADIAL =================
    renderApexChart("#chart4", {
        series: [d.photo1, d.photo2, d.photo3, d.photo4],
        chart: { width: 350, type: 'radialBar' },
        labels: ['Photo 1', 'Photo 2', 'Photo 3', 'Photo 4'],
        plotOptions: {
            radialBar: {
                dataLabels: {
                    name: { show: true },   // keeps "Photo 4" label
                    value: { show: false }  // removes the "3%" percentage
                }
            }
        },
        colors: ['#2E93fA', '#00E396', '#FEB019', '#FF4560'],
        legend: {
            show: true,
            position: 'right',
            formatter: function (seriesName, opts) {
                return seriesName + ": " + opts.w.globals.series[opts.seriesIndex];
            }
        }
    });

    // ================= CHART 6 : STACKED MEMBERS =================
    renderApexChart("#chart5", {
        series: [
            { name: 'Approved', data: d.totalApprovedMembers },
            { name: 'Unapproved', data: d.totalUnapprovedMembers },
            { name: 'Suspended', data: d.totalSuspendedMembers }
        ],
        chart: { type: 'bar', height: 350, stacked: true },
        colors: ['#00e396', '#feb019', '#ff3e1d'],
        xaxis: { categories: d.recentMonthsName },
        legend: { position: 'top' }
    });
}