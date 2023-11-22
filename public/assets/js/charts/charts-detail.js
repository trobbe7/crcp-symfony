// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

var id = result_id.innerHTML;

$.ajax({
  url:"../../../charts/historique/" + id,
  method:"GET",
  success:function(data)  {

var ctx = document.getElementById("chartBar_detail");
var ctx2 = document.getElementById("chartPie_detail");

// Bar Chart - Détail

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Téléphone", "Mail", "DEC"],
    datasets: [{
      label: "Traitements",
      data: [data["chartTel"], data["chartMail"], data["chartDEC"]],
      backgroundColor: ['#4e73df', '#00ff00', '#f6c23e'],
      hoverBackgroundColor: ['#4e73df', '#00ff00', '#f6c23e'],
      hoverBorderColor: "rgba(234, 236, 244, 1)",
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 6
        },
        maxBarThickness: 25,
      }],
      yAxes: [{
        ticks: {
          maxTicksLimit: 5,
          padding: 10,
          beginAtZero: true,
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: false
    },
    tooltips: {
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
  }
});

// Pie Chart - Détail

var data_total = data["chartTel"] + data["chartMail"] + data["chartDEC"];
var data_tel_format = (data["chartTel"] * 100) / data_total;
var data_mail_format = (data["chartMail"] * 100) / data_total;
var data_dec_format = (data["chartDEC"] * 100) / data_total;

new Chart(ctx2, {
  type: 'doughnut',
  data: {
    labels: ["Téléphone", "Mail", "DEC"].reverse(),
    datasets: [{
      data: [data_tel_format.toFixed(1), data_mail_format.toFixed(1), data_dec_format.toFixed(1)].reverse(),
      backgroundColor: ['#4e73df', '#00ff00', '#f6c23e'].reverse(),
      hoverBackgroundColor: ['#4e73df', '#00ff00', '#f6c23e'].reverse(),
      hoverBorderColor: "rgba(234, 236, 244, 1)",
    }],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      callbacks: {
        label: function(tooltipItem, data) {
            return data.labels[tooltipItem.index] + ': ' + data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] + '%';
        }
      },
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
    legend: {
      display: false,
      reverse: true,
    },
    cutoutPercentage: 80,
  },
});
},
});
