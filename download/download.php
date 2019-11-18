<?php

class DownloadController extends CrudController {

    public function accessRules() {
        return array(
            array('allow',  // allow all users to perform 'view' actions
                'actions'=>array('index'),
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex() {
        if (!empty($_GET['file'])) {
            $file = getenv('DYNAMIC_DIRECTORY');

            $maxRead = 20 * 1024 * 1024; // 20MB
    
            $fileName = $_GET['file'];
    
            $fh = fopen($file . $fileName, 'r');
    
            header('Content-Type: application/octet-stream');
            header('Content-Type: ' . $this->extensao($_GET['file']));
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
    
            while (!feof($fh)) {
                echo fread($fh, $maxRead);
                ob_flush();
            }
    
            exit;
        } else {
            echo 'empty!!!';
        }
        
    }

    public function extensao($value)
    {
        $formato = explode('.', $value);
        
        $tipos = array(
            "htm" => "text/html",
            "exe" => "application/octet-stream",
            "zip" => "application/zip",
            "doc" => "application/msword",
            "jpg" => "image/jpg",
            "php" => "text/plain",
            "xls" => "application/vnd.ms-excel",
            "ppt" => "application/vnd.ms-powerpoint",
            "gif" => "image/gif",
            "pdf" => "application/pdf",
            "txt" => "text/plain",
            "html"=> "text/html",
            "png" => "image/png",
            "jpeg"=> "image/jpg"
        );

        $ext = NULL;
        foreach($tipos as $key => $value){
            if(end($formato) == $key){
                $ext = $value;
                break;
            }
        }

        return $ext;

    }

}
