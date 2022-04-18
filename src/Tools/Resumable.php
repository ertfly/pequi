<?php

namespace Pequi\Tools;

use PequiPHP\Session;

class Resumable
{

    private static $diretorioFiles = null;
    private static $diretorioUpload = null;

    public static function processarArquivo($diretorio, array $extensoesPermitidas)
    {
        self::$diretorioFiles = PATH_UPLOADS;
        self::$diretorioUpload = PATH_UPLOADS . 'temporario' . DS;
        // loop through files and move the chunks to a temporarily created directory
        if (!empty($_FILES)) {
            foreach ($_FILES as $file) {

                // check the error status
                if ($file['error'] != 0) {
                    throw new \Exception('Erro ' . $file['error'] . ' no arquivo ' . $_POST['resumableFilename']);
                }

                // init the destination file (format <filename.ext>.part<#chunk>
                // the file is stored in a temporary directory
                if (isset($_POST['resumableIdentifier']) && trim($_POST['resumableIdentifier']) != '') {
                    $temp_dir = self::$diretorioUpload . $_POST['resumableIdentifier'];
                }
                $dest_file = $temp_dir . '/' . $_POST['resumableFilename'] . '.part' . $_POST['resumableChunkNumber'];

                // create the temporary directory
                if (!is_dir($temp_dir)) {
                    mkdir($temp_dir, 0777, true);
                }

                // move the temporary file
                if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
                    throw new \Exception('Erro ao salvar parte ' . $_POST['resumableChunkNumber'] . ' do arquivo do upload para ' . $_POST['resumableFilename']);
                } else {
                    // check if all the parts present, and create the final destination file
                    Session::data('arquivo', $_POST['resumableFilename']);
                    createFileFromChunks($temp_dir, $_POST['resumableFilename'], $_POST['resumableChunkSize'], $_POST['resumableTotalSize'], $_POST['resumableTotalChunks']);
                }
            }
        }

        if (is_file(self::$diretorioUpload . Session::item('arquivo'))) {

            $arquivo = Session::item('arquivo');
            $extensao = explode('.', $arquivo);
            $extensao = $extensao[count($extensao) - 1];
            $extensao = mb_convert_case($extensao, MB_CASE_LOWER, 'UTF-8');

            if (!in_array($extensao, $extensoesPermitidas)) {
                @unlink(self::$diretorioUpload . Session::item('arquivo'));
                throw new \Exception('Apenas extensões (' . implode(',', $extensoesPermitidas) . ') são permitidas');
            } else {
                $novoArquivo = self::novoNomeDeArquivo($extensao);

                if (!(@copy(self::$diretorioUpload . $arquivo, self::$diretorioFiles . $diretorio . DS . $novoArquivo))) {
                    throw new \Exception('Erro ao copiar o arquivo gerado pelo upload!');
                }

                if (!(@unlink(self::$diretorioUpload . $arquivo))) {
                    throw new \Exception('Erro ao excluir o arquivo gerado pelo upload');
                }

                //removendo a extensão
                $arquivo = explode('.', $arquivo);
                unset($arquivo[count($arquivo) - 1]);
                $arquivo = implode('.', $arquivo);

                $arquivoUpload = array();
                $arquivoUpload['descricao'] = $arquivo;
                $arquivoUpload['arquivo'] = $novoArquivo;

                Session::delete('arquivo');
                return $arquivoUpload;
            }
        }
        return false;
    }

    public static function novoNomeDeArquivo($extensao)
    {
        mt_srand();
        return hash('md5', uniqid(mt_rand())) . '.' . $extensao;
    }
}

/**
 * This is the implementation of the server side part of
 * Resumable.js client script, which sends/uploads files
 * to a server in several chunks.
 *
 * The script receives the files in a standard way as if
 * the files were uploaded using standard HTML form (multipart).
 *
 * This PHP script stores all the chunks of a file in a temporary
 * directory (`temp`) with the extension `_part<#ChunkN>`. Once all
 * the parts have been uploaded, a final destination file is
 * being created from all the stored parts (appending one by one).
 *
 * @author Gregory Chris (http://online-php.com)
 * @email www.online.php@gmail.com
 *
 * @editor Bivek Joshi (http://www.bivekjoshi.com.np)
 * @email meetbivek@gmail.com
 */
////////////////////////////////////////////////////////////////////
// THE FUNCTIONS
////////////////////////////////////////////////////////////////////

/**
 *
 * Delete a directory RECURSIVELY
 * @param string $dir - directory path
 * @link http://php.net/manual/en/function.rmdir.php
 */
function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    rrmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

/**
 *
 * Check if all the parts exist, and
 * gather all the parts of the file together
 * @param string $temp_dir - the temporary directory holding all the parts of the file
 * @param string $fileName - the original file name
 * @param string $chunkSize - each chunk size (in bytes)
 * @param string $totalSize - original file size (in bytes)
 */
function createFileFromChunks($temp_dir, $fileName, $chunkSize, $totalSize, $total_files)
{
    // count all the parts of this file
    $total_files_on_server_size = 0;
    $temp_total = 0;
    foreach (scandir($temp_dir) as $file) {
        $temp_total = $total_files_on_server_size;
        $tempfilesize = filesize($temp_dir . '/' . $file);
        $total_files_on_server_size = $temp_total + $tempfilesize;
    }
    // check that all the parts are present
    // If the Size of all the chunks on the server is equal to the size of the file uploaded.
    if ($total_files_on_server_size >= $totalSize) {
        // create the final destination file 
        if (($fp = fopen(PATH_UPLOADS . 'temporario' . DS . $fileName, 'w')) !== false) {
            for ($i = 1; $i <= $total_files; $i++) {
                fwrite($fp, file_get_contents($temp_dir . '/' . $fileName . '.part' . $i));
            }
            fclose($fp);
        } else {
            throw new \Exception('Não foi possível criar o arquivo no diretório de destino.');
        }

        // rename the temporary directory (to avoid access from other 
        // concurrent chunks uploads) and than delete it
        if (rename($temp_dir, $temp_dir . '_UNUSED')) {
            rrmdir($temp_dir . '_UNUSED');
        } else {
            rrmdir($temp_dir);
        }
    }
}
