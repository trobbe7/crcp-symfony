// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

$.ajax({
url:"./charts/pie",
method:"GET",
success:function(data)  {
console.log(data)

// Area Pie - Poids
var ctx = document.getElementById("chartPoids");
var myLineChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ["Téléphone", "Mail", "DEC"].reverse(),
    datasets: [{
      data: [data["chartTel"], data["chartMail"], data["chartDEC"]].reverse(),
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
