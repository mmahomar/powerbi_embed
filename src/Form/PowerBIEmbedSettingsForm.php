<?php

namespace Drupal\powerbi_embed\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class PowerBIEmbedSettingsForm extends ConfigFormBase {

  public function getFormId() {
    return 'powerbi_embed_settings';
  }

  protected function getEditableConfigNames() {
    return [
      'powerbi_embed.settings',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('powerbi_embed.settings');

    $form['workspace_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Workspace ID'),
      '#description' => $this->t('PowerBI Workspace ID'),
      '#default_value' => $config->get('workspace_id') ?? '',
    ];

    $form['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#description' => $this->t('The application ID that\'s assigned to your app.'),
      '#default_value' => $config->get('client_id') ?? '',
    ];

    $form['client_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#description' => $this->t('The client secret that was generated for your app in the app registration portal.'),
      '#default_value' => $config->get('client_secret') ?? '',
    ];

    $form['tenant'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tenant'),
      '#description' => $this->t('The directory tenant the application plans to operate against, in GUID or domain-name format.'),
      '#default_value' => $config->get('tenant') ?? '',
    ];

    $form['scope'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Scope'),
      '#description' => $this->t('PowerBI scope parameter'),
      '#default_value' => $config->get('scope') ?? '',
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('powerbi_embed.settings');
    $config->set('workspace_id', $form_state->getValue('workspace_id'));
    $config->set('client_id', $form_state->getValue('client_id'));
    $config->set('client_secret', $form_state->getValue('client_secret'));
    $config->set('tenant', $form_state->getValue('tenant'));
    $config->set('scope', $form_state->getValue('scope'));
    $config->save();

    parent::submitForm($form, $form_state);
  }
}
