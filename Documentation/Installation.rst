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

Usage
=====

In your ``config/system/additional.php`` (or ``public/typo3conf/AdditionalConfiguration.php``),
initialize the ``ConfigProvider`` using the default initializer:

.. code-block:: php

    use Maispace\MaiEnvironment\ConfigProvider\ConfigProvider;

    ConfigProvider::initialize()
        ->appendContextToSiteName();

``ConfigProvider::initialize()`` automatically applies context-appropriate presets:
it calls ``useDevelopmentPreset()`` for ``Development`` and ``Testing`` contexts
(plus ``useDDEVConfiguration()`` for either when DDEV is detected), and
``useProductionPreset()`` for ``Production``. It also calls
``includeContextDependentConfigurationFiles()`` at the end, so no additional
configuration is required for a typical setup.

If you need full control, pass ``false`` to skip the automatic defaults:

.. code-block:: php

    use Maispace\MaiEnvironment\ConfigProvider\ConfigProvider;

    ConfigProvider::initialize(false)
        ->useDDEVConfiguration()
        ->useDevelopmentPreset()
        ->includeContextDependentConfigurationFiles();

Context-Dependent Files
=======================

Place context-specific overrides in ``config/system/`` using a lowercase
file name that matches the TYPO3 application context:

.. code-block:: text

    config/
    └── system/
        ├── development.php
        ├── testing.php
        └── production.php

Each file is included automatically when the matching TYPO3 application context
is active.
