<?php
    require_once("./protected/config.php");
    
    session_start();

    // debug stuff for development
    $debug_string = "";

    function debug($var, $name = ""){
        global $debug_string;
    
        if($name != ""){
            $debug_string .= "$name: ";
        }
        
        $debug_string .= $var;
        $debug_string .= "\n<br/>";
    }

    // setting up the include path
    $inc_path = get_include_path();
    debug($inc_path, 'before');
 

    set_include_path($inc_path . PATH_SEPARATOR . implode($lib_paths, PATH_SEPARATOR) );
    debug(get_include_path(), 'after');
 
    
    // Zend autoloading
    require_once 'Zend/Loader/Autoloader.php';
    $loader = Zend_Loader_Autoloader::getInstance();
    
    
    // setting up zend mail
    $config = array(
                'ssl' => 'tls',
                'port' => 587,
                'auth' => 'login',
                'username' => $mail_username,
                'password' => $mail_password);
 
    $transport = new Zend_Mail_Transport_Smtp($mail_server, $config);
    
    
    
    // my helpers
    function link_to( $target ){
        global $base_path;
        return ($base_path . $target);
    }
    
    class PostRequest
    {
        public function __construct(){
            
        }  
        
        public function __get($name){
            if(isset($_POST["$name"])){
                return $_POST["$name"];
            } else {
                throw new Exception("No post value for name $name.");
            }
        }
    }
    
    $post = new PostRequest();
    
    class FileUpload{
        public function __construct($values){
            $this->name         = $values["name"];
            $this->type         = $values["type"];
            $this->tmp_name     = $values["tmp_name"];
            $this->error        = $values["error"];
            $this->size         = $values["size"];
        }
    
        public function has_error(){
            return ($this->error != 0);
        }
        
        public function get_error(){
            $message = "";
            switch ($this->error) { 
                case UPLOAD_ERR_INI_SIZE: 
                    $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini"; 
                    break; 
                case UPLOAD_ERR_FORM_SIZE: 
                    $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"; 
                    break; 
                case UPLOAD_ERR_PARTIAL: 
                    $message = "The uploaded file was only partially uploaded"; 
                    break; 
                case UPLOAD_ERR_NO_FILE: 
                    $message = "No file was uploaded"; 
                    break; 
                case UPLOAD_ERR_NO_TMP_DIR: 
                    $message = "Missing a temporary folder"; 
                    break; 
                case UPLOAD_ERR_CANT_WRITE: 
                    $message = "Failed to write file to disk"; 
                    break; 
                case UPLOAD_ERR_EXTENSION: 
                    $message = "File upload stopped by extension"; 
                    break; 
    
                default: 
                    $message = "Unknown upload error"; 
                    break; 
            } 
            return $message;
        }
        
        public function get_data(){
            if(!$this->has_error()){
                return file_get_contents($this->tmp_name);
            }
        }
    }
    
    $files = array();
    foreach ($_FILES as $name => $upload) {
        $files[$name] = new FileUpload($upload);
    }
    
    
    
    function addMessage( $msg ){
        if( ! isset( $_SESSION['message_cache'] ) ) {
            $_SESSION['message_cache'] = array();
        }
        $_SESSION['message_cache'][] = $msg;
    }
    
    function printMessages(){
        foreach($_SESSION['message_cache'] as $msg){
            echo "<span class='flash_message'>$msg</span><br/>\n";
        }
        $_SESSION['message_cache'] = array();
    }