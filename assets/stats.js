import { Chart,registerables, LineController, LineElement, PointElement, LinearScale, Title } from 'chart.js';

Chart.register(LineController, LineElement, PointElement, LinearScale, Title);

import 'chartjs-adapter-date-fns';
Chart.register(...registerables);


$(document).ready(function (){
    let transactions = $('#data-transactions').attr('data-val');
    let all = $('#data-all').attr('data-val');
    makeTransactionChart(transactions);
    makeAllChart(all);
});

function makeTransactionChart(transactions){
  if(JSON.parse(transactions).length > 0) {
    const ctx = document.getElementById('nb-transaction').getContext('2d');
    fillDataAndLabelsAndMakeChart(transactions,'Toutes les transactions',ctx,'line');
  }
}
function makeAllChart(all){
    const ctx = document.getElementById('nb-all').getContext('2d');
    fillAllChart(ctx,'Toutes les transactions',[],'doughnut','doughnut');
}

function fillChart(ctx,labels,data,label,type){
    const myChart = new Chart(ctx, {
        type: type,
        defaults:{
            borderColor: 'red'
        },
        data: {
            labels: labels,
            datasets: [{
                backgroundColor: "rgba(52,152,219,0.4)",
                pointBackgroundColor: "#a3e635",
                label: 'Transaction',
                data: data,
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        family : 'Helvetica Neue',
                        font: {
                            size: 14
                        }
                    }
                }
            },
            animations: {
                tension: {
                    duration: 1000,
                    easing: 'linear',
                    from: 1,
                    to: 0,
                    loop: true
                }
            },
            scales: {

            }

        }
    });
}
function setBg(){
  const randomColor = Math.floor(Math.random()*16777215).toString(16);
  return "#" + randomColor;
}
function fillAllChart(ctx,labels,data,label,type){
    let dataV = {
        labels: [
            'Red',
            'Blue',
            'Yellow'
        ],
        datasets: [{
            label: 'My First Dataset',
            data: [300, 50, 100],
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)'
            ],
            hoverOffset: 4
        }]
    };
      const myChart = new Chart(ctx,  {
        type: 'doughnut',
        data: dataV,
    });
}
function fillDataAndLabelsAndMakeChart(baseData,label,element,type) {
    let labels = [];
    let data = [];
    JSON.parse(baseData).forEach((v) => {
        let dt = new Date(v.date.date);
        labels.push(dt.getDate() + "-" + (dt.getMonth()) + "-" + dt.getFullYear() + " " + dt.getHours() + ":" + dt.getMinutes());
    })
    JSON.parse(baseData).forEach((v) => {
        data.push(v.nb);
    })
    fillChart(element, labels, data, label, type);
}
