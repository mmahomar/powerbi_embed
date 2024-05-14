<?php

namespace Drupal\powerbi_embed;

/**
 * Define auth type constants.
 */
abstract class AuthType {

  /**
   * Azure Active Directory Authentication Library.
   */
  const ADAL = 'adal';

  /**
   * Microsoft Authentication Library.
   */
  const MSAL = 'msal';

}
