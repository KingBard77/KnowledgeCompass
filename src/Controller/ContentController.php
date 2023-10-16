<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DomCrawler\Crawler;
use App\Service\ContentFileService;  
use App\Service\ContentImageService;

class ContentController extends AbstractController
{
    private $contentFileService;
    private $contentImageService; 

    public function __construct(ContentFileService $contentFileService, ContentImageService $contentImageService)
    {
        $this->contentFileService = $contentFileService;
        $this->contentImageService = $contentImageService; 
    }

    #[Route('/{category}/{subcategory}/{filename}', name: 'app_content')]
    public function convertToHtml(string $category, string $subcategory, string $filename): Response
    {
        $this->contentFileService->convertMarkdownToHtml($category, $subcategory, $filename);

        $projectRoot = $this->getParameter('kernel.project_dir');
        $htmlFilePath = $projectRoot . "/data/html/{$category}/{$subcategory}/{$filename}.html";

        if (!file_exists($htmlFilePath)) {
            return new Response('HTML file not found.', Response::HTTP_NOT_FOUND);
        }

        $htmlContent = file_get_contents($htmlFilePath);
        if ($htmlContent === false) {
            return new Response('Failed to read the HTML file.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $parsedContent = $this->parseHtmlContent($htmlContent, $category, $subcategory);

        return $this->render('content/content.html.twig', array_merge($parsedContent));
        // return $this->render('content/content.html.twig', [
        //     'htmlContent' => $htmlContent,
        //     // other variables you want to pass to the view
        // ]);
    }

    // Function to parseHtmlContent
    private function parseHtmlContent(string $htmlContent, string $category, string $subcategory): array
    {
        $crawler = new Crawler($htmlContent);

        // Parsing headers
        $title = $crawler->filter('h1')->text();
        $h2 = $crawler->filter('h2')->each(fn(Crawler $node) => $node->text());
        $h3 = $crawler->filter('h3')->each(fn(Crawler $node) => $node->text());
        $h5 = $crawler->filter('h5')->each(fn(Crawler $node) => $node->text());

        // Parsing paragraphs
        $paragraphs = [];
        $crawler->filter('body > p')->each(function (Crawler $node) use (&$paragraphs) {
            $html = $node->html();
            if (!empty(trim($html))) {
                $paragraphs[] = $html;
            }
        });

        // Parsing images
        $imageFigures = $crawler->filter('img')->each(function (Crawler $node) use ($category, $subcategory) {
            $src = $node->attr('src');
            $filename = basename($src);  // Assuming src is just the filename

            $projectRoot = $this->getParameter('kernel.project_dir');
            $imagePath = "{$projectRoot}/public/media/{$category}/{$subcategory}/{$filename}";

            if (file_exists($imagePath)) {
                $newSrc = '/media/' . $category . '/' . $subcategory . '/' . $filename;
                $node->getNode(0)->setAttribute('src', $newSrc);
                return ['src' => $newSrc];
            } else {
                // Provide a fallback image or set to empty
                $newSrc = '';
                $node->getNode(0)->setAttribute('src', $newSrc);
                return ['src' => $newSrc];
            }
        });

        // Parsing notes
        $infoNotes = $this->_extractNotes($crawler, 'Info:');
        $remindNotes = $this->_extractNotes($crawler, 'Reminder:');
        $importantNotes = $this->_extractNotes($crawler, 'Important:');
        $warningNotes = $this->_extractNotes($crawler, 'Warning:');

        // Parsing links
        $linkNotes = $this->_extractLinks($crawler, 'Link:');

        // Parsing code blocks
        $codeBlocks = $crawler->filter('code')->each(function (Crawler $node) {
            $codeBlock = trim($node->html());
            $lines = explode("\n", $codeBlock);
            return array_map('trim', $lines);  // Trim each line
        });

        // Parsing yaml blocks
        $yamlHtmlContent = $crawler->filter('pre > code.language-yaml')->html();
        $codeYaml = html_entity_decode($yamlHtmlContent);
        $codeYaml = str_replace(['<br>', '<br/>', '<br />'], "\n", $codeYaml);
    
        // Parsing <ul> <li> list
        $ulLists = $crawler->filter('ul')->each(function (Crawler $ulNode) {
            return $ulNode->filter('li')->each(function (Crawler $liNode) {
                return $liNode->html();
            });
        });


        return [
            'title' => $title,
            'h2' => $h2,
            'h3' => $h3,
            'h5' => $h5,
            'paragraphs' => $paragraphs,
            'imageFigures' => $imageFigures,
            'infoNotes' => $infoNotes,
            'remindNotes' => $remindNotes,
            'importantNotes' => $importantNotes,
            'warningNotes' => $warningNotes,
            'codeBlocks' => $codeBlocks,
            'ulLists' => $ulLists,
            'linkNotes' => $linkNotes,
            'codeYaml' => $codeYaml,
        ];
    }

    // Function to _extractNotes
    private function _extractNotes(Crawler $crawler, string $noteType): array
    {
        $notes = [];
        $crawler->filter('blockquote > p')->each(function (Crawler $node) use (&$notes, $noteType) {
            $html = $node->html(); 
            if (strpos($html, $noteType) !== false) {
                $notes[] = $html; 
            }
        });
        return $notes;
    }

    // Function to _extractLinks
    private function _extractLinks(Crawler $crawler, string $linkType): array
    {
        $linkText = [];
        $link = '';
        $crawler->filter('blockquote > p')->each(function (Crawler $node) use (&$linkText, &$link) {
            $text = $node->text();
            if (strpos($text, 'Link:') !== false) {
                $linkText[] = $text;
                $linkNode = $node->filter('a');
                if ($linkNode->count() > 0) {
                    $link = $linkNode->attr('href');
                }
            }
        });
        return [
            'linkText' => $linkText,
            'link' => $link
        ];
    }
}