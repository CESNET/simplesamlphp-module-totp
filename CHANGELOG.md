## [3.1.3](https://github.com/CESNET/simplesamlphp-module-totp/compare/v3.1.2...v3.1.3) (2021-11-20)


### Bug Fixes

* PerunStorage does not extend DatabaseStorage ([c727f90](https://github.com/CESNET/simplesamlphp-module-totp/commit/c727f90505eec1cb65c7ce241dfd9beb5da4cb3b))

## [3.1.2](https://github.com/CESNET/simplesamlphp-module-totp/compare/v3.1.1...v3.1.2) (2021-11-01)


### Bug Fixes

* add license to composer.json ([d80a270](https://github.com/CESNET/simplesamlphp-module-totp/commit/d80a2701e0386276573ae5223bdf842e86aa919d))

## [3.1.1](https://github.com/CESNET/simplesamlphp-module-totp/compare/v3.1.0...v3.1.1) (2021-10-23)


### Bug Fixes

* **deps:** update dependency robthree/twofactorauth to v1.8.1 ([7efcc5f](https://github.com/CESNET/simplesamlphp-module-totp/commit/7efcc5ff24e4024a3b058080c010acd0793c6aff))

# [3.1.0](https://github.com/CESNET/simplesamlphp-module-totp/compare/v3.0.1...v3.1.0) (2021-09-24)


### Features

* show TOTP secret under the QR code ([80a38af](https://github.com/CESNET/simplesamlphp-module-totp/commit/80a38af27dd258ff7bc4ba9f02fc92366cb0c4c2)), closes [#10](https://github.com/CESNET/simplesamlphp-module-totp/issues/10)

## [3.0.1](https://github.com/CESNET/simplesamlphp-module-totp/compare/v3.0.0...v3.0.1) (2021-09-22)


### Bug Fixes

* **deps:** pin dependencies ([1ea97db](https://github.com/CESNET/simplesamlphp-module-totp/commit/1ea97dbbcd9e69e72c3412de1eb7a7a409f6f75a))

# [3.0.0](https://github.com/CESNET/simplesamlphp-module-totp/compare/v2.0.0...v3.0.0) (2021-09-22)


### Bug Fixes

* composer package name cesnet ([8eaab0f](https://github.com/CESNET/simplesamlphp-module-totp/commit/8eaab0fc401e097d737984e8ad2e233f631caf56))


### BREAKING CHANGES

* new package name cesnet/simplesamlphp-module-totp

# [2.0.0](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/compare/v1.2.1...v2.0.0) (2021-09-14)


### Bug Fixes

* generate tokens refresh fixed, signing tokens data changed ([95da413](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/commit/95da41387fa165f254c87e9cd86f76baf90ad440))


### BREAKING CHANGES

* Removed "payload" from signed tokens data

## [1.2.1](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/compare/v1.2.0...v1.2.1) (2021-09-09)


### Bug Fixes

* added ext-imagick to composer.json ([6b19fc8](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/commit/6b19fc839b418f560e1b9861297bb61d4b0f00b0))

# [1.2.0](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/compare/v1.1.0...v1.2.0) (2021-08-31)


### Features

* prevent generate token refresh ([25d7430](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/commit/25d74307213fd8a1318078ae242a1de65b99f410))

# [1.1.0](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/compare/v1.0.0...v1.1.0) (2021-08-19)


### Features

* token signature support, DecryptSecrets filter, storage classes ([fdde0ef](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/commit/fdde0ef1cfe8c50ab71f70f3bb475daba0c05204))

# 1.0.0 (2021-08-18)


### Bug Fixes

* add translation for submit button ([901203c](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/commit/901203cacc68149fffba5d744f0c6f9108fa0d18))
* default authn context ([e7cbbb6](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/commit/e7cbbb63de8eae1117c02274a8e3d47a3a6e530b))
* small bugfixes in template ([6525635](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/commit/6525635b0ca418ca3bb85e931ad05f83fed6a8d9))
* use inputmode numeric instead of type number ([2d1d3af](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/commit/2d1d3afdfc25c34cd5ebfbad04bdf55536c63671))


### Features

* add support for switching to other authentication method ([b36a6a4](https://gitlab.ics.muni.cz/perun/proxyaai/simplesamlphp/simplesamlphp-module-totp/commit/b36a6a497c3c193683307b6cfcdeeaec037162f6))
