<?php

namespace App\Services;

use App\Models\ComplianceTextTemplate;

class ComplianceTextResolver
{
    /**
     * Resolve a compliance template dynamically
     * @param array $roots  ['car' => $car, 'trim' => $trim, ...]
     */
    public static function resolve(array $roots, string $variant): ?string
    {
        $template = ComplianceTextTemplate::query()
            ->where('variant', $variant)
            ->valid()
            ->orderByDesc('version')
            ->first()?->template;

        if (!$template) {
            return null;
        }

        return self::replaceDotPlaceholders($template, $roots);
    }

    /**
     * Replace dot-notation placeholders
     * Example: :car->name, :trim->name, :powertrain->name
     */
    protected static function replaceDotPlaceholders(string $template, array $roots): string
    {
        return preg_replace_callback('/\{([a-z0-9_]+(?:->[\w_]+)*)\}/i', function ($matches) use ($roots) {
            $path = explode('->', $matches[1]);
            $rootKey = array_shift($path);

            $value = $roots[$rootKey] ?? null;

            foreach ($path as $segment) {
                if ($value === null) return ''; 
                if ($value instanceof \Illuminate\Support\Collection) {
                    $value = $value->pluck('name')->implode(', ');
                } else {
                    $value = $value->{$segment} ?? null;
                }
            }

            return $value ?? '';
        }, $template);
    }
}