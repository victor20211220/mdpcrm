<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends Admin_Controller
{
    private $targetPath;

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mdl_uploads');
        $this->targetPath = getcwd() . '/uploads/customer_files';
    }

    /**
     * @param $customerId
     * @param $urlKey
     * @return bool
     */
    public function upload_file($customerId, $urlKey)
    {
        $this->create_dir($this->targetPath . '/');

        if (!empty($_FILES)) {
            $tempFile = $_FILES['file']['tmp_name'];
            $fileName = preg_replace('/\s+/', '_', $_FILES['file']['name']);
            $targetFile = $this->targetPath . '/' . $urlKey . '_' . $fileName;
            $fileExists = file_exists($targetFile);

            if (!$fileExists)//If file does not exists then upload
            {
                $data = [
                    'client_id'          => $customerId,
                    'url_key'            => $urlKey,
                    'file_name_original' => $fileName,
                    'file_name_new'      => $urlKey . '_' . $fileName
                ];
                $this->Mdl_uploads->create($data);

                move_uploaded_file($tempFile, $targetFile);
            } else {
                echo lang('error_dublicate_file');;
                http_response_code(404);
            }

        } else {
            return $this->show_files($urlKey, $customerId);
        }
    }

    /**
     * Create dir
     * @param $path
     * @param string $chmod
     * @return bool
     */
    public function create_dir($path, $chmod = '0777')
    {
        if (!(is_dir($path) OR is_link($path))) {
            return mkdir($path, $chmod);
        } else {
            return false;
        }
    }

    /**
     * @param $urlKey
     * @param null $customerId
     * @return bool
     */
    public function show_files($urlKey, $customerId = null)
    {
        $result = [];
        $path = $this->targetPath;

        $files = scandir($path);
        if ($files === false) {
            return false;
        }

        foreach ($files as $file) {
            if ('.' != $file && '..' != $file && strpos($file, $urlKey) !== false) {
                $obj['name'] = substr($file, strpos($file, '_', 1) + 1);
                $obj['fullname'] = $file;
                $obj['size'] = filesize($path . '/' . $file);
                $obj['fullpath'] = $path . '/' . $file;
                $result[] = $obj;
            }
        }

        echo json_encode($result);
    }

    /**
     * Delete file
     * @param $urlKey
     * @return bool
     */
    public function delete_file($urlKey)
    {
        $fileName = $_POST['name'];
        $fullPath = "{$this->targetPath}/{$urlKey}_$fileName";

        if (file_exists($fullPath) && is_writable($fullPath)) {
            $this->Mdl_uploads->delete($urlKey, $fileName);
            unlink($this->targetPath . '/' . $urlKey . '_' . $fileName);

            return true;
        }

        return false;
    }
}
