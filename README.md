# symfony-performance-logger
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

Provides basic Symfony performance logging that's safe to use in a live environment.

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
    # Log performance metrics
    performance.logging:
        class: EdmondsCommerce\SymfonyPerformanceLogger\PerformanceListener
        tags:
            - { name: kernel.event_listener, event: console.command }
            - { name: kernel.event_listener, event: console.terminate }
            - { name: kernel.event_listener, event: kernel.request }
            - { name: kernel.event_listener, event: kernel.terminate }

    # Configure the performance logger
    EdmondsCommerce\SymfonyPerformanceLogger\PerformanceLogger:
        arguments: [<Number of seconds you consider critically slow for a command / controller>, '@monolog.logger.performance']
```

And then create the `performance` channel for all environments `config/packages/test/monolog.yaml`,
`config/packages/dev/monolog.yaml` and `config/packages/prod/monolog.yaml`.
This is needed to ensure that `monolog.logger.performance` is available for DI (even though nothing will be logged
in the other environments):

```yaml
monolog:
    # Create performance channel
    channels: ["performance"]
```

Finally configure some logging for the `performance` channel in `config/packages/prod/monolog.yaml`:

```yaml
monolog:
    handlers:
        # Log performance data
        performance-stream:
            type: stream
            path: "%kernel.logs_dir%/%kernel.environment%.performance.log"
            level: info
            channels: ["performance"]
        # Log performance data to Slack
        performance-slackwebhook:
            type: slackwebhook
            level: critical
            bot_name: 'Bot Name'
            webhook_url: "Web Hook Url"
            channel: "Slack Channel"
            channels: ["performance"]
```
