<?php

declare(strict_types=1);

function slugify(string $text): string
{
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);
    $text = trim((string) $text, '-');

    return $text !== '' ? $text : 'producto';
}

/**
 * @return non-empty-string
 */
function unique_product_slug(PDO $pdo, string $base, ?int $exceptProductId): string
{
    $slug = slugify($base);
    if ($slug === '') {
        $slug = 'producto';
    }
    $candidate = $slug;
    $n = 2;
    while (true) {
        if ($exceptProductId !== null) {
            $stmt = $pdo->prepare('SELECT id FROM products WHERE slug = ? AND id != ? LIMIT 1');
            $stmt->execute([$candidate, $exceptProductId]);
        } else {
            $stmt = $pdo->prepare('SELECT id FROM products WHERE slug = ? LIMIT 1');
            $stmt->execute([$candidate]);
        }
        if (!$stmt->fetch()) {
            return $candidate;
        }
        $candidate = $slug . '-' . $n;
        ++$n;
    }
}

function parse_money(string $input): ?float
{
    $normalized = str_replace([' ', ','], ['', '.'], trim($input));
    if ($normalized === '' || !is_numeric($normalized)) {
        return null;
    }
    $v = (float) $normalized;

    return $v >= 0 ? round($v, 2) : null;
}
