<?php

namespace Drupal\powerbi_embed\Util;

use Drupal\powerbi_embed\AuthType;
use GuzzleHttp\Exception\ClientException;

/**
 * Configuration interface class for powerbi_embed module.
 */
class ConfigUtil {

  /**
   * Return PowerBI configuration settings.
   */
  private static function getConfig() {
    return \Drupal::config('powerbi_embed.settings');
  }

  /**
   * Return PowerBI configured Azure Client ID.
   */
  public static function getClientID() {
    $config = self::getConfig();
    return $config->get('adal.client_id');
  }

  /**
   * Return PowerBI configured Workspace ID.
   */
  public static function getWorkspaceID() {
    $config = self::getConfig();
    return $config->get('workspace_id');
  }

  /**
   * Get the auth method.
   *
   * @return string
   *   The auth method either adal or msal.
   */
  public static function getAuthMethod() {
    $config = self::getConfig();
    return $config->get('auth_method');
  }

  /**
   * Return PowerBI configured user name.
   */
  public static function getUsername() {
    $config = self::getConfig();
    return $config->get('adal.username');
  }

  /**
   * Return PowerBI configured password.
   */
  public static function getPassword() {
    $config = self::getConfig();
    return $config->get('adal.password');
  }

  /**
   * Generate an access token by using ADAL method.
   *
   * @return string
   *   The access token.
   */
  protected static function generateAdalAccessToken() {
    // Get oauth2 token using a POST request.
    $curlPostToken = curl_init();
    $theCurlOpts = [
      CURLOPT_URL => 'https://login.windows.net/common/oauth2/token',
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => [
        'grant_type' => 'password',
        'scope' => 'openid',
        'resource' => 'https://analysis.windows.net/powerbi/api',
        // Registered App ApplicationID.
        'client_id' => self::getClientID(),
        // Registered WorkspaceID.
        'workspace_id' => self::getWorkspaceID(),
        // For example john.doe@yourdomain.com.
        'username' => self::getUsername(),
        // Azure password for above user.
        'password' => self::getPassword(),
      ],
    ];
    curl_setopt_array($curlPostToken, $theCurlOpts);
    $tokenResponse = curl_exec($curlPostToken);
    $tokenError = curl_error($curlPostToken);
    curl_close($curlPostToken);

    // Decode result, and store the access_token in $embeddedToken variable:
    $token = '';
    if ($tokenError) {
      \Drupal::logger('powerbi_embed')->error("cURL Error #:" . $tokenError);
    }
    else {
      $tokenResult = json_decode($tokenResponse, TRUE);
      $token = $tokenResult['access_token'];
    }

    return $token;
  }

  /**
   * Generate an access token by using MSAL method.
   *
   * @return string
   *   The access token.
   */
  protected static function generateMsalAccessToken() {
    $config = self::getConfig();
    $client_id = $config->get('msal.client_id');
    $client_secret = $config->get('msal.client_secret');
    $tenant = $config->get('msal.tenant');
    $scope = $config->get('msal.scope');
  
    $client = \Drupal::httpClient();
    $response = $client->post("https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token", [
      'form_params' => [
        'grant_type' => 'client_credentials',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'scope' => $scope,
      ],
    ]);
  
    $body = json_decode($response->getBody()->getContents(), TRUE);
    return $body['access_token'];
  }
  // protected static function generateMsalAccessToken() {
  //   $config = self::getConfig()->get('msal');

  //   if (empty($config['tenant']) || empty($config['client_id']) || empty($config['client_secret'])) {
  //     throw new \Exception('Either Tenant or client id or client secret not provided');
  //   }

  //   $client = \Drupal::httpClient();
  //   $tenant = $config['tenant'];

  //   $params = [
  //     'scope' => $config['scope'] ?? 'https://graph.microsoft.com/.default',
  //     'client_id' => $config['client_id'],
  //     'client_secret' => $config['client_secret'],
  //     'grant_type' => 'client_credentials',
  //   ];

  //   try {
  //     $response = $client->post("https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token", [
  //       'form_params' => $params,
  //       'headers' => [
  //         'Accept' => 'application/json',
  //         'Content-Type' => 'application/x-www-form-urlencoded',
  //       ],
  //     ]);
  //   }
  //   catch (ClientException $e) {
  //     $response = json_decode($e->getResponse()->getBody()->getContents(), TRUE);
  //     \Drupal::logger('powerbi_embed')->error('MSAL token generation error:' . $response['error_description']);

  //     return '';
  //   }

  //   $body = json_decode($response->getBody()->getContents(), TRUE);

  //   return $body['access_token'];
  // }

  /**
   * Return PowerBI Token value.
   */
  // public static function getPowerBIToken() {
  //   $auth_method = self::getAuthMethod();

  //   switch ($auth_method) {
  //     case AuthType::ADAL:
  //       return self::generateAdalAccessToken();

  //     case AuthType::MSAL:
  //       return self::generateMsalAccessToken();
  //   }

  //   throw new \Exception(sprintf('"%s" as auth method not supported', $auth_method));
  // }
  public static function getPowerBIToken() {
    $authMethod = self::getAuthMethod();
    \Drupal::logger('powerbi_embed')->info('Auth method: @method', ['@method' => $authMethod]);
  
    if ($authMethod === 'msal') {
      return self::generateMsalAccessToken();
    } elseif ($authMethod === 'adal') {
      return self::generateAdalAccessToken();
    } else {
      throw new \Exception('"'.$authMethod.'" as auth method not supported');
    }
  }

  public static function getEmbedToken($token, $workspace_id, $report_id) {
    if (!$token) {
      throw new \Exception('Access token not provided');
    }

    $client = \Drupal::httpClient();
    $headers = [
      'Authorization' => "Bearer {$token}",
      'Cache-Control' => 'no-cache',
      'Accept' => 'application/json',
      'Content-Type' => 'application/x-www-form-urlencoded',
    ];

    try {
      $response = $client->post("https://api.powerbi.com/v1.0/myorg/groups/{$workspace_id}/reports/{$report_id}/GenerateToken", [
        'form_params' => [
          'accessLevel' => 'View',
        ],
        'headers' => $headers,
      ]);
    }
    catch (ClientException $e) {
      $response = json_decode($e->getResponse()->getBody()->getContents(), TRUE);
      $message = $response['error_description'] ?? $response['error']['message'] ?? $e->getMessage();

      \Drupal::logger('powerbi_embed')->error('Generating embed token has failed:' . $message);

      return '';
    }

    $body = json_decode($response->getBody()->getContents(), TRUE);

    return $body['token'];
  }

  /**
   * Return PowerBI embedded URL value.
   */
  public static function getPowerBIURL($token, $workspace_id, $report_id) {
    if (!$token) {
      throw new \Exception('Access token not provided');
    }

    $client = \Drupal::httpClient();
    $headers = [
      'Authorization' => "Bearer {$token}",
      'Cache-Control' => 'no-cache',
    ];

    try {
      $response = $client->get("https://api.powerbi.com/v1.0/myorg/groups/{$workspace_id}/reports/{$report_id}", [
        'headers' => $headers,
      ]);
    }
    catch (ClientException $e) {
      $response = json_decode($e->getResponse()->getBody()->getContents(), TRUE);
      $message = $response['error_description'] ?? $response['error']['message'] ?? $e->getMessage();

      \Drupal::logger('powerbi_embed')->error('Generating reports embed url has failed:' . $message);

      return '';
    }

    $body = json_decode($response->getBody()->getContents(), TRUE);

    return $body['embedUrl'];
  }

}
