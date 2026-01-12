<?php

namespace App\Mappers;

use App\Data\DealerData;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use App\Data\Dealer\{
    ChannelsData,
    EmailsData,
    UrlsData,
    TypesData,
    OpeningHoursData,
    OpeningHours\SalesData,
    OpeningHours\WorkshopData,
    PostalCodesData
};
use Throwable;

class DealerMapper
{
    public static function map(array $dealerData): DealerData
    {

        try {
            $dynamicsId = $dealerData['pin_forhandlerid'];
            $accountNumber = $dealerData['nel_accountid']['accountnumber'];
            $companyId = $dealerData['pin_companyid'] ?? '';
            $crmId = $dealerData['pin_crmforhandlerid'] ?? '';
            $dealerbridgeId = $dealerData['pin_dealerbridgeforhandlernr'] ?? '';
            $bilinfoId = $dealerData['pin_bilinfoid'] ?? '';
            $autouncleDepartmentId = $dealerData['pin_autouncledepartmentid'] ?? '';
            $rooftopId = $dealerData['pin_rooftopid'] ?? '';
            $dealerGuid = $dealerData['pin_dealerguid'] ?? '';
            $ownerGuid = $dealerData['pin_ownerguid'] ?? '';
            $name = $dealerData['pin_navn'];
            $displayName = $dealerData['pin_webnavn'] ?? '';
            $cvrNumber = $dealerData['pin_cvrnumber'] ?? null;
            $group = $dealerData['pin_koncern'] ?? '';
            $streetName = $dealerData['pin_vejnavn'] ?? '';
            $streetNumber = $dealerData['pin_vejnummer'] ?? '';
            $city = $dealerData['pin_by'] ?? '';
            $zipCode = $dealerData['pin_postnr'] ?? null;
            $country = $dealerData['pin_land'] ?? '';
            $latitude = $dealerData['pin_latitude'] ?? null;
            $longitude = $dealerData['pin_longitude'] ?? null;
            $phone = $dealerData['pin_telefon'] ?? '';

            $channels = self::mapChannels($dealerData);
            $emails = self::mapEmails($dealerData);
            $urls = self::mapUrls($dealerData);
            $types = self::mapTypes($dealerData);
            $openingHours = self::mapOpeningHours($dealerData);
            $postalCodes = self::mapPostalCodes($dealerData);

            return new DealerData(
                dynamics_id: $dynamicsId,
                account_number: $accountNumber,
                company_id: $companyId,
                crm_id: $crmId,
                dealerbridge_id: $dealerbridgeId,
                bilinfo_id: $bilinfoId,
                autouncle_department_id: $autouncleDepartmentId,
                rooftop_id: $rooftopId,
                dealer_guid: $dealerGuid,
                owner_guid: $ownerGuid,
                name: $name,
                display_name: $displayName,
                cvr_number: $cvrNumber,
                group: $group,
                street_name: $streetName,
                street_number: $streetNumber,
                city: $city,
                zip_code: $zipCode,
                country: $country,
                latitude: $latitude,
                longitude: $longitude,
                phone: $phone,
                channels: $channels,
                emails: $emails,
                urls: $urls,
                types: $types,
                opening_hours: $openingHours,
                postal_codes: $postalCodes
            );

        } catch (Throwable $e) {
            Log::error('Error mapping Dealer', [
                'dynamicsId' => $dealerData['pin_forhandlerid'],
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    protected static function mapChannels(array|Collection|null $attributes): ?ChannelsData
    {
        if (!$attributes) return null;

        $values = [
            'web' => Arr::get($attributes, 'pin_aktivweb'),
            'test_drive' => Arr::get($attributes, 'pin_aktivproevetur'),
            'webshop' => Arr::get($attributes, 'pin_aktivwebshop'),
            'sales_advisor' => Arr::get($attributes, 'pin_salgsraadgiver'),
            'pickup_location' => Arr::get($attributes, 'pin_webshoppickuplocation'),
            'insurance_calculator' => Arr::get($attributes, 'pin_forsikringsberegner'),
            'pdf_list' => Arr::get($attributes, 'pin_pdfliste')
        ];

        if (!array_filter($values)) return null;

        return new ChannelsData(...$values);
    }

    protected static function mapEmails(array|Collection|null $attributes): ?EmailsData
    {
        if (!$attributes) return null;

        $values = [
            'contact' => Arr::get($attributes, 'pin_emailkontakt'),
            'sales' => Arr::get($attributes, 'pin_emailsalg'),
            'marketing' => Arr::get($attributes, 'pin_emailmarketing'),
            'workshop' => Arr::get($attributes, 'pin_emailvaerksted'),
            'spare_parts' => Arr::get($attributes, 'pin_emailreservedele'),
            'private_leasing' => Arr::get($attributes, 'pin_emailprivatleasing')
        ];

        if (!array_filter($values)) return null;

        return new EmailsData(...$values);
    }

    protected static function mapUrls(array|Collection|null $attributes): ?UrlsData
    {
        if (!$attributes) return null;

        $values = [
            'website' => Arr::get($attributes, 'pin_website'),
            'intern_website' => Arr::get($attributes, 'pin_nellemanndealerwebsite'),
            'privacy_policy' => Arr::get($attributes, 'pin_privatlivspolitik'),
            'service_booking' => Arr::get($attributes, 'pin_linkservicebooking')
        ];

        if (!array_filter($values)) return null;

        return new UrlsData(...$values);
    }

    protected static function mapTypes(array|Collection|null $attributes): ?TypesData
    {
        if (!$attributes) return null;

        $values = [
            'b2c' => Arr::get($attributes, 'nel_b2cdealer'),
            'b2b' => Arr::get($attributes, 'nel_b2bdealer'),
            'service' => Arr::get($attributes, 'nel_servicedealer')
        ];

        if (!array_filter($values)) return null;

        return new TypesData(...$values);
    }

    protected static function mapOpeningHours(array|Collection|null $attributes): ?OpeningHoursData
    {
        if (!$attributes) return null;

        $values = [
            'sales' => self::mapSalesOpeningHours($attributes),
            'workshop' => self::mapWorkshopOpeningHours($attributes)
        ];

        if (!array_filter($values)) return null;

        return new OpeningHoursData(...$values);
    }

    protected static function mapSalesOpeningHours(array|Collection|null $attributes): ?SalesData
    {
        if (!$attributes) return null;

        $values = [
            'monday' => Arr::get($attributes, 'pin_openinghoursmonday'),
            'tuesday' => Arr::get($attributes, 'pin_openinghourstuesday'),
            'wednesday' => Arr::get($attributes, 'pin_openinghourswednesday'),
            'thursday' => Arr::get($attributes, 'pin_openinghoursthursday'),
            'friday' => Arr::get($attributes, 'pin_openinghoursfriday'),
            'saturday' => Arr::get($attributes, 'pin_openinghourssaturday'),
            'sunday' => Arr::get($attributes, 'pin_openinghourssunday')
        ];

        if (!array_filter($values)) return null;

        return new SalesData(...$values);
    }

    protected static function mapWorkshopOpeningHours(array|Collection|null $attributes): ?WorkshopData
    {
        if (!$attributes) return null;

        $values = [
            'monday' => Arr::get($attributes, 'pin_workshopopeninghoursmonday'),
            'tuesday' => Arr::get($attributes, 'pin_workshopopeninghourstuesday'),
            'wednesday' => Arr::get($attributes, 'pin_workshopopeninghourswedneysday'),
            'thursday' => Arr::get($attributes, 'pin_workshopopeninghoursthursday'),
            'friday' => Arr::get($attributes, 'pin_workshopopeninghoursfriday'),
            'saturday' => Arr::get($attributes, 'pin_workshopopeninghourssaturday'),
            'sunday' => Arr::get($attributes, 'pin_workshopopeninghourssunday')
        ];

        if (!array_filter($values)) return null;

        return new WorkshopData(...$values);
    }

    protected static function mapPostalCodes(array|Collection|null $attributes): ?PostalCodesData
    {
        if (!$attributes) return null;

        $values = [
            'b2b' => self::mapPostalCodeGroups(Arr::get($attributes, 'nel_pin_forhandlerpostnumre_ForhandlerErhverv_pin_forhandler')),
            'b2c' => self::mapPostalCodeGroups(Arr::get($attributes, 'pin_pin_forhandlerpostnumre_Forhandler_pin_fo')),
        ];

        if (!array_filter($values)) return null;

        return new PostalCodesData(...$values);
    }

    protected static function mapPostalCodeGroups(array|Collection|null $groups): array
    {
        if (!$groups) return [];
        $data = $groups instanceof Collection ? $groups : collect($groups);

        return $data->pluck('pin_name')->values()->all();
    }
}
