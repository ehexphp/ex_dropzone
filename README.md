# EasyTax exDropZone (v1.0)

#### Required
> Nothing, Just  ```Model1FileLocator```


#### Use 
> Add exDropZone Plugin Folder to your project or shared plugin folder.
 >This Allows you to use exDropZone in your EasyTax Project.

```php 
    // Parameter
    echo exropZone::imagesDragDropUploadBox($model = null, $uploadToImgur = false,  $hideByFilePath = [], $maxFileCount = 6, $maxFileSize = 20, $allowedExtension = "image/*,application/pdf,.doc,.docx,.xls,.xlsx,.csv,.tsv,.ppt,.pptx,.pages,.odt,.rtf", $title = '<i class="fa fa-upload"></i> UPLOAD YOUR FILES')
    
    // Use Example
    echo exDropZone::imagesDragDropUploadBox( User::find(['id'=>1]), false ) 
```


#### Get Files
> To get saved files Urls
```php
   // From Model1FileLocator
   var_dump( Model1FileLocator::selectAll_fromDb($model) )
   
   // Or from Model
   var_dump( $model->getFileUrlList(true) )
```

