<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use DocuSign\eSign\Client\ApiClient;
use DocuSign;

class DocusignController extends BaseController
{
    protected $first = 'hi myname is G-lalala';
    protected static $access_token;

    /**
     * 初見
     */
    public function index() 
    {
        return view('index')->with([
            'first' => $_SERVER['DOCUMENT_ROOT']
        ]);
    }
    
    /**
     * Get OAUTH provider
     * @return DocuSign $provider
     */
    function get_oauth_provider(): DocuSign
    {
        return new DocuSign([
            'clientId' => getenv('DS_CLIENT_ID'),
            'clientSecret' => getenv('DS_CLIENT_SECRET'),
            'redirectUri' => getenv('APP_URL'),
            'authorizationServer' => 'https://account-d.docusign.com',
            // 'allowSilentAuth' => $GLOBALS['DS_CONFIG']['allow_silent_authentication']
        ]);
    }

    /**
     * DocuSign login handler
     * @param $redirectUrl
     */
    function authCallback($redirectUrl = ""): void
    {
        $provider = $this->get_oauth_provider();
        // Check given state against previously stored one to mitigate CSRF attack
        if (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
            exit('Invalid OAuth state');
        } else {
            try {
                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);

                $this->flash('You have authenticated with DocuSign.');
                // We have an access token, which we may use in authenticated
                // requests against the service provider's API.
                $_SESSION['ds_access_token'] = $accessToken->getToken();
                $_SESSION['ds_refresh_token'] = $accessToken->getRefreshToken();
                $_SESSION['ds_expiration'] = $accessToken->getExpires(); # expiration time.

                // Using the access token, we may look up details about the
                // resource owner.
                $user = $provider->getResourceOwner($accessToken);
                $_SESSION['ds_user_name'] = $user->getName();
                $_SESSION['ds_user_email'] = $user->getEmail();

                $account_info = $user->getAccountInfo();
                $base_uri_suffix = '/restapi';
                $_SESSION['ds_account_id'] = $account_info["account_id"];
                $_SESSION['ds_account_name'] = $account_info["account_name"];
                $_SESSION['ds_base_path'] = $account_info["base_uri"] . $base_uri_suffix;
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                // Failed to get the access token or user details.
                exit($e->getMessage());
            }
            if (! $redirectUrl) {
                $redirectUrl = $GLOBALS['app_url'];
            }
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
}
