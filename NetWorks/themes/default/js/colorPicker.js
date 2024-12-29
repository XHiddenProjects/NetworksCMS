$(document).ready(()=>{
    $('.color-picker').each((index,el)=>{
        const picker = new Alwan(el,{
            parent: el.parentNode,
            popover: true,
            classname: 'color-picker-ui btn',
            theme: 'light',
            disabled: (el.hasAttribute('disabled') ? true : false),
            closeOnScroll: true,
            copy: true,
            swatches: [
                'white',
                'red',
                'orange',
                'yellow',
                'green',
                'blue',
                'black'
            ]
        });
        picker.on('color',function(color){
            color.source.config.parent.querySelector('input').value = color.hex;
        });
    });
});