# Usage example

```php
$formatter = service(Formatter::class);
$settings = new Medas();

$code = '<?php ...';

$formattedCode = $formatter->format($code, $settings);
```
