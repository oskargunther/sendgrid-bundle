# Readme
# Symfony =2.8 SendGrid Bundle
## Documentation


### Features
1. Configure sendgrid-php through yaml
2. Disable delivery through parameter
3. WebProfiler Extension
4. WebHook events dispatcher
5. Redirect all messages to specific address

## Using the Bundle.

### Installation:

```
composer require oskargunther/sendgrid-bundle
```

    
    
### Add bundle to kernel:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            ....
            new OG\SendGridBundle\OGSendGridBundle(),
        ];

    }
}
```

### Configuration:

- config.yml

```yaml
og_send_grid:
    api_key: string
    disable_delivery: false # default false
```

- config_dev.yml

```yaml
og_send_grid:
    web_profiler: true # default %kernel.debug% (enabled)
    redirect_to: test@test.com # default false
```

### Usage:

```php
use OG\SendGridBundle\Exception\SendGridException;

$provider = $this-get('og_send_grid.provider');

$email = $provider-createMessage();

$email-setFrom("test@test.pl", "Example User");
$email-setSubject("Test subject");
$email-addTo("o.gunther@test.pl", "Example User");
$email-addContent("text/plain", "and easy to do anywhere, even with PHP");
$email-addContent("text/html", "<strongand easy to do anywhere, even with PHP</strong");

try {
    $messageId = $provider-send($email);
} catch (SendGridException $e) {
    echo 'Caught exception: '. $e-getMessage() ."\n";
}
```

## Working with SendGrid WebHook events

### Configuration:

- Event subscriber:

```php
namespace AppBundle\Subscriber;


use OG\SendGridBundle\Event\WebHookEvent;
use OG\SendGridBundle\EventSubscriber\WebHookEventSubscriber;

class WebHookSubcriber extends WebHookEventSubscriber
{
    function onBounce(WebHookEvent $event)
    {
        $event-getWebHook()-getSmtpId();
    }

    function onClick(WebHookEvent $event)
    {
    }
    
    function onDeferred(WebHookEvent $event)
    {
    }

    function onDelivered(WebHookEvent $event)
    {
    }
    
    function onDropped(WebHookEvent $event)
    {
    }

    function onGroupResubscribe(WebHookEvent $event)
    {
    }

    function onGroupUnsubscribe(WebHookEvent $event)
    {
    }

    function onOpen(WebHookEvent $event)
    {
    }

    function onProcessed(WebHookEvent $event)
    {
    }

    function onSpamreport(WebHookEvent $event)
    {
    }

    function onUnsubscribe(WebHookEvent $event)
    {
    }

}
```

- routing.yml

```yaml
sendgrid_webhook:
    path: /sendgrid/webhook
    controller: OGSendGridBundle:WebHook:dispatch
```

- services.yml

```yaml
app.subscriber.send_grid:
    class: AppBundle\Subscriber\WebHookSubcriber
    tags:
    - { name: kernel.event_subscriber }
```

## Profiling sent messages (even if delivery is disabled):

1. Messages count
2. [sendgrid/sendgrid-php](https://github.com/sendgrid/sendgrid-php) - execution time in miliseconds
3. [symfony/stopwatch](https://github.com/symfony/stopwatch) events

![alt text](https://oskargunther.github.io/sendgrid-bundle/Doc/profiler.png)