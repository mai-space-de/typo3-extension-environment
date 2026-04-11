## Environment Configuration

* `.env` file loading — loads environment variables from `.env` via `dotenv-connector` before TYPO3 bootstraps
* Environment detection — reads `APP_ENV` or TYPO3 context to identify the runtime environment
* Configuration helpers — utility methods for conditionally applying configuration based on environment
* Install tool access — `cms-install` is declared here as the install tool is an environment-management concern
