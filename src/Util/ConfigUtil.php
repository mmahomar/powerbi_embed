<?php

namespace Drupal\powerbi_embed\Util;

use Drupal\powerbi_embed\AuthType;
use GuzzleHttp\Exception\ClientException;

/**
 * Configuration interface class for powerbi_embed module.
 */
class ConfigUtil {

  private static function getConfig() {
    return \Drupal::config('powerbi_embed.settings');
  }

  public static function getClientID() {
    $config = self::getConfig();
    return $config->get('adal.client_id');
  }

  public static function getWorkspaceID() {
    $config = self::getConfig();
    return $config->get('workspace_id');
  }

  public static function getAuthMethod() {
    return self::MSAL;
  }
  
  protected static function generateMsalAccessToken() {
    $config = self::getConfig();
    $client_id = $config->get('client_id');
    $client_secret = $config->get('client_secret');
    $tenant = $config->get('tenant');
    $scope = $config->get('scope');

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
  
  public static function getPowerBIToken() {
    return self::generateMsalAccessToken();
  }

  private static function logMessage($message) {
    \Drupal::logger('powerbi_embed')->info($message);
  }

  private static function getLoggedInEmail() {
    $account = \Drupal::currentUser();
    $email = $account->getEmail();
    self::logMessage("Current logged in email: {$email}");
    return $email;
  }

  public static function getReportDetails($token, $workspace_id, $report_id) {
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
    } catch (ClientException $e) {
      $response = json_decode($e->getResponse()->getBody()->getContents(), TRUE);
      $message = $response['error_description'] ?? $response['error']['message'] ?? $e->getMessage();

      \Drupal::logger('powerbi_embed')->error('Fetching report details has failed:' . $message);

      return null;
    }

    $body = json_decode($response->getBody()->getContents(), TRUE);

    return $body;
  }

  public static function getEmbedToken($token, $workspace_id, $report_id) {
    if (!$token) {
      throw new \Exception('Access token not provided');
    }

    $email = self::getLoggedInEmail();
    self::logMessage("Generating embed token for user with email: {$email}");

    $reportDetails = self::getReportDetails($token, $workspace_id, $report_id);

    if (!$reportDetails || !isset($reportDetails['datasetId'])) {
      self::logMessage("Failed to fetch report details or dataset ID is missing");
      return '';
    }

    $dataset_id = $reportDetails['datasetId'];
    self::logMessage("Using dataset ID: {$dataset_id} for report: {$report_id}");

    $client = \Drupal::httpClient();
    $headers = [
      'Authorization' => "Bearer {$token}",
      'Cache-Control' => 'no-cache',
      'Accept' => 'application/json',
      'Content-Type' => 'application/json',
    ];

    $body = [
      'accessLevel' => 'View',
      'identities' => [
        [
          'username' => $email,
          'roles' => ['UsernameDynamic'],
          'datasets' => [$dataset_id]
        ]
      ]
    ];

    try {
      $response = $client->post("https://api.powerbi.com/v1.0/myorg/groups/{$workspace_id}/reports/{$report_id}/GenerateToken", [
        'json' => $body,
        'headers' => $headers,
      ]);
    } catch (ClientException $e) {
      $response = json_decode($e->getResponse()->getBody()->getContents(), TRUE);
      $message = $response['error_description'] ?? $response['error']['message'] ?? $e->getMessage();

      self::logMessage("Generating embed token has failed: {$message}");

      return '';
    }

    $body = json_decode($response->getBody()->getContents(), TRUE);

    self::logMessage("Embed token generated successfully for user with email: {$email}");

    return $body['token'];
  }

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
    } catch (ClientException $e) {
      $response = json_decode($e->getResponse()->getBody()->getContents(), TRUE);
      $message = $response['error_description'] ?? $response['error']['message'] ?? $e->getMessage();

      \Drupal::logger('powerbi_embed')->error('Generating reports embed url has failed:' . $message);

      return '';
    }

    $body = json_decode($response->getBody()->getContents(), TRUE);

    return $body['embedUrl'];
  }
}
