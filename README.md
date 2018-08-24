Instalation

    composer require oskargunther/sendgrid-bundle
    
    
Add bundle to kernel:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            ....
            new \OG\SendGridBundle\OGSendGridBundle(),
            
        ];

    }
}
```

Configuration:

```yaml
og_send_grid:
   api_key: string
   disable_delivery: false
```


Usage:

```php
$provider = $container->get('disable_delivery');

$message = $provider->createNewMessage();

$email->setFrom("test@example.com", "Example User");
$email->setSubject("Sending with SendGrid is Fun");
$email->addTo("test@example.com", "Example User");
$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
$email->addContent(
    "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
);

try {
    $response = $sendgrid->send($email);
} catch (OG\SendGridBundle\Exception\SendGridException $e) {
    echo 'Caught exception: '. $e->getMessage() ."\n";
}
```
