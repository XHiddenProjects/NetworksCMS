$(document).ready(()=>{
    if(document.querySelector('#nw_users')){
        const mixedChart = new Chart(document.querySelector('#nw_users'), {
            data: {
                datasets: [{
                    type: 'bar',
                    label: getDict()['charts']['userGraph']['label'],
                    backgroundColor: random(RGBA,0,5,true,{alpha: 0.5}),
                    data: countUsers()
                }],
                labels: [...rangeYear(DELAY_YEAR_INCREMENT(5))]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins:{
                    title: {
                        display: true,
                        text: getDict()['charts']['userGraph']['title']
                    }
                }
                
            }
        });
    }
});
