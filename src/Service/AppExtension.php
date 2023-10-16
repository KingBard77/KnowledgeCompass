<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function getGlobals(): array
    {
        $subcategories = $this->readCategoryFile('/data/.meta/subcategory.md');
    
        return [
            'global_subcategories' => $subcategories,
        ];
    }
    
    private function readCategoryFile($relativePath): array
    {
        $projectDir = $this->params->get('kernel.project_dir');
        $filePath = $projectDir . $relativePath;
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        array_shift($lines);
        array_shift($lines);

        $categories = [];

        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) >= 3) {
                $category = trim($parts[2]);
                $categories[] = $category;
            }
        }

        $categories = array_unique($categories);
        sort($categories);

        return $categories;
    }
}