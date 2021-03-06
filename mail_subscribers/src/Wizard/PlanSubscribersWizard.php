<?php
/********************************************************* {COPYRIGHT-TOP} ***
 * Licensed Materials - Property of IBM
 * 5725-L30, 5725-Z22
 *
 * (C) Copyright IBM Corporation 2018, 2020
 *
 * All Rights Reserved.
 * US Government Users Restricted Rights - Use, duplication or disclosure
 * restricted by GSA ADP Schedule Contract with IBM Corp.
 ********************************************************** {COPYRIGHT-END} **/

namespace Drupal\mail_subscribers\Wizard;

use Drupal\ctools\Event\WizardEvent;
use Drupal\ctools\Wizard\FormWizardBase;
use Drupal\ctools\Wizard\FormWizardInterface;

class PlanSubscribersWizard extends FormWizardBase {

  /**
   * {@inheritdoc}
   */
  public function getWizardLabel() {
    return t('Mail Plan Subscribers Wizard');
  }

  /**
   * {@inheritdoc}
   */
  public function getMachineLabel(): string {
    return 'mail_plan_subscribers_wizard';
  }

  /**
   * {@inheritdoc}
   */
  public function getRouteName() {
    return 'mail_subscribers.plan_wizard.step';
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations($cached_values): array {
    $steps = [];

    $steps['chooseitem'] = [
      'title' => t('Select a product'),
      'form' => 'Drupal\mail_subscribers\Wizard\Mail\ChooseProductStep',
    ];

    $steps['chooseplan'] = [
      'title' => t('Select a plan'),
      'form' => 'Drupal\mail_subscribers\Wizard\Mail\ChoosePlanStep',
    ];

    $steps['choosesubs'] = [
      'title' => t('Select Subscribers'),
      'form' => 'Drupal\mail_subscribers\Wizard\Mail\ChooseRoleStep',
    ];

    $steps['entercontent'] = [
      'title' => t('Enter content'),
      'form' => 'Drupal\mail_subscribers\Wizard\Mail\EnterContentStep',
    ];

    $steps['confirm'] = [
      'title' => t('Confirm'),
      'form' => 'Drupal\mail_subscribers\Wizard\Mail\ConfirmSend',
    ];

    $steps['summary'] = [
      'title' => t('Summary'),
      'form' => 'Drupal\mail_subscribers\Wizard\Mail\MailSummary',
    ];

    return $steps;
  }

  public function initValues() {
    $values = [];
    $event = new WizardEvent($this, $values);
    $this->dispatcher->dispatch(FormWizardInterface::LOAD_VALUES, $event);
    $tempValues = $event->getValues();
    $tempValues['objectType'] = 'plan';
    $event->setValues($tempValues);
    return $event->getValues();
  }
}
