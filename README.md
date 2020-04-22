# ajaxImageUp

### Bare bones ajax image uploader

### Uploads multiple images via ajax

Applies naming convention to uploaded files:
[field name][multiple file index]_[original filename]

Handles file name collissions: 
[field name][multiple file index]-[collission index]_[original filename]

**logo.png** uploaded twice with `uloadFieldName` set to "**filefield**" becomes: 
**filefield0_logo.png** and **filefield0-1_logo.png**

Repeating this process will produce: 
**filefield0-2_logo.png** and **filefield0-3_logo.png**
