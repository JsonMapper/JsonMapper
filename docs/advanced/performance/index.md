---
permalink: /docs/advanced/performance
title: Performance  
---

The JsonMapper library has not only been build for comfort but also taking performance into account. This
page touches some of the performance improvements you could consider based on the needs.

## Large arrays 
If your planning to map large arrays using this library it might be very helpful to tweak your JsonMapper
object to best fit your needs. As an example the JsonMapperFactory will load both the [DocBlock Annotations]({% link docs/middleware/doc-block-annotations/index.md %}) 
and the [Types Properties]({% link docs/middleware/typed-properties/index.md %}) middleware which do the same for different version of the PHP runtime.

## Large nested objects
When dealing with large nested objects it could help the performance it you where to load the property map up front. This could easily be achieved using 
a custom middleware where the property map is populated with written out property information.   