$(document).ready(()=>{
    if($('#users_chart').length>0){
        const years = chartRangeYear(),
        users = sendRequest(`${DOMAIN}/api/user/list?joined=${years[0]}-${years[years.length-1]}`,'GET',false,true),
        lang = sendRequest(`${DOMAIN}/plugins/core/core_lang.php`,'GET',false,true)['dictionary'],
        data = {};
        for(let y in years) data[years[y]] = 0;
        for(let u in users){
            const getJoinedYear = users[u]['joined'].match(/[\d]{4}/);
            data[getJoinedYear[0]]+=1;
        }
        new Chart($('#users_chart').eq(0), {
            type: 'bar',
            data: {
                labels: years,
                datasets: [{
                    label: lang['users'],
                    data: Object.values(data),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins:{
                    title:{
                        display: true,
                        text: lang['users'],
                        align: 'center'
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            stepSize: 1
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    }
    if($('#users_social').length>0){
        const years = chartRangeYear(),
        forums = sendRequest(`${DOMAIN}/api/forums/list`,'GET',false,true),
        topics = sendRequest(`${DOMAIN}/api/topics/list`,'GET',false,true),
        replies = sendRequest(`${DOMAIN}/api/replies/list`,'GET',false,true),
        lang = sendRequest(`${DOMAIN}/plugins/core/core_lang.php`,'GET',false,true)['dictionary'],
        data = {forums:{},topics:{},replies:{}};
        for(let y in years){
            data['forums'][years[y]] = 0;
            data['topics'][years[y]] = 0;
            data['replies'][years[y]] = 0;
        }
        for(let forum in forums){
            const getForumYear = forums[forum]['created'].match(/[\d]{4}/);
            data[getForumYear[0]]+=1;
        }
        for(let reply in replies){
            const getReplyYear = replies[reply]['created'].match(/[\d]{4}/);
            data[getReplyYear[0]]+=1;
        }
        for(let topic in topics){
            const getTopicYear = topics[topic]['created'].match(/[\d]{4}/);
            data[getTopicYear[0]]+=1;
        }
        new Chart($('#users_social').eq(0), {
            type: 'line',
            data: {
                datasets: [{
                    label: lang['forums'],
                    data: Object.values(data['forums']),
                    order: 1
                }, {
                    label: lang['topics'],
                    data: Object.values(data['topics']),
                    type: 'line',
                    order: 2
                }, {
                    label: lang['replies'],
                    data: Object.values(data['replies']),
                    type: 'line',
                    order: 3
                }],
                labels: years
            },
            options: {
                responsive: true,
                plugins:{
                    title:{
                        display: true,
                        text: lang['overall_social'],
                        align: 'center'
                    }
                },
                scales: {
                    y: {
                        ticks:{
                            stepSize: 1
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    }
    if($('#users_self_social').length>0){
        const topics = sendRequest(`${DOMAIN}/api/topics/list`,'GET',false,true),
        replies = sendRequest(`${DOMAIN}/api/replies/list`,'GET',false,true),
        lang = sendRequest(`${DOMAIN}/plugins/core/core_lang.php`,'GET',false,true)['dictionary'],
        data = {topics:0,replies:0};
        for(let reply in replies){
            data['topics']+=1;
        }
        for(let topic in topics){
            data['replies']+=1;
        }
        new Chart($('#users_self_social').eq(0), {
            type: 'doughnut',
            data: {
                labels: [lang['topics'],lang['replies']],
                datasets:[{
                    data: [data['topics'],data['replies']]
                    //backgroundColor: generateColor()
                }]
            },
            options: {
                responsive: true,
                plugins:{
                    title:{
                        display: true,
                        text: lang['social'],
                        align: 'center'
                    }
                }
            }
        });
    }
});