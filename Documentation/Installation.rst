.. include:: /Includes.rst.txt

.. _installation:

============
Installation
============

Requirements
============

*  PHP 8.2 or later
*  TYPO3 CMS 13.4 LTS
*  Composer (recommended)

Composer Installation
=====================

Run the following command in your TYPO3 project root:

.. code-block:: bash

    composer require maispace/mai-environment

Activate the Extension
======================

The extension is activated automatically when installed via Composer. If you manage
extensions via the TYPO3 backend, activate ``mai_environment`` in the **Extension Manager**.

Include TypoScript
==================

Include the TypoScript setup in your site's TypoScript template:

.. code-block:: typoscript

    @import 'EXT:mai_environment/Configuration/TypoScript/setup.typoscript'

Usage
=====

In your ``config/system/additional.php`` (or ``public/typo3conf/AdditionalConfiguration.php``),
initialize the ``ConfigProvider`` to apply the appropriate environment preset:

.. code-block:: php

    use Maispace\MaiEnvironment\ConfigProvider\ConfigProvider;

    (new ConfigProvider())
        ->includeContextDependentConfigurationFiles()
        ->appendContextToSiteName();

For DDEV environments the provider detects the context automatically:

.. code-block:: php

    use Maispace\MaiEnvironment\ConfigProvider\ConfigProvider;

    (new ConfigProvider())
        ->useDDEVConfiguration()
        ->useDevelopmentPreset()
        ->includeContextDependentConfigurationFiles();

Context-Dependent Files
=======================

Place context-specific overrides in ``config/system/additional/``:

.. code-block:: text

    config/
    └── system/
        └── additional/
            ├── Development.php
            ├── Testing.php
            └── Production.php

Each file is included automatically when the matching TYPO3 application context
is active.
