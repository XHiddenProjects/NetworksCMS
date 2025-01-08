$(document).ready(()=>{
    if(navigator.geolocation){
        (new GeoLoc()).watch().then((s)=>{
            const lat = s.coords.latitude,
            lon = s.coords.longitude;
            $.getJSON(`https://api.weather.gov/points/${lat},${lon}`,(results)=>{
                if(document.querySelector('.weather_api')){
                    const loc = `${results['properties']['relativeLocation']['properties']['city']}, ${results['properties']['relativeLocation']['properties']['state']}`;
                    document.querySelector('.weather_api img').alt = loc
                    document.querySelector('.weather_location').innerText = loc;
                    $.getJSON(`${results['properties']['forecastHourly']}`,(results)=>{
                        const periods = results['properties']['periods'][0];
                        document.querySelector('.weather_api img').src = periods['icon'];
                        document.querySelector('.weather_api .weather_api_desc').innerHTML = `${periods['temperature']}&#176;${periods['temperatureUnit']} &#8212; ${periods['shortForecast']}`;
                        $('.weather_api_img').each((i,e)=>{
                            e.src = e.src.replace(/small/,'medium');
                        });
                    });
                }
            });
        });
    }
});