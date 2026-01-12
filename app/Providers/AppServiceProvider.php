<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->addHttpMacro();
        $this->addDirectives();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    protected function addHttpMacro(): void
    {
        Http::macro('nellemannPIM', function (): PendingRequest {
            return Http::baseUrl(config('nellemann.pim.api_url'))
                ->withHeaders([
                    'Authorization' => config('nellemann.pim.api_key'),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]);
        });

        Http::macro('nellemannAzure', function (): PendingRequest {
            return Http::baseUrl(config('nellemann.azure.web_app.url'))
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->withQueryParameters([
                    'code' => config('nellemann.azure.web_app.code'),
                ]);
        });

        Http::macro('nellemannBilInfo', function (): PendingRequest {
            return Http::baseUrl(config('nellemann.bilinfo.api_url'))
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->withBasicAuth(
                    config('nellemann.bilinfo.username'),
                    config('nellemann.bilinfo.password')
                );
        });

        Http::macro('nellemannDynamics', function (): PendingRequest {
            $accessToken = Cache::remember('dataverse_access_token', now()->addMinutes(50), function () {
                $tenantId = config('nellemann.dynamics.tenant_id');
                $clientId = config('nellemann.dynamics.client_id');
                $clientSecret = config('nellemann.dynamics.client_secret');
                $resource = config('nellemann.dynamics.resource');
                $tokenUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/token";

                $response = Http::asForm()->post($tokenUrl, [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'resource'      => $resource,
                ]);

                if (!$response->successful()) {
                    throw new \Exception('Unable to fetch access token: ' . $response->body());
                }

                return $response->json()['access_token'];
            });

            return Http::baseUrl(config('nellemann.dynamics.api_url'))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ]);
        });

    }

    protected function addDirectives(): void
    {
        Blade::directive('duration', function ($expression) {
            return "<?php
                list(\$h, \$m) = explode(':', $expression);
                \$h = (int) \$h;
                \$m = (int) \$m;

                if (\$h > 0 && \$m > 0) {
                    // Begge dele
                    echo \$h . ' t ' . \$m . ' min';
                } elseif (\$h > 0) {
                    echo \$h . ' t';
                } elseif (\$m > 0) {
                    echo \$m . ' min';
                } else {
                    echo '0 min';
                }
            ?>";
        });

        Blade::directive('durationRange', function ($expression) {
            return "<?php
                \$values = $expression;

                if (!is_array(\$values)) {
                    \$values = [\$values, \$values];
                }

                list(\$minValue, \$maxValue) = \$values;

                // Inline closure til formatering
                \$formatDuration = function(\$time) {
                    list(\$h, \$m) = explode(':', \$time);
                    \$h = (int) \$h;
                    \$m = (int) \$m;

                    if (\$h > 0 && \$m > 0) {
                        return \"{\$h} t {\$m} min\";
                    } elseif (\$h > 0) {
                        return \"{\$h} t\";
                    } elseif (\$m > 0) {
                        return \"{\$m} min\";
                    } else {
                        return \"0 min\";
                    }
                };

                if (\$minValue === \$maxValue) {
                    echo \$formatDuration(\$minValue);
                } else {
                    echo \$formatDuration(\$minValue) . ' - ' . \$formatDuration(\$maxValue);
                }
            ?>";
        });



        Blade::directive('number', function ($expression) {
            return "<?php
                \$params = [$expression];
                \$value = \$params[0] ?? 0;
                \$decimals = \$params[1] ?? 0;
                echo number_format(\$value, \$decimals, ',', '.');
            ?>";
        });

    }
}
