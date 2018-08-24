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
$provider = $this->get('og_send_grid.provider');

$email = $provider->createMessage();

$email->setFrom("test@test.pl", "Example User");
$email->setSubject("Testn");
$email->addTo("o.gunther@test.pl", "Example User");
$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
$email->addContent(
    "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
);

try {
    $response = $provider->send($email);
} catch (SendGridException $e) {
    echo 'Caught exception: '. $e->getMessage() ."\n";
}
```
