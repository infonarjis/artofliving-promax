<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\AdvertisementMaster;

class AdvertisementComposer
{
    public function compose(View $view)
    {
        $adv_type = $view->getData()['adv_type'] ?? null;

        if ($adv_type) {
            $advertisementData = AdvertisementMaster::getByLevel($adv_type);
            $view->with('advertisementData', $advertisementData);
        }
    }
}