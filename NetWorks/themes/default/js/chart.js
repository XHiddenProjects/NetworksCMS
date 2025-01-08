$(document).ready(()=>{
    //Plugins
    Chart.register({
        id: 'DoughnutText',
        beforeDraw: (chart) => {
            // Retrieve the options from the chart's config
            const { options } = chart.config;
            const { DoughnutText } = options.plugins || {};
            let text = DoughnutText.text || '80%', // Default text
            fontSize = DoughnutText.size || (chart.height / 114).toFixed(2), // Default font size
            fontFamily = DoughnutText.family || 'sans-serif', // Default font family
            textColor = DoughnutText.color || '#000', // Default text color
            textItalic = DoughnutText.italic||false,
            textBold = DoughnutText.bold||false;
        
            let width = chart.width;
            let height = chart.height;
            let ctx = chart.ctx;
        
            ctx.restore();
            ctx.font = `${textBold? 'bold' : ''} ${textItalic ? 'italic' : ''} ${fontSize}em ${fontFamily}`;
            ctx.textBaseline = 'middle';
            ctx.fillStyle = textColor;
        
            // Calculate text position
            let textX = Math.round((width - ctx.measureText(text).width) / 2);
            let textY = height / 1.85;
        
            // Draw the text
            ctx.fillText(text, textX, textY);
            ctx.save();
        },
    });
    if(document.querySelector('#nw_users')){
        new Chart(document.querySelector('#nw_users'), {
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
                    },
                    DoughnutText: false
                }
                
            }
        });
    }
    if(document.querySelector('#nw_posts')){
        new Chart(document.querySelector('#nw_posts'), {
            type: 'doughnut',
            data: {
                labels: [
                    getDict()['Posts']['forum']+getDict()['pleural'],
                    getDict()['Posts']['replies'],
                    getDict()['Posts']['topic']+getDict()['pleural']
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [countForums(), countReplies(), countTopics()],
                    backgroundColor: random(RGB,0,3,true),
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins:{
                    DoughnutText: {
                        text: `${MLMath.format.fixed(MLMath.stats.mean(countForums(),countReplies(),countTopics()),2)}%`,
                        color: random(RGB,0,1,false)
                    }
                }
            }
        });
    }
});
