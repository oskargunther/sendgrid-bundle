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

