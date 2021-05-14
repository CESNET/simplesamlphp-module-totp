<?php

namespace SimpleSAML\Module\totp\Auth\Process;

use SimpleSAML\Auth\ProcessingFilter;
use SimpleSAML\Auth\State;
use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSAML\Module;
use SimpleSAML\Utils\HTTP;

/**
 * TOTP Authentication Processing filter
 */
class Totp extends ProcessingFilter
{
    /**
     * Attribute that stores the TOTP secret
     */
    private $secret_attr = 'totp_secret';

    /**
     * Value of the TOTP secret
     */
    private $secret_vals = null;

    /**
     * Whether or not the user should be forced to use 2fa.
     *  If false, a user that does not have a TOTP secret will be able to continue
     *   authentication
     */
    private $enforce_2fa = false;

    /**
     * External URL to redirect user to if $enforce_2fa is true and they do not
     *  have a TOTP attribute set.  If this attribute is NULL, the user will
     *  be redirect to the internal error page.
     */
    private $not_configured_url = null;

    private $skip_redirect_url = null;

    /**
     * Initialize the filter.
     *
     * @param array $config  Configuration information about this filter.
     * @param mixed $reserved  For future use
     */
    public function __construct(array $config, $reserved)
    {
        parent::__construct($config, $reserved);

        $config = Configuration::loadFromArray($config);

        $this->enforce_2fa = $config->getBoolean('enforce_2fa', $this->enforce_2fa);
        $this->secret_attr = $config->getString('secret_attr', $this->secret_attr);
        $this->skip_redirect_url = $config->getString('skip_redirect_url', $this->skip_redirect_url);
        $this->not_configured_url = HTTP::checkURLAllowed(
            $config->getString('not_configured_url', $this->not_configured_url)
        );
    }

    /**
     * Apply TOTP filter
     *
     * @param array &$state  The current state
     */
    public function process(&$state)
    {
        assert(is_array($state));
        assert(isset($state['Attributes']));

        $attributes = $state['Attributes'];

        // check for secret_attr coming from user store and make sure it is not empty
        if (!empty($attributes[$this->secret_attr])) {
            $this->secret_vals = $attributes[$this->secret_attr];
        }

        if ($this->secret_vals === null && $this->enforce_2fa === true) {
            #2f is enforced and user does not have it configured..
            Logger::debug('User with ID xxx does not have 2f configured when it is
            mandatory for xxxSP');

            //send user to custom error page if configured
            if ($this->not_configured_url !== null) {
                HTTP::redirectUntrustedURL($this->not_configured_url);
            } else {
                HTTP::redirectTrustedURL(Module::getModuleURL('totp/not_configured.php'));
            }
        } elseif ($this->secret_vals === null && $this->enforce_2fa === false) {
            Logger::debug('User with ID xxx does not have 2f configured but SP does not
            require it. Continue.');
            return;
        }

        //as the attribute is configurable, we need to store it in a consistent location
        $state['2fa_secrets'] = $this->secret_vals;
        $state['skip_redirect_url'] = $this->skip_redirect_url;

        //this means we have secret_vals configured for this session, time to 2fa
        $id = State::saveState($state, 'totp:request');
        $url = Module::getModuleURL('totp/authenticate.php');

        HTTP::redirectTrustedURL($url, ['StateId' => $id]);
    }
}
