<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

use DateTimeInterface;

/**
 * Interface PublishedDateEntityInterface
 * @package App\Entity\Interfaces
 */
interface PublishedDateEntityInterface
{
    /**
     * @param DateTimeInterface $published
     *
     * @return PublishedDateEntityInterface
     */
    public function setPublished(DateTimeInterface $published): PublishedDateEntityInterface;
}
