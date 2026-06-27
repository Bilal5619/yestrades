<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostalCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'postal_code',
        'normalized_postal_code',
        'price',
        'service_id',
        'service_package_id',
        'service_category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'service_id' => 'integer',
        'service_package_id' => 'integer',
        'service_category_id' => 'integer',
    ];

    public static function normalizePostcode(?string $postcode): string
    {
        return Str::upper(preg_replace('/\s+/', '', trim((string) $postcode)));
    }

    public static function booted(): void
    {
        static::saving(function (PostalCode $postalCode) {
            $postalCode->normalized_postal_code = self::normalizePostcode($postalCode->postal_code);
        });
    }

    public static function matchingRules(?string $postcode): Collection
    {
        $normalized = self::normalizePostcode($postcode);

        if ($normalized === '') {
            return new Collection();
        }

        return self::query()
            ->get()
            ->filter(function (PostalCode $rule) use ($normalized) {
                $rulePostcode = $rule->normalized_postal_code ?: self::normalizePostcode($rule->postal_code);

                return $rulePostcode !== '' && Str::startsWith($normalized, $rulePostcode);
            })
            ->sortByDesc(function (PostalCode $rule) {
                return strlen($rule->normalized_postal_code ?: self::normalizePostcode($rule->postal_code));
            })
            ->values();
    }

    public static function surchargeFor(?string $postcode, array $context = []): ?array
    {
        $rules = self::matchingRules($postcode);

        if ($rules->isEmpty()) {
            return null;
        }

        $matchingRules = $rules->filter(function (PostalCode $rule) use ($context) {
            if (!empty($rule->service_package_id)) {
                return (int) ($context['service_package_id'] ?? 0) === (int) $rule->service_package_id;
            }

            if (!empty($rule->service_id)) {
                return (int) ($context['service_id'] ?? 0) === (int) $rule->service_id;
            }

            if (!empty($rule->service_category_id)) {
                return (int) ($context['service_category_id'] ?? 0) === (int) $rule->service_category_id;
            }

            return true;
        });

        if ($matchingRules->isEmpty()) {
            return null;
        }

        $bestRule = $matchingRules
            ->sortByDesc(function (PostalCode $rule) {
                $scopeWeight = !empty($rule->service_package_id) ? 4
                    : (!empty($rule->service_id) ? 3
                    : (!empty($rule->service_category_id) ? 2 : 1));

                $prefixLength = strlen($rule->normalized_postal_code ?: self::normalizePostcode($rule->postal_code));

                return ($prefixLength * 10) + $scopeWeight;
            })
            ->first();

        return [
            'id' => $bestRule->id,
            'postal_code' => $bestRule->postal_code,
            'price' => round((float) $bestRule->price, 2),
            'service_id' => $bestRule->service_id,
            'service_package_id' => $bestRule->service_package_id,
            'service_category_id' => $bestRule->service_category_id,
        ];
    }
}
