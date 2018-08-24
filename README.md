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

