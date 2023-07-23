<?php
if (! function_exists('mobilenumb')) {
    function mobilenumb($number){
        $phoneNumber = str_replace('+', '', preg_replace('/[\s-]+/', '', $number));  
        $phoneNumber = preg_replace('/[\s_]+/', '',$phoneNumber); 
        
        if(strlen($phoneNumber) == 10 && substr($phoneNumber, 0, 1) != 0 ){
            $phoneNumber = '0'.$phoneNumber;
            }
        
            if(substr($phoneNumber, 0, 2)=='88'){ 
            return 	substr($phoneNumber, 2, 11);
            }elseif(substr($phoneNumber, 0, 1)=='8'){
            return 	substr($phoneNumber, 1, 11);  
            }else{
            return 	substr($phoneNumber, 0, 11);  
            }
        return $phoneNumber;
    }
}


if (! function_exists('cronjob_exists')) {
    function cronjob_exists($command){

        $cronjob_exists=false;
    
        exec('crontab -l', $crontab);
    
    
        if(isset($crontab)&&is_array($crontab)){
    
            $crontab = array_flip($crontab);
    
            if(isset($crontab[$command])){
    
                $cronjob_exists=true;
    
            }
    
        }
        return $cronjob_exists;
    }
}


if (! function_exists('append_cronjob')) {
    function append_cronjob($command){
        cronjob_exists($command);
      
          if(is_string($command)&&!empty($command)&&cronjob_exists($command)===FALSE){
       
              //add job to crontab
              exec('echo -e "`crontab -l`\n'.$command.'" | crontab -', $output); 
            // shell_exec('echo -e "`crontab -l`\n'.$command.'" | crontab -', $output); 
          }
      
          return $output;
      }
}