<?php
    function AutoLoader(): void{
        //libs
        foreach(array_diff(scandir(directory: dirname(path: __FILE__).'/libs'),['.','..']) as $file){
            if(is_file(filename: dirname(path: __FILE__)."/libs/$file"))
                include_once dirname(path: __FILE__)."/libs/$file";
        }
        //plugins
        foreach(array_diff(scandir(directory: dirname(path: __FILE__).'/plugins'),['.','..']) as $plugin){
            if(file_exists(filename: dirname(path: __FILE__).'/plugins/'.$plugin.'/'.$plugin.'.plg.php'))
                include_once dirname(path: __FILE__)."/plugins/$plugin/$plugin.plg.php"; 
        }
        //modals
        include_once dirname(path: __FILE__).'/Models/Model.php';
        foreach(array_diff(scandir(directory: dirname(path: __FILE__).'/Models'),['.','..']) as $models){
            if(file_exists(filename: dirname(path: __FILE__)."/Models/$models")&&$models!=='Model.php')
                include_once dirname(path: __FILE__)."/Models/$models"; 
        }
        
        //api
        foreach(array_diff(scandir(directory: dirname(path: __FILE__).'/Controllers/api'),['.','..']) as $api){
            if(file_exists(filename: dirname(path: __FILE__)."/Controllers/api/$api"))
                include_once dirname(path: __FILE__)."/Controllers/api/$api"; 
        }
        include_once dirname(path: __FILE__).'/libs/PHPMailer/src/Exception.php';
        include_once dirname(path: __FILE__).'/libs/PHPMailer/src/PHPMailer.php';
        include_once dirname(path: __FILE__).'/libs/PHPMailer/src/SMTP.php';
    }
    AutoLoader();

?>