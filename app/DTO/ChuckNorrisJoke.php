<?php

namespace App\DTO;

use DateTimeImmutable;

class ChuckNorrisJoke
{
    /** @var string */
    private $id;

    /** @var string */
    private $iconUrl;

    /** @var string */
    private $url;

    /** @var string */
    private $value;

    /** @var array */
    private $categories;

    /** @var DateTimeImmutable */
    private $createdAt;

    /** @var DateTimeImmutable|null */
    private $updatedAt;

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['icon_url'],
            $data['url'],
            $data['value'],
            $data['categories'],
            new DateTimeImmutable($data['created_at']),
            !empty($data['updated_at']) ? new DateTimeImmutable($data['updated_at']) : null,
        );
    }

    public function __construct(
        string $id,
        string $iconUrl,
        string $url,
        string $value,
        array $categories,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->iconUrl = $iconUrl;
        $this->url = $url;
        $this->value = $value;
        $this->categories = $categories;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function value(): string
    {
        return $this->value;
    }
}
