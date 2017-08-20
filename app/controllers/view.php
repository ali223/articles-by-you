<?php


namespace App\controllers;

class View {
    private $_headerFile;
    private $_contentFile;
    private $_footerFile;
    
    private $_data = [];
    
    public function __construct($headerFile = "views/header.php", $contentFile, $footerFile="views/footer.php"){
        $this->_headerFile = $headerFile;
        $this->_contentFile = $contentFile;
        $this->_footerFile = $footerFile;
        
    }
    
    public function setHeaderFile($headerFile){
        $this->_headerFile = $headerFile;
    }
    
    public function getHeaderFile(){
        return $this->_headerFile;
    }
    
    public function setContentFile($contentFile){
        $this->_contentFile = $contentFile;
    }
    
    public function getContentFile(){
        return $this->_contentFile;
    }
    
    public function setFooterFile($footerFile){
        $this->_footerFile = $footerFile;
    }
    
    public function getFooterFile(){
        return $this->_footerFile;
    }
    
    public function setData($key, $value){
       
        $this->_data[$key] = $value;        
        
        
    }
    
    public function getData($key){
        return $this->_data[$key];      
    }
    
    public function renderView(){
        if(!file_exists($this->_headerFile)){
            throw new Exception("Template Header File " . $this->_headerFile . " does not exist");
        }
        if(!file_exists($this->_contentFile)){
            throw new Exception("Template Content File " . $this->_contentFile . " does not exist");
        }
        if(!file_exists($this->_footerFile)){
            throw new Exception("Template Footer File " . $this->_footerFile . " does not exist");
        }
        
        
        extract($this->_data);
        
        include($this->_headerFile);
        include($this->_contentFile);
        include($this->_footerFile);
        
        
    }
    
}
