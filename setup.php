<?php
class ArthurSetup {
    private $config = array();
    private $savedSettings = array();
    
    function loadConfig($configFile = "config.json") {
        try {
            $this->config = json_decode(file_get_contents($configFile));
        }
        catch(Exception $ex) {
            throw new ErrorException("Cannot open config file $configFile");
        }
    }
    
    function parseFile($fileName, $fileDestination, $fileNamespace){
        if(empty($this->config->files->$fileName)) {
            throw new ErrorException("File $fileName not found in config");
        }
        
        $fileContents = file_get_contents($fileName);
        
        echo "== Now in ".$this->config->root.$fileDestination.PHP_EOL;
        
        $matches = array();
        preg_match_all("!<%([\w ]+)%>!u", $fileContents, $matches);
        
        if(!empty($matches)) {
            foreach($matches[1] as $setting) {
                $value = $this->getSettingValue(trim($setting), $fileNamespace);
                $fileContents = str_replace("<%$setting%>", $value, $fileContents);
            }
        }
        
        try {
            file_put_contents($this->config->root.$fileDestination, $fileContents);
            echo "== File ".$this->config->root.$fileDestination." created".PHP_EOL;
        }
        catch (Exception $ex) {
            throw new ErrorException("Cannot write to ".$this->config->root.$fileDestination);
        }
    }
    
    function getSettingValue($settingName, $namespace = "") {
        $value = "";
        
        if(!empty($namespace)) {
            echo "$namespace::";
        }
        
        echo $settingName;
        echo "? ";
        
        if(!empty($this->savedSettings[$namespace."_".$settingName])){
            $value = $this->savedSettings[$namespace."_".$settingName];
            echo $value.PHP_EOL;
        }
        else {
            flush();
            $value = trim(fgets(STDIN));
        }
        
        $this->savedSettings[$namespace."_".$settingName] = $value;
        
        return $value;
    }
    
    function run() {
        $this->loadConfig();
        if(!empty($this->config->files)) {
            foreach($this->config->files as $filename => $file) {
                if(empty($filename)) {
                    throw new ErrorException("File name cannon be empty");
                }
                
                if(empty($file->destination)) {
                    throw new ErrorException("File destination cannot be empty");
                }
                
                if(empty($file->namespace)) {
                    $file->namespace = "";
                }
                
                $this->parseFile($filename, $file->destination, $file->namespace);
            }
        }
    }
}

$setup = new ArthurSetup();
$setup->run();
?>
