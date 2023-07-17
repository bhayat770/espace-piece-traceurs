<?php

// src/Twig/SlugifyExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugifyExtension extends AbstractExtension
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('slugify', [$this, 'slugifyFilter']),
        ];
    }

    public function slugifyFilter(string $text): string
    {
        return $this->slugger->slug($text)->lower();
    }
}
