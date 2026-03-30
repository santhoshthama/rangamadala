<?php 
trait Controller
{
    public function view($name,$data=[])
    {
        // First try .view.php extension
        $filename = "../app/views/" . $name . ".view.php";
        if (file_exists($filename)) {
               extract($data);
               require $filename;
               return;
        }
        
        // Fallback to .php extension
        $filename = "../app/views/" . $name . ".php";
        if (file_exists($filename)) {
               extract($data);
               require $filename;
               return;
        }
        
        // If neither exists, show 404
        require "../app/views/_404.view.php";
    }
    public function getModel($name)
    {
        $modelFile = ucfirst($name) . ".php";
        if (file_exists("../app/models/" . $modelFile)) {
            require "../app/models/" . $modelFile;
            return new $name();
        }
        return null;
    }

    public function getQueryParam($key = null, $default = null) {
            // Get all GET parameters except 'url'
            $params = $_GET;
            if(isset($params['url'])) {
                unset($params['url']);
            }
            
            // If a specific key is requested
            if($key !== null) {
                return isset($params[$key]) ? $params[$key] : $default;
            }
            
            // Return all query parameters
            return $params;
        }
}
