<?php

declare(strict_types=1);

namespace App\Twig\Widget\Font;

use App\Model\Font\Entity\Face\Face;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FaceStyleWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('font_face_style', [$this, 'render'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function render(Environment $twig, Face $face): string
    {
        return $twig->render('widget/font/face-style.html.twig', [
            'face' => $face
        ]);
    }
}
