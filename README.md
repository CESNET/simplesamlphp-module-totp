# TOTP

TOTP is a [SimpleSAMLphp](https://simplesamlphp.org/) [auth processing filter](https://simplesamlphp.org/docs/stable/simplesamlphp-authproc) that enables the use of the _Time-Based One-Time Password Algorithm_ (TOTP) as a second-factor authentication mechanism on either an Identity Provider or Service Provider (or both).

This has been tested with Google Authenticator and FreeOTP.

As an auth processing filter, this module is flexible in a number of ways:

- agnostic to where the TOTP secret is stored
- can be enabled on select Service Providers or an entire Identity Provider

## Usage

Like any other auth process filter, this module needs to be configured in an authproc array in either config.php or in the metadata for a particular service provider or identity provider.

### Prerequisites

The `secret_attr` needs to be available in the attribute payload as it is used to generate the token for comparison. This can be added using other auth process filters to look up an external databases of sorts (SQL, LDAP, etc).

After the module has been called, the attribute will be moved out of the user attribute array. As a safety precaution an extra step should be taken ensure this attribute is removed. This can be done using the `core:AttributeAlter` filter or similar.

### Example

Placed in either config.php's authproc or in the appropriate metadata entity:

```php
10 => array(
	'class' => 'totp:Totp',
	'secret_attr' => 'totp_secret', //default
	'enforce_2fa' => false, //default
	'not_configured_url' => NULL,  //default
),
```

Placed in config.php authproc as one of the last functions to be processed:

```php
99 => array(
	'class' => 'core:AttributeAlter',
	'subject' => 'totp_secret',
	'pattern' => '/.*/',
	'%remove',
),
```

Example of how it can work with example-userpass module. Below config goes in authsource.php
This module is enabled by default but if it is not make sure you create a file called enable
inside modules/exampleauth directory.

```php
	'example-userpass' => array(
		'exampleauth:UserPass',
		'student:studentpass' => array(
			'uid' => array('test'),
			'ga_secret' => array('4HX4WBKVIJWDUV5I'),
			'eduPersonAffiliation' => array('member', 'student'),
		),
	),
```

After logging in with username: `student`, password: `studentpass`, you will be challenged for TOTP.
`4HX4WBKVIJWDUV5I` is a secret key that can be generate by visiting `/simplesaml/module.php/totp/generate_token.php`

A random one will be generated on the first load and saved in the session. A new token is generated when the page is visited with a fresh session. You can use the QR code to register your IdP with apps such as FreeOTP, Google Authenticator etc.

**NOTE**: for TOTP to work you **MUST** ensure that the clock on your server is in sync. If it is not, a matching token will never be generated and authentication will fail.

## Installation

## DecryptSecrets filter

DecryptSecrets filter decrypts encrypted secrets and save them to Attributes array. This filter also supports verification of signed secrets and takes configuration options from module_totp.php. If secret signature verification fails the token can not be used.

## Perun integration

In order to use PerunStorage and sync tokens with Perun, you have to add PerunStorage array with configuration options to `module_totp.php`.

## Installation

This module uses Composer for dependencies. To install it, clone the repository and use `composer install`.

## Notes

This module does not offer brute force protection, which is required to ensure security (there are only 100000 options for a 6 digit code). The authentication page should be brute force protected in order to stop potential attackers.
