<?php

declare(strict_types=1);

namespace App\Twig\Widget\Font;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class YesOrNoWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('badge_yes_or_no', [$this, 'render'], ['is_safe' => ['html']]),
        ];
    }

    public function render(bool $value): string
    {
        $badge = $value ? 'badge-success' : 'badge-secondary';
        $verbose = $value ? 'Yes' : 'No';
        return '<span class="badge '.$badge.'">'.$verbose.'</span>';
    }
}
