<?php

namespace App\Classe;

use App\Entity\Category;
use App\Entity\Tag;

class Search
{
    /**
     * @var string
     * Représente la chaîne de recherche
     */
    public $string = '';

    /**
     * @var Category[]
     * Représente les catégories sélectionnées
     */
    public $categories = [];

    /**
     * @var Tag[]
     * Représente les tags sélectionnés
     */
    public $tags = [];

    /**
     * @var int|null
     * Représente le prix maximum
     */
    public $minPrice = null;
    public $maxPrice = null;
}
