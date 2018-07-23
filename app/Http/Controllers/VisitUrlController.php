<?php

namespace App\Http\Controllers;

use App\ShortenedUrl;
use Jenssegers\Agent\Agent;
use libphonenumber\PhoneNumberFormat;

class VisitUrlController extends Controller
{
    public function go($alias)
    {
        $url = ShortenedUrl::whereAlias($alias)->firstOrFail();

        $text = rawurlencode($url->text);

        if ($url->type === 'single') {
            $mobileNumber = phone($url->mobile_number, 'MY', PhoneNumberFormat::E164);
        } elseif ($url->type === 'group') {
            $mobileNumber = $url->group()->inRandomOrder()->get();
        }

        $redirectApp = "whatsapp://send?text={$text}&phone={$mobileNumber}";
        $redirectWeb = "https://web.whatsapp.com/send?text={$text}&phone={$mobileNumber}";

        $agent = new Agent();

        if ($agent->isMobile()) {
            return redirect($redirectApp);
        }

        return view('redirector', [
            'redirectApp' => $redirectApp,
            'redirectWeb' => $redirectWeb,
            'os'          => $agent->platform(),
        ]);
    }
}
