---
permalink: /docs/middleware/namespace-resolver/  
title: Namespace resolver  
---

The namespace resolver middleware will tokenize the target object using [nikic/php-parser](https://github.com/nikic/PHP-Parser){:target="_blank"}
in order to get the namespaces that are imported.  These imports will be applied to the object properties found in the property map. 

_This middleware is part of both the default and best fit factory methods as it provides elementary functionality to JsonMapper_ 
