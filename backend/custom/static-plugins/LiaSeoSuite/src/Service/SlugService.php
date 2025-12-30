<?php declare(strict_types=1);

namespace Lia\SeoSuite\Service;

use Cocur\Slugify\Slugify;

class SlugService
{
    public static function slug(string $string, $options = null): string
    {
        $slugify = new Slugify();

        return $slugify->slugify($string, $options);
    }
}
