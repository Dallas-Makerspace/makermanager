<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Network\Http\Client;
use Cake\Log\Log;

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
        // urlencode the names so that it works for people with two last names or two first names
        $first_name = urlencode($first_name);
        $last_name = urlencode($last_name);

        // Grab a list of waivers that match verified=true AND firstName AND lastName
        $list_response = $this->http->get(
            "https://api.smartwaiver.com/v4/waivers?limit=100&firstName={$first_name}&lastName={$last_name}",
            [], // We don't need to send anything in the body
            $this->options // Use our preset headers and API key
        );
        $waivers = $list_response->json;
        if (!$waivers || empty($waivers['waivers'])) {
            Log::write('error', "No waiver found for user by first and last: first_name='{$first_name}' last_name='{$last_name}' email='{$email}'");
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
            if (!empty($waiver_json['waiver']['email']) && strtolower($waiver_json['waiver']['email']) == strtolower($email)) {
                Log::write('error', "Found a waiver: first_name='{$first_name}' last_name='{$last_name}' email='{$email}' waiver_email='{$waiver_json['waiver']['email']}' waiver_id={$waiver['waiverId']}");
                return $waiver['waiverId'];
            }

            // We didn't find their email, so the loop continues on
        }

        Log::write(
            'error',
            "Found some waivers by first and last, but no matching email: first_name='{$first_name}' last_name='{$last_name}' email='{$email}'\n"
            . "Waiver response:\n{$list_response->body}"
        );

        // We didn't find a matching waiver
        return false;
    }
}
