# Usage example

```php
$formatterManager = service(FormatterManager::class);
$settings = new Medas();

$code = '<?php ...';

$formattedCode = $formatterManager->format($code, $settings);
```
