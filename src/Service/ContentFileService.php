<?php

namespace App\Service;

use Michelf\Markdown;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

class ContentFileService
{
    private $filesystem;
    private $projectRoot;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->filesystem = new Filesystem();
        $this->projectRoot = $parameterBag->get('kernel.project_dir');
    }

    public function convertMarkdownToHtml(string $category, string $subcategory, string $filename): string
    {
        $inputFilePath = $this->projectRoot . "/data/text/{$category}/{$subcategory}/{$filename}.md";
        $outputFilePath = $this->projectRoot . "/data/html/{$category}/{$subcategory}/{$filename}.html";
    
        if (!$this->filesystem->exists($inputFilePath)) {
            return 'Markdown file not found at ' . $inputFilePath;
        }
    
        $markdownContent = file_get_contents($inputFilePath);
        if ($markdownContent === false) {
            return 'Failed to read the Markdown file at ' . $inputFilePath;
        }
    
        // Ensure the directory exists
        $outputDirectory = dirname($outputFilePath);
        if (!is_dir($outputDirectory)) {
            mkdir($outputDirectory, 0777, true);
        }
    
        $htmlContent = Markdown::defaultTransform($markdownContent);
    
        $result = file_put_contents($outputFilePath, $htmlContent);
        if ($result === false) {
            return 'Failed to write the HTML file at ' . $outputFilePath;
        }
    
        return 'HTML file has been generated at ' . $outputFilePath;
    }

    public function getHtmlContent(string $category, string $subcategory, string $filename): array
    {
        try {
            $result = $this->convertMarkdownToHtml($category, $subcategory, $filename);
            $htmlFilePath = $this->projectRoot . "/data/html/{$category}/{$subcategory}/{$filename}.html";
    
            if (!file_exists($htmlFilePath)) {
                throw new \Exception('HTML file not found.');
            }
    
            $htmlContent = file_get_contents($htmlFilePath);
            if ($htmlContent === false) {
                throw new \Exception('Failed to read the HTML file.');
            }
    
            return [
                'content' => $htmlContent,
                'statusCode' => Response::HTTP_OK
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }
    }
}