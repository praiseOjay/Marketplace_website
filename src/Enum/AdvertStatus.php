<?php

namespace App\Enum;

enum AdvertStatus: string
{
    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case PUBLISHED = 'published';
    case REJECTED = 'rejected';
    case SOLD = 'sold';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING_REVIEW => 'Pending Review',
            self::PUBLISHED => 'Published',
            self::REJECTED => 'Rejected',
            self::SOLD => 'Sold',
        };
    }
}
