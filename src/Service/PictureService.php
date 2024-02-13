<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        // On renomme l'image
        $fichier = md5(uniqid(rand(), true)) . '.webp' ;

        // On prend la taille de l'image
        $picture_info = getimagesize($picture);

        if($picture_info === false) {
            throw new Exception("Format d'image incorrect");
        }

        switch($picture_info['mime']) {
            case 'image/png':
                $pictureSource = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $pictureSource = imagecreatefromjpeg($picture);
                break;
            case 'image/webp':
                $pictureSource = imagecreatefromwebp($picture);
                break;
            default:
                throw new Exception("Format d'image incorrect");
                
        }

        // On recadre l'image
        // On récupère les dimensions
        $imageWidth = $picture_info[0];
        $imageHeight = $picture_info[1];

        switch ($imageWidth <=> $imageHeight) {
            case -1: // portrait
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;
            case 0: // carre
                    $squareSize = $imageWidth;
                    $src_x = 0;
                    $src_y = 0;
                    break;
            case 1: // paysage
                    $squareSize = $imageHeight;
                    $src_x = ($imageWidth - $squareSize) / 2;
                    $src_y = 0;
                    break;
        }

        // new empty image

        $resised_picture = imagecreatetruecolor($width, $height);

        imagecopyresampled($resised_picture, $pictureSource, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory') . $folder;

        // Creation of the destination foder if it does not exists

        if (!file_exists($path . '/mini/')) {
            mkdir($path . '/mini/', 0755, true);
        }

        // Save the image

        imagewebp($resised_picture, $path . '/mini/' . $width . 'x' . $height . '-' . $fichier);

        $picture->move($path . '/', $fichier);

        return $fichier;
    }

    public function delete(string $fichier, ?int $height, ?int $width, ?string $folder = '')
    {
        if($fichier !== 'default.webp') {
            $success = false;
            $path = $this->params->get('images_directory') . $folder;

            $mini = $path . '/mini/'. $width . 'x' . $height . '-' . $fichier;

            if(file_exists($mini)) {
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $fichier;
            
            if(file_exists($original)) {
                unlink($original);
                $success = true;
            }

            return $success;
        }

        return false;
    }
}