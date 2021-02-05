<?php

declare(strict_types=1);

namespace App\Twig\Widget\Font;

use App\Model\Font\Entity\Status;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('font_status', [$this, 'status'], ['is_safe' => ['html']]),
        ];
    }

    public function status(string $status): string
    {
        switch ($status) {
            case Status::ACTIVE:
                $badge = 'badge-success';
                break;
            case Status::HIDDEN:
                $badge = 'badge-secondary';
                break;
            default:
                $badge = 'badge-secondary';
        }
        return '<span class="badge '.$badge.'">'.Status::verbose($status).'</span>';
    }
}
