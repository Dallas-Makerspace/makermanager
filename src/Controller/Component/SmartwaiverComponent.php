<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Network\Http\Client;

class SmartwaiverComponent extends Component {
    private $http;
    private $options;

    public function initialize()
    {
        // Setup an HTTP Client and configure it to use our API key for all requests
        $this->http = new Client();
        $this->options = [
            'type' => 'json',
            'headers' => [
                'sw-api-key' => Configure::read('Smartwaiver.v4_apikey')
            ]
        ];
    }

    public function check($first_name, $last_name, $email) {
        // Grab a list of waivers that match verified=true AND firstName AND lastName
        $list_response = $this->http->get(
            "https://api.smartwaiver.com/v4/waivers?limit=100&verified=true&firstName={$first_name}&lastName={$last_name}",
            [], // We don't need to send anything in the body
            $this->options // Use our preset headers and API key
        );
        $waivers = $list_response->json;
        if (!$waivers || empty($waivers['waivers'])) {
            // Nothing found, fail out
            return false;
        }

        // For each of the listed waivers, we need to check to see if the specific one we want is there
        foreach ($waivers['waivers'] as $waiver) {
            $waiver_response = $this->http->get(
                "https://api.smartwaiver.com/v4/waivers/{$waiver['waiverId']}?pdf=false",
                [], // We don't need to send anything in the body
                $this->options // Use our preset headers and API key
            );

            $waiver_json = $waiver_response->json;

            // If the request worked and we have something in the email field and the email matches
            if (!empty($waiver_json['waiver']['email']) && $waiver_json['waiver']['email'] == $email) {
                return true;
            }

            // We didn't find their email, so the loop continues on
        }

        // We didn't find a matching waiver
        return false;
    }
}
