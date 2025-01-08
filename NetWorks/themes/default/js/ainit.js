//Tooltip initiate
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
[...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

// Initialize the variables

var toRelPath = ()=>{
    let path='';
    if((new URLParse()).getPath().filter((i)=>{return i!=='';}).length>2){
    for(let i=0;i<(new URLParse()).getPath().filter((i)=>{return i!=='';}).length;i++){
        path+='.';
        if(path.match(/\.\.(?!\/)/)) path+='/';
    }
    if(path.match(/\.\.\/\.$/)) path=path.replace(/\.$/,'');
    else if(path.match(/\.$/)) path += '/';
    else if(path.match(/\/\//)) path = path.replace(/\/\//,'/');
    }else path = './';
    return path;
};

let lang=null, dict=null;
(new Request(`${toRelPath()}assets/php/config.php?type=config&value=lang`)).send().onSuccess((d)=>{
    d = JSON.parse(d);
    lang = d['success'];
});

(new Request(`${toRelPath()}languages/${lang}.json`)).send().onSuccess((d)=>{
    dict = JSON.parse(d);
});


// Constants
const RGB = 'rgb',
RGBA = 'rgba',
NUMBER = 'number',
DOMAIN = '',
/**
 * Names of the months
 * @returns {String[]} The Names of the months
 */
getMonths = ()=>{
    return Object.values(dict['dateTimes']['months']);
},
/**
 * Returns the range of years
 * @param {Number} startYear Starting year
 * @param {Number} count The future or past years (represented as +/-. Ex: 5, -5)
 * @returns {Number|Number[]} The range of years
 */
rangeYear = (startYear=(new Date()).getFullYear(), count=5)=>{
    const years = [];
    if(count<0){
        for(let i=startYear;i>=(startYear-count);i--) years.push(i);
        return years;
    }else if(count>0){
        for(let i=startYear;i<=(startYear+count);i++) years.push(i);
        return years;
    }else return startYear;
},
/**
 *  Returns the starting year of the increment of the delay
 * @param {Number} delay The delay of the increment
 * @param {Number} year The year to check in range of increment change. Starting point if not set
 * @returns {Number} The starting year of the increment of the delay
 */
DELAY_YEAR_INCREMENT = (delay,year=(new Date()).getFullYear())=>{
    let startDate=null;
    if(!sessionStorage.getItem('nw_delayYearIncrement')){
        sessionStorage.setItem('nw_delayYearIncrement',[year,year+delay]);
        startDate = year;
    }
    else startDate = sessionStorage.getItem('nw_delayYearIncrement')[0];
    if(year > sessionStorage.getItem('nw_delayYearIncrement')[1]){
        sessionStorage.setItem('nw_delayYearIncrement',[year,year+delay]);
        return year;
    }else return parseInt(startDate);
},
/**
 * Gets Dictionary
 * @returns {String} Returns the dictionary
 */
getDict = ()=>{
    return dict;
},
/**
 * Counts the number of users
 * @param {Number} addYears The number of years to add
 * @returns {String[]} The number of users
 */
countUsers = (addYears=5)=>{
    let numUsers=[];
    (new Request(`${toRelPath()}api/users/limit?by=year&startYear=${(new Date()).getFullYear()}&endYear=${(new Date().getFullYear() + addYears)}`)).send().onSuccess((d)=>{
        d = JSON.parse(d);
        const yearData = {};
        rangeYear((new Date()).getFullYear(),addYears).forEach((years)=>{
            yearData[years] = 0;
        });
        d['success'].forEach((a)=>{
            const match = a['accCreated'].match(/(\d{4})/);
            yearData[parseInt(match[0])]+=1;
        });
        numUsers = Object.values(yearData);
    });
    return numUsers;
},

countForums = ()=>{
    let forumNum=0;
    (new Request(`${toRelPath()}api/forums/list`)).send().onSuccess((d)=>{
        d = JSON.parse(d);
        forumNum = d['success'].length;
    });
    return forumNum;
},
countTopics = ()=>{
    let forumNum=0;
    (new Request(`${toRelPath()}api/topics/list`)).send().onSuccess((d)=>{
        d = JSON.parse(d);
        forumNum = d['success'].length;
    });
    return forumNum;
},
countReplies = ()=>{
    let forumNum=0;
    (new Request(`${toRelPath()}api/replies/list`)).send().onSuccess((d)=>{
        d = JSON.parse(d);
        forumNum = d['success'].length;
    });
    return forumNum;
}

/**
 * Returns the random number
 * @param {String} type The type of randomness
 * @param {Number} min The minimum number
 * @param {Number} max The maximum number
 * @param {Boolean} [inclusive=false] If the range is inclusive
 * @param {Object} [options={}] Additional options
 * @returns {(Number|String[])} The random number
 */
random = (type, min,max,inclusive=false,options={})=>{
    switch(type.toLocaleLowerCase()){
        case RGB:
            const c1 = [];
            if(inclusive){
                for(let i=min;i<=max;i++)
                    c1.push(`rgb(${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)})`);
            }else{
                for(let i=min;i<max;i++)
                    c1.push(`rgb(${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)})`);
            }
            return c1;
        case RGBA:
            const c2 = [];
            if(inclusive){
                for(let i=min;i<max;i++)
                    c2.push(`rgba(${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)},${options.hasOwnProperty('alpha') ? options['alpha'] : (Math.random() === 1 ? 1 : Math.random()).toFixed(1)})`);
            Math.random().toFixed(1)
            Math.random().toFixed(1)
            }else{
                for(let i=min;i<=max;i++)
                    c2.push(`rgba(${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)},${options.hasOwnProperty('alpha') ? options['alpha'] : (Math.random() === 1 ? 1 : Math.random()).toFixed(1)})`);
            }
            return c2;
        default:
            if(inclusive) return Math.floor(Math.random()*(max-min+1))+min;
            else return Math.floor(Math.random()*(max-min))+min;
    }
}