# symfony-performance-logger
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

Provides basic symfony performance logging that's safe to use in a live environment.

## Install

In order to install this module add the following to your `composer.json`:

```text
{
  ...
  "require": {
      "edmondscommerce/symfony-performance-logger": "dev-master"
  }, 
  ...
  "repositories": [
      {
          "type": "vcs",
          "url": "https://github.com/edmondscommerce/symfony-performance-logger.git"
      }
  ],
  ...
}
```

## Usage

In order to use these listeners you simply need to add the following to your `config/services.yaml`:

```yaml
services:
kernel.listener.command_dispatch:
        class: EdmondsCommerce\SymfonyPerformanceLogger\PerformanceListener
        tags:
            - { name: kernel.event_listener, event: console.command }
            - { name: kernel.event_listener, event: console.terminate }
            - { name: kernel.event_listener, event: kernel.request }
            - { name: kernel.event_listener, event: kernel.terminate }
```