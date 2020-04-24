---
permalink: /docs/middleware/doc-block-annotations/  
title: DocBlock annotations  
---

The DocBlock annotations middleware will scan the target object using [Reflection](https://www.php.net/manual/en/intro.reflection.php){:target="_blank"}
for properties and their [DocBlock](https://docs.phpdoc.org/latest/references/phpdoc/index.html){:target="_blank"}  annotations. 
Using the annotations it will determine the property type and amend these results to the property map.
The property map is utilised by the PropertyMapper when applying the data from the JSON object to the target object. 

_This middleware is part of both the default and best fit factory methods as it provides elementary functionality to JsonMapper_ 