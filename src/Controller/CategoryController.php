<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/blockchain/{subcategory}', name: 'app_subcategory')]
    public function subCategory(string $subcategory): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $filePath = $projectDir . '/data/.meta/tables.md';
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
        array_shift($lines);
        array_shift($lines);
    
        $data = [];
    
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            $entry = [
                'title' => trim($parts[2]),
                'category' => trim($parts[3]),
                'subcategory' => trim($parts[4]),
                'date' => trim($parts[5]),
                'description' => trim($parts[6]),
                'path' => count($parts) >= 8 ? basename(trim($parts[7]), '.md') : 'N/A'
            ];
            $data[] = $entry;
        }
    
        // Filter data based on subcategory if needed
        if ($subcategory !== null) {
            $data = array_filter($data, function($entry) use ($subcategory) {
                return $entry['subcategory'] === $subcategory;
            });
        }
    
        // Sort data based on subcategory
        usort($data, function($a, $b) {
            return strcmp($a['subcategory'], $b['subcategory']);
        });
    
        return $this->render('page/category.html.twig', [
            'controller_name' => 'CategoryController',
            'data' => $data,
            'subcategory' => $subcategory,
            'current_page' => 'category',
        ]);
    }
}
