<?php

class Syncsmssms_Download_log implements Syncsmssms_Register_Interface
{
    protected $log_directory;

    public function __construct()
    {
        $upload_dir          = wp_upload_dir();
        $this->log_directory = $upload_dir['basedir'] . '/syncsms-woocommerce-logs/';
    }

    public function register()
    {
        add_submenu_page(null, '', '', 'manage_options', 'syncsms-download-file', array( $this, 'download' ));
    }

    public function download()
    {
        global $wp_filesystem;
        
        if (isset($_GET['file'])) {
            $logFile = $this->log_directory . $_GET['file'] . '.log';

            if (file_exists($logFile)) {
                header('Content-Description: File Transfer');
                header('Content-Type: text/plain');
                header('Content-Disposition: attachment; filename="' . basename($logFile) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($logFile));
                ob_clean();
                flush();
                echo $wp_filesystem->get_contents($logFile);
            }
        }
        exit;
    }
}
