.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

**Maispace Environment** provides environment-dependent helper functions and
configuration presets for setting up TYPO3 across different runtime contexts —
local development, DDEV, and production.

Features
========

Environment Detection
---------------------

*  Automatic detection of the current TYPO3 application context
   (``Development``, ``Testing``, ``Production``).
*  DDEV environment detection and automatic database/Redis configuration.

Configuration Presets
---------------------

*  ``useDevelopmentPreset()`` — applies common settings for local development
   (debug mode, verbose error reporting, disabled caching).
*  ``useProductionPreset()`` — applies hardened settings for production
   (error suppression, full caching, performance tuning).
*  ``useDDEVConfiguration()`` — auto-configures database credentials and Redis
   from DDEV environment variables.

Database & Caching Helpers
--------------------------

*  ``initializeDatabaseConnection()`` — sets up the TYPO3 database connection
   from environment variables or DDEV-injected credentials.
*  ``initializeRedisCaching()`` — configures the TYPO3 caching framework to
   use Redis as the backend.

Context-Dependent Config Files
-------------------------------

*  ``includeContextDependentConfigurationFiles()`` — auto-includes PHP files
   from ``config/system/additional/<Context>.php`` based on the active
   application context, enabling clean per-environment overrides.

Site Name Decoration
--------------------

*  ``appendContextToSiteName()`` — appends the current context name
   (e.g. ``[Development]``) to the TYPO3 site name for easy identification
   in multi-environment setups.
