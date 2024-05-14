<?php

namespace Drupal\powerbi_embed\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\powerbi_embed\AuthType;

/**
 * Configure settings for PowerBI Embed.
 */
class PowerBIEmbedSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'powerbi_embed_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'powerbi_embed.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('powerbi_embed.settings');

    $form['workspace_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Workspace ID'),
      '#description' => $this->t('PowerBI Workspace ID'),
      '#default_value' => $config->get('workspace_id') ?? '',
    ];

    $form['auth_method'] = [
      '#type' => 'radios',
      '#title' => $this->t('Auth method'),
      '#options' => [
        AuthType::ADAL => $this->t('Azure Active Directory Authentication Library (ADAL)'),
        AuthType::MSAL => $this->t('Microsoft Authentication Library (MSAL)'),
      ],
      '#description' => $this->t('Choose a connection type for authentication and authorization functionality.'),
      '#default_value' => $config->get('auth_method') ?? AuthType::MSAL,
    ];

    $form['adal'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('ADAL connection'),
      '#tree' => TRUE,
      '#description' => $this->t('Set up Azure Active Directory Authentication Library (ADAL) for authentication and authorization functionality. Be aware, ADAL support ends in June 2023.'),
      '#states' => [
        'visible' => [
          ':input[name="auth_method"]' => ['value' => AuthType::ADAL],
        ],
      ],
    ];

    $form['adal']['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#description' => $this->t('PowerBI Client ID'),
      '#default_value' => $config->get('adal.client_id') ?? '',
    ];

    $form['adal']['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#description' => $this->t('PowerBI Username'),
      '#default_value' => $config->get('adal.username') ?? '',
    ];

    $form['adal']['password'] = [
      '#type' => 'password',
      '#title' => $this->t('PowerBI password'),
      '#description' => $this->t('PowerBI Password'),
      '#default_value' => $config->get('adal.password') ?? '',
    ];

    $form['msal'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('MSAL connection'),
      '#tree' => TRUE,
      '#description' => $this->t('Set up Microsoft Authentication Library (MSAL) for authentication and authorization functionality.'),
      '#states' => [
        'visible' => [
          ':input[name="auth_method"]' => ['value' => AuthType::MSAL],
        ],
      ],
    ];

    $form['msal']['tenant'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tenant'),
      '#description' => $this->t('The directory tenant the application plans to operate against, in GUID or domain-name format.'),
      '#default_value' => $config->get('msal.tenant') ?? '',
    ];
    $form['msal']['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#description' => $this->t("The application ID that's assigned to your app. You can find this information in the portal where you registered your app."),
      '#default_value' => $config->get('msal.client_id') ?? '',
    ];
    $form['msal']['client_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#description' => $this->t('The client secret that you generated for your app in the app registration portal.'),
      '#default_value' => $config->get('msal.client_secret') ?? '',
    ];
    $form['msal']['scope'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Scope'),
      '#description' => $this->t('PowerBI scope parameter'),
      '#default_value' => $config->get('msal.scope') ?? '',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('powerbi_embed.settings');
    $config->set('auth_method', $form_state->getValue('auth_method'));
    $config->set('workspace_id', $form_state->getValue('workspace_id'));
    $config->set('adal', $form_state->getValue('adal'));
    $config->set('msal', $form_state->getValue('msal'));
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
