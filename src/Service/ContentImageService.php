<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class ContentImageService
{
    private $projectDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->projectDir = $kernel->getProjectDir();
    }

    public function getImageResponse(string $category, string $subcategory, string $filename): Response
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $filenameWithExtension = $extension === 'png' ? $filename : "{$filename}.png";
        $imagePath = $this->projectDir . "/public/media/{$category}/{$subcategory}/{$filenameWithExtension}";

        dump($imagePath);

        if (!file_exists($imagePath)) {
            return new Response('Image not found', Response::HTTP_NOT_FOUND);
        }

        try {
            $imageData = file_get_contents($imagePath);
        } catch (FileException $e) {
            return new Response('Failed to read the image file', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = new Response($imageData);
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }
}
