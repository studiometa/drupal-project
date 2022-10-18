<?php

namespace Drupal\studiometa_pages_manager\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\studiometa\Manager\Node\NodeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SiteConfigurationForm.
 */
final class SiteConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'studiometa_pages_manager.site_configuration',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'site_configuration_page_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('studiometa_pages_manager.site_configuration');

    // Short description about this page.
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Configure your global parameters.'),
    ];

    // Create form structure.
    $form['parent'] = [
      '#type' => 'vertical_tabs',
    ];

    // Global.
    $form['global'] = [
      '#type' => 'details',
      '#title' => $this->t('Général'),
      '#group' => 'parent',
    ];

    // Social Networks.
    $form['social-networks'] = [
      '#type' => 'details',
      '#title' => $this->t('Social network'),
      '#group' => 'parent',
    ];

    $social_networks = [
      'facebook' => $this->t('Facebook'),
      'instagram' => $this->t('Instagram'),
      'twitter' => $this->t('twitter'),
      'youtube' => $this->t('Youtube'),
    ];

    foreach ($social_networks as $name => $social_network) {
      $form['social-networks'][$name] = [
        '#type' => 'textfield',
        '#title' => $social_network,
        '#required' => FALSE,
        '#default_value' => $config->get($name),
      ];
    }

    // Listings.
    $form['listings'] = [
      '#type' => 'details',
      '#title' => $this->t('Listings'),
      '#group' => 'parent',
    ];

    // Actions.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);

    $config = $this->config('studiometa_pages_manager.site_configuration');

    $values = $form_state->getValues();

    foreach ($values as $key => $value) {
      $config->set($key, $value);
    }

    $config->save();
  }

}
